<div class="modal request-form-modal offmarket-request" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-close modal-close" data-dismiss="modal" aria-label="Close"></button>
				<h4 class="modal-title"><?php _e('We need your contact details to provide you information about the Off-Market properties', 'leadindprops'); ?></h4>
			</div>
			<div class="modal-body">
				<form action="" class="request-form">
					<fieldset data-count="1">
						<div class="request-form-row">
							<div class="input-group">
								<label class="request-form-label" for="om-first-name"><?php _e('Enter your first name', 'leadingprops'); ?></label>
								<input id="om-first-name" type="text" class="first-name text-input" data-validation="name">
							</div>
							<div class="input-group">
								<label class="request-form-label" for="om-last-name"><?php _e('and your last name', 'leadingprops'); ?></label>
								<input id="om-last-name" type="text" class="last-name text-input" data-validation="name">
							</div>
						</div>
					</fieldset>
					<fieldset class="request-form" data-count="2">
						<div class="input-group request-phone-group">
							<label class="request-form-label" for="om-phone"><?php _e('How can we reach you?', 'leadingprops'); ?><span class="contact-icon"><i class="soc-icon icon-viber"></i><i class="soc-icon icon-whatsapp"></i><i class="soc-icon icon-telegram"></i></span></label>
							<div class="request-phone-wrap">
								<div class="phone-group-wrap">
									<div class="phone-number-wrap">
										<input id="om-phone" type="text" class="your-phone text-input" placeholder="<?php _e('your phone', 'leadingprops'); ?>" data-validation="phone">
									</div>
								</div>
							</div>
						</div>
						<div class="request-form-row">
							<div class="input-group soc-icon icon-mail">
								<label class="sr-only" for="om-email"><?php _e('Email', 'leadingprops'); ?></label>
								<input id="om-email" type="email" class="your-email text-input" placeholder="<?php _e('your e-mail', 'leadingprops'); ?>" data-validation="email">
							</div>
							<div class="input-group soc-icon icon-skype">
								<label class="sr-only" for="om-skype"><?php _e('Skype', 'leadingprops'); ?></label>
								<input id="om-skype" type="text" class="your-skype text-input" placeholder="<?php _e('your Skype name', 'leadingprops'); ?>" data-validation="skype">
							</div>
						</div>
					</fieldset>
					<fieldset data-count="3">
						<div class="input-group">
							<label class="request-form-label" for="om-question"><?php _e('Do you have some questions?', 'leadingprops'); ?></label>
							<textarea id="om-question" class="text-input" rows="4"></textarea>
						</div>
					</fieldset>
					<div class="request-form-footer request-form-row">
						<div class="input-group">
							<button type="submit" class="btn btn-green btn-submit"><?php _e('Send request', 'leadingprops'); ?></button>
						</div>
						<div class="disclaimer-note">
							<p><?php _e('I hereby agree and authorize LPW to disclose my personal information collected on this form to the property developers and / or sale agents who have signed a Marketing services agreement with LPW in respect to requested properties. Read more about our', 'leadingprops'); ?> <a href="https://www.leadingproperties.com/protection-policy-personal-information" target="_blank"><?php _e('Privacy policy', 'leadingprops'); ?></a>
							</p>
						</div>
					</div>
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->