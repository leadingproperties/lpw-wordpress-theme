<?php
namespace LPW;

class Tags
{
    private $api_url;
    private $token;

    protected $currencies = array(
        1 => 'EUR',
        2 => 'CHF',
        3 => 'CZK',
        4 => 'USD',
        5 => 'GBR',
        6 => 'RUR',
        7 => 'AED',
        8 => 'THB'
    );

    public function __construct() {
        $this->api_url = get_field('api_url', 'option');
        $this->token = get_field('api_key', 'option');
    }

    function get_tags_html($request_data){
        $answer = '';
        $counters = $this->get_counters($request_data['raw']);

        if(count($counters) > 0){
            $format = '<ul class="tag-list">%1$s%2$s%3$s%4$s%5$s%6$s%7$s%8$s%9$s<li><span class="tag-remove-all" data-tag_type="all"></span></li></ul>';

            $autocomplete =  $this->get_autocomplete_tag_html($request_data['autocomplete'], $counters, $request_data['raw']);
            $rent_bool_tags = $request_data['raw']['for_rent'] ? $this->get_rent_bool_tags($request_data['raw'], $counters) : '';
            $rent_persons = $request_data['raw']['for_rent'] ? $this->get_rent_persons_tag($request_data['raw']['persons'], $counters) : '';
            $property_type = $this->get_property_type_tags_html($request_data['raw']['property_types'], $counters);
            $rooms = $this->get_rooms_tag_html($request_data['raw']['rooms'], $counters);
            $hq_photos = $this->get_hq_photos_tag_html($request_data['raw']['hd_photos'], $counters);
            $price = $this->get_price_tag_html($request_data['raw']['price'], $counters);
            $area = $this->get_area_tag_html($request_data['raw']['area'], $counters);
            $similar = $this->get_similar_tag_html($request_data['raw']['similar'], $request_data['raw']['location_point']['radius'], $counters);

            $answer = sprintf(
                $format,
                $autocomplete,
                $rent_bool_tags,
                $rent_persons,
                $property_type,
                $rooms,
                $hq_photos,
                $price,
                $area,
                $similar
            );
        }
        return $answer;
    }

    function get_counters($params){
        if($params['l_id']){
            $params['property_object'] = $params['l_id'];
        }
        $url = $this->api_url . '/counters?' . preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', http_build_query($params));

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
            return json_encode(['error' => true, 'errorMessage' => 'Shit happens']);
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($resp, $header_size);

        curl_close($ch);

        return json_decode($body, true)['counters']['buckets'];
    }

    function get_autocomplete_tag_html($autocomplete_data, $counters, $raw){
        $autocomplete_tag_html = '';
        if($autocomplete_data){
            $autocomplete_tag_html = '<li>' . stripslashes($autocomplete_data['text']) . ' <sup>' . (($raw['ids']) ? count($raw['ids']) : $counters['geo_location']['doc_count']) . '</sup> <span class="tag-remove" data-tag_type="autocomplete"></span></li>';
        }
        return $autocomplete_tag_html;
    }

    function get_property_type_tags_html($property_type_ids, $counters){
        $answer = '';
        foreach ($property_type_ids as $pt_id){
            switch ($pt_id){
                case 1:
                    $answer .= '<li>' . __('search_panel:property_types:apartments', 'leadingprops') . ' <sup>'. $counters['apartment']['doc_count'] .'</sup> <span class="tag-remove" data-tag_type="property_type" data-tag_data="apartments"></span></li>';
                    break;
                case 2:
                    $answer .= '<li>' . __('search_panel:property_types:houses', 'leadingprops') . ' <sup>'. $counters['house']['doc_count'] .'</sup> <span class="tag-remove" data-tag_type="property_type" data-tag_data="houses"></span></li>';
                    break;
                case 3:
                    $answer .= '<li>' . __('search_panel:property_types:commercial', 'leadingprops') . ' <sup>'. $counters['commercial']['doc_count'] .'</sup> <span class="tag-remove" data-tag_type="property_type" data-tag_data="commercial"></span></li>';
                    break;
                case 4:
                    $answer .= '<li>' . __('search_panel:property_types:plots', 'leadingprops') . ' <sup>'. $counters['plot']['doc_count'] .'</sup> <span class="tag-remove" data-tag_type="property_type" data-tag_data="plots"></span></li>';
                    break;
            }
        };
        return $answer;
    }

    function get_rooms_tag_html($rooms, $counters){
        $answer = '';
        foreach ($rooms as $room){
            $counter_key = $room . '_room';
            $answer .= '<li>' . __('search_panel:rooms_label', 'leadingprops') . ': ' . $room . ($room == 5 ? '+' : '') .' <sup>'. $counters[$counter_key]['doc_count'] .'</sup> <span class="tag-remove" data-tag_type="room" data-tag_data="' . $this->get_room_html_id($room) . '"></span></li>';
        }
        return $answer;
    }

