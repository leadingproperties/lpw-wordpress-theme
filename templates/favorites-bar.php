<?php global $pos; ?>
<section class="favorites-bar<?php if($pos) { echo ' favorites-bar-bottom';} ?>">
	<div class="container">
		<div class="fav-link-wrap">
			<input type="text" class="text-input fav-link-input" value="" readonly>
			<button type="button" class="btn btn-green fav-copy"><?php _e('Copy', 'leadingprops'); ?></button>
		</div>
		<div class="social-sharing favorites-sharing">
			<ul class="tooltip-sharing tooltip-sharing-top" data-toggle="tooltip" title="<?php _e('Share your favorite properties with friends or colleagues', 'leadingprops'); ?>">
				<li><a class="obj-share-email half-opaque soc-icon email-icon"></a></li>
				<li><a target="_blank" class="obj-share-fb half-opaque soc-icon fb-icon"></a></li>
				<li><a target="_blank" class="obj-share-tw half-opaque soc-icon twitter-icon"></a></li>
				<li><a target="_blank" class="obj-share-ln half-opaque soc-icon ln-icon"></a></li>
				<li><a target="_blank" class="obj-share-gplus half-opaque soc-icon gplus-icon"></a></li>
			</ul>
		</div>
	</div>
</section><!-- /.favorites-bar -->