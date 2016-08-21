<?php

class LP_ObjectList {
    /**
     * Arguments set by user
     *
     * @since 1.0
     * @access private
     * @var array
     */
    private $args = [];

	/**
	 * API url
	 * @since 1.0
	 * @access private
	 * @var string
	 */

	private $api_url;
    /**
     * Authorazation token
     * @since 1.0
     * @access private
     * @var string
     */

    private $token;

    /**
     * Objects array
     *
     * @since 1.0
     * @access private
     * @var array
     */

    public $objects;

    /**
     * Total sale objects in DB
     *
     * @since 1.0
     * @access private
     * @var integer
     */

    private $total_sale = 0;

    /**
     * Total offmarket objects in DB
     *
     * @since 1.0
     * @access private
     * @var integer
     */

    private $total_offmarket = 0;

    /**
     * Error
     *
     * @since 1.0
     * @access public
     * @var array
     */

    public $error = false;

	/**
	 * Error Message
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */

    public $error_message;

    public function __construct($args = []) {
        $defaults = [
            'lang'  => 'en',
            'page'  => 1,
            'per_page'  => 9,
            'for_sale'  => true,
            'for_rent'  => false
        ];
        $this->args = array_merge($defaults, $args);
	    $this->api_url = get_field('api_url', 'option');
	    $this->token = get_field('api_key', 'option');
	    if(empty($this->api_url) || empty($this->token)) {
		    $this->error = true;
		    $this->error_message = 'Set the API url and API key';
	    }
    }

    /**
     * Get objects from API
     *
     * @since 1.0
     * @access pubic
     * @return string
     */

    public function get_api_objects() {
        $url = $this->api_url . $this->args['lang'] . '/property_objects/';
        if(isset($this->args['slug'])) {
            $url .= $this->args['slug'];
        } else {
            $url .= '?page=' . $this->args['page'] . '&for_sale=' . $this->args['for_sale'] . '&for_rent=' . $this->args['for_rent'];
            if ( isset( $this->args['ids'] ) && is_array( $this->args['ids'] ) ) {
                foreach ( $this->args['ids'] as $id ) {
                    $url .= '&' . 'ids[]=' . (int) $id;
                }
            }
            $url .= '&per_page=' . $this->args['per_page'];
            if ( isset( $this->args['area'] ) && is_array( $this->args['area'] ) ) {
                foreach ( $this->args['area'] as $k => $v ) {
                    if ( $v !== '' ) {
                        $url .= '&area[' . $k . ']=' . $v;
                    }
                }
            }
            if ( isset( $this->args['price'] ) && is_array( $this->args['price'] ) ) {
                foreach ( $this->args['price'] as $k => $v ) {
                    if ( $v !== '' ) {
                        $url .= '&price[' . $k . ']=' . $v;
                    }
                }
            }
            if ( isset( $this->args['property_types'] ) && is_array( $this->args['property_types'] ) ) {
                foreach ( $this->args['property_types'] as $v ) {
                    $url .= '&property_types[]=' . $v;
                }
            }
            if ( isset( $this->args['rooms'] ) && is_array( $this->args['rooms'] ) ) {
                foreach ( $this->args['rooms'] as $v ) {
                    $url .= '&rooms[]=' . $v;
                }
            }
            if ( isset( $this->args['hd_photos'] ) ) {
                $url .= '&hd_photos=' . $this->args['hd_photos'];
            }
            if ( isset( $this->args['long_rent'] ) ) {
                $url .= '&long_rent=' . $this->args['long_rent'];
            }
            if ( isset( $this->args['short_rent'] ) ) {
                $url .= '&short_rent=' . $this->args['short_rent'];
            }
            if ( isset( $this->args['persons'] ) && !empty( $this->args['persons'] ) ) {
                $url .= '&persons=' . $this->args['persons'];
            }
            if ( isset( $this->args['child_friendly'] ) ) {
                $url .= '&child_friendly=' . $this->args['child_friendly'];
            }
            if ( isset( $this->args['child_friendly'] ) ) {
                $url .= '&pets_allowed=' . $this->args['pets_allowed'];
            }
        }
       // echo $url; die();
        $curl_options = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Authorization: Token token=' . $this->token
            ],
            CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_HEADER => true,
	        CURLOPT_VERBOSE => true
        ];
        $ch = curl_init();

        curl_setopt_array($ch, $curl_options);
        $resp = curl_exec($ch);
        if(!$resp) {
            curl_close($ch);
            $this->error = true;
            $this->error_message = "No connection to API";
            return json_encode(['error' => true, 'errorMessage' => $this->error_message]);
        }

	    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	    $header = substr($resp, 0, $header_size);
	    $body = substr($resp, $header_size);

        curl_close($ch);
	    $headers = $this->parse_headers($header);
	    if($headers && $headers[0] === 'HTTP/1.1 401 Unauthorized') {
		    $this->error = true;
		    $this->error_message = "Authorization failed";
		    return json_encode(['error' => true, 'errorMessage' => $this->error_message]);
	    }
	    if(!$this->isJson($body)) {
		    $this->error = true;
		    $this->error_message = "Service returns wrong format";
		    return json_encode(['error' => true, 'errorMessage' => $this->error_message]);
	    }
        return $body;
    }

    /**
     * Return LP Objects in JSON format
     *
     * @since 1.0
     * @access public
     * @param string
     * @return string
     */

    public function get_json_objects() {
        if($this->error) {
	        return json_encode(['error' => true, 'errorMessage' => $this->error_message]);
        }
        return $objects = $this->get_api_objects();
    }

    /**
     * Get general object info
     *
     * @since 1.0
     * @access public
     * @return array
     */

    public function get_objects_info() {
        $return = [];
      /*  $objects = json_decode($this->get_api_objects());
        if($this->error) {
            $return['total_sale'] = 0;
            $return['total_offmarket'] = 0;
        } else {
            $return['total_sale']      = ( $objects->total ) ? (int) $objects->total : 0;
            $return['total_offmarket'] = ( $objects->offmarket ) ? (int) $objects->offmarket : 0;
        }*/

        return $return;
    }

    /**
     * Check if string is JSON
     *
     * @since 1.0
     * @access private
     * @return bool
     */
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

	private function parse_headers($header) {
		$headersAssociative = [];

		$headers = ($header) ? $header : '';
		$headers = explode("\r\n", $headers);
		if(is_array($headers)) {
			foreach ( $headers as $i => $headerLine ) {
				if ( $headerLine === '' ) { //skip empty lines.
					continue;
				}

				$parts = explode( ': ', $headerLine );
				if ( isset( $parts[1] ) ) {
					$headersAssociative[ $parts[0] ] = $parts[1]; //use key name
				} else {
					$headersAssociative[ $i ] = $headerLine; //use index as key name
				}
			}
		} else {
			$headersAssociative = false;
		}
		return $headersAssociative;
	}
}