(function ( $ ) {
	'use strict';

	console.group( 'template' );
	const template = wp.template( 'am-system-report' );
	const $el = $( document.getElementById( 'am-js-system-report' ) );

	console.log( 'data: %O', morganAMSystemReport.data );

	const markup = template( morganAMSystemReport.data );

	console.log( 'markup: %O', markup );

	$el.html( markup );

	const meetsRequirementsFields = document.querySelectorAll( '.meets_recommendations' );

	/**
	 * Checks if a given value meets requirements.
	 *
	 * @since 0.2.0
	 *
	 * @param {int} val The number returned from PHP's version_compare().
	 * @returns {string} 'yes' or 'no', to match a dashicon class name.
	 */
	const requirementsCheck = ( val ) => {
		return val < 0 ? 'no' : 'yes';
	};

	// Convert the field value into a dashicon.
	_.each( meetsRequirementsFields, ( check ) => {
		let val = parseInt( check.innerText, 10 );

		check.dataset.meetsRequirements = val.toString();
		check.innerHTML = `<span class="dashicons dashicons-${requirementsCheck( val )}"></span>`;
	} );

	console.groupEnd();
})( jQuery );
