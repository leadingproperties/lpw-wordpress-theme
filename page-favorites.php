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
			<span class="fav-count-field icon"><?php _e('Your favorite properties', 'leadingprops'); ?>: <span class="favorite_objects_count">0 </span> <span class="fav-remove tag-remove"></span></span>
		</div>
		<div class="sorting-field sorting">
			<label for="sorting" class="sr-only"><?php _e('Sort', 'leadingprops'); ?></label>
			<select class="sorting-select" id="sorting">
				<option><?php _e('Most recent', 'leadingprops'); ?></option>
				<option><?php _e('Highest price', 'leadingprops'); ?></option>
				<option><?php _e('Lowest price', 'leadingprops'); ?></option>
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
      <p><?php _e('Are you sure you want to delete all properties from favorites?') ?></p>
      <div class="favorites-button-wrap">
        <button type="button" class="btn btn-white btn-fav-confirm" data-action="delete">Yes, delete</button>
        <button type="button" class="btn btn-white btn-fav-confirm" data-action="close">No</button>
      </div>
    </div>
</div>
<?php get_footer();
