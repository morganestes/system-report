<?php
/**
 * General plugin functions
 *
 * @package MorganEstes\SystemReport
 */

namespace MorganEstes\SystemReport;

// Create the settings page.
add_action( 'init', [ new Settings(), 'init' ] );

// Make the settings page menu item top-level.
add_filter( 'morgan_am_menu_dashboard_sub', '__return_false' );
