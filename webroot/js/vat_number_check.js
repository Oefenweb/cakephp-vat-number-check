/* jshint jquery:true */

/* global klass: false */

var VatNumberCheck = klass({

	/**
	 * A css class selector to trigger `check` logic.
	 *
	 */
	elementSelector: '',

	/**
	 * An url to use for checking.
	 *
	 */
	checkUrl: '',

	/**
	 * An object with (status) images to use as background after checking.
	 *
	 */
	checkImages: {},

	/**
	 * Constructor.
	 *
	 *  Initializes instance variables and attaches a blur event.
	 *
	 *  Possible keys:
	 *  - ok
	 *  - failure
	 *  - serviceUnavailable
	 *
	 * @param {Object} options An options object
	 */
	initialize: function (options) {
		this.elementSelector = options.elementSelector || this.elementSelector;
		this.checkUrl = options.checkUrl || this.checkUrl;
		this.checkImages = options.checkImages || this.checkImages;

		jQuery(this.elementSelector).blur(jQuery.proxy(this._check, this));
	},

	/**
	 * A blur event handler.
	 *
	 *  Requests the check url and displays it's status.
	 *
	 * @param {Object} event
	 */
	_check: function (event) {
		var element = jQuery(event.target);

		jQuery.ajax({
			type: 'POST',
			url: this.checkUrl,
			beforeSend: jQuery.proxy(this._beforeSend, this, element),
			data: {
				vatNumber: element.val()
			}
		}).done(
			jQuery.proxy(this._done, this, element)
		).fail(
			jQuery.proxy(this._fail, this, element)
		);
	},

	/**
	 * A request handler for `done`.
	 *
	 * @param {Object} element A DOM element
	 * @param {Object} result Request data
	 */
	_done: function (element, result) {
		if (result.status === 'ok') {
			this._setBackground(element, this.checkImages.ok);
		} else {
			if (result.vatNumber.length === 0) {
				this._setBackground(element);
			} else {
				this._setBackground(element, this.checkImages.failure);
			}
		}

		element.val(result.vatNumber);
	},

	/**
	 * A request handler for `fail`.
	 *
	 * @param {Object} element A DOM element
	 */
	_fail: function (element) {
		this._setBackground(element, this.checkImages.serviceUnavailable);
	},

	/**
	 * A request handler for `beforeSend`.
	 *
	 * @param {Object} element A DOM element
	 */
	_beforeSend: function (element) {
		this._setBackground(element, '');
	},

	/**
	 * Changes the background image of a given element.
	 *
	 * @param {Object} element A DOM element
	 * @param {String} image An image (url)
	 */
	_setBackground: function (element, image) {
		var backgroundImage;
		if (image) {
			backgroundImage = 'url("' + image + '")';
		} else {
			backgroundImage = 'none';
		}

		element.css({
			backgroundImage: backgroundImage,
			backgroundRepeat: 'no-repeat',
			backgroundPosition: 'right'
		});
	}

});
