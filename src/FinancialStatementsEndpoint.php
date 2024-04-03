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

/**
 * Financial statements endpoint class
 */
final class FinancialStatementsEndpoint extends ResourceEndpoint {
	/**
	 * Create financial statement.
	 * 
	 * @param FinancialStatement $financial_statement Financial statement.
	 * @return
	 */
	public function create( FinancialStatement $financial_statement ) {
		$url = $this->get_api_url( 'financial_statements' );

		$data = [
			'financial_statement' => $financial_statement,
		];

		$response = $this->client->post( $url, $data );

		$response_status = (string) $response->status();
		$response_data   = $response->json();

		if ( '201' !== $response_status ) {
			throw new \Exception( '' );
		}
	}
}
