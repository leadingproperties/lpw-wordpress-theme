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
					$args = [
						'lang'  => $lp_settings['lang'],
						'page'  => 1,
						'per_page'  => 9,
						'for_sale'  => 'false',
						'for_rent'  => 'true',
						'price' => [
							'currency'  => $lp_settings['currency_id']
						],
						'action' => 'get_objects',
						'ids'   => $ids
					];
					$list = get_object_list($args);

					if(isset($list['html'])) {
						echo $list['html'];
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
