<?php  global $lp_settings; ?>
<div class="sp-filters sp-filters-buy">
	<div class="sp-filter-wrap">
		<form action="" id="filter-form">
			<div class="sp-filters-group filter-type">
				<span class="sp-group-title"><?php _e('search_panel:property_types:label', 'leadingprops'); ?></span>
				<div class="sp-checkbox">
					<input id="apartments" class="property_type" type="checkbox" name="property_types[]" value="1">
					<label for="apartments"><?php _e('search_panel:property_types:apartments', 'leadingprops'); ?></label>
				</div>
				<div class="sp-checkbox">
					<input id="houses" class="property_type" type="checkbox" name="property_types[]" value="2">
					<label for="houses"><?php _e('search_panel:property_types:houses', 'leadingprops'); ?></label>
				</div>
			</div>
			<div class="sp-760-group sp-760-middle">
				<div class="sp-filters-group filter-price">
					<div class="filter-price-group">
						<span class="sp-group-title"><?php _e('search_panel:price_label', 'leadingprops'); ?></span>
						<label for="price-min" class="sr-only">Price min</label>
						<input type="number" min="0" class="filter-input price-input" id="price-min" name="price[min]" placeholder="<?php _e('search_panel:min', 'leadingprops'); ?>">
						&nbsp;&ndash;&nbsp;
						<label for="price-max" class="sr-only">Price max</label>
						<input type="number" min="0" class="filter-input price-input" id="price-max" name="price[max]" placeholder="<?php _e('search_panel:max', 'leadingprops'); ?>">
						<label for="price-currency" class="sr-only">Currency</label>
						<div class="price-select-wrap">
							<select name="price[currency]" id="price-currency" class="price-input price-select">
                                <option value="1"<?php if($lp_settings['currency_id'] === 1) echo ' selected'; ?>>EUR</option>
                                <option value="4"<?php if($lp_settings['currency_id'] === 4) echo ' selected'; ?>>USD</option>
                                <option value="5"<?php if($lp_settings['currency_id'] === 5) echo ' selected'; ?>>GBR</option>
                                <option value="2"<?php if($lp_settings['currency_id'] === 2) echo ' selected'; ?>>CHF</option>
                                <option value="3"<?php if($lp_settings['currency_id'] === 3) echo ' selected'; ?>>CZK</option>
                                <option value="7"<?php if($lp_settings['currency_id'] === 7) echo ' selected'; ?>>AED</option>
                                <option value="8"<?php if($lp_settings['currency_id'] === 8) echo ' selected'; ?>>THB</option>
							</select>
						</div>
					</div>
				</div>
				<div class="sp-filters-group filter-area">
					<div class="filter-area-group">
						<span class="sp-group-title"><?php _e('search_panel:area_label', 'leadingprops'); ?>, <?php _e('search_panel:sqm', 'leadingprops'); ?></span>
						<label for="area-min" class="sr-only">Area min</label>
						<input type="number" min="0" class="filter-input area-input" id="area-min" placeholder="<?php _e('search_panel:min', 'leadingprops'); ?>" name="area[min]">
						&nbsp;&ndash;&nbsp;
						<label for="area-max" class="sr-only">Area max</label>
						<input type="number" min="0" class="filter-input area-input" id="area-max" placeholder="<?php _e('search_panel:max', 'leadingprops'); ?>" name="area[max]">
					</div>
				</div>
			</div>
			<div class="sp-filters-group filter-rooms">
				<div class="sp-checkboxes">
					<span class="sp-group-title"><?php _e('search_panel:rooms_label', 'leadingprops'); ?></span>
					<div class="sp-checkbox">
						<input id="one" class="filter-room" type="checkbox" name="rooms[]" value="1">
						<label for="one">1</label>
					</div>
					<div class="sp-checkbox">
						<input id="two" class="filter-room" type="checkbox" name="rooms[]" value="2">
						<label for="two">2</label>
					</div>
					<div class="sp-checkbox">
						<input id="three" class="filter-room" type="checkbox" name="rooms[]" value="3">
						<label for="three">3</label>
					</div>
					<div class="sp-checkbox">
						<input id="four" class="filter-room" type="checkbox" name="rooms[]" value="4">
						<label for="four">4</label>
					</div>
					<div class="sp-checkbox">
						<input id="five" class="filter-room" type="checkbox" name="rooms[]" value="5">
						<label for="five">5+</label>
					</div>
				</div>
			</div>
			<div class="sp-760-group">
				<div class="sp-filters-group filter-quality">
					<div class="sp-checkbox">
						<input id="quality" type="checkbox" name="hd_photos">
						<label for="quality"><?php _e('search_panel:hd_photos:label', 'leadingprops'); ?></label>
					</div>
				</div>
				<div class="filter-submit">
					<button type="submit" class="btn btn-green btn-filter-submit"><?php _e('search_panel:submit_button', 'leadingprops'); ?></button>
				</div>
			</div>
		</form>
	</div>
	<a href="#" class="btn-close filter-close"><span class="sr-only">Close</span></a>
</div><!-- /.sp-filters -->
