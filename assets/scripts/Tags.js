(function($) {
	"use strict";

	function Tags(
		pathToBuilder,
		autoCompleteInstance,
		filterFormInstance,
		filterSortingInstance
	) {
		this.pathToBuilder = pathToBuilder;
		this.autoComplete = autoCompleteInstance;
		this.filterForm = filterFormInstance;
		this.filterSorting = filterSortingInstance;

		this.soptingPanel = $('.sorting-wrapper');

		this.jQTagsField = $('#panel-tags');

		this.attachListeners();
	}

	Tags.prototype.attachListeners = function() {
		this.jQTagsField.on('click', '.tag-remove, .tag-remove-all', this.onTagRemove.bind(this));
	};

	Tags.prototype.onTagRemove = function(event) {
		var type = $(event.target).data('tag_type'),
		    data = $(event.target).data('tag_data'),
		    jqItem;

		switch (type){
			case 'property_type':
			case 'room':
			case 'quality':
			case 'long-term':
			case 'short-term':
			case 'child-friendly':
			case 'pets-allowed':
				jqItem = $('#' + data);
				if(jqItem.length > 0){
					jqItem.prop('checked', false).change();
					this.filterForm.trigger('submit');
				}
				break;
			case 'similar':
			case 'autocomplete':
				this.autoComplete.setSelected(null);
				break;
			case 'price':
				$('#price-min').add('#price-max').val(undefined);
				//$('#price-currency').val('1').change();
				this.filterForm.trigger('submit');
				break;
			case 'area':
				$('#area-min').add('#area-max').val(undefined);
				this.filterForm.trigger('submit');
				break;
			case 'persons-max':
				$('#persons-max').val(undefined);
				this.filterForm.trigger('submit');
				break;
			case 'all':
				this.filterSorting.val('false').trigger('change');
				this.filterForm[0].reset();
				this.autoComplete.setSelected(null, null, true);
				this.filterForm.trigger('submit');
				break;
		}
	};

	Tags.prototype.buildTags = function(params) {
		this.getTags(params).done(this.getTagsSuccess.bind(this)).fail(this.getTagsError.bind(this));
	};

	Tags.prototype.getTags = function(params) {
		var $this = this,
		    data = {
			    action: 'do_ajax',
			    fn: 'get_tags',
			    raw: params //we need it for tag counters
		    };

		if(this.autoComplete.autocompleteSelected){
			data.autocomplete = this.getAutocompleteData(params);
		} else if(params.autocomplete) {

			data.autocomplete = params.autocomplete;
			delete data.raw.autocomplete;

		}

		return $.ajax({
			url: $this.pathToBuilder,
			method: 'post',
			data: data
		});
	};

	Tags.prototype.getTagsSuccess = function(answer) {
		if(answer && answer !== '0') {
			this.jQTagsField.html(answer);
			this.soptingPanel.show();
		} else {
			this.soptingPanel.hide();
		}
	};

	Tags.prototype.getTagsError = function(error) {
		console.debug('getTagsError', error.responseText);
	};

	Tags.prototype.getAutocompleteData = function(params) {
		if(!this.autoComplete.autocompleteSelected){
			return null;
		}

		var answer = {
			text: this.autoComplete.jqInput.val(),
			data: {}
		};

		if(params.l_id){
			answer.data = {
				l_id: params.l_id,
				l_type: params.l_type
			};
		}else if(params.location_point || params.location_shape){
			answer.data = {
				location_point: params.location_point,
				location_shape: params.location_shape
			};
		}
		return answer;
	};

	window.lpw = window.lpw || {};
	window.lpw.Tags = Tags;

})(jQuery);
