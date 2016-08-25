(function($){
	"use strict";
	window.lpw = window.lpw || {};
	window.lpw.ContactForm = ContactForm;

		function ContactForm() {
			var $this = this,
				timer = null,
				message = '';


			function isValidName(input) {
				var val = input.val();
				return (val.length > 1);
			}
			function isValidEmail(input) {
				var val = input.val().trim(),
					pattern = new RegExp(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i);
				return pattern.test(val) || val.length === 0;
			}
			function isValidSkype (input) {
				var val = input.val().trim();
				return (val.length > 2) || val.length === 0;
			}
			function isValidPhone(input) {
				return input.intlTelInput("isValidNumber") || input.val().trim().length === 0;
			}
			function isValidForm(el) {
				var type = el.data('validation'),
					valid = false;
				switch (type) {
					case 'name':
						valid = isValidName(el);
						break;
					case 'email':
						valid = isValidEmail(el);
						break;
					case 'phone':
						valid = isValidPhone(el);
						break;
					case 'skype':
						valid = isValidSkype(el);
						break;
				}
				if(!valid) {
					el.addClass('has-error');
				} else {
					el.removeClass('has-error');
				}
				if(valid && isValidName($this.fname) && isValidName($this.lname)
					&& isValidPhone($this.phone) && isValidEmail($this.email) && isValidSkype($this.skype)
					&&(($this.phone.val().trim().length > 0) || ($this.email.val().trim().length > 0)
					|| ($this.skype.val().trim().length > 0))) {
					$this.formValid = true;
					$this.submit.prop("disabled", false);
				} else {
					$this.formValid = false;
					$this.submit.prop("disabled", true);
				}
			}
			function resetForm() {
				if(timer) {
					clearTimeout(timer);
				}
				this.message.remove();
				this.form.show();
			}
			function showMessage(type, msg) {
				message = $('<div class="request-form-message"><p class="text-'+type+'">'+ msg +'</p></div>');
				$this.contactModal.find('.modal-body').append(message);
				    timer = setTimeout(function(){
					$this.contactModal.modal('hide');
				}, 5000);
			}

			function ajaxSendForm(ev) {
				ev.preventDefault();
				if(!$this.formValid) {
					return false;
				}

				var data = {
						first_name: $this.fname.val(),
						last_name: $this.lname.val(),
						phone: $this.phone.val(),
						email: $this.email.val(),
						skype: $this.skype.val(),
						question: $this.message.val(),
						form_type: $this.type,
						action: 'do_ajax',
						fn: 'contact_form'
					};
				if($this.type === 'single_property') {
					data.property_id = $this.property_id;
					data.property_code = $this.property_code;
					data.is_rent = $this.is_rent;
				}

				$this.submit.prop('disabled', true);

				$.ajax({
					url: LpData.ajaxUrl,
					dataType : 'json',
					method: 'post',
					data: data,
					success : function(data){
						if(data.success) {
							$this.form.hide();
							showMessage(data.type, data.message);
						}
					},
					error : function (error){
						console.error(error);
					},
					complete: function() {
						$this.form.trigger('reset');
					}
				});

			}

		function modalInit(ev) {
			var btn = $(ev.relatedTarget);
			$this.type = btn.data('type');
			$this.contactModal = $(this);
			$this.form = $this.contactModal.find('form');
			$this.submit = $this.contactModal.find('.btn-submit');
			$this.fname = $this.form.find('.first-name');
			$this.lname = $this.form.find('.last-name');
			$this.phone = $this.form.find('.your-phone');
			$this.email = $this.form.find('.your-email');
			$this.skype = $this.form.find('.your-skype');
			$this.message = $this.form.find('.your-message');

			if($this.type === 'single_property') {
				$this.is_rent = btn.data('object-type') === 'rent';
				$this.property_id = btn.data('id');
				$this.property_code = btn.data('code');
			}


			$this.phone.intlTelInput({
					initialCountry: "auto",
					preferredCountries: [],
					autoPlaceholder: false,
					separateDialCode: true,
					dropdownContainer: '.phone-group-wrap',
					nationalMode: true,
					geoIpLookup: function (callback) {
						$.get(window.location.protocol + '//ipinfo.io/?token=7e9a08789b534d', function () {
						}, "jsonp").always(
							function (resp) {
								var countryCode = (resp && resp.country) ? resp.country : "";
								callback(countryCode);
							}
						);

					}
				});
			$this.form.attr('novalidate', 'novalidate');
			$this.submit.prop("disabled", true);

			$this.form.on('input', 'input', function() {
				isValidForm($(this));
			});
			$this.form.on('submit', ajaxSendForm);

			$this.contactModal.on('hidden.bs.modal', resetForm);
		}

		this.init = function() {
			$('.request-form-modal').on('show.bs.modal', modalInit);
		}
	}


})(jQuery);