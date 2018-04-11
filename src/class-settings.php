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
	 * @var \string The hook_suffix generated after menu is created.
	 */
	public $menu_hook;

	/**
	 * Settings constructor.
	 */
	public function __construct() {
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
	public function create_settings_page() {

		/**
		 * Filter the menu location of the Settings page.
		 *
		 * @since 0.1.0
		 *
		 * @param bool $is_dashboard_sub The menu location. Default 'true' for
		 *                               Dashboard sub-menu item; 'false' will
		 *                               make this a main-level menu item.
		 */
		$is_dashboard_sub = (bool) apply_filters( 'morgan_am_menu_dashboard_sub', true );

		if ( $is_dashboard_sub ) {
			$this->menu_hook = add_submenu_page( 'index.php',
				__( 'System Report', 'morgan-am-system-report' ),
				_x( 'System Report', 'menu title', 'morgan-am-system-report' ),
				'manage_options',
				'morgan-am-system-report',
				[ $this, 'options_page' ]
			);
		} else {
			$this->menu_hook = add_menu_page(
				__( 'System Report', 'morgan-am-system-report' ),
				_x( 'System Report', 'menu title', 'morgan-am-system-report' ),
				'manage_options',
				'morgan-am-system-report',
				[ $this, 'options_page' ],
				get_am_logo_encoded()
			);
		}
	}

	/**
	 * Displays the HTML for the options page.
	 *
	 * @since    0.1.0
	 * @internal Callback for {@see add_menu_page()}.
	 */
	public function options_page() {
		?>
		<h2><?php esc_html_e( 'System Report', 'morgan-am-system-report' ); ?></h2>
			<?php show_versions_list_table(); ?>
		<?php
	}

}
