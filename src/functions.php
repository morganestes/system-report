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

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\load_scripts' );

/**
 * Get the latest version info from WP.org.
 *
 * @since 0.1.0
 *
 * @return mixed A collection of WP version data.
 */
function get_latest_wp_versions() {
	$api_url  = 'https://api.wordpress.org/core/version-check/1.7/';
	$request  = wp_safe_remote_get( $api_url );
	$response = wp_remote_retrieve_body( $request );
	$versions = json_decode( $response );

	if ( empty ( $versions->offers ) ) {
		return new \WP_Error( 'no_data', __( 'Could not get version info from WP.org API', 'morgan-am-system-report' ), [
			'request'  => $request,
			'response' => $response,
		] );
	}

	return $versions->offers;
}

/**
 * Get cached version of report data.
 *
 * @since 0.1.0
 * @uses  \wp_cache_remember()
 * @uses  \wp_cache_delete()
 *
 * @return array Report data, or empty array if there's an error.
 */
function get_report_data() {
	$cache_key   = 'version_check_data';
	$cache_group = 'morgan-am-system-report';
	$data        = wp_cache_remember( $cache_key,
		__NAMESPACE__ . '\\generate_report_data',
		$cache_group,
		DAY_IN_SECONDS );

	// Don't cache errors.
	if ( is_wp_error( $data ) ) {
		wp_cache_delete( $cache_key, $cache_group );

		return [];
	}

	return $data;
}

/**
 * Generate system report data for display.
 *
 * @since 0.1.0
 *
 * @return array|\WP_Error A collection of version data, or error object if they can't be calculated.
 */
function generate_report_data() {
	$wp_latest_versions = get_latest_wp_versions();

	if ( is_wp_error( $wp_latest_versions ) ) {
		return $wp_latest_versions;
	}

	$wp_current = $wp_latest_versions[0];

	global $wpdb, $wp_version;

	/* Include an unmodified $wp_version. */
	include ABSPATH . WPINC . '/version.php';

	$yes = _x( 'yes', 'affirmative', 'morgan-am-system-report' );
	$no  = _x( 'no', 'negative', 'morgan-am-system-report' );

	$report_data = [
		'wp_version'    => [
			'title'                 => __( 'WordPress', 'morgan-am-system-report' ),
			'current'               => $wp_version,
			'recommended'           => $wp_current->version,
			'meets_recommendations' => version_compare( $wp_version, $wp_current->version ),
		],
		'php_version'   => [
			'title'                 => __( 'PHP', 'morgan-am-system-report' ),
			'current'               => phpversion(),
			'recommended'           => $wp_current->php_version,
			'meets_recommendations' => version_compare( phpversion(), $wp_current->php_version ),

		],
		'mysql_version' => [
			'title'                 => __( 'MySQL', 'morgan-am-system-report' ),
			'current'               => $wpdb->db_version(),
			'recommended'           => $wp_current->mysql_version,
			'meets_recommendations' => version_compare( $wpdb->db_version(), $wp_current->mysql_version ),
		],
	];

	return $report_data;
}

/**
 * Generate the markup to display the versions list table.
 *
 * @since 0.1.0
 */
function show_versions_list_table() {
	$testListTable = new Details_List();
	$testListTable->prepare_items();
	?>
	<div class="wrap">
		<?php $testListTable->display(); ?>
	</div>
	<?php
}

/**
 * Get the Awesome Motive logo for use in the plugin.
 *
 * @since 0.1.0
 *
 * @return string The base64-encoded logo data.
 */
