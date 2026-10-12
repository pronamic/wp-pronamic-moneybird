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
			fn( $item ) => FinancialMutation::from_object( $item ),
			$response_data
		);

		return $financial_mutations;
	}

	/**
	 * Link booking to financial mutation.
	 *
	 * @link https://developer.moneybird.com/api/financial-mutations#link-a-booking-to-a-financial-mutation
	 * @param FinancialMutation $financial_mutation Financial mutation.
	 * @param string            $booking_type       Booking type, for example `ExternalSalesInvoice`.
	 * @param string            $booking_id         Booking ID.
	 * @param string            $price_base         Price base.
	 * @return void
	 * @throws \Exception Throws an exception if financial mutation has no ID.
	 */
	public function link_booking( FinancialMutation $financial_mutation, $booking_type, $booking_id, $price_base ) {
		if ( null === $financial_mutation->id ) {
			throw new \Exception( 'Cannot link a booking remotely to a financial mutation without an ID.' );
		}

		$url = $this->get_api_url(
			\strtr(
				'financial_mutations/:id/link_booking',
				[
					':id' => $financial_mutation->id,
				]
			)
		);

		$data = [
			'booking_type' => $booking_type,
			'booking_id'   => $booking_id,
			'price_base'   => $price_base,
		];

		$this->client->patch( $url, $data, '200' );
	}
}
