<?php

class WP_Repro_39234_Command {

    /**
     * Populate a shop order with two coupons.
     *
     * ## EXAMPLES
     *
     *     wp repro_39234 populate
     *
     * @when after_wp_load
     */
	public function create_order() {
		// install wc via composer...
		$order = new WC_Order( 0 );
		$order->set_customer_id( 1 );

		$order_item_1 = new WC_Order_Item_Product(); // get product post id
		$coupon_line_item_1 = new WC_Order_Item_Coupon(); // get coupon post id
		$coupon_line_item_2 = new WC_Order_Item_Coupon(); // get coupon post id

		$order->add_items( array(
			$order_item_1,
		) );
		$order->calculate_totals();
		$order->save();

		$order->apply_coupon( $coupon_line_item_1 );
		$order->apply_coupon( $coupon_line_item_2 );
	}
}