function get_am_logo_encoded() {
	$svg_base64 = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDQzOCAyMzYiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM6c2VyaWY9Imh0dHA6Ly93d3cuc2VyaWYuY29tLyIgc3R5bGU9ImZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkO3N0cm9rZS1saW5lam9pbjpyb3VuZDtzdHJva2UtbWl0ZXJsaW1pdDoxLjQxNDIxOyI+CiAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgzLjI2NjQsMCwwLDMuNTA4MzIsLTY2LjIwNjYsLTE2OTYpIj4KICAgICAgICA8cGF0aCBkPSJNODIuNzY5LDU0Mi40MjJDODIuOTAyLDU0MC45NTYgODIuNzM2LDUzOS42NTYgODIuMjY5LDUzOC41MjJDODIuMDY5LDUzNy45MjIgODEuODAyLDUzNy44MjIgODEuNDY5LDUzOC4yMjJDODAuMDAyLDU0MC4yMjIgNzguMTY5LDU0MS43ODkgNzUuOTY5LDU0Mi45MjJDNzMuODM2LDU0NC4xMjIgNzIuMDAyLDU0NC42NTYgNzAuNDY5LDU0NC41MjJDNjguNjY5LDU0NC4zMjIgNjcuNTM2LDU0NC4wODkgNjcuMDY5LDU0My44MjJDNjYuMjAyLDU0My4zNTYgNjUuNzY5LDU0Mi40NTYgNjUuNzY5LDU0MS4xMjJDNjUuNzY5LDUzOS43ODkgNjYuMTAyLDUzNy45ODkgNjYuNzY5LDUzNS43MjJDNjcuNDM2LDUzMy41ODkgNjguNDE5LDUzMC43NTYgNjkuNzE5LDUyNy4yMjJDNzEuMDE5LDUyMy42ODkgNzIuNjY5LDUxOS40MjIgNzQuNjY5LDUxNC40MjJDNzYuMjAyLDUxMC42MjIgNzcuNTE5LDUwNy40NTYgNzguNjE5LDUwNC45MjJDNzkuNzE5LDUwMi4zODkgODAuNTY5LDUwMC40NTYgODEuMTY5LDQ5OS4xMjJDODEuNTY5LDQ5OC4zMjIgODEuNzY5LDQ5Ny42MjIgODEuNzY5LDQ5Ny4wMjJDODEuNzY5LDQ5NS40ODkgODAuNzM2LDQ5NC41ODkgNzguNjY5LDQ5NC4zMjJDNzUuNjAyLDQ5My44NTYgNzMuNTY5LDQ5NC42NTYgNzIuNTY5LDQ5Ni43MjJDNjkuNTY5LDUwMi41ODkgNjYuOTUyLDUwNy42MDYgNjQuNzE5LDUxMS43NzJDNjIuNDg2LDUxNS45MzkgNjAuNTM2LDUxOS4yODkgNTguODY5LDUyMS44MjJDNTAuMDAyLDUzNS40ODkgNDIuODM2LDU0Mi4zMjIgMzcuMzY5LDU0Mi4zMjJDMzYuNTAyLDU0Mi4zMjIgMzUuNzY5LDU0Mi4xODkgMzUuMTY5LDU0MS45MjJDMzIuNTY5LDU0MS4wNTYgMzEuMjY5LDUzOS4wODkgMzEuMjY5LDUzNi4wMjJDMzEuMjY5LDUzMi43NTYgMzIuNzY5LDUyOC4zNTYgMzUuNzY5LDUyMi44MjJDMzYuOTY5LDUyMC42MjIgMzguMTg2LDUxOC42MjIgMzkuNDE5LDUxNi44MjJDNDAuNjUyLDUxNS4wMjIgNDEuOTAyLDUxMy4zODkgNDMuMTY5LDUxMS45MjJDNTUuMjM2LDQ5OC4zMjIgNjYuMzY5LDQ5MC45ODkgNzYuNTY5LDQ4OS45MjJDNzkuMjM2LDQ4OS42NTYgODAuNTY5LDQ4OS4wODkgODAuNTY5LDQ4OC4yMjJDODAuNTY5LDQ4Ny44ODkgODAuMTAyLDQ4Ny4wNTYgNzkuMTY5LDQ4NS43MjJDNzguMDM2LDQ4NC4xODkgNzYuNjM2LDQ4My40MjIgNzQuOTY5LDQ4My40MjJDNjkuNjM2LDQ4My40MjIgNjMuMTM2LDQ4NS40NTYgNTUuNDY5LDQ4OS41MjJDNDYuODAyLDQ5NC4xMjIgMzkuNjM2LDQ5OS45MjIgMzMuOTY5LDUwNi45MjJDMjQuODM2LDUxOC4xODkgMjAuMjY5LDUyOC4zNTYgMjAuMjY5LDUzNy40MjJDMjAuMjY5LDU0Mi40MjIgMjEuODAyLDU0NS45NTYgMjQuODY5LDU0OC4wMjJDMjcuMjY5LDU0OS42MjIgMjkuODY5LDU1MC40MjIgMzIuNjY5LDU1MC40MjJDMzcuNzM2LDU1MC40MjIgNDMuMjM2LDU0Ny43MjIgNDkuMTY5LDU0Mi4zMjJDNTMuMzY5LDUzOC41MjIgNTYuNzM2LDUzNC40ODkgNTkuMjY5LDUzMC4yMjJDNTkuNTM2LDUyOS44MjIgNTkuNzY5LDUyOS42MjIgNTkuOTY5LDUyOS42MjJDNjAuNDM2LDUyOS42MjIgNjAuNTAyLDUyOS45ODkgNjAuMTY5LDUzMC43MjJDNTcuNzAyLDUzNi4zMjIgNTYuNDY5LDU0MC44NTYgNTYuNDY5LDU0NC4zMjJDNTYuNDY5LDU0NS42NTYgNTYuNzAyLDU0Ni41ODkgNTcuMTY5LDU0Ny4xMjJDNTguOTAyLDU0OS4wNTYgNjIuNTAyLDU1MC4wMjIgNjcuOTY5LDU1MC4wMjJDNjkuNjM2LDU1MC4wMjIgNzEuMDY5LDU0OS45MjIgNzIuMjY5LDU0OS43MjJDNzYuMTM2LDU0OC45ODkgNzkuNDAyLDU0Ny4yODkgODIuMDY5LDU0NC42MjJDODIuNDAyLDU0NC4yODkgODIuNjM2LDU0My41NTYgODIuNzY5LDU0Mi40MjJaIiBzdHlsZT0iZmlsbC1ydWxlOm5vbnplcm87Ii8+CiAgICAgICAgPHBhdGggZD0iTTE1NC4xNjksNTQyLjQyMkMxNTQuMzAyLDU0MC45NTYgMTU0LjEzNiw1MzkuNjU2IDE1My42NjksNTM4LjUyMkMxNTMuNDY5LDUzNy45MjIgMTUzLjIwMiw1MzcuODIyIDE1Mi44NjksNTM4LjIyMkMxNTEuNjAyLDU0MC4wMjIgMTQ5Ljc2OSw1NDEuNTg5IDE0Ny4zNjksNTQyLjkyMkMxNDUuMzY5LDU0NC4wNTYgMTQzLjczNiw1NDQuNjU2IDE0Mi40NjksNTQ0LjcyMkMxMzkuODY5LDU0NC44NTYgMTM3LjkzNiw1NDQuNTIyIDEzNi42NjksNTQzLjcyMkMxMzUuNzM2LDU0My4xODkgMTM1LjI2OSw1NDIuNDIyIDEzNS4yNjksNTQxLjQyMkMxMzUuMjY5LDU0MS4wMjIgMTM1LjM2OSw1NDAuNDU2IDEzNS41NjksNTM5LjcyMkMxMzUuNjM2LDUzOS41ODkgMTM1LjkzNiw1MzguNzg5IDEzNi40NjksNTM3LjMyMkMxMzcuMDAyLDUzNS44NTYgMTM3LjgwMiw1MzMuNjg5IDEzOC44NjksNTMwLjgyMkMxNDAuODY5LDUyNS41NTYgMTQyLjE2OSw1MjEuNzU2IDE0Mi43NjksNTE5LjQyMkMxNDMuMTY5LDUxOC4wMjIgMTQzLjM2OSw1MTYuNzU2IDE0My4zNjksNTE1LjYyMkMxNDMuMzY5LDUxMy4wMjIgMTQyLjMwMiw1MTEuMjIyIDE0MC4xNjksNTEwLjIyMkMxMzguOTAyLDUwOS41NTYgMTM3LjU2OSw1MDkuMjIyIDEzNi4xNjksNTA5LjIyMkMxMzMuNjM2LDUwOS4yMjIgMTMwLjgwMiw1MTAuNDg5IDEyNy42NjksNTEzLjAyMkMxMjUuNjY5LDUxNC42ODkgMTIzLjQ2OSw1MTcuMDU2IDEyMS4wNjksNTIwLjEyMkMxMTkuMDY5LDUyMi42NTYgMTE4LjAzNiw1MjMuOTIyIDExNy45NjksNTIzLjkyMkMxMTcuNjM2LDUyMy45MjIgMTE3LjczNiw1MjMuMzU2IDExOC4yNjksNTIyLjIyMkMxMTkuNTM2LDUxOS40MjIgMTIwLjE2OSw1MTYuOTg5IDEyMC4xNjksNTE0LjkyMkMxMjAuMTY5LDUxMS43ODkgMTE4LjUwMiw1MDkuOTIyIDExNS4xNjksNTA5LjMyMkMxMTQuODM2LDUwOS4yNTYgMTE0LjUzNiw1MDkuMjA2IDExNC4yNjksNTA5LjE3MkMxMTQuMDAyLDUwOS4xMzkgMTEzLjczNiw1MDkuMTIyIDExMy40NjksNTA5LjEyMkMxMDguMjY5LDUwOS4xMjIgMTAyLjczNiw1MTMuNTU2IDk2Ljg2OSw1MjIuNDIyQzk1LjczNiw1MjQuMTU2IDk1LjE2OSw1MjQuNTIyIDk1LjE2OSw1MjMuNTIyQzk1LjE2OSw1MjIuODU2IDk1LjczNiw1MjAuOTIyIDk2Ljg2OSw1MTcuNzIyQzk3LjgwMiw1MTUuMTIyIDk4LjYwMiw1MTMuMTg5IDk5LjI2OSw1MTEuOTIyQzk5LjUzNiw1MTEuNDU2IDk5LjY2OSw1MTAuOTg5IDk5LjY2OSw1MTAuNTIyQzk5LjY2OSw1MDkuNTIyIDk4LjkzNiw1MDguNzIyIDk3LjQ2OSw1MDguMTIyQzk2LjQ2OSw1MDcuNjU2IDk1LjMwMiw1MDcuNDIyIDkzLjk2OSw1MDcuNDIyQzkxLjE2OSw1MDcuNDIyIDg5LjUzNiw1MDguNDIyIDg5LjA2OSw1MTAuNDIyQzg3LjIwMiw1MTcuMjIyIDg1LjQxOSw1MjMuNzA2IDgzLjcxOSw1MjkuODcyQzgyLjAxOSw1MzYuMDM5IDgwLjM2OSw1NDEuOTU2IDc4Ljc2OSw1NDcuNjIyQzc4LjQzNiw1NDkuMjIyIDc5Ljg2OSw1NTAuMDIyIDgzLjA2OSw1NTAuMDIyQzg2LjI2OSw1NTAuMDIyIDg4LjEzNiw1NDkuMzU2IDg4LjY2OSw1NDguMDIyQzg5LjUzNiw1NDYuMDIyIDkxLjczNiw1NDEuODU2IDk1LjI2OSw1MzUuNTIyQzk5LjIwMiw1MjguMzIyIDEwMi4xMDIsNTIzLjY1NiAxMDMuOTY5LDUyMS41MjJDMTA2LjM2OSw1MTguNjU2IDEwOC4yNjksNTE3LjMyMiAxMDkuNjY5LDUxNy41MjJDMTEwLjkzNiw1MTcuNzIyIDExMS4yMzYsNTE4Ljc1NiAxMTAuNTY5LDUyMC42MjJDMTA4LjYzNiw1MjUuNjg5IDEwNi43NjksNTMwLjQ3MiAxMDQuOTY5LDUzNC45NzJDMTAzLjE2OSw1MzkuNDcyIDEwMS41MDIsNTQzLjc1NiA5OS45NjksNTQ3LjgyMkM5OS40MzYsNTQ5LjM1NiAxMDEuMDAyLDU1MC4xMjIgMTA0LjY2OSw1NTAuMTIyQzEwNy45MzYsNTUwLjEyMiAxMDkuODM2LDU0OS4zNTYgMTEwLjM2OSw1NDcuODIyQzExMC43NjksNTQ2LjU1NiAxMTEuNjAyLDU0NC43ODkgMTEyLjg2OSw1NDIuNTIyQzExNC4xMzYsNTQwLjI1NiAxMTUuODM2LDUzNy40ODkgMTE3Ljk2OSw1MzQuMjIyQzExOS43MDIsNTMxLjU1NiAxMjEuMjg2LDUyOS4xODkgMTIyLjcxOSw1MjcuMTIyQzEyNC4xNTIsNTI1LjA1NiAxMjUuNDY5LDUyMy4yNTYgMTI2LjY2OSw1MjEuNzIyQzEyOC40NjksNTE5LjQ1NiAxMjkuODM2LDUxOC4zMjIgMTMwLjc2OSw1MTguMzIyQzEzMS43NjksNTE4LjMyMiAxMzIuMjY5LDUxOC44MjIgMTMyLjI2OSw1MTkuODIyQzEzMi4yNjksNTIwLjU1NiAxMzEuODM2LDUyMS43NTYgMTMwLjk2OSw1MjMuNDIyQzEzMC4zNjksNTI0LjYyMiAxMjkuNzM2LDUyNi4wMDYgMTI5LjA2OSw1MjcuNTcyQzEyOC40MDIsNTI5LjEzOSAxMjcuNzAyLDUzMC44ODkgMTI2Ljk2OSw1MzIuODIyQzEyNS4zMDIsNTM3LjM1NiAxMjQuNDY5LDU0MC41NTYgMTI0LjQ2OSw1NDIuNDIyQzEyNC40NjksNTQ1LjU1NiAxMjYuMzAyLDU0Ny44MjIgMTI5Ljk2OSw1NDkuMjIyQzEzMi4wMzYsNTQ5Ljk1NiAxMzQuNTM2LDU1MC4zMjIgMTM3LjQ2OSw1NTAuMzIyQzEzOS40NjksNTUwLjMyMiAxNDEuNTM2LDU1MC4xMjIgMTQzLjY2OSw1NDkuNzIyQzE0Ny40MDIsNTQ5LjEyMiAxNTAuNjY5LDU0Ny40MjIgMTUzLjQ2OSw1NDQuNjIyQzE1My44MDIsNTQ0LjI4OSAxNTQuMDM2LDU0My41NTYgMTU0LjE2OSw1NDIuNDIyWiIgc3R5bGU9ImZpbGwtcnVsZTpub256ZXJvOyIvPgogICAgPC9nPgo8L3N2Zz4K';

	/**
	 * Filter the logo used in the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @param string $svg_base64 A base64-encoded string value of the logo image.
	 */
	$logo = (string) apply_filters( 'morgan_am_logo', $svg_base64 );

	return sprintf( 'data:image/svg+xml;base64,%s', $logo );
}

