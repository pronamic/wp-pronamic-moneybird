<?php
/**
 * Sales invoices controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Pronamic\WordPress\Http\Facades\Http;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Sales invoices controller class
 */
class SalesInvoicesController {
	/**
	 * Error.
	 * 
	 * @var WP_Error|null
	 */
	private $error;

	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		\add_action( 'init', [ $this, 'maybe_create_new_sales_invoice' ] );

		\add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );

		\add_filter( 'pronamic_moneybird_errors', [ $this, 'errors' ] );
	}

	/**
	 * REST API initialize.
	 *
	 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 */
	public function rest_api_init() {
		$namespace = 'pronamic-moneybird/v1';

		\register_rest_route(
			$namespace,
			'/sales-invoices',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'rest_api_new_sales_invoice' ],
				'permission_callback' => [ $this, 'permission_callback' ],
				'args'                => [
					'authorization_id'  => [
						'description'       => \__( 'Authorization post ID.', 'pronamic-moneybird' ),
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'required'          => true,
					],
					'administration_id' => [
						'description'       => \__( 'Moneybird administration ID.', 'pronamic-moneybird' ),
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'required'          => true,
					],
					'sales_invoice'     => [
						'description' => \__( 'Moneybird sales invoice.', 'pronamic-moneybird' ),
						'type'        => 'object',
						'required'    => true,
						'properties'  => [
							'contact_id'         => [
								'description'       => \__( 'Moneybird contact ID.', 'pronamic-moneybird' ),
								'type'              => 'integer',
								'sanitize_callback' => 'absint',
								'required'          => true,
							],
							'details_attributes' => [
								'description' => \__( 'Contains the sales invoice lines.', 'pronamic-moneybird' ),
								'type'        => 'array',
								'items'       => [
									'type'       => 'object',
									'properties' => [
										'description' => [
											'description' => \__( 'Description.', 'pronamic-moneybird' ),
											'type'        => 'string',
										],
										'price'       => [
											'description' => \__( 'Price.', 'pronamic-moneybird' ),
											'type'        => 'string',
										],
										'amount'      => [
											'description' => \__( 'Amount.', 'pronamic-moneybird' ),
											'type'        => 'string',
										],
										'product_id'  => [
											'description' => \__( 'Product ID.', 'pronamic-moneybird' ),
											'type'        => 'string',
										],
										'period'      => [
											'description' => \__( 'Period.', 'pronamic-moneybird' ),
											'type'        => 'string',
										],
									],
								],
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Permission callback.
	 * 
	 * @return bool
	 */
	public function permission_callback() {
		return \current_user_can( 'manage_options' );
	}

	/**
	 * REST API new sales invoice.
	 * 
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function rest_api_new_sales_invoice( WP_REST_Request $request ) {
		$authorization_id  = $request->get_param( 'authorization_id' );
		$administration_id = $request->get_param( 'administration_id' );

		$sales_invoice = $request->get_param( 'sales_invoice' );

		$request_data = [
			'sales_invoice' => $sales_invoice,
		];

		$api_token = \get_post_meta( $authorization_id, '_pronamic_moneybird_api_token', true );

		$api_url = \strtr(
			'https://moneybird.com/api/:version/:administration_id/:resource_path.:format',
			[
				':version'           => 'v2',
				':administration_id' => $administration_id,
				':resource_path'     => 'sales_invoices',
				':format'            => 'json',
			]
		);

		$response = Http::post(
			$api_url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $api_token,
					'Content-Type'  => 'application/json',
				],
				'body'    => \wp_json_encode( $request_data ),
			]
		);

		$response_data = $response->json();
		echo '<pre>';
		var_dump( $response_data );
		echo '</pre>';
		exit;
		if ( '201' === (string) $response->status() ) {
			$result = \preg_match_all(
				'/#subscription_(?P<subscription_id>[0-9]+)/',
				$line,
				$matches
			);

			if ( false === $result ) {
				throw new \Exception( 'Something went wrong finding subscription IDs in the Moneybird sales invoice detail description.' );
			}       
		}

		$result = [
			'api_url'  => $api_url,
			'request'  => $request_data,
			'response' => $response_data,
		];

		return \rest_ensure_response( $result );
	}

	/**
	 * Maybe create new sales invoice.
	 * 
	 * @return void
	 */
	public function maybe_create_new_sales_invoice() {
		if ( ! \array_key_exists( 'pronamic_moneybird_nonce', $_POST ) ) {
			return;
		}

		$nonce = \sanitize_key( $_POST['pronamic_moneybird_nonce'] );

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_moneybird_create_sales_invoice' ) ) {
			return;
		}

		$request = new WP_REST_Request( 'POST', '/pronamic-moneybird/v1/sales-invoices' );

		if ( isset( $_POST['authorization_id'] ) ) {
			$request->set_param( 'authorization_id', \sanitize_text_field( \wp_unslash( $_POST['authorization_id'] ) ) );
		}

		if ( isset( $_POST['administration_id'] ) ) {
			$request->set_param( 'administration_id', \sanitize_text_field( \wp_unslash( $_POST['administration_id'] ) ) );
		}

		if ( isset( $_POST['sales_invoice'] ) ) {
			$data = \map_deep( $_POST['sales_invoice'], 'sanitize_text_field' );

			if ( \array_key_exists( 'details_attributes', $data ) && \is_array( $data['details_attributes'] ) ) {
				$data['details_attributes'] = \array_filter(
					\array_map(
						function ( $data ) {
							return \is_array( $data ) ? \array_filter( $data ) : $data;
						},
						$data['details_attributes']
					)
				);
			}

			$request->set_param( 'sales_invoice', $data );
		}

		$response = \rest_do_request( $request );

		if ( $response->is_error() ) {
			$this->error = $response->as_error();

			return;
		}

		echo '<pre>';
		echo \wp_json_encode( $response, JSON_PRETTY_PRINT );
		echo '</pre>';

		exit;
	}

	/**
	 * Errors.
	 * 
	 * @param array $errors Errors.
	 * @return array
	 */
	public function errors( $errors ) {
		if ( null !== $this->error ) {
			$errors[] = $this->error;
		}

		return $errors;
	}
}
