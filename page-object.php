<?php
/**
 *  Template Name: Single Object
 */
?>
<?php
	global $lp_settings, $objects_obj;
	$object_slug = get_query_var('object_slug', false);

	$list_url = '';

	$objects_obj = new StdClass;
	if($object_slug) {
		$objects = new LP_ObjectList([
			'lang' => $lp_settings['lang'],
			'slug'  => $object_slug
		]);
		$objects_obj = $objects->get_objects_array();
	} else {
		$objects_obj->error = true;
		$objects_obj->errorMessage  = 'Nothing found';
	}

?>
<?php
	get_template_part('templates/head');
	get_header();
?>
<?php if(!isset($objects_obj->error)) :
	$object_type = (isset($objects_obj->slug_type) && $objects_obj->slug_type === 'PropertyObject') ? 'sale' : 'rent';
	$object_class = ($object_type === 'sale') ? ' object-sale' : ' object-rent';
	$object_title = ($object_type === 'sale') ? $objects_obj->description->title : $objects_obj->description->rent_title;

	if($object_type === 'sale') {
		$list_url = $lp_settings['sale_page'] . '?filter=';
	} elseif($object_type === 'rent') {
		$list_url = $lp_settings['rent_page'] . '?filter=';
	}

	?>
	<div class="single-object-container<?= $object_class; ?>">
		<header class="single-object-header">
			<div class="single-object-wrap">
				<div class="detailed-link-wrap">
					<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-target=".single-object-request" data-type="single_property" data-object-type="<?= $object_type; ?>" data-id="<?= $objects_obj->id; ?>" data-code="<? $objects_obj->code; ?>">
						<span><?php _e('s_object:header:detailed_info', 'leadingprops'); ?></span>
					</a>
				</div>
				<ul class="single-object-menu">
					<li><a href="#" data-id="<?= $objects_obj->id; ?>"<?php if($object_type === 'rent') { echo 'data-is_rent="true"'; } ?>  class="pdf-link"><sup class="text-red">PDF</sup></a></li>
					<li><a href="#" class="add-favorite-button" data-id="<?= $objects_obj->id ?>"></a></li>
				</ul>
			</div>
		</header><!-- /.single-object-header -->
		<div class="single-object-content">
			<div class="single-object-content-inner">
				<?php if(is_array($objects_obj->parameters->images)) :
					$images_arr = $objects_obj->parameters->images;
				?>
				<div id="gallery-<?= $objects_obj->id ?>" class="single-slider carousel slide" data-ride="carousel">
					<!-- Indicators -->
					<ol class="carousel-indicators">
						<?php for($i = 0; $i < count($images_arr); $i++) { ?>
							<li data-target="#gallery-<?= $objects_obj->id ?>" data-slide-to="<?= $i ?>" <?php if( $i === 0 ) { echo 'class="active"'; } ?>></li>
						<?php } ?>
					</ol>
					<!-- Wrapper for slides -->
					<div class="carousel-inner" role="listbox">
						<?php
						$j = 0;
						foreach($images_arr as $image) { ?>
							<div class="item<?php if( $j === 0 ) { echo ' active'; } ?>">
								<img src="<?= esc_url($image); ?>" alt="">
							</div>
						<?php $j++;
						} ?>
					</div>
					<!-- Controls -->
					<a class="left carousel-control" href="#gallery-<?= $objects_obj->id ?>" role="button" data-slide="prev">
						<span class="sr-only">Previous</span>
					</a>
					<a class="right carousel-control" href="#gallery-<?= $objects_obj->id ?>" role="button" data-slide="next">
						<span class="sr-only">Next</span>
					</a>
				</div><!-- /.single-slider -->
				<?php endif; ?>
				<div class="single-object-details">
					<ul class="single-object-locations">
						<li><a class="icon cursor-default"><span>
							<?php if(isset($objects_obj->country) && $objects_obj->country->title) {
								echo $objects_obj->country->title;
							}
							if(isset($objects_obj->region) && $objects_obj->region->title) {
								echo ', ' . $objects_obj->region->title;
							}
							if(isset($objects_obj->city) && $objects_obj->city->title) {
								echo ', ' . $objects_obj->city->title;
							}
							if(isset($objects_obj->district) && $objects_obj->district->title) {
								echo ', ' . $objects_obj->district->title;
							}
							?>
						</span></a></li>
					</ul><!-- /single-object-locations -->
					<h1 class="single-object-title">
						<?php if($objects_obj->sold_state) echo '<span class="object-sold">' . __('s_object:sold', 'leadingprops') . '</span> ' ?>
						<?= $object_title; ?>
					</h1>
					<?php /* Rent info */ ?>
					<?php if($object_type === 'rent') : ?>
						<?php if($objects_obj->features->bedrooms->min ||
						         $objects_obj->features->bedrooms->max ||
						         $objects_obj->features->bathrooms->min ||
						         $objects_obj->features->bathrooms->max ||
						         $objects_obj->features->bathrooms->min ||
						         $objects_obj->property_rent->persons_max ||
						         $objects_obj->property_rent->child_friendly ||
						         $objects_obj->property_rent->pets_allowed) { ?>
					<ul class="object-short-info">
						<?php if($objects_obj->features->bedrooms->min || $objects_obj->features->bedrooms->max) {
							echo '<li class="icon icon-bedroom">';
							if($objects_obj->features->bedrooms->min) { echo $objects_obj->features->bedrooms->min; }
							if($objects_obj->features->bedrooms->max) { echo ' - ' . $objects_obj->features->bedrooms->max; }
							echo '</li>';
						}
						if($objects_obj->features->bathrooms->min || $objects_obj->features->bathrooms->max) {
							echo '<li class="icon icon-bedroom">';
							if($objects_obj->features->bathrooms->min) { echo $objects_obj->features->bathrooms->min; }
							if($objects_obj->features->bathrooms->max) { echo ' - ' . $objects_obj->features->bathrooms->max; }
							echo '</li>';
						}
						if($objects_obj->property_rent->persons_max) {
							echo '<li class="icon icon-person">' . $objects_obj->property_rent->persons_max . '</li>';
						}
						if($objects_obj->property_rent->child_friendly) {
							echo '<li class="icon icon-child"></li>';
						}
						if($objects_obj->property_rent->pets_allowed) {
							echo '<li class="icon icon-pet"></li>';
						}
						?>
					</ul>
						<?php } ?>
					<?php endif; ?>
					<?php /* EOF Rent info */ ?>

					<div class="single-object-description">
						<p><?= $objects_obj->description->main_text; ?></p>
					</div><!-- /.single-object-description -->


					<?php if($object_type === 'sale') : ?>
						<div class="single-object-info">
							<p class="object-price icon">
								<?php if($objects_obj->parameters->price->on_demand) {
									_e('s_object:on_demand', 'leadingprops');
								} else {
									if($objects_obj->parameters->price->min) {
										echo number_format($objects_obj->parameters->price->min, 0, ".", " ");
									}
									if($objects_obj->parameters->price->max) {
										echo '&nbsp;-&nbsp;' . number_format($objects_obj->parameters->price->max, 0, ".", " ");
									}
									echo '&nbsp;' . $objects_obj->parameters->price->currency;
								} ?>
							</p>
							<?= '<p class="object-code icon">' . $objects_obj->code . '</p>'; ?>
						</div><!-- /.single-object-info -->
					<?php elseif($object_type === 'rent') : ?>
						<div class="rent-rate">
							<?php if($objects_obj->property_rent->long_rent) {
								echo '<p class="icon icon-month">' . __('s_object:rent:long-term_rent', 'leadingprops') . ': ';
									if($objects_obj->property_rent->rent_long->on_demand) {
										echo __('s_object:rent:on_request', 'leadingprops') . '*';
									} elseif($objects_obj->property_rent->rent_long->monthly_rate) {
										echo  number_format($objects_obj->property_rent->rent_long->monthly_rate, 0, ".", " ") . '&nbsp;' . $objects_obj->property_rent->rent_long->currency_code . '*';
									}
								echo '</p>';
								echo '<hr>';
								echo '<p class="footnote">* ';
									if($objects_obj->property_rent->rent_long->vat_in_price === false) {
										if($objects_obj->property_rent->rent_long->vat) {
											echo __('s_object:rent:vat', 'leadingprops') . ' ' . $objects_obj->property_rent->rent_long->vat . '% ' . __('s_object:rent:not_included', 'leadingprops') . '; ';
										}
									} else {
										echo __('s_object:rent:vat', 'leadingprops') . ' ' . __('s_object:rent:included', 'leadingprops') . '; ';
									}
								echo __('s_object:rent:deposit', 'leadingprops') . ': ';
									if($objects_obj->property_rent->rent_long->deposit_on_demand === true) {
										echo __('s_object:rent:on_request', 'leadingprops') . '; ';
									} else {
										if($objects_obj->property_rent->rent_long->deposit_type === 1) {
											if($objects_obj->property_rent->rent_long->deposit) {
												echo $objects_obj->property_rent->rent_long->deposit . '%; ';
											} else {
												echo __('s_object:rent:on_request', 'leadingprops') . '; ';
											}
										} else {
											if($objects_obj->property_rent->rent_long->deposit) {
												echo number_format($objects_obj->property_rent->rent_long->deposit, 0, ".", " ") . '&nbsp;' . $objects_obj->property_rent->rent_long->currency_code . '; ';
											} else {
												echo __('s_object:rent:on_request', 'leadingprops') . '; ';
											}
										}
									}
								echo __('s_object:rent:commission', 'leadingprops') . ': ';
								if($objects_obj->property_rent->rent_long->commission_on_demand === true) {
									echo __('s_object:rent:on_request', 'leadingprops') . '; ';
								} else {
									if($objects_obj->property_rent->rent_long->commission_type === 1) {
										if($objects_obj->property_rent->rent_long->commission) {
											echo $objects_obj->property_rent->rent_long->commission . '%; ';
										} else {
											echo __('s_object:rent:on_request', 'leadingprops') . '; ';
										}
									} else {
										if($objects_obj->property_rent->rent_long->commission) {
											echo number_format($objects_obj->property_rent->rent_long->commission, 0, ".", " ") . '&nbsp;' . $objects_obj->property_rent->rent_long->currency_code . '; ';
										} else {
											echo __('s_object:rent:on_request', 'leadingprops') . '; ';
										}
									}
								}
								echo '</p>';
							} ?>

							<?php if($objects_obj->property_rent->short_rent === true) {
								echo '<p class="icon icon-day">' . __('s_object:rent:rent_short_title', 'leadingprops') . ', ' . $objects_obj->property_rent->rent_short->currency_code . '*</p>
									  <div class="rent-price-wrap">
                                      <ul class="rent-price-list">
                                      <li class="heading">' . __('s_object:rent:min_stay', 'leadingprops') . '</li>
                                      <li class="heading">' . __('s_object:rent:low_season', 'leadingprops') . '</li>
                                      <li>1 ' .__('s_object:rent:day', 'leadingprops') . '</li>
                                      <li>';
								echo ($objects_obj->property_rent->rent_short->ls_daily_rate) ? number_format($objects_obj->property_rent->rent_short->ls_daily_rate, 0, ".", " ")  : '&mdash;';
								echo '</li>
								      <li>1 ' . __('s_object:rent:week', 'leadingprops') . '</li>
								      <li>';
								echo ($objects_obj->property_rent->rent_short->ls_weekly_rate) ? number_format($objects_obj->property_rent->rent_short->ls_weekly_rate, 0, ".", " ")  : '&mdash;';
								echo '</li>
								      <li>1 ' . __('s_object:rent:month', 'leadingprops') . '</li>
								      <li>';
								echo ($objects_obj->property_rent->rent_short->ls_monthly_rate) ? number_format($objects_obj->property_rent->rent_short->ls_monthly_rate, 0, ".", " ")  : '&mdash;';
								echo '</li>
								      </ul>';

								echo  '<ul class="rent-price-list">
                                      <li class="heading hidden-760">' . __('s_object:rent:min_stay', 'leadingprops') . '</li>
                                      <li class="heading">' . __('s_object:rent:medium_season', 'leadingprops') . '</li>
                                      <li class="hidden-760">1 ' .__('s_object:rent:day', 'leadingprops') . '</li>
                                      <li>';
								echo ($objects_obj->property_rent->rent_short->ms_daily_rate) ? number_format($objects_obj->property_rent->rent_short->ms_daily_rate, 0, ".", " ")  : '&mdash;';
								echo '</li>
									  <li class="hidden-760">1 ' . __('s_object:rent:week', 'leadingprops') . '</li>
									  <li>';
								echo ($objects_obj->property_rent->rent_short->ms_weekly_rate) ? number_format($objects_obj->property_rent->rent_short->ms_weekly_rate, 0, ".", " ")  : '&mdash;';
								echo '</li>
									  <li class="hidden-760">1 ' . __('s_object:rent:month', 'leadingprops') . '</li>
									  <li>';
								echo ($objects_obj->property_rent->rent_short->ms_monthly_rate) ? number_format($objects_obj->property_rent->rent_short->ms_monthly_rate, 0, ".", " ") : '&mdash;';
								echo '</li>
									 </ul>';

								echo  '<ul class="rent-price-list">
                                      <li class="heading hidden-760">' . __('s_object:rent:min_stay', 'leadingprops') . '</li>
                                      <li class="heading">' . __('s_object:rent:hight_season', 'leadingprops') . '</li>
                                      <li class="hidden-760">1 ' .__('s_object:rent:day', 'leadingprops') . '</li>
                                      <li>';
								echo ($objects_obj->property_rent->rent_short->hs_daily_rate) ? number_format($objects_obj->property_rent->rent_short->hs_daily_rate, 0, ".", " ")  : '&mdash;';
								echo '</li>
									  <li class="hidden-760">1 ' . __('s_object:rent:week', 'leadingprops') . '</li>
									  <li>';
								echo ($objects_obj->property_rent->rent_short->hs_weekly_rate) ? number_format($objects_obj->property_rent->rent_short->hs_weekly_rate, 0, ".", " ")  : '&mdash;';
								echo '</li>
									  <li class="hidden-760">1 ' . __('s_object:rent:month', 'leadingprops') . '</li>
									  <li>';
								echo ($objects_obj->property_rent->rent_short->hs_monthly_rate) ? number_format($objects_obj->property_rent->rent_short->hs_monthly_rate, 0, ".", " ")  : '&mdash;';
								echo '</li>
									 </ul>
									 </div>';

								echo '<p class="footnote">* ';
								if($objects_obj->property_rent->rent_short->vat_in_price === false) {
									if($objects_obj->property_rent->rent_short->vat) {
										echo __('s_object:rent:vat', 'leadingprops') . ' ' . $objects_obj->property_rent->rent_short->vat . '% ' . __('s_object:rent:not_included') . '; ';
									}
								} else {
									echo __('s_object:rent:vat', 'leadingprops') . ' ' . __('s_object:rent:included', 'leadingprops') . '; ';
								}
								echo __('s_object:rent:deposit', 'leadingprops') . ': ';
								if($objects_obj->property_rent->rent_short->deposit_on_demand === true) {
									echo __('s_object:rent:on_request', 'leadingprops') . '; ';
								} else {
									if($objects_obj->property_rent->rent_short->deposit_type === 1) {
										if($objects_obj->property_rent->rent_short->deposit) {
											echo $objects_obj->property_rent->rent_short->deposit . '%; ';
										} else {
											echo __('s_object:rent:on_request', 'leadingprops') . '; ';
										}
									} else {
										if($objects_obj->property_rent->rent_short->deposit) {
											echo $objects_obj->property_rent->rent_short->deposit . '&nbsp;' . $objects_obj->property_rent->rent_short->currency_code . '; ';
										} else {
											echo __('s_object:rent:on_request', 'leadingprops') . '; ';
										}
									}
								}
								echo __('s_object:rent:commission', 'leadingprops') . ': ';
								if($objects_obj->property_rent->rent_short->commission_on_demand === true) {
									echo __('s_object:rent:on_request', 'leadingprops') . '; ';
								} else {
									if($objects_obj->property_rent->rent_short->commission_type === 1) {
										if($objects_obj->property_rent->rent_short->commission) {
											echo $objects_obj->property_rent->rent_short->commission . '%; ';
										} else {
											echo __('s_object:rent:on_request', 'leadingprops') . '; ';
										}
									} else {
										if($objects_obj->property_rent->rent_short->commission) {
											echo $objects_obj->property_rent->rent_short->commission . '&nbsp;' . $objects_obj->property_rent->rent_short->currency_code . '; ';
										} else {
											echo __('s_object:rent:on_request', 'leadingprops') . '; ';
										}
									}
								}
								echo '</p>';
							}
							?>
						</div><!-- /.rent-rate -->
					<?php endif; ?>

					<?php echo '<ul class="object-properties">
								<li>' . __('s_object:property type', 'leadingprops') . '</li>
								<li>' . $objects_obj->parameters->property_object_type . '</li>';
					if($objects_obj->parameters->rooms->min || $objects_obj->parameters->rooms->max) {
						echo '<li>' . __('s_object:number_of_rooms', 'leadingprops') . '</li>';
						echo '<li>';
						if($objects_obj->parameters->rooms->min) {
							echo $objects_obj->parameters->rooms->min;
						}
						if($objects_obj->parameters->rooms->max) {
							echo ' - ' . $objects_obj->parameters->rooms->max;
						}
						echo '</li>';
					}
					if($objects_obj->parameters->area->min || $objects_obj->parameters->area->max) {
						echo '<li>' . __('s_object:area_sq_m', 'leadingprops') . '</li>';
						echo '<li>';
						if($objects_obj->parameters->area->min) {
							echo $objects_obj->parameters->area->min;
						}
						if($objects_obj->parameters->area->max) {
							echo ' - ' . $objects_obj->parameters->area->max;
						}
						echo '</li>';
					}
					if($objects_obj->features->bedrooms->min || $objects_obj->features->bedrooms->max) {
						echo '<li>' . __('s_object:bedrooms', 'leadingprops') . '</li>';
						echo '<li>';
						if($objects_obj->features->bedrooms->min) {
							echo $objects_obj->features->bedrooms->min;
						}
						if($objects_obj->features->bedrooms->max) {
							echo ' - ' . $objects_obj->features->bedrooms->max;
						}
						echo '</li>';
					}
					if($objects_obj->features->bathrooms->min || $objects_obj->features->bathrooms->max) {
						echo '<li>' . __('s_object:bathrooms', 'leadingprops') . '</li>';
						echo '<li>';
						if($objects_obj->features->bathrooms->min) {
							echo $objects_obj->features->bathrooms->min;
						}
						if($objects_obj->features->bathrooms->max) {
							echo ' - ' . $objects_obj->features->bathrooms->max;
						}
						echo '</li>';
					}
					if($object_type === 'rent') {
						if($objects_obj->property_rent->persons_max) {
							echo '<li>' . __('s_object:rent:max_persons', 'leadingprops') . '</li><li>' . $objects_obj->property_rent->persons_max .  '</i></li>';
						}
					}
					if($objects_obj->features->land_area->min || $objects_obj->features->land_area->max) {
						echo '<li>' . __('s_object:land_area', 'leadingprops') . '</li>';
						echo '<li>';
						if($objects_obj->features->land_area->min) {
							echo $objects_obj->features->land_area->min;
						}
						if($objects_obj->features->land_area->max) {
							echo ' - ' . $objects_obj->features->land_area->max;
						}
						echo '</li>';
					}
					if($objects_obj->features->building_storeys && ($objects_obj->features->building_storeys->min || $objects_obj->features->building_storeys->max)) {
						echo '<li>' . __('s_object:building_storeys', 'leadingprops') . '</li>';
						echo '<li>';
						if($objects_obj->features->building_storeys->min) {
							echo $objects_obj->features->building_storeys->min;
						}
						if($objects_obj->features->building_storeys->max) {
							echo ' - ' . $objects_obj->features->building_storeys->max;
						}
						echo '</li>';
					}
					if(isset($objects_obj->features->terrace_balcony->present) && $objects_obj->features->terrace_balcony->present === true) {
						echo '<li>' . __('s_object:terrace_balcony', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
					if(isset($objects_obj->features->pool->present) && $objects_obj->features->pool->present === true) {
						echo '<li>' . __('s_object:pool', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
					if(isset($objects_obj->features->garage_parking->present) && $objects_obj->features->garage_parking->present === true) {
						echo '<li>' . __('s_object:garage_parking', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
					if(isset($objects_obj->features->utility_rooms->present) && $objects_obj->features->utility_rooms->present === true) {
						echo '<li>' . __('s_object:utility_rooms', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
					if($object_type === 'rent') {
						if($objects_obj->property_rent->child_friendly === true) {
							echo '<li>' . __('search_panel:child_friendly', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
						}
						if($objects_obj->property_rent->pets_allowed === true) {
							echo '<li>' . __('search_panel:pets_allowed', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
						}
					}
					echo '</ul><!-- /.object-properties -->';
					?>

				</div><!-- /.single-object-details -->
			</div><!-- /.single-object-content-inner -->
		</div><!-- /.single-object-content -->

		<div class="similar-object-search">
			<div class="similar-search-container">
				<i class="icon icon-radar"></i>
				<p>Find similar properties in the same area</p>
				<ul class="similar-locations">
					<?= '<li><a href="' . $list_url . urlencode('{"location_point":{"lat":' . $objects_obj->location->lat . ' ,"lon":' . $objects_obj->location->lon . ',"radius":1},"similar":{"code":"' . $objects_obj->code . '"}}') . '">1 km</a></li>'; ?>
					<?= '<li><a href="' . $list_url . urlencode('{"location_point":{"lat":' . $objects_obj->location->lat . ' ,"lon":' . $objects_obj->location->lon . ',"radius":5},"similar":{"code":"' . $objects_obj->code . '"}}') . '">5 km</a></li>'; ?>
					<?= '<li><a href="' . $list_url . urlencode('{"location_point":{"lat":' . $objects_obj->location->lat . ' ,"lon":' . $objects_obj->location->lon . ',"radius":10},"similar":{"code":"' . $objects_obj->code . '"}}') . '">10 km</a></li>'; ?>
				</ul>
			</div>
		</div><!-- /.similar-object-search -->
		<footer class="single-object-footer">
			<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-target=".single-object-request" data-type="single_property" data-object-type="<?= $object_type; ?>" data-id="<?= $objects_obj->id; ?>" data-code="<? $objects_obj->code; ?>">
				<span><?php _e('s_object:header:detailed_info', 'leadingprops'); ?></span>
			</a>
		</footer>

	</div><!-- /.single-object-container -->
	<?php

	?>
<?php else : // Error Message
	endif; ?>
<?php
	get_footer();

