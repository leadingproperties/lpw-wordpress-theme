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
     * @access public
     * @var array
     */

    public $objects;


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
            'for_rent'  => false,
            'action' => 'get_objects',
            'query' => ''
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
        if($this->error) {
            return json_encode(['error' => true, 'errorMessage' => $this->error_message]);
        }
        $url = $this->api_url;

        switch($this->args['action']) {
            case 'get_counters':
                $url .= '/counters/global';
                break;
            case 'get_suggestions':
                $url .= '/suggest?q=' . urlencode($this->args['query']);
                break;
            case 'get_geopoints':
                if($this->args['type'] === 'sale') {
                    $url .= '/property_objects/geo_points';
                } elseif ($this->args['type'] === 'rent') {
                    $url .= '/property_objects/rent_geo_points';
                }
                break;
            default:

                $url .= $this->args['lang'] . '/property_objects/';
                if ( isset( $this->args['slug'] ) ) {
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
                    if ( isset( $this->args['persons'] ) && ! empty( $this->args['persons'] ) ) {
                        $url .= '&persons=' . $this->args['persons'];
                    }
                    if ( isset( $this->args['child_friendly'] ) ) {
                        $url .= '&child_friendly=' . $this->args['child_friendly'];
                    }
                    if ( isset( $this->args['child_friendly'] ) ) {
                        $url .= '&pets_allowed=' . $this->args['pets_allowed'];
                    }
                    if(isset($this->args['location_point']) && is_array($this->args['location_point'])) {
                        foreach ($this->args['location_point'] as $k => $v) {
                            $url .= '&location_point[' . $k . ']=' . $v;
                        }
                    }
                    if(isset($this->args['location_shape']) && is_array($this->args['location_shape'])) {
                        foreach ($this->args['location_shape'] as $key => $value) {
                            if(is_array($value)) {
                                foreach($value as $k => $v) {
                                    $url .= '&location_shape[' . $key . '][' .  $k . ']=' . $v;
                                }
                            } else {
                                $url .= '&location_shape[' . $key . ']=' . $value;
                            }
                        }
                    }
                }

        }

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
     * Return LP Objects in array format
     *
     * @since 1.0
     * @access public
     * @return array
     */

    public function get_objects_array() {
        return json_decode($this->get_api_objects());
    }

    /**
     * Return global counters
     * @since 1.0
     * @access public
     * @return array
     */
    public function get_global_counters() {
        $this->args['action'] = 'get_counters';
        $data = $this->get_api_objects();
        if(!$this->error) {
            return json_decode($data, true);
        } else {
            return [
                'global_counters' => [
                    'for_sale'  => '',
                    'for_rent'  => '',
                    'commercial'  => ''
                ]
            ];
        }
    }

    public function send_request_form() {
        $url = $this->api_url . '/request';
        $return = '';
        $data = [
            'first_name' => ($this->args['first_name']) ? $this->args['first_name'] : '',
            'last_name' => ($this->args['last_name']) ? $this->args['last_name'] : '',
            'phone' => ($this->args['phone']) ? $this->args['phone'] : '',
            'email' => ($this->args['email']) ? $this->args['email'] : '',
            'skype' => $this->args['skype'] ? $this->args['skype'] : '',
            'question'  => $this->args['question'] ? $this->args['question'] : '',
            'form_type' => $this->args['form_type']
        ];
        if(isset($this->args['is_rent'])) {
            $data['is_rent'] = $this->args['is_rent'];
        }
        $content = json_encode($data);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            [
                "Content-type: application/json",
                'Authorization: Token token=' . $this->token
            ]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status !== 200 ) {
            $return = json_encode([
                'success' => true,
                'type'  => 'red',
                'message'   => __('An error was occurred. Please try again later.', 'leadingprops'),
                'status' => $status
            ]);
        } else {
             /*$return = [
                'success' => true,
                'type'  => 'green',
                'message'   => __('Thank you, request was received. We will send detailed information about the object, including plans, prices and review information soon ', 'leadingprops')
            ];*/
            $return = $json_response;
        }
        curl_close($curl);

        return $return;
    }

    /**
     * Check if string is JSON
     *
     * @since 1.0
     * @access public
     * @return bool
     */
    static public function isJson($string) {
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