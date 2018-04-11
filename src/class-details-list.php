<?php
/**
 * List Table for version information
 *
 * @package MorganEstes\SystemReport
 */

namespace MorganEstes\SystemReport;

if ( ! class_exists( 'WP_List_Table' ) ) {
	/* Load WP_List_Table class. */
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Details_List
 *
 * @package MorganEstes\SystemReport
 */
class Details_List extends \WP_List_Table {

	/**
	 * Details_List constructor.
	 */
	function __construct() {
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'datum',     //singular name of the listed records
			'plural'   => 'data',    //plural name of the listed records
			'ajax'     => false        //does this table support ajax?
		) );
	}

	/**
	 * Sets the values for the columns.
	 *
	 * @see WP_List_Table::single_row_columns()
	 *
	 * @param array  $item        A singular item (one full row's worth of data)
	 * @param string $column_name The name/slug of the column to be processed
	 *
	 * @return string Text or HTML to be placed inside the column <td>
	 */
	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * This method dictates the table's columns and titles.
	 *
	 * @see WP_List_Table::single_row_columns()
	 *
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns() {
		$columns = [
			'title'                 => __( 'Name', 'morgan-am-system-report' ),
			'current'               => __( 'Current Version', 'morgan-am-system-report' ),
			'recommended'           => __( 'Recommended Version', 'morgan-am-system-report' ),
			'meets_recommendations' => __( 'Meets Recommendations', 'morgan-am-system-report' ),
		];

		return $columns;
	}

	/**
	 * Prepare  data for display.
	 *
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = [];

		// There's no setter for this, so we have to use the old way of setting them directly.
		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$this->items = get_report_data();
	}

	/**
	 * Add heading text at the top of the table.
	 *
	 * @inheritdoc
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' === $which ) {
			?>
			<h3><?php esc_html_e( 'Version Checks List Table', 'morgan-am-system-report' ); ?></h3>
			<?php
		}
	}

}

