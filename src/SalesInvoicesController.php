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
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'required'          => true,
					],
					'administration_id' => [
						'description'       => 'Moneybird administration ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'required'          => true,
					],
					'contact_id'        => [
						'description'       => 'Moneybird contact ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'required'          => true,
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
		$administration_id = $request->get_param( 'administration_id' );
		$contact_id        = $request->get_param( 'contact_id' );

		$request_data = [
			'sales_invoice' => [
				'reference'          => null,
				'contact_id'         => $contact_id,
				'details_attributes' => [
					[
						'description' => 'Rocking Chair',
						'price'       => 129.95,
					],
				],
			],
		];

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

		// OK.
		$request = new WP_REST_Request( 'POST', '/pronamic-moneybird/v1/sales-invoices' );

		/**
		 * REST API.
		 *
		 * @link https://github.com/WordPress/WordPress/blob/6.4/wp-includes/rest-api/class-wp-rest-server.php#L366-L372
		 */
		if ( \array_key_exists( 'sales_invoice', $_POST ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitization is handled by WordPress REST API.
			$request->set_body_params( \wp_unslash( $_POST['sales_invoice'] ) );
		}

		$response = \rest_do_request( $request );

		if ( $response->is_error() ) {
			$this->error = $response->as_error();

			return;
		}

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
