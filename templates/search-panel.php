<?php global $lp_settings; ?>
<section class="search-panel-wrap">
	<div class="container sp-container">
		<div class="sp-field sp-search-field">
			<label for="sp-search" class="sr-only"><?php _e('Search', 'leadingprops'); ?></label>
			<div class="sp-input-wrap">
				<input id="sp-search" type="text" class="sp-input text-input dropdown-trigger" placeholder="<?php _e('Address or reference number', 'leadingprops'); ?>">
			</div>
			<a href="#" class="filter-toggle tooltip-type-search icon" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Advanced Search', 'leadingprops'); ?>"><span class="sr-only">Filter</span></a>
			<?php if( is_page_template('page-buy.php') ) {
				get_template_part('templates/filter', 'sale');
			} elseif( is_page_template('page-rent.php') ) {
				get_template_part('templates/filter', 'rent');
			} ?>
		</div><!-- /.sp-search-field-->
		<nav class="sp-menu">
			<ul>
				<li class="regions-menu tooltip-type-1" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Show<br> Map', 'leadingprops'); ?>"><a class="menu-link" data-toggle="modal" data-target="#map-modal"></a></li>
				<li class="favorites-menu tooltip-type-1" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Favorite Properties', 'leadingprops'); ?>"><a class="menu-link" href="<?php if( is_page_template('page-buy.php' ) ) { echo $lp_settings['favorites']; } else { echo $lp_settings['favorites_rent']; } ?>"><sup class="text-red"></sup></a></li>
				<li class="off-market-menu tooltip-type-1" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Off-Market Properties', 'leadingprops'); ?>"><a class="menu-link half-opaque"><sup class="text-red"></sup></a></li>
			</ul>
		</nav><!-- /.favorites-menu -->
		<div class="off-marker-alert">
			<div class="alert alert-danger">
				<a href="#" class="alert-close btn-close" data-target=".off-marker-alert"></a>
				<p><?php _e('We have found Off-Market properties that may fit your search criteria', 'leadingprops'); ?>. <a href="#" data-toggle="modal" data-target=".offmarket-request" data-type="off_market"><?php _e('Request information', 'leadingprops'); ?></a></p>
			</div>
		</div>
	</div><!-- /.container -->
</section><!-- /.search-panel -->