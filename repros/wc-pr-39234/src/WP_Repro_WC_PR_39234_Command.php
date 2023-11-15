<?php

namespace Nimblelight\WP_Repro_WC_PR_39234;

class WP_Repro_WC_PR_39234_Command {
    /**
     * Populate a shop order with two coupons.
     *
     * ## EXAMPLES
     *
     *     wp repro_39234 populate
     *
     * @when after_wp_load
     */
	public function populate() {
		// Create a test product.
		$product = new \WC_Product_Simple();
		$product->set_name( 'Test Product' );
		$product->set_regular_price( '20.00' );
		$product->save();

		// Create a test coupon.
		$coupon = new \WC_Coupon();
		$coupon->set_code( 'test_coupon' );
		$coupon->set_amount( 5.0 );
		$coupon->save();

		// Create a test order under the super admin user.
		$order = new \WC_Order();
		$order->set_customer_id( 1 );
		$order->add_product( $product, 1 );

		$coupon_order_item_1 = new \WC_Order_item_Coupon();
		$coupon_order_item_1->set_code( 'test_coupon' );
		$coupon_order_item_1->set_discount( '5.00' );

		$coupon_order_item_2 = new \WC_Order_item_Coupon();
		$coupon_order_item_2->set_code( 'test_coupon' );
		$coupon_order_item_2->set_discount( '5.00' );

		$order->add_item( $coupon_order_item_1 );
		$order->add_item( $coupon_order_item_2 );
		$order->save();

		// Recalculate totals and taxes.
		$order->recalculate_coupons();
	}

    /**
     * Remove all products, coupons, and orders.
     *
     * ## EXAMPLES
     *
     *     wp repro_39234 clean
     *
     * @when after_wp_load
     */
	public function clean() {
		$posts_to_clean = get_posts( array(
			'post_type' => array( 'product', 'shop_coupon', 'shop_order' ),
			'post_status' => 'any',
			'posts_per_page' => -1,
		) );

		foreach ( $posts_to_clean as $post_to_clean ) {
			wp_delete_post( $post_to_clean->ID, true );
		}
	}
}
