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
	 * @param array $items Items.
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
	 * @throws Error Throws an exception if financial statement creation fails.
	 */
	public function create( FinancialStatement $financial_statement ) {
		$url = $this->get_api_url( 'financial_statements' );

		$data = [
			'financial_statement' => $financial_statement->get_create_parameters(),
		];

		$response = $this->client->post( $url, $data, '201' );

		$response_data = $response->json();

		return FinancialStatement::from_object( $response_data );
	}
}
