<?php
/**
 * Financial mutations endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Financial mutations endpoint class
 */
final class FinancialMutationsEndpoint extends ResourceEndpoint {
	/**
	 * Get financial mutations.
	 *
	 * @link https://developer.moneybird.com/api/financial-mutations#list-all-financial-mutations
	 * @param array $parameters Parameters.
	 * @return FinancialMutation[]
	 * @throws Error Throws an exception if get external sales invoices fails.
	 */
	public function get_financial_mutations( array $parameters = [] ) {
		$url = $this->get_api_url( 'financial_mutations' );

		$response = $this->client->get( $url, $parameters, '200' );

		$response_data = $response->json();

		$financial_mutations = \array_map(
			function ( $item ) {
				return FinancialMutation::from_object( $item );
			},
			$response_data
		);

		return $financial_mutations;
	}
}
