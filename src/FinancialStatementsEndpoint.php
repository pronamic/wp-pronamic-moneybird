<?php
/**
 * Financial statements endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Exception;

/**
 * Financial statements endpoint class
 */
final class FinancialStatementsEndpoint extends ResourceEndpoint {
	/**
	 * Array filter null.
	 * 
	 * @param array
	 * @return array
	 */
	public static function array_filter_null( $items ) {
		return \array_filter(
			$items,
			function ( $item ) {
				return ( null !== $item );
			}
		);
	}

	/**
	 * Create financial statement.
	 * 
	 * @link https://developer.moneybird.com/api/financial_statements/#post_financial_statements
	 * @param FinancialStatement $financial_statement Financial statement.
	 * @return FinancialStatement
	 */
	public function create( FinancialStatement $financial_statement ) {
		$url = $this->get_api_url( 'financial_statements' );

		$data = [
			'financial_statement' => $financial_statement->get_create_parameters(),
		];

		$response = $this->client->post( $url, $data );

		$response_status = (string) $response->status();
		$response_data   = $response->json();

		if ( '201' !== $response_status ) {
			$http_exception = new Exception( 'Unexpected HTTP response: ' . $response_status, (int) $response_status );

			throw Error::from_response_object( $response_data, (int) $response_status, $http_exception );
		}

		return FinancialStatement::from_object( $response_data );
	}
}
