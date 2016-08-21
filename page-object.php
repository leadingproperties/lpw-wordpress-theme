<?php
/**
 *  Template Name: Single Object
 */
?>
<?php
	$object_slug = get_query_var('object_slug', false);
	$objects_obj = new StdClass;
	if($object_slug) {
		$objects = new LP_ObjectList([
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

	?>
	<div class="single-object-container<?= $object_class; ?>">
		<header class="single-object-header">
			<div class="single-object-wrap">
				<div class="detailed-link-wrap">
					<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-target=".single-object-request">
						<span><?php _e('Get detailed information', 'leadingprops'); ?></span>
					</a>
				</div>
				<ul class="single-object-menu">
					<li><a href="#" class="pdf-link"><sup class="text-red">PDF</sup></a></li>
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
						<?php if($objects_obj->sold_state) echo '<span class="object-sold">' . __('sold', 'leadingprops') . '</span> ' ?>
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
									_e('Price on demand', 'leadindprops');
								} else {
									if($objects_obj->parameters->price->min) {
										echo $objects_obj->parameters->price->min;
									}
									if($objects_obj->parameters->price->max) {
										echo '&nbsp;-&nbsp;' . $objects_obj->parameters->price->max;
									}
									echo '&nbsp;' . $objects_obj->parameters->price->currency;
								} ?>
							</p>
							<?= '<p class="object-code icon">' . $objects_obj->code . '</p>'; ?>
						</div><!-- /.single-object-info -->
					<?php elseif($object_type === 'rent') : ?>
						<div class="rent-rate">
							<?php if($objects_obj->property_rent->long_rent) {
								echo '<p class="icon icon-month">' . __('Long term rental', 'leadingprops') . ', ' . __('monthly rate', 'leadingprops') . ': ';
									if($objects_obj->property_rent->rent_long->on_demand) {
										echo __('on request', 'leadingprops') . '*';
									} elseif($objects_obj->property_rent->rent_long->monthly_rate) {
										echo $objects_obj->property_rent->rent_long->monthly_rate . '&nbsp;' . $objects_obj->property_rent->rent_long->currency_code . '*';
									}
								echo '</p>';
								echo '<hr>';
								echo '<p class="footnote">* ';
									if($objects_obj->property_rent->rent_long->vat_in_price === false) {
										if($objects_obj->property_rent->rent_long->vat) {
											echo __('VAT', 'leadingprops') . ' ' . $objects_obj->property_rent->rent_long->vat . '% ' . __('is not included') . '; ';
										}
									} else {
										echo __('VAT is included', 'leadingprops') . '; ';
									}
								echo __('deposit', 'leadingprops') . ': ';
									if($objects_obj->property_rent->rent_long->deposit_on_demand === true) {
										echo __('on request', 'leadingprops') . '; ';
									} else {
										if($objects_obj->property_rent->rent_long->deposit_type === 1) {
											if($objects_obj->property_rent->rent_long->deposit) {
												echo $objects_obj->property_rent->rent_long->deposit . '%; ';
											} else {
												echo __('on request', 'leadingprops') . '; ';
											}
										} else {
											if($objects_obj->property_rent->rent_long->deposit) {
												echo $objects_obj->property_rent->rent_long->deposit . '&nbsp;' . $objects_obj->property_rent->rent_long->currency_code . '; ';
											} else {
												echo __('on request', 'leadingprops') . '; ';
											}
										}
									}
								echo __('commission', 'leadingprops') . ': ';
								if($objects_obj->property_rent->rent_long->commission_on_demand === true) {
									echo __('on request', 'leadingprops') . '; ';
								} else {
									if($objects_obj->property_rent->rent_long->commission_type === 1) {
										if($objects_obj->property_rent->rent_long->commission) {
											echo $objects_obj->property_rent->rent_long->commission . '%; ';
										} else {
											echo __('on request', 'leadingprops') . '; ';
										}
									} else {
										if($objects_obj->property_rent->rent_long->commission) {
											echo $objects_obj->property_rent->rent_long->commission . '&nbsp;' . $objects_obj->property_rent->rent_long->currency_code . '; ';
										} else {
											echo __('on request', 'leadingprops') . '; ';
										}
									}
								}
								echo '</p>';
							} ?>

							<?php if($objects_obj->property_rent->short_rent === true) {
								echo '<p class="icon icon-day">' . __('Short term rental', 'leadingprops') . ', ' . __('daily rate', 'leadingprops') . ', ' . $objects_obj->property_rent->rent_short->currency_code . '*</p>
									  <div class="rent-price-wrap">
                                      <ul class="rent-price-list">
                                      <li class="heading">' . __('min. stay', 'leadingprops') . '</li>
                                      <li class="heading">' . __('low season', 'leadingprops') . '</li>
                                      <li>1 ' .__('day', 'leadingprops') . '</li>
                                      <li>';
								echo ($objects_obj->property_rent->rent_short->ls_daily_rate) ? $objects_obj->property_rent->rent_short->ls_daily_rate : '&mdash;';
								echo '</li>
								      <li>1 ' . __('week', 'leadingprops') . '</li>
								      <li>';
								echo ($objects_obj->property_rent->rent_short->ls_weekly_rate) ? $objects_obj->property_rent->rent_short->ls_weekly_rate : '&mdash;';
								echo '</li>
								      <li>1 ' . __('month', 'leadingprops') . '</li>
								      <li>';
								echo ($objects_obj->property_rent->rent_short->ls_monthly_rate) ? $objects_obj->property_rent->rent_short->ls_monthly_rate : '&mdash;';
								echo '</li>
								      </ul>';

								echo  '<ul class="rent-price-list">
                                      <li class="heading hidden-760">' . __('min. stay', 'leadingprops') . '</li>
                                      <li class="heading">' . __('medium season', 'leadingprops') . '</li>
                                      <li class="hidden-760">1 ' .__('day', 'leadingprops') . '</li>
                                      <li>';
								echo ($objects_obj->property_rent->rent_short->ms_daily_rate) ? $objects_obj->property_rent->rent_short->ms_daily_rate : '&mdash;';
								echo '</li>
									  <li class="hidden-760">1 ' . __('week', 'leadingprops') . '</li>
									  <li>';
								echo ($objects_obj->property_rent->rent_short->ms_weekly_rate) ? $objects_obj->property_rent->rent_short->ms_weekly_rate : '&mdash;';
								echo '</li>
									  <li class="hidden-760">1 ' . __('month', 'leadingprops') . '</li>
									  <li>';
								echo ($objects_obj->property_rent->rent_short->ms_monthly_rate) ? $objects_obj->property_rent->rent_short->ms_monthly_rate : '&mdash;';
								echo '</li>
									 </ul>';

								echo  '<ul class="rent-price-list">
                                      <li class="heading hidden-760">' . __('min. stay', 'leadingprops') . '</li>
                                      <li class="heading">' . __('hight season', 'leadingprops') . '</li>
                                      <li class="hidden-760">1 ' .__('day', 'leadingprops') . '</li>
                                      <li>';
								echo ($objects_obj->property_rent->rent_short->hs_daily_rate) ? $objects_obj->property_rent->rent_short->hs_daily_rate : '&mdash;';
								echo '</li>
									  <li class="hidden-760">1 ' . __('week', 'leadingprops') . '</li>
									  <li>';
								echo ($objects_obj->property_rent->rent_short->hs_weekly_rate) ? $objects_obj->property_rent->rent_short->hs_weekly_rate : '&mdash;';
								echo '</li>
									  <li class="hidden-760">1 ' . __('month', 'leadingprops') . '</li>
									  <li>';
								echo ($objects_obj->property_rent->rent_short->hs_monthly_rate) ? $objects_obj->property_rent->rent_short->hs_monthly_rate : '&mdash;';
								echo '</li>
									 </ul>
									 </div>';

								echo '<p class="footnote">* ';
								if($objects_obj->property_rent->rent_short->vat_in_price === false) {
									if($objects_obj->property_rent->rent_short->vat) {
										echo __('VAT', 'leadingprops') . ' ' . $objects_obj->property_rent->rent_short->vat . '% ' . __('is not included') . '; ';
									}
								} else {
									echo __('VAT is included', 'leadingprops') . '; ';
								}
								echo __('deposit', 'leadingprops') . ': ';
								if($objects_obj->property_rent->rent_short->deposit_on_demand === true) {
									echo __('on request', 'leadingprops') . '; ';
								} else {
									if($objects_obj->property_rent->rent_short->deposit_type === 1) {
										if($objects_obj->property_rent->rent_short->deposit) {
											echo $objects_obj->property_rent->rent_short->deposit . '%; ';
										} else {
											echo __('on request', 'leadingprops') . '; ';
										}
									} else {
										if($objects_obj->property_rent->rent_short->deposit) {
											echo $objects_obj->property_rent->rent_short->deposit . '&nbsp;' . $objects_obj->property_rent->rent_short->currency_code . '; ';
										} else {
											echo __('on request', 'leadingprops') . '; ';
										}
									}
								}
								echo __('commission', 'leadingprops') . ': ';
								if($objects_obj->property_rent->rent_short->commission_on_demand === true) {
									echo __('on request', 'leadingprops') . '; ';
								} else {
									if($objects_obj->property_rent->rent_short->commission_type === 1) {
										if($objects_obj->property_rent->rent_short->commission) {
											echo $objects_obj->property_rent->rent_short->commission . '%; ';
										} else {
											echo __('on request', 'leadingprops') . '; ';
										}
									} else {
										if($objects_obj->property_rent->rent_short->commission) {
											echo $objects_obj->property_rent->rent_short->commission . '&nbsp;' . $objects_obj->property_rent->rent_short->currency_code . '; ';
										} else {
											echo __('on request', 'leadingprops') . '; ';
										}
									}
								}
								echo '</p>';
							}
							?>
						</div><!-- /.rent-rate -->
					<?php endif; ?>

					<?php echo '<ul class="object-properties">
								<li>' . __('property type', 'leadingprops') . '</li>
								<li>' . $objects_obj->parameters->property_object_type . '</li>';
					if($objects_obj->parameters->rooms->min || $objects_obj->parameters->rooms->max) {
						echo '<li>' . __('number of rooms', 'leadingprops') . '</li>';
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
						echo '<li>' . __('area, m', 'leadingprops') . ' <sup>2</sup></li>';
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
						echo '<li>' . __('bedrooms', 'leadingprops') . '</li>';
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
						echo '<li>' . __('bedrooms', 'leadingprops') . '</li>';
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
							echo '<li>' . __('max. persons', 'leadingprops') . '</li><li>' . $objects_obj->property_rent->persons_max .  '</i></li>';
						}
					}
					if($objects_obj->features->land_area->min || $objects_obj->features->land_area->max) {
						echo '<li>' . __('land area', 'leadingprops') . '</li>';
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
						echo '<li>' . __('building_storeys', 'leadingprops') . '</li>';
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
						echo '<li>' . __('terrace/balcony', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
					if(isset($objects_obj->features->pool->present) && $objects_obj->features->pool->present === true) {
						echo '<li>' . __('pool', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
					if(isset($objects_obj->features->garage_parking->present) && $objects_obj->features->garage_parking->present === true) {
						echo '<li>' . __('parking/garage') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
					if(isset($objects_obj->features->utility_rooms->present) && $objects_obj->features->utility_rooms->present === true) {
						echo '<li>' . __('cellars', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
					}
					if($object_type === 'rent') {
						if($objects_obj->property_rent->child_friendly === true) {
							echo '<li>' . __('child friendly', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
						}
						if($objects_obj->property_rent->pets_allowed === true) {
							echo '<li>' . __('pets allowed', 'leadingprops') . '</li><li><i class="icon icon-checkmark-circle"></i></li>';
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
					<li><a href="#">1 km</a></li>
					<li><a href="#">5 km</a></li>
					<li><a href="#">10 km</a></li>
				</ul>
			</div>
		</div><!-- /.similar-object-search -->
		<footer class="single-object-footer">
			<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-target=".single-object-request"><span>Get detailed information</span></a>
		</footer>

	</div><!-- /.single-object-container -->
	<?php

	?>
<?php else : // Error Message
	endif; ?>
<?php
	get_footer();

