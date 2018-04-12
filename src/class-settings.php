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

		add_action( "admin_print_footer_scripts-{$this->menu_hook}", [ $this, 'create_list_template' ] );
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
		<div id="am-js-system-report" class="wrap"></div>
		<?php
	}

	/**
	 * Generate the markup for the list table header and footer columns.
	 *
	 * @since 0.2.0
	 *
	 * @param array $cols A collection of columns to display.
	 */
	function js_template_thead_tfoot( $cols ) {
		?>
		<tr>
			<?php foreach ( $cols as $col => $text ) : ?>
				<th scope="col" id="<?php echo esc_attr( $col ); ?>"
				    class="manage-column column-<?php echo esc_attr(
					    $col );
				    if ( 'title' === $col ) {
					    echo ' column-primary';
				    } ?>">
					<?php echo esc_html( $text ); ?>
				</th>
			<?php endforeach; ?>
		</tr>
		<?php
	}

	/**
	 * Create the markup for Underscore template to generate a list table.
	 *
	 * @since 0.2.0
	 * @see wp.template() JS function.
	 */
	function create_list_template() {
		$cols = get_version_header_cols();
		?>
		<script type="text/html" id="tmpl-am-system-report">
			<h3><?php esc_html_e( 'Version Checks JS Template', 'morgan-am-system-report' ); ?></h3>
			<table class="wp-list-table widefat fixed striped data">
				<thead><?php $this->js_template_thead_tfoot( $cols ); ?></thead>

				<tbody id="the-list-js" data-wp-lists="list:datum">
				<# _.each(data, function(datum, idx) { #>
					<tr id="{{ idx }}">
						<# _.each(datum, function(val, key) { #>
							<# if ( 'title' === key ) { #>
								<td class="{{ key }} column-{{ key }} has-row-actions column-primary"
								    data-colname="{{{ morganAMSystemReport.cols.title }}}">{{ val }}
									<button type="button" class="toggle-row">
										<span class="screen-reader-text">
											<?php esc_html_e( 'Show more details', 'morgan-am-system-report' ); ?>
										</span>
									</button>
								</td>
							<# } else { #>
								<td class="{{ key }} column-{{ key }}" data-colname="{{{ morganAMSystemReport.cols[key] }}}">{{ val }}</td>
							<# } #>
						<# }); #>
					</tr>
				<# }); #>

				</tbody>

				<tfoot>
				<?php $this->js_template_thead_tfoot( $cols ); ?>
				</tfoot>

			</table>
		</script>
		<?php
	}
}
