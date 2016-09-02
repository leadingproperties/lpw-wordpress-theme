<div class="sp-filters sp-filters-rent">
	<div class="sp-filter-wrap">
		<form action="" id="filter-form">
			<div class="sp-filters-group filter-rent-type">
				<div class="sp-checkbox">
					<input id="short-term" type="checkbox" name="rent-type">
					<label for="short-term"><?php _e('Short term rental', 'leadingprops'); ?></label>
				</div>
				<div class="sp-checkbox">
					<input id="long-term" type="checkbox" name="rent-type">
					<label for="long-term"><?php _e('Long term rental', 'leadingprops'); ?></label>
				</div>
			</div>
			<div class="sp-filters-group filter-type">
				<span class="sp-group-title"><?php _e('Property type', 'leadingprops'); ?></span>
				<div class="sp-checkbox">
					<input id="apartments" class="property_type" type="checkbox" value="1" name="type">
					<label for="apartments"><?php _e('Apartments', 'leadingprops'); ?></label>
				</div>
				<div class="sp-checkbox">
					<input id="houses" class="property_type" type="checkbox" value="2" name="type">
					<label for="houses"><?php _e('Houses', 'leadingprops'); ?></label>
				</div>
				<div class="sp-checkbox">
					<input id="commercial" class="property_type" type="checkbox" value="3" name="type">
					<label for="commercial"><?php _e('Commercial Properties', 'leadingprops'); ?></label>
				</div>
			</div>
			<div class="sp-760-group sp-760-middle">
				<div class="sp-filters-group filter-rooms">
					<div class="sp-checkboxes">
						<span class="sp-group-title"><?php _e('Rooms', 'leadingprops'); ?></span>
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
						<span class="sp-group-title"><?php _e('area, m', 'leadingprops'); ?><sup>2</sup></span>
						<label for="area-min" class="sr-only">Area min</label>
						<input type="number" min="0" class="filter-input area-input" id="area-min" placeholder="<?php _e('min', 'leadingprops'); ?>">
						&nbsp;&ndash;&nbsp;
						<label for="area-max" class="sr-only">Area max</label>
						<input type="number" min="0" class="filter-input area-input" id="area-max" placeholder="<?php _e('max', 'leadingprops'); ?>">
					</div>
				</div>
			</div>
			<div class="sp-760-group sp-760-middle">
				<div class="sp-filters-group filter-persons">
					<span class="sp-group-title"><?php _e('Persons', 'leadingprops'); ?></span>
					<label for="persons-max" class="sr-only">Persons max</label>
					<input type="number" min="0" class="filter-input area-input" id="persons-max" placeholder="<?php _e('max', 'leadingprops'); ?>">
				</div>
				<div class="sp-filters-group filter-conditions">
					<div class="sp-checkbox">
						<input id="child-friendly" type="checkbox" name="condition">
						<label for="child-friendly"><span class="icon icon-child"><?php _e('child friendly', 'leadingprops'); ?></span></label>
					</div>
					<div class="sp-checkbox">
						<input id="pets-allowed" type="checkbox" name="condition">
						<label for="pets-allowed"><span class="icon icon-pet"><?php _e('pets allowed', 'leadingprops'); ?></span></label>
					</div>
				</div>
			</div>
			<div class="sp-filters-group filter-price">
				<div class="filter-price-group">
					<span class="sp-group-title"><?php _e('Price', 'leadingprops'); ?></span>
					<label for="price-min" class="sr-only">Price min</label>
					<input type="number" min="0" class="filter-input price-input" id="price-min" placeholder="<?php _e('min', 'leadingprops'); ?>">
					&nbsp;&ndash;&nbsp;
					<label for="price-max" class="sr-only">Price max</label>
					<input type="number" min="0" class="filter-input price-input" id="price-max" placeholder="<?php _e('max', 'leadingprops'); ?>">
					<label for="price-currency" class="sr-only">Currency</label>
					<div class="rent-select-wrap">
						<select name="price[currency]" id="price-currency" class="price-input price-select">
							<option value="1">EUR</option>
							<option value="4">USD</option>
							<option value="5">GBR</option>
							<option value="2">CHF</option>
							<option value="3">CZK</option>
							<option value="7">AED</option>
							<option value="8">THB</option>
						</select>
						&nbsp;&frasl;&nbsp;
						<select name="period" id="price-period" class="price-input">
							<option value="day"><?php _e('day', 'leadingprops'); ?></option>
							<option value="month"><?php _e('month', 'leadingprops'); ?></option>
						</select>
					</div>
				</div>
			</div>

			<div class="sp-760-group">
				<div class="sp-filters-group filter-quality">
					<div class="sp-checkbox">
						<input id="quality" type="checkbox" name="type">
						<label for="quality"><?php _e('Listings with high quality photos only', 'leadingprops'); ?></label>
					</div>
				</div>
				<div class="filter-submit">
					<button type="submit" class="btn btn-green btn-filter-submit"><?php _e('Show results', 'leadingprops'); ?></button>
				</div>
			</div>
		</form>
	</div>
	<a href="#" class="btn-close filter-close"><span class="sr-only">Close</span></a>
</div><!-- /.sp-filters -->
