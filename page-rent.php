<?php
/**
 *  Template Name: Rent
 */
?>

<?php
	get_template_part('templates/head');
	get_header();
	get_template_part('templates/search', 'panel');
?>
	<section class="objects-list-wrapper">
		<div class="container">
			<div id="object-list" class="row"></div>
		</div>
	</section>
	<div class="loader">
		<span class="spin"></span>
	</div>

<?php
	get_footer();

