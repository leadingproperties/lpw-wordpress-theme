<?php
	get_template_part('templates/head');
	get_header();
	global $lp_settings;
?>
	<section class="static-page-content static-page-404">
		<div class="static-page-container">
			<h1 class="err-404-title"><?php _e('page_404:title_404', 'leadingprops'); ?></h1>
			<div class="err-404-msg">
				<h2><?php _e('page_404:title', 'leadingprops'); ?></h2>
				<p><?php _e('page_404:text');
					if($lp_settings['contact_email']) {
						echo '<a href="mailto:' . $lp_settings['contact_email'] . '">' . $lp_settings['contact_email'] . '</a></p>';
					}?>
			</div>
		</div><!-- /.static-page-container -->
	</section>
<?php
	get_footer();
