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
	public function __construct() {
	}

	public function __clone() {
		// TODO: Implement __clone() method.
	}

	public function init() {
		\add_action( 'admin_notices', function () {
			?>
			<div class="notice notice-info is-dismissible">
				<p><?php esc_html_e( 'Hi from Morgan\'s System Reports!', 'morgan-am-system-report' ); ?>
				</p>
			</div>
			<?php
		} );
	}

	public static function runner() {
		$me = new self();
		$me->init();
	}
}
