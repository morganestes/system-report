(function ($) {
	'use strict';

	console.group('template');
	var template = wp.template('am-system-report');
	var $el = $(document.getElementById('am-js-system-report'));

	console.log('data: %O', morganAMSystemReport.data);

	var markup = template(morganAMSystemReport.data);

	console.log('markup: %O', markup);

	$el.html(markup);

	console.groupEnd();
})(jQuery);
//# sourceMappingURL=app.js.map
