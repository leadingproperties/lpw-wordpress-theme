<?php global $lp_settings; ?>
<section class="search-panel-wrap">
	<div class="container sp-container">
		<div class="sp-field sp-search-field">
			<label for="sp-search" class="sr-only">Search</label>
			<div class="sp-input-wrap">
				<input id="sp-search" type="text" class="sp-input text-input dropdown-trigger" placeholder="<?php _e('search_panel:autocomplete', 'leadingprops'); ?>">
			</div>
			<a href="#" class="filter-toggle tooltip-type-search icon" data-toggle="tooltip" data-placement="bottom" title="<?php _e('alerts:advanced_search', 'leadingprops'); ?>"><span class="sr-only">Filter</span></a>
			<?php if( is_page_template('page-buy.php') || is_page_template('page-location-buy.php') ) {
				get_template_part('templates/filter', 'sale');
			} elseif( is_page_template('page-rent.php') || is_page_template('page-location-rent.php')) {
				get_template_part('templates/filter', 'rent');
			} ?>
		</div><!-- /.sp-search-field-->
		<nav class="sp-menu">
			<ul>
				<li class="regions-menu tooltip-type-1" data-toggle="tooltip" data-placement="bottom" title="<?php _e('alerts:show_map_tooltip', 'leadingprops'); ?>"><a class="menu-link" data-toggle="modal" data-target="#map-modal"></a></li>
				<li class="favorites-menu tooltip-type-1" data-toggle="tooltip" data-placement="bottom" title="<?php _e('alerts:favorite_properties', 'leadingprops'); ?>"><a class="menu-link" href="<?php if( is_page_template('page-buy.php' ) || is_page_template('page-location-buy.php' ) ) { echo $lp_settings['favorites']; } else { echo $lp_settings['favorites_rent']; } ?>"><sup class="text-red"></sup></a></li>
				<li class="off-market-menu tooltip-type-1" data-toggle="tooltip" data-placement="bottom" title="<?php _e('alerts:offmarket_properties', 'leadingprops'); ?>"><a class="menu-link half-opaque"><sup class="text-red"></sup></a></li>
			</ul>
		</nav><!-- /.favorites-menu -->
		<div class="off-marker-alert">
			<div class="alert alert-danger">
				<a href="#" class="alert-close btn-close" data-target=".off-marker-alert"></a>
				<p><?php _e('alerts:offmarket_found:text', 'leadingprops'); ?>. <a href="#" data-toggle="modal" data-target=".offmarket-request" data-type="off_market"><?php _e('alerts:offmarket_found:link', 'leadingprops'); ?></a></p>
			</div>
		</div>
	</div><!-- /.container -->
    <?php if( is_page_template('page-rent.php') || is_page_template('page-location-rent.php') ) { ?>
        <div class="term-selector-wrap">
            <button id="selector-rent-long" class="btn btn-term-selector" data-rent="long">
                <span><?php _e('search_panel:long_rent', 'leadingprops'); ?> <sup><?= $lp_settings['counters']['long_rent']; ?></sup></span>
            </button>
            <button id="selector-rent-short" class="btn btn-term-selector" data-rent="short">
                <span><?php _e('search_panel:short_rent', 'leadingprops'); ?> <sup><?= $lp_settings['counters']['short_rent']; ?></sup></span>
            </button>
        </div><!-- /.term-selector-wrap -->
    <?php } ?>
</section><!-- /.search-panel -->
