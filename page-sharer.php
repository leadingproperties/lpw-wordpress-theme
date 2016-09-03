<?php
/**
 *  Template Name: Sale Share
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
				<option value="false"><?php _e('search_panel:order:recent', 'leadingprops'); ?></option>
				<option value="desc"><?php _e('search_panel:order:price_desc', 'leadingprops'); ?></option>
				<option value="asc"><?php _e('search_panel:order:price_asc', 'leadingprops'); ?></option>
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
						'for_sale'  => true,
						'for_rent'  => false,
						'action' => 'get_objects',
						'ids'   => $ids
					]);
					$object_array = $shared_objects->get_objects_array();

					if(isset($object_array->property_objects) && is_array($object_array->property_objects)) {
						foreach($object_array->property_objects as $object) { ?>
						<article class="object-item" id="object-<?= $object->id; ?>" data-object-id="<?= $object->id; ?>">
							<div class="object-inner-wrapper">
								<div class="object-thumbnail">
									<a href="<?= $lp_settings['property_page'] . $object->slug; ?>" class="open-object-modal object-thumbnail-holder" title="<?= $object->description->title; ?>">
										<img class="img-responsive" src="<?= $object->image; ?>"  alt="<?= $object->description->title; ?>">
									</a>
									<span class="add-favorite-button" data-action="add" data-id="<?= $object->id; ?>"></span>
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
										<a class="open-object-modal object-link" href="<?= $lp_settings['property_page'] . $object->slug; ?>"><?= $object->description->title; ?></a>
									</h2>
									<p class="info-details"><span>
                                    <?php if($object->parameters->price->on_demand) { ?>
                                        <span>Price on demand</span>&nbsp;
                                    <?php } else {
                                        if ($object->parameters->price->min) { ?>
                                            <span><?= number_format($object->parameters->price->min, 0, ".", " "); ?></span>
                                        <?php }
	                                    if ($object->parameters->price->max) { ?>
		                                    &nbsp;&ndash;&nbsp;<span><?= number_format($object->parameters->price->max, 0, ".", " "); ?></span>
	                                    <?php } ?>
                                           &nbsp;<span><?= $object->parameters->price->currency; ?></span></span>
										<?php }

										if ($object->parameters->rooms->min || $object->parameters->rooms->max) { ?>
												,&nbsp;
										<?php }
										if ($object->parameters->rooms->min) { ?>
									            <span><?= $object->parameters->rooms->min; ?></span>
										<?php }
										if ($object->parameters->rooms->max) { ?>
												&nbsp;&ndash;&nbsp;<span><?= $object->parameters->rooms->max; ?></span>
										<?php }
										if ($object->parameters->rooms->min || $object->parameters->rooms->max) { ?>
												&nbsp;rooms
										<?php }
										if ($object->parameters->area->min || $object->parameters->area->max) { ?>
												,&nbsp;
										<?php }
										if ($object->parameters->area->min) { ?>
											<span><?= $object->parameters->area->min; ?></span>
										<?php }
										if ($object->parameters->area->max) { ?>
												&nbsp;&ndash;&nbsp;<span><?= $object->parameters->area->max; ?></span>
										<?php }
										if ($object->parameters->area->min || $object->parameters->area->max) { ?>
												&nbsp;m<sup>2</sup>
										<?php } ?>
									</p>
								</div>
							</div>
						</article>
						<?php }
					}
				}
				 ?>
			</div>
		</div>
	</section>
	<div class="loader">
		<span class="spin"></span>
	</div>
<?php get_favorites_bar(true); ?>
<?php get_footer();
