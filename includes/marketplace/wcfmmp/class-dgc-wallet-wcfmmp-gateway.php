<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('dgc_WCFMmp_Payment_Gateway') && class_exists('WCFMmp_Abstract_Gateway')) {

    class dgc_WCFMmp_Payment_Gateway extends WCFMmp_Abstract_Gateway {

        public $id;
        public $message = array();
        public $gateway_title;
        public $payment_gateway;
        public $withdrawal_id;
        public $vendor_id;
        public $withdraw_amount = 0;
        public $currency;
        public $transaction_mode;
        private $reciver_email;

        public function __construct() {
            $this->id = dgc_wallet_WCFMMP::$gateway_slug;
            $this->gateway_title = __('Payment', 'text-domain');
            $this->payment_gateway = $this->id;
        }

        public function gateway_logo() {
            global $WCFMmp;
            return $WCFMmp->plugin_url . 'assets/images/' . $this->id . '.png';
        }

        public function process_payment($withdrawal_id, $vendor_id, $withdraw_amount, $withdraw_charges, $transaction_mode = 'auto') {
            global $WCFMmp;
            $this->withdrawal_id = $withdrawal_id;
            $this->vendor_id = $vendor_id;
            $this->withdraw_amount = $withdraw_amount;
            $this->currency = get_woocommerce_currency();
            $this->transaction_mode = $transaction_mode;
            $this->reciver_email = $WCFMmp->wcfmmp_vendor->get_vendor_payment_account($this->vendor_id, $this->id);
            if ($this->validate_request()) {
                $transaction_id = dgc_wallet()->wallet_core->credit($this->vendor_id, $this->withdraw_amount, __('Commission received for commission id #', 'text-domain') . $this->withdrawal_id);
                // Updating withdrawal meta
                $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta($this->withdrawal_id, 'withdraw_amount', $this->withdraw_amount);
                $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta($this->withdrawal_id, 'currency', $this->currency);
                $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta($this->withdrawal_id, 'reciver_email', $this->reciver_email);
                $WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta($this->withdrawal_id, 'payment_transaction_id', $transaction_id);
                return array('status' => true, 'message' => __('New transaction has been initiated', 'text-domain'));
            } else {
                return $this->message;
            }
        }

        public function validate_request() {
            return true;
        }

    }

}