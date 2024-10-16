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

use Pronamic\WooSubscriptionsPeriod\Period as WooPeriod;
use WP_CLI;
use WC_Order;
use WC_Product;
use WC_Product_Subscription;
use WC_Order_Item_Product;
use WP_Post;

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

		\add_action( 'init', [ $this, 'init' ] );

		\add_action( 'admin_init', [ $this, 'admin_init' ] );

		\add_action( 'cli_init', [ $this, 'cli_init' ] );

		if ( \is_admin() ) {
			\add_action( 'add_meta_boxes', [ $this, 'maybe_add_pronamic_moneybird_meta_box_to_wc_order' ], 10, 2 );
		}

		\add_action( 'woocommerce_process_shop_order_meta', [ $this, 'process_shop_order_meta' ] );

		// Orders as CPT.
		\add_filter( 'manage_edit-shop_order_columns', [ $this, 'add_column' ], 20 );
		\add_action(
			'manage_shop_order_posts_custom_column',
			function ( $column_name, $post_id ) {
				$order = \wc_get_order( $post_id );

				if ( $order instanceof WC_Order ) {
					$this->show_column( $column_name, $order );
				}
			},
			5,
			2 
		);

		// Orders with HPOS.
		\add_filter( 'manage_woocommerce_page_wc-orders_columns', [ $this, 'add_column' ], 20 );
		\add_action( 'manage_woocommerce_page_wc-orders_custom_column', [ $this, 'show_column' ], 5, 2 );
	}

	/**
	 * Initialize.
	 * 
	 * @return void
	 */
	public function init() {
		\add_post_type_support( 'product', 'pronamic_moneybird_product' );

		\register_setting(
			'pronamic_moneybird',
			'pronamic_moneybird_woocommerce_tax_rates',
			[
				'type' => 'array',
			]
		);
	}

	/**
	 * Admin initialize.
	 */
	public function admin_init() {
		\add_settings_section(
			'pronamic_moneybird_woocommerce',
			\__( 'WooCommerce', 'pronamic-moneybird' ),
			function () {
			},
			'pronamic_moneybird'
		);

		\add_settings_field(
			'pronamic_moneybird_woocommerce_tax_rates',
			\__( 'Tax rates', 'pronamic-moneybird' ),
			function () {
				include __DIR__ . '/../admin/settings-field-woocommerce-tax-rates.php';
			},
			'pronamic_moneybird',
			'pronamic_moneybird_woocommerce'
		);
	}

	/**
	 * WP-CLI initialize.
	 * 
	 * @link https://github.com/wp-cli/wp-cli/blob/9aec20fd711a8b7442cc2f89e32af276e3f16045/php/WP_CLI/Runner.php#L1724
	 * @return void
	 */
	public function cli_init() {
		WP_CLI::add_command( 'pronamic-moneybird create-contacts-for-wc-orders', [ $this, 'cli_create_contacts_for_wc_orders' ] );
		WP_CLI::add_command( 'pronamic-moneybird create-external-sales-invoices-for-wc-orders', [ $this, 'cli_create_external_sales_invoices_for_wc_orders' ] );
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
				'limit' => 1,
			]
		);

		/**
		 * Moneybird client.
		 */
		$authorization_id  = (int) \get_option( 'pronamic_moneybird_authorization_post_id' );
		$administration_id = ( 0 === $authorization_id ) ? 0 : (int) \get_post_meta( $authorization_id, '_pronamic_moneybird_administration_id', true );

		$api_token = \get_post_meta( $authorization_id, '_pronamic_moneybird_api_token', true );

		$client = new Client( $api_token );

		$administration_endpoint = $client->get_administration_endpoint( $administration_id );

		$contacts_endpoint = $administration_endpoint->get_contacts_endpoint();

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
					'wc-completed',
					'wc-refunded',
				],
				'type'       => 'shop_order',
				'limit'      => $assoc_args['limit'],
				'meta_query' => [
					[
						'key'     => '_pronamic_moneybird_contact_id',
						'compare' => 'NOT EXISTS',
					],
				],
				'orderby'    => 'date',
				'order'      => 'ASC',
			]
		);

		WP_CLI::log( 'Orders: ' . \count( $orders ) );

		foreach ( $orders as $order ) {
			WP_CLI::log( 'Order: ' . $order->get_id() );

			try {
				WP_CLI::log( $order->get_edit_order_url() );

				$contact = $this->new_contact_based_on_woocommerce_order( $order );

				$moneybird_contacts = $this->get_similar_contacts( $contacts_endpoint, $contact );

				if ( \count( $moneybird_contacts ) > 0 ) {
					WP_CLI::log(
						\sprintf(
							'Similar Moneybird contacts have been found for: %s',
							\wp_json_encode( $contact->remote_serialize(), \JSON_PRETTY_PRINT )
						)
					);

					$items = [];

					foreach ( $moneybird_contacts as $moneybird_contact ) {
						$similarity_report = $contact->get_similarity_report( $moneybird_contact );

						if ( $similarity_report->is_perfect_match() ) {
							$order->update_meta_data( '_pronamic_moneybird_contact_id', $moneybird_contact->id );

							$order->save();

							continue 2;
						}

						$items[] = [
							'id'                     => $moneybird_contact->id,
							'company_name'           => $moneybird_contact->company_name,
							'address_1'              => $moneybird_contact->address_1,
							'address_2'              => $moneybird_contact->address_2,
							'zip_code'               => $moneybird_contact->zip_code,
							'city'                   => $moneybird_contact->city,
							'country_code'           => $moneybird_contact->country_code,
							'send_invoices_to_email' => $moneybird_contact->send_invoices_to_email,
							'similarity'             => $similarity_report->get_average_similarity(),
						];
					}

					\WP_CLI\Utils\format_items(
						'table',
						$items,
						[
							'id',
							'company_name',
							'address_1',
							'address_2',
							'zip_code',
							'city',
							'country_code',
							'send_invoices_to_email',
							'similarity',
						]
					);

					$is_in_list = $this->cli_confirm( 'Is the contact in the list above?' );

					if ( true === $is_in_list ) {
						WP_CLI::error( 'The contact is in the list, this path still needs to be implemented.' );
					}

					$create_confirmed = $this->cli_confirm( 'Are you sure that a new Moneybird contact needs to be created?' );

					if ( false === $create_confirmed ) {
						WP_CLI::confirm( 'Do you want to continue creating contacts for other WooCommerce orders?', $assoc_args );

						continue;
					}
				}

				$contact = $this->create_contact_based_on_woocommerce_order( $contacts_endpoint, $order );

				WP_CLI::log( $contact->get_remote_link() );
			} catch ( \Exception $e ) {
				WP_CLI::error( $e->getMessage(), false );

				WP_CLI::confirm( 'Do you want to continue creating contacts for other WooCommerce orders?', $assoc_args );
			}
		}
	}

	/**
	 * CLI confirm.
	 *
	 * @link https://github.com/wp-cli/wp-cli/blob/0ca6d920123ac904c918d69181edc5071dc92c9d/php/class-wp-cli.php#L963-L991
	 * @param string $question Question.
	 * @return bool
	 */
	private function cli_confirm( $question ) {
		\fwrite( \STDOUT, $question . ' [y/n] ' );

		$answer = strtolower( trim( fgets( STDIN ) ) );

		$result = ( 'y' === $answer );

		return $result;
	}

	/**
	 * This method tries to create a contact based on a WooCommerce order.
	 * 
	 * @link https://developer.moneybird.com/api/contacts/#get_contacts
	 * @param ContactsEndpoint $contacts_endpoint Contacts endpoint.
	 * @param WC_Order         $order             WooCommerce order.
	 * @return Contact
	 * @throws \Exception Throws an exception if contact creation fails.
	 */
	private function create_contact_based_on_woocommerce_order( $contacts_endpoint, WC_Order $order ) {
		$user = $order->get_user();

		if ( false !== $user ) {
			$value = \get_user_meta( $user->ID, '_pronamic_moneybird_contact_id', true );

			if ( '' !== $value ) {
				throw new \Exception( 'Found Moneybird contact ID in user meta: ' . \esc_html( $order->get_id() ) );
			}
		}

		$contact = $this->new_contact_based_on_woocommerce_order( $order );

		$contact = $contacts_endpoint->create_contact( $contact );

		$order->update_meta_data( '_pronamic_moneybird_contact_id', $contact->id );

		$order->save();

		if ( false !== $user ) {
			\update_user_meta( $user->ID, '_pronamic_moneybird_contact_id', $contact->id );
		}

		return $contact;
	}

	/**
	 * Get similar contacts.
	 * 
	 * @link https://developer.moneybird.com/api/contacts/#get_contacts
	 * @param ContactsEndpoint $contacts_endpoint Contacts endpoint.
	 * @param Contact          $contact           Moneybird contact.
	 * @return array
	 */
	private function get_similar_contacts( $contacts_endpoint, Contact $contact ) {
		/**
		 * Step 1: Query Moneybird contacts to check whether contacts are found.
		 * 
		 * @link https://github.com/pronamic/pronamic.shop/issues/48#issuecomment-2045339077
		 * @link https://developer.moneybird.com/api/contacts/#get_contacts
		 */
		$search_terms = '';

		if ( null !== $contact->company_name ) {
			$search_terms = $contact->company_name;
		}

		if ( null === $contact->company_name ) {
			$first_name = $contact->first_name ?? '';
			$last_name  = $contact->last_name ?? '';

			$search_terms = \trim( $first_name . ' ' . $last_name );
		}

		if ( '' === $search_terms ) {
			throw new \Exception( 'Contact search query is empty for contact: ' . \esc_html( \wp_json_encode( $contact->remote_serialize(), \JSON_PRETTY_PRINT ) ) );
		}

		$moneybird_contacts = $contacts_endpoint->get_contacts(
			[
				'query' => $search_terms,
			]
		);

		return $moneybird_contacts;
	}

	/**
	 * This method creates a new contact based on a WooCommerce order.
	 * 
	 * @param WC_Order $order WooCommerce order.
	 * @return Contact
	 */
	private function new_contact_based_on_woocommerce_order( WC_Order $order ) {
		$contact = new Contact();

		$company_name = $order->get_billing_company();
		$address_1    = $order->get_billing_address_1();
		$address_2    = $order->get_billing_address_2();

		$contact->company_name = ( '' === $company_name ) ? null : $company_name;
		$contact->address_1    = ( '' === $address_1 ) ? null : $address_1;
		$contact->address_2    = ( '' === $address_2 ) ? null : $address_2;
		$contact->zip_code     = $order->get_billing_postcode();
		$contact->city         = $order->get_billing_city();
		$contact->country_code = $order->get_billing_country();
		$contact->phone        = $order->get_billing_phone();
		$contact->customer_id  = \strtr(
			\get_option( 'pronamic_moneybird_customer_id_template', '{customer_id}' ),
			[
				'{customer_id}' => $order->get_customer_id(),
				'{user_id}'     => $order->get_user_id(),
			]
		);

		/**
		 * Tax number.
		 * 
		 * @link https://github.com/pronamic/woocommerce-eu-vat-number/blob/615b15d02888209137a6ba4e95a1e8c1181e834b/includes/wc-eu-vat-functions.php#L8-L27
		 */
		$contact->tax_number = null;

		if ( \function_exists( '\wc_eu_vat_get_vat_from_order' ) ) {
			$value = \wc_eu_vat_get_vat_from_order( $order );

			if ( '' !== $value ) {
				$contact->tax_number = $value;
			}
		}

		$contact->first_name                  = $order->get_billing_first_name();
		$contact->last_name                   = $order->get_billing_last_name();
		$contact->chamber_of_commerce         = null;
		$contact->bank_account                = null;
		$contact->send_invoices_to_attention  = null;
		$contact->send_invoices_to_email      = $order->get_billing_email();
		$contact->send_estimates_to_attention = null;
		$contact->send_estimates_to_email     = null;
		$contact->sepa_active                 = null;

		/**
		 * IBAN.
		 *
		 * Pronamic Pay.
		 *
		 * @link https://github.com/pronamic/wp-pay-core/blob/6bb82841cb2059e1eceedbafac31d73e5493c4c0/src/Payments/PaymentInfo.php#L130-L135
		 */
		$contact->sepa_iban              = null;
		$contact->sepa_iban_account_name = null;
		$contact->sepa_bic               = null;

		if ( \function_exists( '\get_pronamic_payment' ) ) {
			$payment_id = (int) $order->get_meta( '_pronamic_payment_id' );

			$payment = \get_pronamic_payment( $payment_id );

			if ( null !== $payment ) {
				$consumer_bank_details = $payment->get_consumer_bank_details();

				if ( null !== $consumer_bank_details ) {
					$contact->sepa_iban              = $consumer_bank_details->get_iban();
					$contact->sepa_iban_account_name = $consumer_bank_details->get_name();
					$contact->sepa_bic               = $consumer_bank_details->get_bic();
				}
			}
		}

		$contact->sepa_mandate_id      = null;
		$contact->sepa_mandate_date    = null;
		$contact->sepa_sequence_type   = null;
		$contact->si_identifier_type   = null;
		$contact->si_identifier        = null;
		$contact->invoice_workflow_id  = null;
		$contact->estimate_workflow_id = null;
		$contact->email_ubl            = null;
		$contact->direct_debit         = null;
		$contact->custom_fields        = [];

		/**
		 * Contact person.
		 * 
		 * Please note: a contact without a company name is a private individual
		 * and cannot contain a contact person.
		 */
		if ( '' !== $company_name ) {
			$contact->contact_person = new ContactPerson( $contact->first_name, $contact->last_name );
		}

		return $contact;
	}

	/**
	 * Format price with no HTML.
	 * 
	 * @param float $price Price.
	 * @param array $args  Arguments.
	 * @return string
	 */
	private function format_price_no_html( $price, $args = [] ) {
		return \html_entity_decode(
			\wp_strip_all_tags(
				\wc_price( $price, $args )
			)
		);
	}

	/**
	 * WP-CLI create external sales invoices for WooCommerce orders.
	 * 
	 * @param array $args       Arguments.
	 * @param array $assoc_args Associative arguments.
	 * @return void
	 * @throws \Exception Throws an exception if external sales invoice creation fails.
	 */
	public function cli_create_external_sales_invoices_for_wc_orders( $args, $assoc_args ) {
		$assoc_args = \wp_parse_args(
			$assoc_args,
			[
				'limit' => 1,
			]
		);

		/**
		 * Moneybird client.
		 */
		$authorization_id  = (int) \get_option( 'pronamic_moneybird_authorization_post_id' );
		$administration_id = ( 0 === $authorization_id ) ? 0 : (int) \get_post_meta( $authorization_id, '_pronamic_moneybird_administration_id', true );

		$api_token = \get_post_meta( $authorization_id, '_pronamic_moneybird_api_token', true );

		$client = new Client( $api_token );

		$administration_endpoint = $client->get_administration_endpoint( $administration_id );

		$external_sales_invoices_endpoint = $administration_endpoint->get_external_sales_invoices_endpoint();

		/**
		 * Tax.
		 */
		$tax_rates = get_option( 'pronamic_moneybird_woocommerce_tax_rates' );
		$tax_rates = is_array( $tax_rates ) ? $tax_rates : [];

		$inc_tax = false;

		/**
		 * WooCommerce orders.
		 * 
		 * @link https://github.com/woocommerce/woocommerce/wiki/HPOS:-new-order-querying-APIs
		 * @link https://developer.wordpress.org/reference/classes/wp_query/#custom-field-post-meta-parameters
		 * @link https://wordpress.stackexchange.com/questions/337852/query-woocommerce-orders-where-meta-data-does-not-exist
		 */
		$orders = \wc_get_orders(
			[
				'status'         => [
					'wc-completed',
					'wc-refunded',
				],
				'type'           => 'shop_order',
				'date_completed' => '>=2024-01-01',
				'limit'          => $assoc_args['limit'],
				'meta_query'     => [
					[
						'key'     => '_pronamic_moneybird_external_sales_invoice_id',
						'compare' => 'NOT EXISTS',   
					],
				],
				'orderby'        => 'date',
				'order'          => 'ASC',
			]
		);

		WP_CLI::log( 'Orders: ' . \count( $orders ) );

		foreach ( $orders as $order ) {
			WP_CLI::log( 'Order: ' . $order->get_id() );

			WP_CLI::log( $order->get_edit_order_url() );

			$wcpdf_invoice = \wcpdf_get_invoice( $order, true );

			$external_sales_invoice = new ExternalSalesInvoice();

			$external_sales_invoice->contact_id          = $order->get_meta( '_pronamic_moneybird_contact_id' );
			$external_sales_invoice->reference           = $wcpdf_invoice->get_number()->get_formatted();
			$external_sales_invoice->date                = $wcpdf_invoice->get_date()->format( 'Y-m-d' );
			$external_sales_invoice->currency            = $order->get_currency();
			$external_sales_invoice->prices_are_incl_tax = $inc_tax;
			$external_sales_invoice->source              = \get_bloginfo( 'name' );
			$external_sales_invoice->source_url          = $order->get_edit_order_url();

			$external_sales_invoice->details = [];

			$subscriptions = [];

			if ( \function_exists( '\wcs_get_subscriptions_for_order' ) ) {
				$subscriptions = \wcs_get_subscriptions_for_order( $order );
			}

			foreach ( $order->get_items() as $item ) {
				$detail = new ExternalSalesInvoiceDetail();

				$detail->description = $item->get_name();
				$detail->amount      = $item->get_quantity();
				$detail->price       = $order->get_item_subtotal( $item, $inc_tax, true );

				if ( $item instanceof WC_Order_Item_Product ) {
					$product = $item->get_product();

					if ( false !== $product ) {
						$ledger_account_id = $product->get_meta( '_pronamic_moneybird_ledger_account_id' );

						if ( '' !== $ledger_account_id ) {
							$detail->ledger_account_id = $ledger_account_id;
						}
					}

					if ( $product instanceof WC_Product_Subscription ) {
						foreach ( $subscriptions as $subscription ) {
							if ( $subscription->has_product( $product->get_id() ) ) {
								$start_date = $order->get_date_completed();

								$timestamp = \wcs_add_time(
									$subscription->get_billing_interval(),
									$subscription->get_billing_period(),
									$start_date->getTimestamp()
								);

								$end_date = new Date( '@' . $timestamp );
								$end_date = $end_date->modify( '-1 day' );

								$detail->period = new Period( $start_date, $end_date );
							}
						}
					}
				}

				/**
				 * Pronamic period information for Woo Subscriptions.
				 * 
				 * @link https://github.com/pronamic/pronamic-woocommerce-subscriptions-period
				 * @link https://github.com/pronamic/wp-pronamic-moneybird/issues/9
				 */
				if ( \class_exists( WooPeriod::class ) ) {
					$woo_period = WooPeriod::from_woocommerce_order_item( $item );

					if ( null !== $woo_period ) {
						$detail->period = new Period(
							$woo_period->start_date,
							$woo_period->end_date->modify( '-1 day' )
						);
					}
				}

				if ( \method_exists( $item, 'get_taxes' ) ) {
					$taxes = $item->get_taxes();

					if ( ! \array_key_exists( 'total', $taxes ) ) {
						throw new \Exception( 'Order item taxes data does not contain total.' );
					}

					$totals = $taxes['total'];

					$values = \array_filter( $totals );

					if ( \count( $values ) > 1 ) {
						throw new \Exception( 'Moneybird does not support multiple tax rates per line.' );
					}

					$rate_id = \array_key_first( $values );

					if ( \array_key_exists( $rate_id, $tax_rates ) ) {
						$detail->tax_rate_id = $tax_rates[ $rate_id ];
					}
				}

				$external_sales_invoice->details[] = $detail;

				/**
				 * Discount.
				 * 
				 * @link https://github.com/woocommerce/woocommerce/blob/deef144a433ae8765b01883ff13fad221d98c918/plugins/woocommerce/includes/admin/meta-boxes/views/html-order-item.php#L102-L117
				 */
				if ( $item->get_subtotal() !== $item->get_total() ) {
					$detail_discount = clone $detail;

					$detail_discount->description = '└─ _' . \sprintf(
						\__( 'Discount', 'pronamic-moneybird' ),
						$this->format_price_no_html(
							\wc_format_decimal( $item->get_subtotal() - $item->get_total(), '' ),
							[
								'currency' => $order->get_currency(),
							]
						)
					) . '_';

					$detail_discount->amount = null;
					$detail_discount->price  = $item->get_total() - $item->get_subtotal();

					$external_sales_invoice->details[] = $detail_discount;
				}
			}

			/**
			 * Create remote.
			 */
			$external_sales_invoice = $external_sales_invoices_endpoint->create_external_sales_invoice( $external_sales_invoice );

			WP_CLI::log( $external_sales_invoice->get_remote_link() );

			$order->update_meta_data( '_pronamic_moneybird_external_sales_invoice_id', $external_sales_invoice->id );

			$order->save();

			$attachment = new Attachment(
				$wcpdf_invoice->get_filename(),
				$wcpdf_invoice->get_pdf(),
				'application/pdf'
			);

			$external_sales_invoices_endpoint->add_attachment_to_external_sales_invoice( $external_sales_invoice, $attachment );
		}
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
				\wp_nonce_field( 'pronamic_moneybird_save_wc_order', 'pronamic_moneybird_nonce' );

				include __DIR__ . '/../admin/meta-box-woocommerce-order.php';
			},
			$post_type_or_screen_id,
			'advanced',
			'default'
		);
	}

	/**
	 * Process shop order meta.
	 * 
	 * @link https://github.com/woocommerce/woocommerce/blob/deef144a433ae8765b01883ff13fad221d98c918/plugins/woocommerce/includes/admin/class-wc-admin-meta-boxes.php#L255-L263
	 * @link https://github.com/woocommerce/woocommerce/blob/deef144a433ae8765b01883ff13fad221d98c918/plugins/woocommerce/includes/admin/meta-boxes/class-wc-meta-box-order-data.php#L632-L771
	 * @param int $order_id Order ID.
	 */
	public function process_shop_order_meta( $order_id ) {
		if ( ! \array_key_exists( 'pronamic_moneybird_nonce', $_POST ) ) {
			return;
		}

		$nonce = \sanitize_key( $_POST['pronamic_moneybird_nonce'] );

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_moneybird_save_wc_order' ) ) {
			return;
		}

		$order = \wc_get_order( $order_id );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$keys = [
			'_pronamic_moneybird_contact_id',
			'_pronamic_moneybird_external_sales_invoice_id',
		];

		foreach ( $keys as $key ) {
			if ( ! \array_key_exists( $key, $_POST ) ) {
				continue;
			}

			$value = \sanitize_text_field( \wp_unslash( $_POST[ $key ] ) );

			if ( '' === $value ) {
				$order->delete_meta_data( $key );
			}

			if ( '' !== $value ) {
				$order->update_meta_data( $key, $value );
			}
		}

		$order->save();
	}

	/**
	 * Add column.
	 *
	 * @param string[] $columns Columns.
	 * @return string[]
	 */
	public function add_column( $columns ) {
		$columns['pronamic_moneybird'] = \__( 'Moneybird', 'pronamic-moneybird' );

		$new_columns = [];

		foreach ( $columns as $name => $label ) {
			if ( \in_array( $name, [ 'order_total' ], true ) ) {
				$new_columns['pronamic_moneybird'] = $columns['pronamic_moneybird'];
			}

			$new_columns[ $name ] = $label;
		}

		$columns = $new_columns;

		return $columns;
	}

	/**
	 * Show column.
	 *
	 * @link https://github.com/woocommerce/woocommerce/blob/9888ee7f4bb8e3170cb7345c1a5c8603f316de35/plugins/woocommerce/src/Internal/Admin/Orders/ListTable.php#L186-L194
	 * @param string   $column_name Column name.
	 * @param WC_Order $order       Order.
	 */
	public function show_column( $column_name, $order ) {
		if ( 'pronamic_moneybird' !== $column_name ) {
			return;
		}

		$authorization_id  = (int) \get_option( 'pronamic_moneybird_authorization_post_id' );
		$administration_id = ( 0 === $authorization_id ) ? 0 : (int) \get_post_meta( $authorization_id, '_pronamic_moneybird_administration_id', true );

		$contact_id = $order->get_meta( '_pronamic_moneybird_contact_id' );

		if ( '' !== $contact_id ) {
			printf(
				'<a href="%s" title="%s"><span class="dashicons dashicons-businessperson"></span></a>',
				\esc_url(
					Contact::get_remote_link_by_id(
						$administration_id,
						$contact_id
					)
				),
				\esc_attr(
					\sprintf(
						/* translators: %s: Contact ID. */
						__( 'Contact ID: %s', 'pronamic-moneybird' ),
						$contact_id
					)
				)
			);
		}

		echo ' ';

		$external_sales_invoice_id = $order->get_meta( '_pronamic_moneybird_external_sales_invoice_id' );

		if ( '' !== $external_sales_invoice_id ) {
			printf(
				'<a href="%s" title="%s"><span class="dashicons dashicons-media-spreadsheet"></span></a>',
				\esc_url(
					ExternalSalesInvoice::get_remote_link_by_id(
						$administration_id,
						$external_sales_invoice_id
					)
				),
				\esc_attr(
					\sprintf(
						/* translators: %s: Product ID. */
						__( 'External sales invoice ID: %s', 'pronamic-moneybird' ),
						$external_sales_invoice_id
					)
				)
			);
		}
	}
}
