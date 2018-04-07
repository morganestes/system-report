<?php
/**
 * Plugin Name:     System Report
 * Plugin URI:      https://github.com/morganestes/system-report
 * Description:     Displays system info in the WP Admin dashboard.
 * Author:          Morgan Estes
 * Author URI:      https://morganestes.com/
 * Text Domain:     shiny-funicular
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         MorganEstes\SystemReport
 */

namespace MorganEstes\SystemReport;

define( __NAMESPACE__ . '\\PLUGIN_VERSION', '0.1.0' );
define( __NAMESPACE__ . '\\PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\\PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( __NAMESPACE__ . '\\PLUGIN_NAME', plugin_basename( __FILE__ ) );

add_action( 'activate_' . PLUGIN_NAME, '__return_null' );
