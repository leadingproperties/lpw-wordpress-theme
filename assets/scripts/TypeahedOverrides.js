(function($){
	"use strict";

	/**
	 * Overrides several buggy methods of Typeahead plugin
	 * @see https://github.com/bassjobsen/Bootstrap-3-Typeahead
	 *
	 * @constructor
	 */
	function TypeaheadOverrides() {
		$.fn.typeahead.Constructor.prototype.next = this.nextForTypeahead;
		$.fn.typeahead.Constructor.prototype.prev = this.prevForTypeahead;
		$.fn.typeahead.Constructor.prototype.click = this.clickForTypeahead;
		$.fn.typeahead.Constructor.prototype.input = this.inputForTypeahead;
		$.fn.typeahead.Constructor.prototype.lookup = this.lookupForTypeahead;
		$.fn.typeahead.Constructor.prototype.render = this.renderForTypeahead;
	}

	/**
	 * Modified to avoid .dropdown-header items
	 * @param event
	 *
	 * @see bower_components/bootstrap3-typeahead/bootstrap3-typeahead.js:316
	 */
	TypeaheadOverrides.prototype.nextForTypeahead = function (event) {
		var active = this.$menu.find('.active').removeClass('active');
		var next = active.next(':not(.dropdown-header)');

		if (!next.length) {
			next = this.$menu.find('li').filter(':not(.dropdown-header)').first();
		}

		next.addClass('active');
	};

	/**
	 * Modified to avoid .dropdown-header items
	 * @param event
	 *
	 * @see bower_components/bootstrap3-typeahead/bootstrap3-typeahead.js:327
	 */
	TypeaheadOverrides.prototype.prevForTypeahead = function(event) {
		var active = this.$menu.find('.active').removeClass('active');
		var prev = active.prev(':not(.dropdown-header)');

		if (!prev.length) {
			prev = this.$menu.find('li').filter(':not(.dropdown-header)').last();
		}

		prev.addClass('active');
	};

	/**
	 * Modified to avoid .dropdown-header item click
	 * @param e
	 *
	 * @see bower_components/bootstrap3-typeahead/bootstrap3-typeahead.js:490
	 */
	TypeaheadOverrides.prototype.clickForTypeahead = function(e) {
		e.preventDefault();
		if(!$(e.target).hasClass('dropdown-header')){
			this.skipShowHintOnFocus = true;
			this.select();
			this.$element.focus();
			this.hide();
		}
	};

	/**
	 * Modified to remove redundant manipulations. Especially this.$element.text()
	 * @param e
	 *
	 * @see bower_components/bootstrap3-typeahead/bootstrap3-typeahead.js:424
	 */
	TypeaheadOverrides.prototype.inputForTypeahead = function(e) {
		this.lookup();
	};

	/**
	 * Modified to remove this.$element.text()
	 * @param query
	 * @returns {TypeaheadOverrides}
	 *
	 * @see bower_components/bootstrap3-typeahead/bootstrap3-typeahead.js:156
	 */
	TypeaheadOverrides.prototype.lookupForTypeahead = function(query) {
		if (typeof(query) != 'undefined' && query !== null) {
			console.debug('case 1');
			this.query = query;
		} else {
			this.query = this.$element.val() || '';
		}

		if (this.query.length < this.options.minLength && !this.options.showHintOnFocus) {
			console.debug('case 3');
			return this.shown ? this.hide() : this;
		}

		var worker = $.proxy(function () {

			if ($.isFunction(this.source)) {
				console.debug('case 4');
				this.source(this.query, $.proxy(this.process, this));
			} else if (this.source) {
				console.debug('case 5');
				this.process(this.source);
			}
		}, this);

		clearTimeout(this.lookupWorker);
		this.lookupWorker = setTimeout(worker, this.delay);
	};

	/**
	 * Overrides `render` method of Typeahed plugin to add css class for google items, headers.
	 * It's copy+paste version of Typeahed.render method, because `render` method inserts items directly in DOM
	 * and returns only `this` reference.
	 *
	 *
	 * @param {Array} items
	 * @returns {Object} - Typeahead instance
	 *
	 * @see bower_components/bootstrap3-typeahead/bootstrap3-typeahead.js:259 (original render method)
	 */
	TypeaheadOverrides.prototype.renderForTypeahead = function(items) {
		var that = this;
		var self = this;
		var activeFound = false;
		var data = [];
		var _category = that.options.separator;
		var hasApiItems = false;

		$.each(items, function (key,value) {
			// inject separator
			if (key > 0 && value[_category] !== items[key - 1][_category]){
				data.push({
					__type: 'divider'
				});
			}

			// inject category header
			if (value[_category] && (key === 0 || value[_category] !== items[key - 1][_category])){
				data.push({
					__type: 'category',
					name: value[_category]
				});
			}
			data.push(value);
		});

		items = $(data).map(function (i, item) {
			if (((item.__type || false) == 'category') || item._type === 'dropdownHeader'){
				return $(that.options.headerHtml).text(item.name)[0];
			}

			if ((item.__type || false) == 'divider'){
				return $(that.options.headerDivider)[0];
			}

			var text = self.displayText(item);
			i = $(that.options.item).data('value', item);
			i.find('a').html(that.highlighter(text, item));
			if (text == self.$element.val()) {
				i.addClass('active');
				self.$element.data('active', item);
				activeFound = true;
			}

			if(item._type === 'tip' && item.counter){
				i.find('a').append('&nbsp;<sup>' + item.counter + '</sup>');
			}

			// set hasApiItems to true once
			if(!hasApiItems && item.parent_id){
				hasApiItems = true;
			}
			if(item._cssClass){
				i.addClass(item._cssClass);
			}
			return i[0];
		});

		//add item-divider class
		if(hasApiItems){
			items.filter('.pbgoogle').first().addClass('item-divider');
		}

		if (this.autoSelect && !activeFound) {
			items.filter(':not(.dropdown-header)').first().addClass('active');
			this.$element.data('active', items.first().data('value'));
		}
		this.$menu.html(items);
		return this;
	};

	window.lpw = window.lpw || {};
	window.lpw.TypeaheadOverrides = TypeaheadOverrides;

})(jQuery);