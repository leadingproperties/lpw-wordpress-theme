<?php
	get_template_part('templates/head');
	get_header();
	global $lp_settings;
?>
	<section class="static-page-content static-page-404">
		<div class="static-page-container">
			<h1 class="err-404-title"><?php _e('Error 404', 'leadingprops'); ?></h1>
			<div class="err-404-msg">
				<h2><?php _e('Sorry, that page doesn’t exist!', 'leadingprops'); ?></h2>
				<p><?php _e('The reasons that could cause the error: incorrectly typed address, the
				      page was never on this site or this page was, but can not be accessed anymore.
				      If you reached this page through a link from our website, please report this
				      at:');
					if($lp_settings['contact_email']) {
						echo '<a href="mailto:' . $lp_settings['contact_email'] . '">' . $lp_settings['contact_email'] . '</a></p>';
					}?>
			</div>
		</div><!-- /.static-page-container -->
	</section>
<?php
	get_footer();
