<?php
/**
 * Financial statements controller
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
 * Financial statements controller class
 */
class FinancialStatementsController {
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
		\add_action( 'init', [ $this, 'maybe_create_new_financial_statement' ] );

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
			'/financial-statements',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'rest_api_new_financial_statement' ],
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
					'financial_statement' => [
						'description' => \__( 'Moneybird financial statement.', 'pronamic-moneybird' ),
						'type'        => 'object',
						'required'    => true,
						'properties'  => [
							'financial_account_id' => [
								'description'       => \__( 'Moneybird financial account ID.', 'pronamic-moneybird' ),
								'type'              => 'integer',
								'sanitize_callback' => 'absint',
								'required'          => true,
							],
							'reference'          => [
								'description' => \__( 'Reference.', 'pronamic-moneybird' ),
								'type'        => 'string',
							],
							'official_date'          => [
								'description' => \__( 'Official date.', 'pronamic-moneybird' ),
								'type'        => 'string',
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
	 * REST API new financial statement.
	 * 
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function rest_api_new_financial_statement( WP_REST_Request $request ) {
		$authorization_id  = $request->get_param( 'authorization_id' );
		$administration_id = $request->get_param( 'administration_id' );

		$financial_statement = $request->get_param( 'financial_statement' );

		$request_data = [
			'financial_statement' => $financial_statement,
		];

		$api_token = \get_post_meta( $authorization_id, '_pronamic_moneybird_api_token', true );

		$api_url = \strtr(
			'https://moneybird.com/api/:version/:administration_id/:resource_path.:format',
			[
				':version'           => 'v2',
				':administration_id' => $administration_id,
				':resource_path'     => 'financial_statements',
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

		$response_status = (string) $response->status();
		$response_data   = $response->json();

		if ( '201' === $response_status ) {
			\do_action( 'pronamic_moneybird_financial_statement_created', $response_data );
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

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_moneybird_create_financial_statement' ) ) {
			return;
		}

		$request = new WP_REST_Request( 'POST', '/pronamic-moneybird/v1/financial-statements' );

		if ( isset( $_POST['authorization_id'] ) ) {
			$request->set_param( 'authorization_id', \sanitize_text_field( \wp_unslash( $_POST['authorization_id'] ) ) );
		}

		if ( isset( $_POST['administration_id'] ) ) {
			$request->set_param( 'administration_id', \sanitize_text_field( \wp_unslash( $_POST['administration_id'] ) ) );
		}

		if ( isset( $_POST['financial_statement'] ) ) {
			$data = \map_deep( $_POST['financial_statement'], 'sanitize_text_field' );

			if ( \array_key_exists( 'financial_mutations_attributes', $data ) && \is_array( $data['financial_mutations_attributes'] ) ) {
				$data['financial_mutations_attributes'] = \array_filter(
					\array_map(
						function ( $data ) {
							return \is_array( $data ) ? \array_filter( $data ) : $data;
						},
						$data['financial_mutations_attributes']
					)
				);
			}

			$request->set_param( 'financial_statement', $data );
		}

		$response = \rest_do_request( $request );

		if ( $response->is_error() ) {
			$this->error = $response->as_error();

			return;
		}

		$url = \add_query_arg(
			[
				'pronamic_moneybird_financial_statement_created' => true,
			],
			\wp_get_referer()
		);

		\wp_safe_redirect( $url );

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
