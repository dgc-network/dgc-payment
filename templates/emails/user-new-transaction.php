<?php
/**
 * Customer wallet transaction email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/user-new-transaction.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 	dgc.network
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<?php if ( $type == 'credit' ) { ?>
<p><?php _e( "Thank you for using dgc wallet.", 'text-domain' ); ?> <?php echo wc_price( $amount, dgc_wallet_wc_price_args($user->ID) ); ?> <?php _e( 'has been credited to your wallet.', 'text-domain' ); ?> <?php _e( 'Current wallet balance is', 'text-domain' ); ?> <?php echo dgc_wallet()->wallet_core->get_wallet_balance( $user->ID ); ?></p>
<?php } ?>
<?php if ( $type == 'debit' ) { ?>
    <p><?php _e( "Thank you for using dgc wallet.", 'text-domain' ); ?> <?php echo wc_price( $amount, dgc_wallet_wc_price_args($user->ID) ); ?> <?php _e( 'has been debited from your wallet.', 'text-domain' ); ?> <?php _e( 'Current wallet balance is', 'text-domain' ); ?> <?php echo dgc_wallet()->wallet_core->get_wallet_balance( $user->ID ); ?></p>
<?php } ?>
<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