/**
 * Store the column header text for reuse.
 *
 * @since 0.2.0
 *
 * @return array The collection of list table header columns.
 */
function get_version_header_cols() {
	$cols = [
		'title'                 => _x( 'Name', 'column name', 'morgan-am-system-report' ),
		'current'               => _x( 'Current Version', 'column name', 'morgan-am-system-report' ),
		'recommended'           => _x( 'Recommended Version', 'column name', 'morgan-am-system-report' ),
		'meets_recommendations' => _x( 'Meets Recommendations', 'column name', 'morgan-am-system-report' ),
	];

	/**
	 * Filter the header columns.
	 *
	 * @since 0.2.0
	 *
	 * @param array $cols Columns in the format $id => $text.
	 */
	return (array) apply_filters( 'morgan_am_header_cols', $cols );
}

/**
 * Load scripts for the plugin.
 *
 * @since 0.2.0
 */
function load_scripts() {
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_script( 'morgan-am-system-report',
		PLUGIN_URL . "assets/js/app{$min}.js",
		[ 'jquery', 'wp-util' ],
		PLUGIN_VERSION,
		true
	);

	wp_localize_script( 'morgan-am-system-report', 'morganAMSystemReport', [
		'data' => get_report_data(),
		'cols' => get_version_header_cols(),
	] );
}
