(function ( $ ) {
	'use strict';

	console.group( 'template' );
	const template = wp.template( 'am-system-report' );
	const $el = $( document.getElementById( 'am-js-system-report' ) );

	console.log( 'data: %O', morganAMSystemReport.data );

	const markup = template( morganAMSystemReport.data );

	console.log( 'markup: %O', markup );

	$el.html( markup );

	console.groupEnd();
})( jQuery );
