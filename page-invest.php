<?php
/**
 *  Template Name: Invest
 */
?>

<?php
	get_template_part('templates/head');
	get_header();

	$subtypes = new LP_ObjectList([
		'action' => 'get_subtypes',
		'subtype_parent_id' => 3
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
				<p class="footnote"><?php _e('commercial:note_text', 'leadingprops') ?> <br><a href="#"><?php _e('commercial:note_link', 'leadingprops') ?></a></p>
			</div>
		</div>
	</div><!-- /.page-content -->

	<div class="comm-form-wrap">
		<div class="page-container">
			<form action="" class="request-form">
				<fieldset data-count="1">
					<div class="request-form-row">
						<div class="input-group">
							<label class="request-form-label" for="comm-first-name">Enter your first name</label>
							<input id="comm-first-name" type="text" class="text-input">
						</div>
						<div class="input-group">
							<label class="request-form-label" for="comm-last-name">and your last name</label>
							<input id="comm-last-name" type="text" class="text-input has-error">
						</div>
					</div>
				</fieldset>
				<fieldset class="request-form" data-count="2">
					<div class="request-form-row">
						<div class="input-group request-phone-group">
							<label class="request-form-label" for="comm-phone">How to contact you?<span class="contact-icon"><i class="soc-icon icon-viber"></i><i class="soc-icon icon-whatsapp"></i><i class="soc-icon icon-telegram"></i></span></label>
							<div class="request-phone-wrap">
								<div class="phone-group-wrap">
									<input id="comm-phone" type="text" class="text-input" placeholder="your phone">
								</div>
							</div>
						</div>

						<div class="input-group soc-icon icon-mail">
							<label class="sr-only" for="comm-email">Email</label>
							<input id="comm-email" type="email" class="text-input" placeholder="your e-mail">
						</div>
					</div>
				</fieldset>
				<fieldset data-count="3">
					<div class="request-form-row">
						<div class="input-group">
							<label class="request-form-label" for="comm-countries">In which country are you looking for properties?</label>
							<select name="countries" class="country-select" id="comm-countries">
								<option value="lorem ipsum">All countries</option>
								<option value="lorem ipsum">Lorem ipsum</option>
								<option value="lorem ipsum">Lorem ipsum</option>
								<option value="lorem ipsum">Lorem ipsum</option>
								<option value="lorem ipsum">Lorem ipsum</option>
								<option value="lorem ipsum">Lorem ipsum</option>
								<option value="lorem ipsum">Lorem ipsum</option>
							</select>
						</div>
						<div class="input-group input-group-checkbox">
							<input id="several-countries" type="checkbox">
							<label for="several-countries">I'm interested in several countries</label>
						</div>
					</div>
				</fieldset>

				<fieldset data-count="4">

					<label class="request-form-label">Your budget, EUR</label>
					<div class="request-checkbox-group">
						<div class="request-checkbox">
							<input id="to_one" type="checkbox">
							<label for="to_one">up to 1 min</label>
						</div>
						<div class="request-checkbox">
							<input id="one_five" type="checkbox">
							<label for="one_five">1&ndash;5 min</label>
						</div>
						<div class="request-checkbox">
							<input id="five_ten" type="checkbox">
							<label for="five_ten">5&ndash;10 min</label>
						</div>
						<div class="request-checkbox">
							<input id="ten_fifty" type="checkbox">
							<label for="ten_fifty">10&ndash;50 min</label>
						</div>
						<div class="request-checkbox">
							<input id="fifty_plus" type="checkbox">
							<label for="fifty_plus">50+ min</label>
						</div>
					</div>

				</fieldset>
				<fieldset data-count="5">
					<div class="input-group">
						<label class="request-form-label" for="comm-question">Any questions or comments?</label>
						<textarea id="comm-question" class="text-input" rows="4"></textarea>
					</div>
				</fieldset>
				<div class="request-form-footer request-form-row">
					<div class="input-group">
						<button type="submit" class="btn btn-green btn-submit">Send request</button>
					</div>
					<div class="disclaimer-note">
						<p>I hereby agree and authorize LPW to disclose my personal
							information collected on this form to the property developers
							and / or sale agents who have signed a Marketing services
							agreement with LPW in respect to requested properties. Read
							more about our <a href="#">Privacy policy</a></p>
					</div>
				</div>
			</form>
			<div class="request-form-message">
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

