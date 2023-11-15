# Reproducing WooCommerce PR 39234

## NMBL WP Reproductions

https://github.com/woocommerce/woocommerce/pull/39234

> Calling `recalculate_coupons` on an order with two `fixed_cart` coupons with the same code will cause an exception.

```
Fatal error: Uncaught TypeError: Argument 1 passed to Automattic\WooCommerce\Utilities\StringUtil::is_null_or_whitespace() must be of the type string or null, array given, called in /var/www/html/wp-content/plugins/woocommerce/includes/class-wc-discounts.php on line 260 and defined in /var/www/html/wp-content/plugins/woocommerce/src/Utilities/StringUtil.php:104
Stack trace:
#0 /var/www/html/wp-content/plugins/woocommerce/includes/class-wc-discounts.php(260): Automattic\WooCommerce\Utilities\StringUtil::is_null_or_whitespace(Array)
#1 /var/www/html/wp-content/plugins/woocommerce/includes/abstracts/abstract-wc-order.php(1394): WC_Discounts->apply_coupon(Object(WC_Coupon), false)
#2 /var/www/html/wp-content/plugins/woocommerce-39234/src/WP_Repro_39234_Command.php(45): WC_Abstract_Order->recalculate_coupons()
#3 [internal function]: Johnrom\WpReproWoocommerce39234\WP_Repro_39234_Command->populate(Array, Array)
#4 phar:///usr/local/bin/wp/vendor/wp-cli/wp-cli/php/WP_CLI/Dispatcher/CommandFactory.php(100): call_user_func(Array, in /var/www/html/wp-content/plugins/woocommerce/src/Utilities/StringUtil.php on line 104
```

- [Reproducing the issue with NMBL WP Reproductions](#reproducing-the-issue-with-nmbl-wp-reproductions)
- [Reproducing the issue in another environment](#reproducing-the-issue-in-another-environment)

## Reproducing the issue with NMBL WP Reproductions

NMBL WP Reproductions uses [`@wordpress/env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/).

- Install Docker, NodeJS, PHP, and other requirements as described in [the `@wordpress/env` documentation](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/#prerequisites).
- Pull this repository and navigate to this folder (`repros/woocommerce-39234`) in a command line.
  - If you have installed `nvm`, run `npm run setup`.
  - Otherwise, run `npm install` and `composer install` with valid versions of NodeJS / NPM / PHP installed.
- Run `npm run populate`, which runs a WP-CLI command which reproduces the issue.
  - An error will be returned from `npm start`, or an exception breakpoint will be hit by `npm run debug`.
  - To see steps for reproducing the issue outside of NMBL WP Reproductions, see [Reproducing the issue in another environment](#reproducing-the-issue-in-another-environment).

### Debugging the project

- Run `npm run debug` to boot up `@wordpress/env` with `xdebug`.
- Run the `Woo PR 39234: Listen for XDebug` launch task in VS Code.
- In VS Code's debug breakpoints pane, check `Everything` to break on all exceptions, including the one targeted by this repro.

### Validating the fix

The fix can be applied in this repo by using `git LFS` and downloading the `woocommerce-with-fix.zip` file.

- Make sure `woocommerce-with-fix.zip` was downloaded with git LFS.
- Run `npm run apply-fix` and re-run `npm start` or `npm debug`.
  - This will extract the fixed version of WooCommerce and override the plugin in WP Env.
- Copy `.wp-env.override.with-fix.json` to `.wp-env.override.json`.
- Run `npm start` or `npm run debug` again to start the site with the fix.
- Run `npm run populate` to replay the command and generate an order with two of the same coupon.
- Log in with `admin/password` as described in [the `@wordpress/env` documentation](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env) to view the order.

## Reproducing the issue in another environment.

The relevant code snippet is here:

```php
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

// Add two coupon line items with the same coupon code.
$order->add_item( $coupon_order_item_1 );
$order->add_item( $coupon_order_item_2 );
$order->save();

// Recalculate totals and taxes.
// ERR:
// Argument 1 passed to Automattic\WooCommerce\Utilities\StringUtil::is_null_or_whitespace() must be of the type string or null, array given,
// called in /var/www/html/wp-content/plugins/woocommerce/includes/class-wc-discounts.php on line 260
// and defined in /var/www/html/wp-content/plugins/woocommerce/src/Utilities/StringUtil.php:104
$order->recalculate_coupons();
```
