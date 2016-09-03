<?php
/**
 *  Template Name: Sale Favorites
 */
?>

<?php
get_template_part('templates/head');
get_header();
get_favorites_bar();
?>
	<section class="sorting-wrapper container">
		<div class="fav-count-wrap">
			<span class="fav-count-field icon"><?php _e('favorites:title', 'leadingprops'); ?> <span class="favorite_objects_count">0 </span> <span class="fav-remove tag-remove"></span></span>
		</div>
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
			<div id="object-list" class="row"></div>
		</div>
	</section>
	<div class="loader">
		<span class="spin"></span>
	</div>
<?php get_favorites_bar(true); ?>
<div class="favorites-confirmation-message">
    <div class="container">
      <p><?php _e('favorites:delete_alert', 'leadingprops') ?></p>
      <div class="favorites-button-wrap">
        <button type="button" class="btn btn-white btn-fav-confirm" data-action="delete">_e('favorites:delete_y', 'leadingprops')</button>
        <button type="button" class="btn btn-white btn-fav-confirm" data-action="close"_e('favorites:delete_n', 'leadingprops')/button>
      </div>
    </div>
</div>
<?php get_footer();
