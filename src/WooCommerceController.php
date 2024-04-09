<?php
/**
 * WooCommerce controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use WP_CLI;
use WC_Order;

/**
 * WooCommerce controller class
 */
final class WooCommerceController {
	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		if ( ! \class_exists( 'woocommerce' ) ) {
			return;
		}

		\add_action( 'cli_init', [ $this, 'cli_init' ] );

		if ( \is_admin() ) {
			\add_action( 'add_meta_boxes', [ $this, 'maybe_add_pronamic_moneybird_meta_box_to_wc_order' ], 10, 2 );
		}
	}

	/**
	 * WP-CLI initialize.
	 * 
	 * @link https://github.com/wp-cli/wp-cli/blob/9aec20fd711a8b7442cc2f89e32af276e3f16045/php/WP_CLI/Runner.php#L1724
	 * @return void
	 */
	public function cli_init() {
		WP_CLI::add_command( 'pronamic-moneybird create-contacts-for-wc-orders', [ $this, 'cli_create_contacts_for_wc_orders' ] );
	}

	/**
	 * WP-CLI create contact for WooCommerce orders.
	 * 
	 * @param array $args       Arguments.
	 * @param array $assoc_args Associative arguments.
	 * @return void
	 */
	public function cli_create_contacts_for_wc_orders( $args, $assoc_args ) {
		$assoc_args = \wp_parse_args(
			$assoc_args,
			[
				'limit' => 10,
			]
		);

		/**
		 * WooCommerce orders.
		 * 
		 * @link https://github.com/woocommerce/woocommerce/wiki/HPOS:-new-order-querying-APIs
		 * @link https://developer.wordpress.org/reference/classes/wp_query/#custom-field-post-meta-parameters
		 * @link https://wordpress.stackexchange.com/questions/337852/query-woocommerce-orders-where-meta-data-does-not-exist
		 */
		$orders = \wc_get_orders(
			[
				'status'     => [
					'completed',
				],
				'limit'      => $assoc_args['limit'],
				'meta_query' => [
					[
						'key'     => '_pronamic_moneybird_contact_id',
						'compare' => 'NOT EXISTS',   
					],
				],
			]
		);

		foreach ( $orders as $order ) {
			WP_CLI::log( 'Order: ' . $order->get_id() );
		}
	}

	/**
	 * This method tries to create a contact based on a WooCommerce order.
	 * 
	 * @link https://developer.moneybird.com/api/contacts/#get_contacts
	 * @param WC_Order $order WooCommerce order.
	 */
	private function create_contact_based_on_woocommerce_order( WC_Order $order ) {
	}

	/**
	 * Maybe add a Pronamic Moneybird meta box the WooCommerce order.
	 * 
	 * @link https://github.com/pronamic/wp-pronamic-pay-woocommerce/issues/41
	 * @link https://developer.wordpress.org/reference/hooks/add_meta_boxes/
	 * @param string           $post_type_or_screen_id Post type or screen ID.
	 * @param WC_Order|WP_Post $post_or_order_object   Post or order object.
	 * @return void
	 */
	public function maybe_add_pronamic_moneybird_meta_box_to_wc_order( $post_type_or_screen_id, $post_or_order_object ) {
		if ( ! \in_array( $post_type_or_screen_id, [ 'shop_order', 'woocommerce_page_wc-orders' ], true ) ) {
			return;
		}

		$order = $post_or_order_object instanceof WC_Order ? $post_or_order_object : \wc_get_order( $post_or_order_object->ID );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		\add_meta_box(
			'woocommerce-order-pronamic-moneybird',
			\__( 'Pronamic Moneybird', 'pronamic-moneybird' ),
			function () use ( $order ) {
				include __DIR__ . '/../admin/meta-box-woocommerce-order.php';
			},
			$post_type_or_screen_id,
			'side',
			'default'
		);
	}
}
