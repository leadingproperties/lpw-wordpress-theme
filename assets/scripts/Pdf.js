(function($) {
	"use strict";
	window.lpw = window.lpw || {};
	window.lpw.Pdf = Pdf;

	function Pdf() {
		this.loading = false;
		this.attemptsMax = 20;
		this.delay       = 1000;
		this.attachListeners();
	}

	Pdf.prototype.attachListeners = function(){
		$(document).on('click', '.pdf-link', this.clickHandler.bind(this));
	};

	Pdf.prototype.clickHandler = function(event) {
		event.preventDefault();

		var $this = this,
		    item = $(event.target),
		    propertyId = item.data('id'),
		    isRent = item.data('is_rent');

		if(!propertyId || this.loading){
			return;
		}

		this.loading = true;

		this.getPDFLink(propertyId, isRent)
			.done(
				function(pdfPath){
					$this.loading = false;
					window.location = pdfPath;
				}
			)
			.fail(
				function(reason){
					$this.loading = false;
					$this.downloadError = true;
					console.error("getPDFLink->reject", reason);
				}
			);
	};

	Pdf.prototype.getPDFLink = function(id, isRent) {
		var deferred = $.Deferred(),
		    attempt = 0,
		    timeout = null;
		this.doRequest(id, attempt, deferred, timeout, isRent);
		return deferred.promise();
	};

	Pdf.prototype.doRequest = function(id, attempt, defer, timeout, isRent) {
		attempt++;

		var ajaxData = {
			    action: 'do_ajax',
			    fn: 'get_pdf',
			    property_id: id
		    },
			$this = this;

		if(isRent && isRent !== 'false'){
			ajaxData.for_rent = true;
		}

		$.ajax({
			url: LpData.ajaxUrl,
			dataType: 'json',
			data: ajaxData,
			success: function(answer) {
				if($this.hasPDFPath(answer)){
					defer.resolve(answer.pdf_path);
				}else if(attempt >= $this.attemptsMax){
					defer.reject("No pdf link yet");
				}else{
					if(timeout){
						clearTimeout(timeout);
					}
					timeout = setTimeout($this.doRequest.bind($this, id, attempt, defer, timeout, isRent), $this.delay);
				}
			},
			error: function(error) {
				console.error("PDF _doRequest", error.status, error.statusText);
				defer.reject("No pdf link yet");
			}
		});
	};

	Pdf.prototype.hasPDFPath = function(data) {
		return _.has(data, "pdf_path") && data.pdf_path;
	};

})(jQuery);