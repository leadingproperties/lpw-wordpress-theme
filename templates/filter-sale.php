<div class="sp-filters sp-filters-buy">
	<div class="sp-filter-wrap">
		<form action="" id="filter-form">
			<div class="sp-filters-group filter-type">
				<span class="sp-group-title"><?php _e('Property type', 'leadingprops'); ?></span>
				<div class="sp-checkbox">
					<input id="apartments" class="property_type" type="checkbox" name="property_types[]" value="1">
					<label for="apartments"><?php _e('Apartments', 'leadingprops'); ?></label>
				</div>
				<div class="sp-checkbox">
					<input id="houses" class="property_type" type="checkbox" name="property_types[]" value="2">
					<label for="houses"><?php _e('Houses', 'leadingprops'); ?></label>
				</div>
				<div class="sp-checkbox">
					<input id="commercial" class="property_type" type="checkbox" name="property_types[]" value="3">
					<label for="commercial"><?php _e('Commercial Properties', 'leadingprops'); ?></label>
				</div>
				<div class="sp-checkbox">
					<input id="plots" class="property_type" type="checkbox" name="property_types[]" value="4">
					<label for="plots"><?php _e('Plots', 'leadingprops'); ?></label>
				</div>
			</div>
			<div class="sp-760-group sp-760-middle">
				<div class="sp-filters-group filter-price">
					<div class="filter-price-group">
						<span class="sp-group-title"><?php _e('Price', 'leadingprops'); ?></span>
						<label for="price-min" class="sr-only"><?php _e('Price min', 'leadingprops'); ?></label>
						<input type="text" class="filter-input price-input" id="price-min" name="price[min]" placeholder="min">
						&nbsp;&ndash;&nbsp;
						<label for="price-max" class="sr-only"><?php _e('Price max'); ?></label>
						<input type="text" class="filter-input price-input" id="price-max" name="price[max]" placeholder="max">
						<label for="price-currency" class="sr-only"><?php _e('Currency', 'leadingprops'); ?></label>
						<div class="price-select-wrap">
							<select name="price[currency]" id="price-currency" class="price-input price-select">
								<option value="1">EUR</option>
								<option value="4">USD</option>
								<option value="5">GBR</option>
								<option value="2">CHF</option>
								<option value="3">CZK</option>
								<option value="7">AED</option>
								<option value="8">THB</option>
							</select>
						</div>
					</div>
				</div>
				<div class="sp-filters-group filter-area">
					<div class="filter-area-group">
						<span class="sp-group-title"><?php _e('Area, m<sup>2</sup>', 'leadingprops'); ?></span>
						<label for="area-min" class="sr-only"><?php _e('Area min', 'leadingprops'); ?></label>
						<input type="number" class="filter-input area-input" id="area-min" placeholder="min" name="area[min]">
						&nbsp;&ndash;&nbsp;
						<label for="area-max" class="sr-only"><?php _e('Area max', 'leadingprops'); ?></label>
						<input type="number" class="filter-input area-input" id="area-max" placeholder="max" name="area[max]">
					</div>
				</div>
			</div>
			<div class="sp-filters-group filter-rooms">
				<div class="sp-checkboxes">
					<span class="sp-group-title"><?php _e('Rooms', 'leadingprops'); ?></span>
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
						<label for="quality"><?php _e('Listings with high quality photos only', 'leadingprops'); ?></label>
					</div>
				</div>
				<div class="filter-submit">
					<button type="submit" class="btn btn-green btn-filter-submit"><?php _e('Show results', 'leadingprops'); ?></button>
				</div>
			</div>
		</form>
	</div>
	<a href="#" class="btn-close filter-close"><span class="sr-only"><?php _e('Close', 'leadingprops'); ?></span></a>
</div><!-- /.sp-filters -->