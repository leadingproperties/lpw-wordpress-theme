<?php
function get_favorites_bar($is_bottom = false) {
	global $pos;
	$pos = $is_bottom;
	get_template_part('templates/favorites', 'bar');
}

function single_object_html($args) {
	global $lp_settings;
	$html = '';
	$objects = new LP_ObjectList([
		'lang' => $args['lang'],
		'slug'  => $args['slug']
	]);
	$objects_obj = $objects->get_objects_array();

	$list_url = '';

	if(!isset($objects_obj->error)) :
	$object_type = (isset($objects_obj->slug_type) && $objects_obj->slug_type === 'PropertyObject') ? 'sale' : 'rent';
	$object_class = ($object_type === 'sale') ? ' object-sale' : ' object-rent';
	$object_title = ($object_type === 'sale') ? $objects_obj->description->title : $objects_obj->description->rent_title;

	if($object_type === 'sale') {
		$list_url = $lp_settings['sale_page'] . '?filter=';
	} elseif($object_type === 'rent') {
		$list_url = $lp_settings['rent_page'] . '?filter=';
	}


	$html .= '<div class="single-object-container single-object-modal' . $object_class . '">';
	$html .= '<header class="single-object-header">';
	$html .= '<div class="single-object-wrap">';
	$html .= '<div class="detailed-link-wrap">';
	$html .= '<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-target=".single-object-request" data-type="single_property" data-object-type="' . $object_type . '" data-id="' . $objects_obj->id . '" data-code="' . $objects_obj->code . '">';
	$html .= '<span>' . __('s_object:header:detailed_info', 'leadingprops') . '</span>';
	$html .= '</a>';
	$html .= '</div>';
	$html .= '<button type="button" class="btn btn-single-close">Close</button>';
	$html .= '<ul class="single-object-menu">';
	$html .= '<li><a href="#" data-id="' . $objects_obj->id . '" ';
		if($object_type === 'rent') {
			$html .=  'data-is_rent="true" ';
		}
		$html .= 'class="pdf-link"><sup class="text-red">PDF</sup></a></li>';
		$html .= '<li><a href="#" class="add-favorite-button" data-id="' . $objects_obj->id . '" data-action="add"></a></li>';
		$html .= '</ul>';
		$html .= '</div>';
		$html .= '</header><!-- /.single-object-header -->';
		$html .= '<div class="single-object-content">';
		$html .= '<div class="single-object-content-inner">';
			  if(is_array($objects_obj->parameters->images)) :
					$images_arr = $objects_obj->parameters->images;
					$html .= '<div id="gallery-' . $objects_obj->id . '" class="single-slider carousel slide" data-ride="carousel">';
			        $html .= '<!-- Indicators -->';
				    $html .= '<ol class="carousel-indicators">';
						 for($i = 0; $i < count($images_arr); $i++) {
							$html .= '<li data-target="#gallery-' . $objects_obj->id . '" data-slide-to="' . $i . '"';
							 if( $i === 0 ) {
							 $html .= 'class="active"';
							  }
							$html .=  '></li>';
						 }
					$html .= '</ol>';
				    $html .= '<!-- Wrapper for slides -->';
				    $html .= '<div class="carousel-inner" role="listbox">';
						$j = 0;
						foreach($images_arr as $image) {
							$html .= '<div class="item';
							 if( $j === 0 ) {
							    $html .= ' active';
							 }
							$html .= '">';
							$html .= '<img src="' . esc_url($image) . '" alt="">';
							$html .= '</div>';
							$j++;
						}
					$html .= '</div>';
				    $html .= '<!-- Controls -->';
				    $html .= '<a class="left carousel-control" href="#gallery-' . $objects_obj->id . '" role="button" data-slide="prev">';
				    $html .= '<span class="sr-only">Previous</span>';
				    $html .= '</a>';
				    $html .= '<a class="right carousel-control" href="#gallery-' . $objects_obj->id  . '" role="button" data-slide="next">';
				    $html .= '<span class="sr-only">Next</span>';
				    $html .= '</a>';
				    $html .= '</div><!-- /.single-slider -->';
			 endif;
		$html .= '<div class="single-object-details">';
		$html .= '<ul class="single-object-locations">';
		$html .= '<li><a class="icon cursor-default"><span>';
				if(isset($objects_obj->country) && $objects_obj->country->title) {
					$html .= $objects_obj->country->title;
				}
				if(isset($objects_obj->region) && $objects_obj->region->title) {
					$html .= ', ' . $objects_obj->region->title;
				}
				if(isset($objects_obj->city) && $objects_obj->city->title) {
					$html .= ', ' . $objects_obj->city->title;
				}
				if(isset($objects_obj->district) && $objects_obj->district->title) {
					$html .= ', ' . $objects_obj->district->title;
				}
		$html .= '</span>';
		$html .= '</a>';
		$html .= '</li>';
		$html .= '</ul><!-- /single-object-locations -->';
		$html .= '<h1 class="single-object-title">';
				if($objects_obj->sold_state) {
					$html .= '<span class="object-sold">' . __('s_object:sold', 'leadingprops') . '</span> ';
				}
		$html .=  $object_title . '</h1>';
				 /* Rent info */
				 if($object_type === 'rent') :
					   if($objects_obj->features->bedrooms->min ||
				          $objects_obj->features->bedrooms->max ||
				          $objects_obj->features->bathrooms->min ||
				          $objects_obj->features->bathrooms->max ||
				          $objects_obj->features->bathrooms->min ||
				          $objects_obj->property_rent->persons_max ||
				          $objects_obj->property_rent->child_friendly ||
				          $objects_obj->property_rent->pets_allowed) {
				    $html .= '<ul class="object-short-info">';
							 if($objects_obj->features->bedrooms->min || $objects_obj->features->bedrooms->max) {
								$html .= '<li class="icon icon-bedroom">';
									if($objects_obj->features->bedrooms->min) {
										$html .= $objects_obj->features->bedrooms->min;
										}
									if($objects_obj->features->bedrooms->max) {
										$html .= ' - ' . $objects_obj->features->bedrooms->max;
										}
								$html .= '</li>';
							}
							if($objects_obj->features->bathrooms->min || $objects_obj->features->bathrooms->max) {
								$html .= '<li class="icon icon-bedroom">';
								if($objects_obj->features->bathrooms->min) {
									$html .= $objects_obj->features->bathrooms->min;
									}
								if($objects_obj->features->bathrooms->max) {
									$html .= ' - ' . $objects_obj->features->bathrooms->max;
									}
								$html .= '</li>';
							}
							if($objects_obj->property_rent->persons_max) {
								$html .= '<li class="icon icon-person">' . $objects_obj->property_rent->persons_max . '</li>';
							}
							if($objects_obj->property_rent->child_friendly) {
								$html .= '<li class="icon icon-child"></li>';
							}
							if($objects_obj->property_rent->pets_allowed) {
								$html .= '<li class="icon icon-pet"></li>';
							}

				 $html .= '</ul>';
					 }
					endif;
				 /* // Rent info */

		$html .= '<div class="single-object-description">';
		$html .= '<p>' . $objects_obj->description->main_text . '</p>';
		$html .= '</div><!-- /.single-object-description -->';

				if($object_type === 'sale') :
					$html .= '<div class="single-object-info">';
					$html .= '<p class="object-price icon">';
							if($objects_obj->parameters->price->on_demand) {
								$html .= __('s_object:on_demand', 'leadingprops');
							} else {
								if($objects_obj->parameters->price->min) {
									$html .= number_format($objects_obj->parameters->price->min, 0, ".", " ");
								}
								if($objects_obj->parameters->price->max) {
									$html .= '&nbsp;-&nbsp;' . number_format($objects_obj->parameters->price->max, 0, ".", " ");
								}
								$html .= '&nbsp;' . $objects_obj->parameters->price->currency;
							}
					$html .= '</p>';
					$html .= '</div><!-- /.single-object-info -->';
					elseif($object_type === 'rent') :
					$html .= '<div class="rent-rate">';
						 if($objects_obj->property_rent->long_rent) {
							$html .= '<p class="icon icon-month">' . __('s_object:rent:long-term_rent', 'leadingprops') . ': ';
							if($objects_obj->property_rent->rent_long->on_demand) {
								$html .= __('s_object:rent:on_request', 'leadingprops') . '*';
							} elseif($objects_obj->property_rent->rent_long->monthly_rate) {
								$html .= number_format($objects_obj->property_rent->rent_long->monthly_rate, 0, ".", " ") . '&nbsp;' . $objects_obj->property_rent->rent_long->currency_code . '*';
							}
							 $html .= '</p>';
							 $html .= '<hr>';
							 $html .= '<p class="footnote">* ';
							if($objects_obj->property_rent->rent_long->vat_in_price === false) {
								if($objects_obj->property_rent->rent_long->vat) {
									$html .= __('s_object:rent:vat', 'leadingprops') . ' ' . $objects_obj->property_rent->rent_long->vat . '% ' . __('s_object:rent:not_included', 'leadingprops') . '; ';
								}
							} else {
								$html .= __('s_object:rent:vat', 'leadingprops') . __('s_object:rent:included', 'leadingprops') . '; ';
							}
							$html .= __('s_object:rent:deposit', 'leadingprops') . ': ';
							if($objects_obj->property_rent->rent_long->deposit_on_demand === true) {
								$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
							} else {
								if($objects_obj->property_rent->rent_long->deposit_type === 1) {
									if($objects_obj->property_rent->rent_long->deposit) {
										$html .= $objects_obj->property_rent->rent_long->deposit . '%; ';
									} else {
										$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
									}
								} else {
									if($objects_obj->property_rent->rent_long->deposit) {
										$html .= number_format($objects_obj->property_rent->rent_long->deposit, 0, ".", " ") . '&nbsp;' . $objects_obj->property_rent->rent_long->currency_code . '; ';
									} else {
										$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
									}
								}
							}
							$html .= __('s_object:rent:commission', 'leadingprops') . ': ';
							if($objects_obj->property_rent->rent_long->commission_on_demand === true) {
								$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
							} else {
								if($objects_obj->property_rent->rent_long->commission_type === 1) {
									if($objects_obj->property_rent->rent_long->commission) {
										$html .= $objects_obj->property_rent->rent_long->commission . '%; ';
									} else {
										$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
									}
								} else {
									if($objects_obj->property_rent->rent_long->commission) {
										$html .= number_format($objects_obj->property_rent->rent_long->commission, 0, ".", " ") . '&nbsp;' . $objects_obj->property_rent->rent_long->currency_code . '; ';
									} else {
										$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
									}
								}
							}
							$html .= '</p>';
						}
						 if($objects_obj->property_rent->short_rent === true) {
							$html .= '<p class="icon icon-day">' . __('s_object:rent:rent_short_title', 'leadingprops') . ', ' . $objects_obj->property_rent->rent_short->currency_code . '*</p>';
							$html .= '<div class="rent-price-wrap">';
							$html .= '<ul class="rent-price-list">';
							$html .= '<li class="heading">' . __('s_object:rent:min_stay', 'leadingprops') . '</li>';
							$html .= '<li class="heading">' . __('s_object:rent:low_season', 'leadingprops') . '</li>';
							$html .= '<li>1 ' .__('s_object:rent:day', 'leadingprops') . '</li>';
							$html .= '<li>';
							$html .= ($objects_obj->property_rent->rent_short->ls_daily_rate) ? number_format($objects_obj->property_rent->rent_short->ls_daily_rate, 0, ".", " ")  : '&mdash;';
							$html .= '</li>';
							$html .= '<li>1 ' . __('s_object:rent:week', 'leadingprops') . '</li>';
							$html .= '<li>';
							$html .= ($objects_obj->property_rent->rent_short->ls_weekly_rate) ? number_format($objects_obj->property_rent->rent_short->ls_weekly_rate, 0, ".", " ")  : '&mdash;';
							$html .= '</li>';
							$html .= '<li>1 ' . __('s_object:rent:month', 'leadingprops') . '</li>';
							$html .= '<li>';
							$html .= ($objects_obj->property_rent->rent_short->ls_monthly_rate) ? number_format($objects_obj->property_rent->rent_short->ls_monthly_rate, 0, ".", " ")  : '&mdash;';
							$html .= '</li>';
							$html .= '</ul>';
							$html .= '<ul class="rent-price-list">';
							$html .= '<li class="heading hidden-760">' . __('s_object:rent:min_stay', 'leadingprops') . '</li>';
							$html .= '<li class="heading">' . __('s_object:rent:medium_season', 'leadingprops') . '</li>';
							$html .= '<li class="hidden-760">1 ' .__('s_object:rent:day', 'leadingprops') . '</li>';
							$html .= '<li>';
							$html .= ($objects_obj->property_rent->rent_short->ms_daily_rate) ? number_format($objects_obj->property_rent->rent_short->ms_daily_rate, 0, ".", " ")  : '&mdash;';
							$html .= '</li>';
							$html .= '<li class="hidden-760">1 ' . __('s_object:rent:week', 'leadingprops') . '</li>';
							$html .= '<li>';
							$html .= ($objects_obj->property_rent->rent_short->ms_weekly_rate) ? number_format($objects_obj->property_rent->rent_short->ms_weekly_rate, 0, ".", " ")  : '&mdash;';
							$html .= '</li>';
							$html .= '<li class="hidden-760">1 ' . __('s_object:rent:month', 'leadingprops') . '</li>';
							$html .= '<li>';
							$html .= ($objects_obj->property_rent->rent_short->ms_monthly_rate) ? number_format($objects_obj->property_rent->rent_short->ms_monthly_rate, 0, ".", " ") : '&mdash;';
							$html .= '</li>';
							$html .= '</ul>';

							$html .=  '<ul class="rent-price-list">';
							$html .= '<li class="heading hidden-760">' . __('s_object:rent:min_stay', 'leadingprops') . '</li>';
							$html .= '<li class="heading">' . __('s_object:rent:hight_season', 'leadingprops') . '</li>';
							$html .= '<li class="hidden-760">1 ' .__('s_object:rent:day', 'leadingprops') . '</li>';
							$html .= '<li>';
							$html .= ($objects_obj->property_rent->rent_short->hs_daily_rate) ? number_format($objects_obj->property_rent->rent_short->hs_daily_rate, 0, ".", " ")  : '&mdash;';
							$html .= '</li>';
							$html .= '<li class="hidden-760">1 ' . __('s_object:rent:week', 'leadingprops') . '</li>';
							$html .= '<li>';
							$html .= ($objects_obj->property_rent->rent_short->hs_weekly_rate) ? number_format($objects_obj->property_rent->rent_short->hs_weekly_rate, 0, ".", " ")  : '&mdash;';
							$html .= '</li>';
							$html .= '<li class="hidden-760">1 ' . __('s_object:rent:month', 'leadingprops') . '</li>';
							$html .= '<li>';
							$html .= ($objects_obj->property_rent->rent_short->hs_monthly_rate) ? number_format($objects_obj->property_rent->rent_short->hs_monthly_rate, 0, ".", " ")  : '&mdash;';
							$html .= '</li>';
							$html .= '</ul>';
							$html .= '</div>';

							$html .= '<p class="footnote">* ';
							if($objects_obj->property_rent->rent_short->vat_in_price === false) {
								if($objects_obj->property_rent->rent_short->vat) {
									$html .= __('s_object:rent:vat', 'leadingprops') . ' ' . $objects_obj->property_rent->rent_short->vat . '% ' . __('s_object:rent:not_included', 'leadingprops') . '; ';
								}
							} else {
								$html .= __('s_object:rent:vat', 'leadingprops') . __('s_object:rent:included', 'leadingprops') . '; ';
							}
							$html .= __('s_object:rent:deposit', 'leadingprops') . ': ';
							if($objects_obj->property_rent->rent_short->deposit_on_demand === true) {
								$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
							} else {
								if($objects_obj->property_rent->rent_short->deposit_type === 1) {
									if($objects_obj->property_rent->rent_short->deposit) {
										$html .= $objects_obj->property_rent->rent_short->deposit . '%; ';
									} else {
										$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
									}
								} else {
									if($objects_obj->property_rent->rent_short->deposit) {
										$html .= $objects_obj->property_rent->rent_short->deposit . '&nbsp;' . $objects_obj->property_rent->rent_short->currency_code . '; ';
									} else {
										$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
									}
								}
							}
							$html .= __('s_object:rent:commission', 'leadingprops') . ': ';
							if($objects_obj->property_rent->rent_short->commission_on_demand === true) {
								$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
							} else {
								if($objects_obj->property_rent->rent_short->commission_type === 1) {
									if($objects_obj->property_rent->rent_short->commission) {
										$html .= $objects_obj->property_rent->rent_short->commission . '%; ';
									} else {
										$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
									}
								} else {
									if($objects_obj->property_rent->rent_short->commission) {
										$html .= $objects_obj->property_rent->rent_short->commission . '&nbsp;' . $objects_obj->property_rent->rent_short->currency_code . '; ';
									} else {
										$html .= __('s_object:rent:on_request', 'leadingprops') . '; ';
									}
								}
							}
							$html .= '</p>';
						}
						$html .= '</div><!-- /.rent-rate -->';
				 endif;

				$html .= '<ul class="object-properties">';
				$html .= '<li>' . __('s_object:ref_num', 'leadingprops') . '</li>';
				$html .= '<li>' . $objects_obj->code . '</li>';
				$html .= '<li>' . __('s_object:property_type', 'leadingprops') . '</li>';
				$html .= '<li>' . $objects_obj->parameters->property_object_type . '</li>';
				if($objects_obj->parameters->rooms->min || $objects_obj->parameters->rooms->max) {
					$html .= '<li>' . __('s_object:number_of_rooms', 'leadingprops') . '</li>';
					$html .= '<li>';
					if($objects_obj->parameters->rooms->min) {
						$html .= $objects_obj->parameters->rooms->min;
					}
					if($objects_obj->parameters->rooms->max) {
						$html .= ' - ' . $objects_obj->parameters->rooms->max;
					}
					$html .= '</li>';
				}
				if($objects_obj->parameters->area->min || $objects_obj->parameters->area->max) {
					$html .= '<li>' . __('s_object:area_sq_m', 'leadingprops') . '</li>';
					$html .= '<li>';
					if($objects_obj->parameters->area->min) {
						$html .= $objects_obj->parameters->area->min;
					}
					if($objects_obj->parameters->area->max) {
						$html .= ' - ' . $objects_obj->parameters->area->max;
					}
					$html .= '</li>';
				}
				if($objects_obj->features->bedrooms->min || $objects_obj->features->bedrooms->max) {
					$html .= '<li>' . __('s_object:bedrooms', 'leadingprops') . '</li>';
					$html .= '<li>';
					if($objects_obj->features->bedrooms->min) {
						$html .= $objects_obj->features->bedrooms->min;
					}
					if($objects_obj->features->bedrooms->max) {
						$html .= ' - ' . $objects_obj->features->bedrooms->max;
					}
					$html .= '</li>';
				}
				if($objects_obj->features->bathrooms->min || $objects_obj->features->bathrooms->max) {
					$html .= '<li>' . __('s_object:bathrooms', 'leadingprops') . '</li>';
					$html .= '<li>';
					if($objects_obj->features->bathrooms->min) {
						$html .= $objects_obj->features->bathrooms->min;
					}
					if($objects_obj->features->bathrooms->max) {
						$html .= ' - ' . $objects_obj->features->bathrooms->max;
					}
					$html .= '</li>';
				}
				if($object_type === 'rent') {
					if($objects_obj->property_rent->persons_max) {
						$html .= '<li>' . __('s_object:rent:max_persons', 'leadingprops') . '</li><li>' . $objects_obj->property_rent->persons_max .  '</i></li>';
					}
				}
				if($objects_obj->features->land_area->min || $objects_obj->features->land_area->max) {
					$html .= '<li>' . __('s_object:land_area', 'leadingprops') . '</li>';
					$html .= '<li>';
					if($objects_obj->features->land_area->min) {
						$html .= $objects_obj->features->land_area->min;
					}
					if($objects_obj->features->land_area->max) {
						$html .= ' - ' . $objects_obj->features->land_area->max;
					}
					$html .= '</li>';
				}
				if($objects_obj->features->building_storeys && ($objects_obj->features->building_storeys->min || $objects_obj->features->building_storeys->max)) {
					$html .= '<li>' . __('s_object:building_storeys', 'leadingprops') . '</li>';
					$html .= '<li>';
					if($objects_obj->features->building_storeys->min) {
						$html .= $objects_obj->features->building_storeys->min;
					}
					if($objects_obj->features->building_storeys->max) {
						$html .= ' - ' . $objects_obj->features->building_storeys->max;
					}
					$html .= '</li>';
				}
				if(isset($objects_obj->features->terrace_balcony) && $objects_obj->features->terrace_balcony === true) {
					$html .= '<li>' . __('s_object:terrace_balcony', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
				}
				if(isset($objects_obj->features->pool) && $objects_obj->features->pool === true) {
					$html .= '<li>' . __('s_object:pool', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
				}
				if(isset($objects_obj->features->garage_parking) && $objects_obj->features->garage_parking === true) {
					$html .= '<li>' . __('s_object:garage_parking', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
				}
				if(isset($objects_obj->features->utility_rooms) && $objects_obj->features->utility_rooms === true) {
					$html .= '<li>' . __('s_object:utility_rooms', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
				}
				if($object_type === 'rent') {
					if($objects_obj->property_rent->child_friendly === true) {
						$html .= '<li>' . __('search_panel:child_friendly', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
					if($objects_obj->property_rent->pets_allowed === true) {
						$html .= '<li>' . __('search_panel:pets_allowed', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
				}
		$html .= '</ul><!-- /.object-properties -->';
		$html .= '</div><!-- /.single-object-details -->';
		$html .= '</div><!-- /.single-object-content-inner -->';
		$html .= '<div class="object-navigation">';
		$html .= '<div class="object-prev">';
		$html .= '<a class="icon open-object-modal"><span class="direction-text">' . __('s_object:prev', 'leadingprops') . '</span></a>';
		$html .= '</div>';
		$html .= '<div class="object-next">';
		$html .= '<a class="icon open-object-modal"><span class="direction-text">' . __('s_object:next', 'leadingprops') . '</span></a>';
		$html .= '</div>';
		$html .= '</div><!-- /.object-navigation -->';
		$html .= '</div><!-- /.single-object-content -->';
		$html .= '<div class="similar-object-search">';
		$html .= '<div class="similar-search-container">';
		$html .= '<i class="icon icon-radar"></i>';
		$html .= '<p>' . __('s_object:similar_label', 'leadingprops') . '</p>';
		$html .= '<ul class="similar-locations">';
		$html .= '<li><a href="' . $list_url . urlencode('{"location_point":{"lat":' . $objects_obj->location->lat . ' ,"lon":' . $objects_obj->location->lon . ',"radius":1},"similar":{"code":"' . $objects_obj->code . '"}}') . '">1 km</a></li>';
		$html .= '<li><a href="' . $list_url . urlencode('{"location_point":{"lat":' . $objects_obj->location->lat . ' ,"lon":' . $objects_obj->location->lon . ',"radius":5},"similar":{"code":"' . $objects_obj->code . '"}}') . '">5 km</a></li>';
		$html .= '<li><a href="' . $list_url . urlencode('{"location_point":{"lat":' . $objects_obj->location->lat . ' ,"lon":' . $objects_obj->location->lon . ',"radius":10},"similar":{"code":"' . $objects_obj->code . '"}}') . '">10 km</a></li>';
		$html .= '</ul>';
		$html .= '</div>';
		$html .= '</div><!-- /.similar-object-search -->';
		$html .= '<footer class="single-object-footer">';
		$html .= '<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-target=".single-object-request" data-type="single_property" data-object-type="' . $object_type . '" data-id="' . $objects_obj->id . '" data-code="' . $objects_obj->code . '">';
		$html .= '<span>' . __('s_object:header:detailed_info', 'leadingprops') . '</span>';
		$html .= '</a>';
		$html .= '</footer>';
		$html .= '<div class="single-object-backdrop"></div>';
		$html .= '</div>';
		return  [
			'html'  => $html,
			'id'    => $objects_obj->id
		];
		else:
			return [
				'error' => true,
				'errorMessage'  => $objects_obj->errorMessage
			];
		endif;

}

function get_object_list($args) {
	global $lp_settings;
	$html = '';
	if(isset($args['fn'])) {
		unset($args['fn']);
	}
	$args['action'] = 'get_objects';
	$objects = new LP_ObjectList($args);
	$object_array = $objects->get_objects_array();
	$category = '';
	$count = 0;
	$i = 1;
	$firstObj = [];
	$triggerId = 0;

	if($args['for_sale'] === 'true') {
		$category = 'sale';
	}
	if($args['for_rent'] === 'true') {
		$category = 'rent';
	}

	if(isset($object_array->property_objects) && is_array($object_array->property_objects)) {
		$count = count($object_array->property_objects);
		if ($count > 0) {
			foreach ( $object_array->property_objects as $object ) {
				$title        = '';
				$slug         = '';
				$minStay      = '';
				$rent_price   = '';
				$object_class = '';

				if ( $category === 'sale' ) {
					$title = $object->description->title;
					$slug  = $object->slug;
				} elseif ( $category === 'rent' ) {

					$title        = ( $object->description->rent_title ) ? $object->description->rent_title : $object->description->title;
					$slug         = ( $object->rent_slug ) ? $object->rent_slug : $object->slug;
					$object_class = ' rent-item';

					if ( $object->property_rent->long_rent === true && $object->property_rent->short_rent === false ) {
						$minStay = __( 's_object:rent:on_request', 'leadingprops' );
					} elseif ( $object->property_rent->short_rent === true ) {
						if ( $object->property_rent->rent_short->min_day === true ) {
							$minStay = '1 ' . __( 's_object:rent:day', 'leadingprops' );
						} elseif ( $object->property_rent->rent_short->min_week === true ) {
							$minStay = '1 ' . __( 's_object:rent:week', 'leadingprops' );
						} else {
							$minStay = '1 ' . __( 's_object:rent:month', 'leadingprops' );
						}
					}

					if ( $object->property_rent->short_rent === true ) {
						if ( $object->property_rent->rent_short->sort_price->day ) {
							$rent_price = '<span dir="ltr" class="price-num">' . number_format( $object->property_rent->rent_short->sort_price->day, 0, ".", " " ) . ' </span>' . $object->property_rent->rent_short->sort_price->currency_code . '&nbsp;/&nbsp;' . __( 's_object:rent:day', 'leadingprops' );
						} elseif ( $object->roperty_rent->rent_short->sort_price->month ) {
							$rent_price = '<span dir="ltr" class="price-num">' . number_format( $object->property_rent->rent_short->sort_price->month, 0, ".", " " ) . ' </span>' . $object->property_rent->rent_short->sort_price->currency_code . '&nbsp;/&nbsp;' . __( 's_object:rent:month', 'leadingprops' );
						}
					} elseif ( $object->property_rent->long_rent === true ) {
						if ( $object->property_rent->rent_long->on_demand === true ) {
							$rent_price = '<span dir="ltr" class="price-onrequest">' . __( 's_object:rent:on_request', 'leadingprops' ) . '</span>';
						} else {
							if ( $object->property_rent->rent_long->sort_price->month ) {
								$rent_price = '<span dir="ltr" class="price-num">' . number_format( $object->property_rent->rent_long->sort_price->month, 0, ".", " " ) . ' </span>' . $object->property_rent->rent_long->sort_price->currency_code . '&nbsp;/&nbsp;' . __( 's_object:rent:month', 'leadingprops' );
							} elseif ( $object->property_rent->rent_long->sort_price->day ) {
								$rent_price = '<span dir="ltr" class="price-num">' . number_format( $object->property_rent->rent_long->sort_price->day, 0, ".", " " ) . ' </span>' . $object->property_rent->rent_long->sort_price->currency_code . '&nbsp;/&nbsp;' . __( 's_object:rent:day', 'leadingprops' );
							}
						}
					}
				}
				if ( $i === 1 ) {
					$firstObj = [
						'slug' => $slug,
						'id'   => $object->id,
						'image' => $object->image
					];
				}
				if ( $i === $count ) {
					$triggerId = $object->id;
				}

				$html .= '<article class="object-item' . $object_class . '" id="object-' . $object->id . '" data-object-id="' . $object->id . '">';
				$html .= '<div class="object-inner-wrapper">';
				$html .= '<div class="object-thumbnail">';
				$html .= '<a href="' . $lp_settings['property_page'] . $slug . '" class="open-object-modal object-thumbnail-holder" title="' . $title . '" data-id=' . $object->id . '>';
				$html .= '<img class="img-responsive" src="' . $object->image . '"  alt="' . $title . '">';
				$html .= '</a>';
				$html .= '<span class="add-favorite-button" data-action="add" data-id="' . $object->id . '"></span>';
				if ( $category === 'rent' ) {
					$html .= '<div class="rent-price"><span>' . $rent_price . '</span></div>';
				}
				$html .= '</div>';
				$html .= '<div class="object-info-holder">';
				$html .= '<div class="info-address-holder">';
				$html .= '<div class="info-address">';
				if ( $object->country && $object->country->title ) {
					$html .= '<a>' . $object->country->title . '</a>';
				}
				if ( $object->region && $object->region->title ) {
					$html .= '<a>' . $object->region->title . '</a>';
				}
				if ( $object->city && $object->city->title ) {
					$html .= '<a>' . $object->city->title . '</a>';
				}
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<h2 class="info-title">';
				$html .= '<a class="open-object-modal object-link" href="' . $lp_settings['property_page'] . $slug . '" data-id="' . $object->id . '">' . $title . '</a>';
				$html .= '</h2>';
				if ( $category === 'sale' ) {
					$html .= '<p class="info-details"><span>';
					if ( $object->parameters->price->on_demand === true ) {
						$html .= '<span>' . __( 's_object:on_demand', 'leadingprops' ) . '</span>&nbsp;';
					} else {
						$html .= '<span dir="ltr">';
						if ( $object->parameters->price->min ) {
							$html .= '<span>' . number_format( $object->parameters->price->min, 0, ".", " " ) . '</span>';
						}
						if ( $object->parameters->price->max ) {
							$html .= '&nbsp;&ndash;&nbsp;<span dir="ltr">' . number_format( $object->parameters->price->max, 0, ".", " " ) . '</span>';
						}
						$html .= '&nbsp;<span class="text-uppercase">' . $object->parameters->price->currency . '</span></span>';
					}
						$html .= '</span>';

					if ( $object->parameters->rooms->min || $object->parameters->rooms->max ) {
						$html .= ',&nbsp';
					}
					if ( $object->parameters->rooms->min ) {
						$html .= '<span>' . $object->parameters->rooms->min . '</span>';
					}
					if ( $object->parameters->rooms->max ) {
						$html .= '&nbsp;&ndash;&nbsp;<span>' . $object->parameters->rooms->max . '</span>';
					}
					if ( $object->parameters->rooms->min || $object->parameters->rooms->max ) {
						$html .= '&nbsp;' . __( 's_object:rooms', 'leadingprops' );
					}
					if ( $object->parameters->area->min || $object->parameters->area->max ) {
						$html .= ',&nbsp;';
					}
					if ( $object->parameters->area->min ) {
						$html .= '<span>' . $object->parameters->area->min . '</span>';
					}
					if ( $object->parameters->area->max ) {
						$html .= '&nbsp;&ndash;&nbsp;<span>' . $object->parameters->area->max . '</span>';
					}
					if ( $object->parameters->area->min || $object->parameters->area->max ) {
						$html .= '&nbsp;' .__( 's_object:m_short', 'leadingprops' ) . ' <sup>2</sup>';
					}
					$html .= '</p>';

				} elseif ( $category === 'rent' ) {
					$html .= '<p class="min-days">' . __( 's_object:rent:min_stay_full', 'leadingprops' ) . ': ' . $minStay . '</p>';
					$html .= '<ul class="rent-details">';
					if ( $object->parameters->area->min ) {
						$html .= '<li class="area">' . $object->parameters->area->min . __( 's_object:m_short', 'leadingprops' ) . ' <sup>2</sup></li>';
					}
					if ( $object->parameters->bedrooms->min ) {
						$html .= '<li class="icon icon-bedroom">' . $object->parameters->bedrooms->min . '</li>';
					}
					if ( $object->parameters->bathrooms->min ) {
						$html .= '<li class="icon icon-bathroom">' . $object->parameters->bathrooms->min . '</li>';
					}
					if ( $object->property_rent->persons_max ) {
						$html .= '<li class="icon icon-person">' . $object->property_rent->persons_max . '</li>';
					}
					$html .= '</ul>';
				}
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</article>';
				$i ++;
			}
		} elseif($args['page'] === "1") {
			$html .= '<div class="no-matches">';
			$html .= '<div class="container">';
			$html .= '<h5 class="text-red">' . __('alerts:no_results:title', 'leadingprops') . '</h5>';
			$html .= '<p>' . __('alerts:no_results:text', 'leadingprops') . '</p>';
			$html .= '<button class="btn btn-red clear-filters-btn icon">' . __('alerts:no_results:button', 'leadingprops') . '</button>';
			$html .= '</div>';
			$html .= '</div><!-- /.no-matches -->';
		}
		return [
			'html' => $html,
			'offmarket' => $object_array->offmarket,
			'total' => $object_array->total,
			'count' => $count,
			'firstObject'   => $firstObj,
			'triggerID' => $triggerId
		];
	} else {
		return [
			'error' => true,
			'errorMessage'  => $objects->error_message
		];
	}
}
