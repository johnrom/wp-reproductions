<?php
/*
Plugin Name: WP Reproduction - WooCommerce PR 39234
Plugin URI: https://github.com/woocommerce/woocommerce/pull/39234
Description: Reproducing https://github.com/woocommerce/woocommerce/pull/39234
Author: John Rom, Nimblelight
Version: 1.0.0
Author URI: https://nmbl.lt/
*/

namespace Johnrom\WpReproWoocommerce39234;

require_once __DIR__ . '/vendor/autoload.php';

if ( defined( 'WP_CLI' ) && constant( 'WP_CLI' ) ) {
	\WP_CLI::add_command('repro_39234', 'Johnrom\WpReproWoocommerce39234\WP_Repro_39234_Command' );
}
