<?php
	global $lp_settings;
	$header_style = get_field('header_style', 'option');
	$lang_indicator = get_field('lang_indicator_type', 'option');
	$lang_class = ($lang_indicator === 'flag-code') ? ' with-code' : '';
?>
<body <?php body_class(); ?>>
	<div class="wrap" role="document">
	<?php if (has_nav_menu('primary_navigation')) : ?>
		<nav class="side-menu slide-left">
			<?php wp_nav_menu([
				'theme_location' => 'primary_navigation',
				'container' => false,
				'walker'    => new LP_Nav_Walker()
			]); ?>
			<a class="menu-close btn-close"><span class="sr-only">Close</span></a>
		</nav><!-- /.side-menu -->
	<?php endif; ?>
		<div class="menu-overlay">
			<div class="site-content">
				<div class="site-content-inner">
					<header class="site-header header-<?= $header_style; ?>" role="banner">
						<div class="logo container">
							<?php if( $header_style === 'center') { ?>
								<div class="contacts contacts-left">
									<?php
									if($lp_settings['contact_phone']) {
										echo '<div class="contact-phone">' . $lp_settings['contact_phone'] . '</div>';
									}
									?>
								</div>
							<?php } ?>

							<?php $logo_array = wp_get_attachment_image_src(get_field('logo', 'option'), 'full'); ?>

							<a class="logo-link" href="<?php echo esc_url(home_url('/')); ?>">
								<?php if($logo_array[0]) {
									echo '<img src="' . $logo_array[0] . '" alt="' . get_bloginfo('name') . '" width="' . $logo_array[1] / 2 . '" height="' . $logo_array[2] / 2 . '">';
								} else {
									bloginfo('name');
								} ?>
							</a>
							<div class="contacts contacts-right">
								<?php
								if( $header_style !== 'center' && $lp_settings['contact_phone']) {
									echo '<div class="contact-phone">' . $lp_settings['contact_phone'] . '</div>';
								}
								if($lp_settings['contact_email']) {
								echo '<div class="contact-email"><a href="mailto:' . $lp_settings['contact_email'] . '">' . $lp_settings['contact_email'] . '</a></div>';
								} ?>
							</div>

						</div>
						<div class="header-inner">
							<div class="container">
								<?php if (has_nav_menu('primary_navigation')) : ?>
									<?php wp_nav_menu([
										'theme_location' => 'primary_navigation',
										'container' => 'nav',
										'container_class' => 'menu-desktop',
										'walker'    => new LP_Nav_Walker()
									]);
									?>
								<?php endif; ?>
								<div class="map-col"><a href="#" class="map-toggle icon" data-toggle="modal" data-target="#map-modal"><span class="sr-only">Select region</span></a></div>
								<div class="menu-col"><a href="#" class="menu-toggle"><span class="sr-only">Menu</span></a></div>
								<div class="lang-col"><a class="lang-toggle collapsed<?= $lang_class; ?>" data-toggle="collapse" data-target="#lang-panel" aria-expanded="false" aria-controls="lang-panel"><span class="lang-<?= $lp_settings['lang']; ?>"></span><span class="ln-code icon"><?= $lp_settings['lang']; ?></span> </a></div>
							</div>
						</div><!-- /.header-inner -->
						<?php do_action('lang_panel') ?>
					</header><!-- /#site-header -->

