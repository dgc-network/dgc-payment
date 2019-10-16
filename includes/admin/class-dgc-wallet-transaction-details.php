<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class dgc_Wallet_Transaction_Details extends WP_List_Table {

    /**
     * Total number of found users for the current query
     *
     * @since 3.1.0
     * @var int
     */
    private $total_count = 0;

    public function __construct() {
        parent::__construct( array(
            'singular' => 'transaction',
            'plural'   => 'transactions',
            'ajax'     => false,
            'screen'   => 'wc-wallet-transactions',
        ) );
    }

    public function get_columns() {
        return array(
            'transaction_id' => __( 'ID', 'dgc-wallet' ),
            'name'           => __( 'Name', 'dgc-wallet' ),
            'type'           => __( 'Type', 'dgc-wallet' ),
            'amount'         => __( 'Amount', 'dgc-wallet' ),
            'details'        => __( 'Details', 'dgc-wallet' ),
            'date'           => __( 'Date', 'dgc-wallet' )
        );
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $perPage     = $this->get_items_per_page( 'transactions_per_page', 10 );
        $currentPage = $this->get_pagenum();

        $data = $this->table_data( ( $currentPage - 1 ) * $perPage, $perPage );
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $this->total_count,
            'per_page'    => $perPage
        ) );
    }
    
    /**
    * Output 'no users' message.
    *
    * @since 3.1.0
    */
    public function no_items() {
        _e( 'No transactions found.', 'dgc-wallet' );
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array( 'transaction_id' );
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        return array();
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data( $lower = 0, $uper = 10 ) {
        global $wpdb;
        $data    = array();
        $user_id = filter_input(INPUT_GET, 'user_id' );
        if ( $user_id == NULL ) {
            return $data;
        }
        $transactions = get_wallet_transactions( array( 'user_id' => $user_id, 'limit' => $lower . ',' . $uper ) );
        //$transactions = get_wallet_transactions( array( 'user_id' => $user_id ) );
        //$this->total_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->base_prefix}dgc_wallet_transactions WHERE user_id={$user_id}" );
        // dgc-API-call:begin: /retrieveRecords
		$dgc_API_args = array(
			'table'		=> $wpdb->prefix . 'dgc_wallet_transactions',
			'query'		=> array(
				'user_id'	=> $user_id,
			)
		);
		$dgc_API_res = dgc_API_call('/retrieveRecords/', 'POST', $dgc_API_args);
		foreach(json_decode($dgc_API_res['body']) as $dgc_API_row) {
            if (null !== $dgc_API_row->properties) {
                $this->total_count += 1;
            }
        }
		// dgc-API-call:end: /retrieveRecords
        
        if ( ! empty( $transactions ) && is_array( $transactions ) ) {
            foreach ( $transactions as $key => $transaction ) {
                $data[] = array(
                    'transaction_id' => $transaction->transaction_id,
                    'name'           => get_user_by( 'ID', $transaction->user_id )->display_name,
                    'type'           => ( 'credit' === $transaction->type) ? __( 'Credit', 'dgc-wallet' ) : __( 'Debit', 'dgc-wallet' ),
                    'amount'         => wc_price( apply_filters( 'dgc_wallet_amount', $transaction->amount, $transaction->currency, $transaction->user_id ), dgc_wallet_wc_price_args($transaction->user_id) ),
                    'details'        => $transaction->details,
                    //'date'           => wc_string_to_datetime( $transaction->date )->date_i18n( wc_date_format() )
                    'date'           => date_i18n( wc_date_format(), $transaction->date )
                );
            }
        }
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'transaction_id':
            case 'name':
            case 'type':
            case 'amount':
            case 'details':
            case 'date':
                return $item[$column_name];
            default:
                return print_r( $item, true );
        }
    }

}
