<?php global $lp_settings; ?>
<div class="sp-filters sp-filters-rent">
	<div class="sp-filter-wrap">
		<form action="" id="filter-form">
			<div class="sp-filters-group filter-type">
				<span class="sp-group-title"><?php _e('search_panel:property_types:label', 'leadingprops'); ?></span>
				<div class="sp-checkbox">
					<input id="apartments" class="property_type" type="checkbox" value="1" name="type">
					<label for="apartments"><?php _e('search_panel:property_types:apartments', 'leadingprops'); ?></label>
				</div>
				<div class="sp-checkbox">
					<input id="houses" class="property_type" type="checkbox" value="2" name="type">
					<label for="houses"><?php _e('search_panel:property_types:houses', 'leadingprops'); ?></label>
				</div>
			</div>
			<div class="sp-760-group sp-760-middle">
				<div class="sp-filters-group filter-rooms">
					<div class="sp-checkboxes">
						<span class="sp-group-title"><?php _e('search_panel:rooms_label', 'leadingprops'); ?></span>
						<div class="sp-checkbox">
							<input id="one" class="filter-room" type="checkbox" name="rooms" value="1">
							<label for="one">1</label>
						</div>
						<div class="sp-checkbox">
							<input id="two" class="filter-room" type="checkbox" name="rooms" value="2">
							<label for="two">2</label>
						</div>
						<div class="sp-checkbox">
							<input id="three" class="filter-room" type="checkbox" name="rooms" value="3">
							<label for="three">3</label>
						</div>
						<div class="sp-checkbox">
							<input id="four" class="filter-room" type="checkbox" name="rooms" value="4">
							<label for="four">4</label>
						</div>
						<div class="sp-checkbox">
							<input id="five" class="filter-room" type="checkbox" name="rooms" value="5">
							<label for="five">5+</label>
						</div>
					</div>
				</div>
				<div class="sp-filters-group filter-area">
					<div class="filter-area-group">
						<span class="sp-group-title"><?php _e('search_panel:area_label', 'leadingprops'); ?>, <?php _e('search_panel:sqm', 'leadingprops'); ?></span>
						<label for="area-min" class="sr-only">Area min</label>
						<input type="number" min="0" class="filter-input area-input" id="area-min" placeholder="<?php _e('search_panel:min', 'leadingprops'); ?>">
						&nbsp;&ndash;&nbsp;
						<label for="area-max" class="sr-only">Area max</label>
						<input type="number" min="0" class="filter-input area-input" id="area-max" placeholder="<?php _e('search_panel:max', 'leadingprops'); ?>">
					</div>
				</div>
			</div>
			<div class="sp-760-group sp-760-middle">
				<div class="sp-filters-group filter-persons">
					<span class="sp-group-title"><?php _e('search_panel:persons', 'leadingprops'); ?></span>
					<label for="persons-max" class="sr-only">Persons max</label>
					<input type="number" min="0" class="filter-input area-input" id="persons-max">
				</div>
				<div class="sp-filters-group filter-conditions">
					<div class="sp-checkbox">
						<input id="child-friendly" type="checkbox" name="condition">
						<label for="child-friendly"><span class="icon icon-child"><?php _e('search_panel:child_friendly', 'leadingprops'); ?></span></label>
					</div>
					<div class="sp-checkbox">
						<input id="pets-allowed" type="checkbox" name="condition">
						<label for="pets-allowed"><span class="icon icon-pet"><?php _e('search_panel:pets_allowed', 'leadingprops'); ?></span></label>
					</div>
				</div>
			</div>
			<div class="sp-filters-group filter-price">
				<div class="filter-price-group">
					<span class="sp-group-title"><?php _e('search_panel:price_label', 'leadingprops'); ?></span>
					<label for="price-min" class="sr-only">Price min</label>
					<input type="number" min="0" class="filter-input price-input" id="price-min" placeholder="<?php _e('search_panel:min', 'leadingprops'); ?>">
					&nbsp;&ndash;&nbsp;
					<label for="price-max" class="sr-only">Price max</label>
					<input type="number" min="0" class="filter-input price-input" id="price-max" placeholder="<?php _e('search_panel:max', 'leadingprops'); ?>">
					<label for="price-currency" class="sr-only">Currency</label>
					<div class="rent-select-wrap">
						<select name="price[currency]" id="price-currency" class="price-input price-select">
                            <option value="1"<?php if($lp_settings['currency_id'] === 1) echo ' selected'; ?>>EUR</option>
                            <option value="4"<?php if($lp_settings['currency_id'] === 4) echo ' selected'; ?>>USD</option>
                            <option value="5"<?php if($lp_settings['currency_id'] === 5) echo ' selected'; ?>>GBR</option>
                            <option value="2"<?php if($lp_settings['currency_id'] === 2) echo ' selected'; ?>>CHF</option>
                            <option value="3"<?php if($lp_settings['currency_id'] === 3) echo ' selected'; ?>>CZK</option>
                            <option value="7"<?php if($lp_settings['currency_id'] === 7) echo ' selected'; ?>>AED</option>
                            <option value="8"<?php if($lp_settings['currency_id'] === 8) echo ' selected'; ?>>THB</option>
						</select>
						&nbsp;&frasl;&nbsp;
						<select name="period" id="price-period" class="price-input">
                            <?php $rentType = getParametersByName('filter');
                            if ($rentType && isset($rentType['short_rent']) && !empty($rentType['short_rent'])) { ?>
                                <option value="day"><?php _e('s_object:rent:day', 'leadingprops'); ?></option>
                                <option value="week"><?php _e('s_object:rent:week', 'leadingprops'); ?></option>
                            <?php } ?>
							<option value="month"><?php _e('s_object:rent:month', 'leadingprops'); ?></option>
						</select>
					</div>
				</div>
			</div>

			<div class="sp-760-group">
				<div class="sp-filters-group filter-quality">
					<div class="sp-checkbox">
						<input id="quality" type="checkbox" name="type">
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
