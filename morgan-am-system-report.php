<?php
/**
 * Plugin Name:     Morgan's AM System Report
 * Plugin URI:      https://github.com/morganestes/system-report
 * Description:     Displays system info in the WP Admin dashboard.
 * Author:          Morgan Estes
 * Author URI:      https://morganestes.com/
 * Text Domain:     morgan-am-system-report
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         MorganEstes\SystemReport
 */

namespace MorganEstes\SystemReport;

define( __NAMESPACE__ . '\\PLUGIN_VERSION', '0.1.0' );
define( __NAMESPACE__ . '\\PLUGIN_PATH', __DIR__ );
define( __NAMESPACE__ . '\\PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( __NAMESPACE__ . '\\PLUGIN_NAME', plugin_basename( __FILE__ ) );

// Use Composer to autoload plugin files.
require_once __DIR__ . '/vendor/autoload.php';

// Create the settings page.
add_action( 'init', [ new Settings(), 'init' ] );

// Make the settings page menu item top-level.
add_filter( 'morgan_am_menu_dashboard_sub', '__return_false' );
