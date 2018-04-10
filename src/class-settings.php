<?php
/**
 * Admin UI Settings page
 *
 * @package MorganEstes\SystemReport
 */

namespace MorganEstes\SystemReport;

/**
 * Class Settings
 *
 * @package MorganEstes\SystemReport
 */
class Settings {

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the plugin settings.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'create_settings_page' ] );
	}

	/**
	 * Register the plugin settings page.
	 *
	 * @since 0.1.0
	 */
	function create_settings_page() {
		add_menu_page(
			__( 'System Report', 'morgan-am-system-report' ),
			_x( 'System Report', 'menu title', 'morgan-am-system-report' ),
			'manage_options',
			'morgan-am-system-report',
			[ $this, 'options_page' ],
			'dashicons-analytics'
		);
	}

	/**
	 * Displays the HTML for the options page.
	 *
	 * @since 0.1.0
	 * @internal Callback for {@see add_menu_page()}.
	 */
	public function options_page() {
		?>
		<h2><?php esc_html_e( 'System Report', 'morgan-am-system-report' ); ?></h2>
		<?php

	}
}
