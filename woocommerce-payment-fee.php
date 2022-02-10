<?php
/**
 * WooCommerce Payment Fee
 *
 * @package           WooCommercePaymentFee
 * @author            Justin Givens
 * @copyright         2022 Justin Givens & Image in a Box
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Payment Fee
 * Plugin URI:        https://github.com/justingivens/woocommerce-payment-fee
 * Description:       Simple plugin for WooCommerce to allow a site to add a convenience fee to cover the cost of the online payment platforms like square, stripe, etc..
 * Version:           0.0.4
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Justin Givens
 * Author URI:        https://github.com/justingivens/woocommerce-payment-fee
 * Text Domain:       woocommerce-payment-fee
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://github.com/justingivens/woocommerce-payment-fee
 */

namespace Nstore\Addons;


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fee class
 *
 * @since          0.0.1
 * @package        nstore.us/plugins
 * @subpackage     nstore.us/plugins/customization
 * @author         Image in a Box <support@imageinabox.com>
 */
if ( ! class_exists( 'Fee' ) ) {

	/**
	 * Class Fee
	 * @package Nstore\Addons
	 */
	class Fee {

		/** @var float Payment Platform Percentage Fee */
		private $percentage_fee = 0.029; //2.9% fee!

		/** @var float Payment Platform Fixed Fee */
		private $fixed_fee = 0.30; // $0.30

		/** @var null|Fee */
		protected static $_instance = null;

		/**
		 * Class Loader
		 *
		 * @since        0.0.1
		 *
		 */
		public function load() {

			add_action( 'woocommerce_cart_calculate_fees', [ $this, 'surcharge' ] );
		}

		/**
		 * Adds the Surcharge to the cart
		 *
		 * @since        0.0.1
		 *
		 */
		public function surcharge() {

			//Allow the themes to disable the platform
			if ( apply_filters( 'nstore_platform_fee_enabled', true ) ) {

				global $woocommerce;

				if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
					return;
				}

				$cart_total_after_discounts = floatval( $woocommerce->cart->get_cart_contents_total() );
				if( $cart_total_after_discounts === 0.00 ) {
					return;
				}

				$surcharge = round( ( ( $cart_total_after_discounts + $this->fixed_fee ) / ( 1 - $this->percentage_fee ) ), 2 );
				$surcharge = $surcharge - $cart_total_after_discounts;

				$woocommerce->cart->add_fee( 'Convenience Fee', $surcharge );

			}
		}

		/**
		 * Ensures only one instance of Fee is loaded or can be loaded.
		 *
		 * @return Fee|null
		 * @since  0.0.1
		 *
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}

	/**
	 * Global function
	 *
	 * @return Fee|null
	 * @since  0.0.1
	 *
	 */
	function fee() {
		return Fee::instance();
	}

	fee()->load();
}