    function get_hq_photos_tag_html($bool, $counters){
        $hq_photos_tag_html = '';
        if($bool){
            $hq_photos_tag_html = '<li>' . __('search_panel:hd_photos:tag', 'ledingprops') . ' <sup>' . $counters['hd_photos']['doc_count'] . '</sup> <span class="tag-remove" data-tag_type="quality" data-tag_data="quality"></span></li>';
        }
        return $hq_photos_tag_html;
    }

    function get_price_tag_html($price, $counters){
        $price_tag_html = '';

        if($price){
            $suffix   = $price && $price['currency'] ? $this->currencies[$price['currency']] : "EUR";
            $price_tag_html = '<li>' . $this->get_range_label($price['min'], $price['max'], $suffix) . ' <sup>' . $counters['price']['doc_count'] . '</sup> <span class="tag-remove" data-tag_type="price" data-tag_data="price"></span></li>';
        }

        return $price_tag_html;
    }

    function get_area_tag_html($area, $counters){
        $area_tag_html = '';

        if($area){
            $suffix   = "m²";
            $area_tag_html = '<li>' . $this->get_range_label($area['min'], $area['max'], $suffix) . ' <sup>' . $counters['area']['doc_count'] . '</sup> <span class="tag-remove" data-tag_type="area" data-tag_data="area"></span></li>';
        }

        return $area_tag_html;
    }

    function get_rent_bool_tags($params, $counters){
        $rent_bool_tags = '';
        if($params['long_rent']){
            $rent_bool_tags .= '<li>' . __('search_panel:long_rent', 'leadingprops') . ' <sup>' . $counters['long_rent']['doc_count'] . '</sup> <span class="tag-remove" data-tag_type="long-term" data-tag_data="long-term"></span></li>';
        }
        if($params['short_rent']){
            $rent_bool_tags .= '<li>' . __('search_panel:short_rent', 'leadingprops') . ' <sup>' . $counters['short_rent']['doc_count'] . '</sup> <span class="tag-remove" data-tag_type="short-term" data-tag_data="short-term"></span></li>';
        }
        if($params['child_friendly']){
            $rent_bool_tags .= '<li>' . __('search_panel:child_friendly', 'leadingprops') . ' <sup>' . $counters['child_friendly']['doc_count'] . '</sup> <span class="tag-remove" data-tag_type="child-friendly" data-tag_data="child-friendly"></span></li>';
        }
        if($params['pets_allowed']){
            $rent_bool_tags .= '<li>' . __('search_panel:pets_allowed', 'leadingprops') . ' <sup>' . $counters['pets_allowed']['doc_count'] . '</sup> <span class="tag-remove" data-tag_type="pets-allowed" data-tag_data="pets-allowed"></span></li>';
        }
        return $rent_bool_tags;
    }

    function get_rent_persons_tag($persons, $counters){
        $rent_persons_tag = '';
        if($persons){
            $rent_persons_tag = '<li>' . __('search_panel:persons', 'leadingprops') . ': ' . $persons . ' <sup>' . $counters['long_rent']['doc_count'] . '</sup> <span class="tag-remove" data-tag_type="persons-max" data-tag_data="persons-max"></span></li>';
        }
        return $rent_persons_tag;
    }

    function get_similar_tag_html($parameters, $radius = 1, $counters){
        $similar_tag= '';
        if($parameters){
            $similar_tag = '<li>' . __('search_panel:similar', 'leadingprops') . ': ' . $radius . ' ' . __('search_panel:km', 'leadingprops') . ' ' . __('search_panel:from', 'leadingprops') . ' ' . $parameters['code'] . ' <sup>' . $counters['geo_location']['doc_count'] . '</sup> <span class="tag-remove" data-tag_type="similar" data-tag_data="similar"></span></li>';
        }
        return $similar_tag;
    }

    function get_room_html_id($num){
        $answer = '';
        switch ($num){
            case 1:
                $answer = 'one';
                break;
            case 2:
                $answer = 'two';
                break;
            case 3:
                $answer = 'three';
                break;
            case 4:
                $answer = 'four';
                break;
            case 5:
                $answer = 'five';
                break;
        }
        return $answer;
    }

    function get_range_label($min, $max, $suffix){
        $prefix = "";
        $body   = "";
        $suffix = $suffix && is_string($suffix) ? " " . $suffix : "";

      if($min && $max){
          $body = $min . "-" . $max;
      }
      if($min && !$max){
          $prefix = "Min. ";
          $body = $min;
      }
      if($max && !$min){
          $prefix = "Max. ";
          $body = $max;
      }
      return $prefix . $body . $suffix;
    }
}
