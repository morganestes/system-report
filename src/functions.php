<?php
/**
 * Created by PhpStorm.
 * User: morganestes
 * Date: 4/7/18
 * Time: 10:38 PM
 */

namespace MorganEstes\SystemReport;

function foo() {
	return 'bar';
}

\add_action( 'admin_init', [ __NAMESPACE__ . '\\Settings', 'runner' ] );
