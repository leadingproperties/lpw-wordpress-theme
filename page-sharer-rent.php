<?php
/**
 *  Template Name: Rent Share
 */
?>
<?php
global $lp_settings;
$ids = (isset($_GET['ids'])) ? explode('.', $_GET['ids']) : false;
get_template_part('templates/head');
get_header();
get_favorites_bar(false);
?>
	<section class="sorting-wrapper container">
		<div class="sorting-field sorting">
			<label for="sorting" class="sr-only">Sort</label>
			<select class="sorting-select" id="sorting">
				<option value="false"><?php _e('Most recent', 'leadingprops'); ?></option>
				<option value="desc"><?php _e('Highest price', 'leadingprops'); ?></option>
				<option value="asc"><?php _e('Lowest price', 'leadingprops'); ?></option>
			</select>
		</div>
	</section><!-- /.sorting -->
	<section class="objects-list-wrapper">
		<div class="container">
			<div id="object-list" class="row">
				<?php if($ids) {
					$shared_objects = new LP_ObjectList([
						'lang'  => $lp_settings['lang'],
						'page'  => 1,
						'per_page'  => 9,
						'for_sale'  => false,
						'for_rent'  => true,
						'action' => 'get_objects',
						'ids'   => $ids
					]);
					$object_array = $shared_objects->get_objects_array();

					if(isset($object_array->property_objects) && is_array($object_array->property_objects)) {
						foreach($object_array->property_objects as $object) {

							$min_stay = '';
							$rent_price = '';
							if($object->property_rent->long_rent === true && $object->property_rent->short_rent === false) {
								$min_stay = __('on request', 'leadingprops');
							} elseif( $object->property_rent->short_rent === true ) {
								if ( $object->property_rent->rent_short->min_day === true ) {
									$minStay = '1 ' . __('day', 'leadingprops');
								} elseif ( $object->property_rent->rent_short->min_week === true ) {
									$minStay = '1 ' . __('week', 'leadingprops');
								} else {
									$minStay = '1 ' . __('month', 'leadingprops');
								}
							}

							if( $object->property_rent->short_rent === true ) {
								if ( $object->property_rent->rent_short->sort_price->day ) {
									$rentPrice = '<span class="price-num">' . number_format( $object->property_rent->rent_short->sort_price->day, 0, ".", " " ) . ' </span>' . $object->property_rent->rent_short->sort_price->currency_code . '&nbsp;/&nbsp;' . __( 'day', 'leadingprops' );
								} elseif ( $object->roperty_rent->rent_short->sort_price->month ) {
									$rentPrice = '<span class="price-num">' . number_format( $object->property_rent->rent_short->sort_price->month, 0, ".", " " ) . ' </span>' . $object->property_rent->rent_short->sort_price->currency_code . '&nbsp;/&nbsp;' . __( 'month', 'leadingprops' );
								}
							} elseif( $object->property_rent->long_rent === true ) {
								if($object->property_rent->rent_long->sort_price->month) {
									$rentPrice = '<span class="price-num">' . number_format( $object->property_rent->rent_long->sort_price->month, 0, ".", " " ) .  ' </span>' . $object->property_rent->rent_long->sort_price->currency_code . '&nbsp;/&nbsp;' . __( 'month', 'leadingprops' );
								} elseif($object->property_rent->rent_long->sort_price->day) {
									$rentPrice = '<span class="price-num">' . number_format( $object->property_rent->rent_long->sort_price->day, 0, ".", " " ) . ' </span>' . $object->property_rent->rent_long->sort_price->currency_code . '&nbsp;/&nbsp;' . __( 'day', 'leadingprops' );
								}
							}

							?>
						<article class="object-item rent-item" id="object-<?= $object->id; ?>" data-object-id="<?= $object->id; ?>">
							<div class="object-inner-wrapper">
								<div class="object-thumbnail">
									<a href="<?= $lp_settings['property_page'] . $object->rent_slug; ?>" class="open-object-modal object-thumbnail-holder" title="<?= $object->description->rent_title; ?>">
										<img class="img-responsive" src="<?= $object->image; ?>"  alt="<?= $object->description->rent_title; ?>">
									</a>
									<span class="add-favorite-button" data-action="add" data-id="<?= $object->id; ?>"></span>
									<div class="rent-price"><span><?= $rentPrice  ?></span></div>
								</div>
								<div class="object-info-holder">
									<div class="info-address-holder">
										<div class="info-address">
											<?php
											if ($object->country && $object->country->title) { ?>
												<a><?= $object->country->title; ?></a>
											<?php }
											if ($object->region && $object->region->title) { ?>
												<a><?= $object->region->title; ?></a>
											<?php }
											if ($object->city && $object->city->title) { ?>
												<a><?= $object->city->title; ?></a>
											<?php } ?>
										</div>
									</div>
									<h2 class="info-title">
										<a class="open-object-modal object-link" href="<?= $lp_settings['property_page'] . $object->rent_slug; ?>"><?= $object->description->rent_title; ?></a>
									</h2>
									<p class="min-days"><?= __('Minimum stay', 'leadingprops'); ?>: <?= $minStay; ?></p>
									<ul class="rent-details">
										<?php if ( $object->parameters->area->min ) { ?>
											<li class="area"><?= $object->parameters->area->min; ?> m<sup>2</sup></li>
										<?php }
										if ( $object->parameters->bedrooms->min ) { ?>
											<li class="icon icon-bedroom"><?= $object->parameters->bedrooms->min; ?></li>
										<?php }
										if ( $object->parameters->bathrooms->min ) { ?>
											<li class="icon icon-bathroom"><?= $object->parameters->bedrooms->min; ?></li>
										<?php }
										if ( $object->property_rent->persons_max ) { ?>
											<li class="icon icon-person"><?= $object->property_rent->persons_max; ?></li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</article>
						<?php }
					}
				} ?>
			</div>
		</div>
	</section>
	<div class="loader">
		<span class="spin"></span>
	</div>
<?php get_favorites_bar(true); ?>
<?php get_footer();
