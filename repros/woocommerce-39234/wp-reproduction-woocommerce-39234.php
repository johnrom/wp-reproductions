<?php
/*
Plugin Name: WP Reproduction - WooCommerce PR 39234
Plugin URI: https://github.com/woocommerce/woocommerce/pull/39234
Description: Reproducing https://github.com/woocommerce/woocommerce/pull/39234
Author: John Rom, Nimblelight
Version: 1.0.0
Author URI: https://nmbl.lt/
*/
class WP_Reproduction_WooCommerce_39234 {
	/**
	 * `$dynamic_coupon_type` must match coupon type in `package.json`
	 */
    private static $dynamic_coupon_type = 'repro_dynamic_cart';
    private static $text_domain = 'wp-reproduction-woocommerce-39234';

    public function __construct() {
        add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_dynamic_coupon_type' ), 10, 1 );
		add_filter( 'woocommerce_coupon_is_valid_for_product', '__return_true', 10, 4 );
		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_dynamic_coupon_discount_amount' ), 10, 5 );
    }

    function add_dynamic_coupon_type( $discount_types ) {
        $discount_types[ self::$dynamic_coupon_type ] = __( 'Dynamic Coupon', self::$text_domain );

        return $discount_types;
    }

	function get_dynamic_coupon_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

		if ( $coupon->type === self::$dynamic_coupon_type ) {
			$discount = $cart_item['quantity'] * $discounting_amount;
		}

		return $discount;
	}

	function populate() {

	}
}

$wp_reproduction_woocommerce_39234 = new WP_Reproduction_WooCommerce_39234();

WP_CLI::add_command('repro', 'WP_Repro_39234_Command' );
