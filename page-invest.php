<?php
/**
 *  Template Name: Invest
 */
?>

<?php
	get_template_part('templates/head');
	get_header();

	global $lp_settings;

	$subtypes = new LP_ObjectList([
		'action' => 'get_subtypes',
		'subtype_parent_id' => 3,
		'lang'  => $lp_settings['lang']
	]);
	$subtypesObj = $subtypes->get_objects_array();
	$totalCnt = ($subtypesObj->total) ? $subtypesObj->total : 0;
	$countersArray = $subtypesObj->counters;
	$typesOrder = [
		'hotel',
		'residential',
		'office_building',
		'retail',
		'shopping_centre',
		'cbr',
		'mixed',
		'other'
	];
	$counters = [];
	foreach($typesOrder as $name) {
		foreach($countersArray as $c) {
			if ($name == $c->name && $c->count > 0) {
				$counters[] = $c;
				break;
			}
		}
	}
	$countries = new LP_ObjectList([
		'action' => 'get_countries',
		'lang'  => $lp_settings['lang']
	]);
?>
	<div class="page-content page-comm-content">
		<div class="page-container">
			<header class="comm-header">
				<h2 class="invest-title"><?php _e('commercial:main_title', 'leadingprops') ?> <span id="total-counter" class="text-red"><?= $totalCnt ?></span></h2>
				<ul class="comm-tags">
					<?php foreach($counters as $counter) {
						echo '<li><span>' . $counter->title . ' <sup class="text-red">' . $counter->count . '</sup></span></li>';
					} ?>
				</ul>

			</header>
			<div class="comm-steps">
				<h3><?php _e('commercial:sub_title_1', 'leadingprops') ?><br> <?php _e('commercial:sub_title_2', 'leadingprops') ?></h3>
				<ul class="comm-step-list">
					<li>
						<span class="red-circle">1</span><?php _e('commercial:step_1', 'leadingprops') ?>
					</li>
					<li>
						<span class="red-circle">2</span><?php _e('commercial:step_2', 'leadingprops') ?>
					</li>
					<li>
						<span class="red-circle">3</span><?php _e('commercial:step_3', 'leadingprops') ?>
					</li>
				</ul>
				<p class="footnote"><?php _e('commercial:note_text', 'leadingprops') ?></p>
			</div>
		</div>
	</div><!-- /.page-content -->

	<div class="cf-wrap comm-form-wrap">
		<div class="page-container">
			<form action="" class="request-form" id="invest-form">
				<fieldset data-count="1">
					<div class="request-form-row">
						<div class="input-group">
							<label class="request-form-label" for="comm-first-name"><?php _e('form:first_name', 'leadingprops'); ?></label>
							<input id="comm-first-name" type="text" class="first-name text-input" data-validation="name">
						</div>
						<div class="input-group">
							<label class="request-form-label" for="comm-last-name"><?php _e('form:last_name', 'leadingprops'); ?></label>
							<input id="comm-last-name" type="text" class="last-name text-input" data-validation="name">
						</div>
					</div>
				</fieldset>
				<fieldset class="request-form" data-count="2">
					<div class="request-form-row">
						<div class="input-group request-phone-group">
							<label class="request-form-label" for="comm-phone"><?php _e('form:how_can_reach', 'leadingprops'); ?><span class="contact-icon"><i class="soc-icon icon-viber"></i><i class="soc-icon icon-whatsapp"></i><i class="soc-icon icon-telegram"></i></span></label>
							<div class="request-phone-wrap">
								<div class="phone-group-wrap">
									<input id="comm-phone" type="text" class="your-phone text-input" placeholder="<?php _e('form:your_phone', 'leadingprops'); ?>" data-validation="phone">
								</div>
							</div>
						</div>

						<div class="input-group soc-icon icon-mail">
							<label class="sr-only" for="comm-email">Email</label>
							<input id="comm-email" type="email" class="your-email text-input" placeholder="<?php _e('form:your_email', 'leadingprops'); ?>" data-validation="email">
						</div>
					</div>
				</fieldset>
				<fieldset data-count="3">
					<div class="request-form-row">
						<div class="input-group">
                            <?php $c = $countries->get_countries(); ?>
							<label class="request-form-label" for="comm-countries"><?php _e('form:country_label', 'leadingprops') ?></label>
							<select name="countries" class="country-select" id="comm-countries">
								<option value="All countries"><?php _e('form:country_default', 'leadingprops') ?></option>
								<?php
                                if($c && is_array($c)) {
	                                foreach ( $c as $country ) {
		                                echo '<option value="' . $country['long_name'] . '">' . $country['long_name'] . '</option>';
	                                }
                                }

								?>
							</select>
						</div>
						<div class="input-group input-group-checkbox">
							<input id="several-countries" type="checkbox">
							<label for="several-countries"><?php _e('form:country_checkbox_label', 'leadingprops') ?></label>
						</div>
					</div>
				</fieldset>

				<fieldset data-count="4">

					<label class="request-form-label"><?php _e('form:budget_label', 'leadingprops') ?></label>
					<div class="request-checkbox-group">
						<div class="request-checkbox">
							<input id="to_one" class="budget-checkbox" type="checkbox" value="up to 1 mln">
							<label for="to_one"><?php _e('form:budget_range_label_1', 'leadingprops') ?></label>
						</div>
						<div class="request-checkbox">
							<input id="one_five" class="budget-checkbox" type="checkbox" value="1 - 5 mln">
							<label for="one_five"><?php _e('form:budget_range_label_2', 'leadingprops') ?></label>
						</div>
						<div class="request-checkbox">
							<input id="five_ten" class="budget-checkbox" type="checkbox" value="5 - 10 mln">
							<label for="five_ten"><?php _e('form:budget_range_label_3', 'leadingprops') ?></label>
						</div>
						<div class="request-checkbox">
							<input id="ten_fifty" class="budget-checkbox" type="checkbox" value="10 - 50 mln">
							<label for="ten_fifty"><?php _e('form:budget_range_label_4', 'leadingprops') ?></label>
						</div>
						<div class="request-checkbox">
							<input id="fifty_plus" class="budget-checkbox" type="checkbox" value="50+ mln">
							<label for="fifty_plus"><?php _e('form:budget_range_label_5', 'leadingprops') ?></label>
						</div>
					</div>

				</fieldset>
				<fieldset data-count="5">
					<div class="input-group">
						<label class="request-form-label" for="comm-question"><?php _e('form:have_questions', 'leadingprops'); ?></label>
						<textarea id="comm-question" class="your-message text-input" rows="4"></textarea>
					</div>
				</fieldset>
				<div class="request-form-footer request-form-row">
					<div class="input-group">
						<button type="submit" class="btn btn-green btn-submit"><?php _e('form:send', 'leadingprops'); ?></button>
					</div>
					<div class="disclaimer-note">
						<p><?php
							printf(
								__('I hereby agree and authorize %s to disclose my personal information collected on this form to the property developers and / or sale agents who have signed a Marketing services agreement with %s in respect to requested properties. Read more about our', 'leadingprops'),
									$lp_settings['site_title'],
									$lp_settings['site_title']
							);
							?> <a href="https://www.leadingproperties.com/protection-policy-personal-information" target="_blank"><?php _e('form:privacy_policy', 'leadingprops'); ?></a></p>
					</div>
				</div>
			</form>
			<div class="invest-form-message">
				<a href="#" class="btn-close"><span class="sr-only">Close</span></a>
				<p>Thank you, request was received.</p>
			</div>
		</div>
	</div><!-- /.comm-form-wrap -->

<div class="map-wrap">
	<div class="btn-map-wrap container">
		<button class="btn icon btn-map-open" data-action="open">
			<span class="map-closed-text"><?php _e('search_panel:toggle_map_y', 'leadingprops') ?></span>
			<span class="map-opened-text"><?php _e('search_panel:toggle_map_n', 'leadingprops') ?></span>
		</button>
	</div>
	<div id="invest-map"></div>
</div>

<?php

	get_footer();

