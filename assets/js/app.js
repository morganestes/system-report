(function ($) {
	'use strict';

	console.group('template');
	var template = wp.template('am-system-report');
	var $el = $(document.getElementById('am-js-system-report'));

	console.log('data: %O', morganAMSystemReport.data);

	var markup = template(morganAMSystemReport.data);

	console.log('markup: %O', markup);

	$el.html(markup);

	var meetsRequirementsFields = document.querySelectorAll('.meets_recommendations');

	/**
  * Checks if a given value meets requirements.
  *
  * @since 0.2.0
  *
  * @param {int} val The number returned from PHP's version_compare().
  * @returns {string} 'yes' or 'no', to match a dashicon class name.
  */
	var requirementsCheck = function requirementsCheck(val) {
		return val < 0 ? 'no' : 'yes';
	};

	// Convert the field value into a dashicon.
	_.each(meetsRequirementsFields, function (check) {
		var val = parseInt(check.innerText, 10);

		check.dataset.meetsRequirements = val.toString();
		check.innerHTML = '<span class="dashicons dashicons-' + requirementsCheck(val) + '"></span>';
	});

	console.groupEnd();
})(jQuery);
//# sourceMappingURL=app.js.map
