<?php global $lp_settings; ?>
<!-- Request single object -->
<div class="modal request-form-modal single-object-request" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-close modal-close" data-dismiss="modal" aria-label="Close"></button>
				<h4 class="modal-title"><?php _e('form:title_single','leadingprops'); ?></h4>
			</div>
			<div class="modal-body">
				<form action="" class="request-form">
					<fieldset data-count="1">
						<div class="request-form-row">
							<div class="input-group">
								<label class="request-form-label" for="rf-first-name"><?php _e('form:first_name', 'leadingprops'); ?></label>
								<input id="rf-first-name" type="text" class="first-name text-input" data-validation="name">
							</div>
							<div class="input-group">
								<label class="request-form-label" for="rf-last-name"><?php _e('form:last_name', 'leadingprops'); ?></label>
								<input id="rf-last-name" type="text" class="last-name text-input" data-validation="name">
							</div>
						</div>
					</fieldset>
					<fieldset class="request-form" data-count="2">
						<div class="input-group request-phone-group">
							<label class="request-form-label" for="rf-phone"><?php _e('form:how_can_reach', 'leadingprops'); ?><span class="contact-icon"><i class="soc-icon icon-viber"></i><i class="soc-icon icon-whatsapp"></i><i class="soc-icon icon-telegram"></i></span></label>
							<div class="request-phone-wrap">
								<div class="phone-group-wrap">
									<input id="rf-phone" type="text" class="your-phone text-input" placeholder="<?php _e('form:your_phone', 'leadingprops'); ?>" data-validation="phone">
								</div>
							</div>
						</div>
						<div class="request-form-row">
							<div class="input-group soc-icon icon-mail">
								<label class="sr-only" for="rf-email">Email</label>
								<input id="rf-email" type="email" class="your-email text-input" placeholder="<?php _e('form:your_email', 'leadingprops'); ?>" data-validation="email">
							</div>
							<div class="input-group soc-icon icon-skype">
								<label class="sr-only" for="rf-skype">Skype</label>
								<input id="rf-skype" type="text" class="your-skype text-input" placeholder="<?php _e('form:your_skype', 'leadingprops'); ?>" data-validation="skype">
							</div>
						</div>
					</fieldset>
					<fieldset data-count="3">
						<div class="input-group">
							<label class="request-form-label" for="rf-question"><?php _e('form:have_questions', 'leadingprops'); ?></label>
							<textarea id="rf-question" class="your-message text-input" rows="4"></textarea>
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
								?> <a href="https://www.leadingproperties.com/protection-policy-personal-information" target="_blank"><?php _e('form:privacy_policy', 'leadingprops'); ?></a>
							</p>
						</div>
					</div>
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
