(function($){
	"use strict";
	window.lpw = window.lpw || {};
	window.lpw.ContactForm = ContactForm;

		function ContactForm(invest) {
			var $this = this,
				timer = null;

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
					valid = false,
					skypeVal = invest ? '' : $this.skype.val(),
					validSkype = invest ? true : isValidSkype($this.skype);
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
					&& isValidPhone($this.phone) && isValidEmail($this.email) && validSkype
					&&(($this.phone.val().trim().length > 0) || ($this.email.val().trim().length > 0)
					|| (skypeVal.trim().length > 0))) {
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
				$('.request-form-message').remove();

				$this.form.show();
				$this.form.off('submit', $this.ajaxSendForm);
			}
			function showMessage(type, msg) {
				if($this.type === 'commercial') {
					if(type === 'green') {
						$('.invest-form-message')
							.removeClass('bg-red')
							.addClass('bg-green')
							.show()
							.find('p').text(msg);
					} else {
						$('.invest-form-message')
							.removeClass('bg-green')
							.addClass('bg-red')
							.show()
							.find('p').text(msg);
					}

				} else {
					$this.contactModal.find('.modal-body').append('<div class="request-form-message"><p class="text-' + type + '">' + msg + '</p></div>');
					timer = setTimeout(function () {
						$this.contactModal.modal('hide');
					}, 5000);
				}
			}

			this.ajaxSendForm = function(ev) {
				ev.preventDefault();
				if(!$this.formValid) {
					return false;
				}

				var data = {
						first_name: $this.fname.val(),
						last_name: $this.lname.val(),
						email: $this.email.val(),
						skype: $this.skype.val(),
						question: $this.message.val(),
						form_type: $this.type,
						locale: LpData.lang,
						action: 'do_ajax',
						fn: 'contact_form'
					};
				if($this.phone.val().trim().length > 0) {
					data.phone = $this.phone.intlTelInput("getNumber");
				}
				if($this.type === 'single_property') {
					data.property_id = $this.property_id;
					data.property_code = $this.property_code;
					data.is_rent = $this.is_rent;
				}
				if($this.type === 'off_market') {
					data.url = $this.url;
				}
				if($this.type === 'commercial') {
					data.country = $this.countries.val();
					if($this.several.is(':checked')) {
						data.several_countries = true;
					}

					var budget = $this.budget.filter(':checked').map(function() {
						return this.value;
					}).get();
					if(budget) {
						data.budget = budget.join(', ');
					}

				}

				$this.submit.prop('disabled', true);

				$.ajax({
					url: LpData.ajaxUrl,
					dataType : 'json',
					method: 'post',
					data: data,
					success : function(data){
						if(data.success) {
							if($this.type !== 'commercial' ) {
								$this.form.hide();
							}
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

			};

			this.investFormInit = function() {
				var formMsg = $('.invest-form-message');
				$this.type = 'commercial';
				$this.form = $('#invest-form');
				$this.formInit($this.form);
				formMsg.on('click.lprop', '.btn-close', function(ev) {
					ev.preventDefault();
					formMsg.hide();
				});
			};

			this.formInit = function(form) {
				$this.submit = form.find('.btn-submit');
				$this.fname = form.find('.first-name');
				$this.lname = form.find('.last-name');
				$this.phone = form.find('.your-phone');
				$this.email = form.find('.your-email');
				$this.skype = form.find('.your-skype');
				$this.message = form.find('.your-message');

				if($this.type === 'commercial') {
					$this.countries = $('.country-select');
					$this.countries.select2({
						minimumResultsForSearch: Infinity,
						containerCssClass : "country-select",
						dropdownCssClass: "country-dropdown",
						width: "100%"
					});
					$this.several = $('#several-countries');
					$this.budget = $('.budget-checkbox');
				}
				var intContainer = form.find('.phone-group-wrap');


				$this.phone.intlTelInput({
					initialCountry: "auto",
					preferredCountries: [],
					autoPlaceholder: false,
					separateDialCode: true,
					dropdownContainer: intContainer,
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
				$this.form.on('submit', $this.ajaxSendForm);
			};

			if(invest === true) {
				this.investFormInit();
			}

		this.modalInit = function(ev) {

			var btn = $(ev.relatedTarget);
			$this.type = btn.data('type');
			$this.contactModal = $(this);
			$this.form = $this.contactModal.find('form');
			$this.formInit($this.form);
			if($this.type === 'single_property') {
				$this.is_rent = btn.data('object-type') === 'rent';
				$this.property_id = btn.data('id');
				$this.property_code = btn.data('code');
			}
			if($this.type === 'off_market') {
				$this.url = window.location.href;
			}
		};

		this.init = function() {
			$('.request-form-modal').on('shown.bs.modal', $this.modalInit)
				.on('hidden.bs.modal', resetForm);
		};
	}


})(jQuery);