<?php global $pos, $lp_settings;
	$link = '';
	$format = '<ul class="tooltip-sharing tooltip-sharing-top" data-toggle="tooltip" title="' . __('Share your favorite properties with friends or colleagues', 'leadingprops') . '">%1$s%2$s%3$s%4$s%5$s</ul>';
	$emailLink = '<li><a class="obj-share-email half-opaque soc-icon email-icon"></a></li>';
	$fbLink = '<li><a target="_blank" class="obj-share-fb half-opaque soc-icon fb-icon"></a></li>';
	$twLink = '<li><a target="_blank" class="obj-share-tw half-opaque soc-icon twitter-icon"></a></li>';
	$lnLink = '<li><a target="_blank" class="obj-share-ln half-opaque soc-icon ln-icon"></a></li>';
	$gpLink	= '<li><a target="_blank" class="obj-share-gplus half-opaque soc-icon gplus-icon"></a></li>';
	if((is_page_template('page-sharer.php') || is_page_template('page-sharer-rent.php')) && isset($_GET['ids'])) {
		$link = get_permalink() . '?ids=' .  $_GET['ids'];
		if(get_field('use_google_shortener', 'option')) {
			$shorten_url = get_shorten_url($link);
			if($shorten_url->id) {
				$link = $shorten_url->id;
			}
		}
		$uncLink = urlencode($link);
		$emailLink = '<li><a class="obj-share-email soc-icon email-icon" href="mailto:?Subject=' . $lp_settings['site_title'] . '&body=' . $link . " \n\n" . __('Property selection powered by The Leading Properties of the World. Best hand-picked properties with beautiful photos. Want to see properties selected just for you? Visit our website and get your personal recommendation today.') . '"></a></li>';
		$fbLink = '<li><a target="_blank" class="obj-share-fb soc-icon fb-icon" href="https://www.facebook.com/sharer/sharer.php?u=' . $uncLink . '"></a></li>';
		$twLink = '<li><a target="_blank" class="obj-share-tw soc-icon twitter-icon" href="https://twitter.com/intent/tweet?url=' . $uncLink . '"></a></li>';
		$lnLink = '<li><a target="_blank" class="obj-share-ln soc-icon ln-icon" href="https://www.linkedin.com/shareArticle?mini=true&url=' . $uncLink .'"></a></li>';
		$gpLink	= '<li><a target="_blank" class="obj-share-gplus soc-icon gplus-icon" href="https://plus.google.com/share?url=' . $uncLink . '"></a></li>';
	}
?>
<section class="favorites-bar<?php if($pos) { echo ' favorites-bar-bottom';} ?>">
	<div class="container">
		<div class="fav-link-wrap">
			<input type="text" class="text-input fav-link-input" value="<?= $link; ?>" readonly>
			<button type="button" class="btn btn-green fav-copy"><?php _e('Copy', 'leadingprops'); ?></button>
		</div>
		<div class="social-sharing favorites-sharing">
			<?php printf(
				$format,
				$emailLink,
				$fbLink,
				$twLink,
				$lnLink,
				$gpLink
			) ?>
		</div>
	</div>
</section><!-- /.favorites-bar -->