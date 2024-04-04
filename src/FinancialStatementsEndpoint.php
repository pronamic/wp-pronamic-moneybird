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
			'financial_statement' => self::array_filter_null(
				[
					'financial_account_id'           => $financial_statement->financial_account_id,
					'reference'                      => $financial_statement->reference,
					'official_date'                  => ( null === $financial_statement->official_date ) ? null : $financial_statement->official_date->format( 'Y-m-d' ),
					'official_balance'               => $financial_statement->official_balance,
					'importer_key'                   => $financial_statement->importer_key,
					'financial_mutations_attributes' => \array_map(
						function ( $financial_mutation ) {
							return self::array_filter_null(
								[
									'date'                => ( null === $financial_mutation->date ) ? null : $financial_mutation->date->format( 'Y-m-d' ),
									'message'             => $financial_mutation->message,
									'amount'              => $financial_mutation->amount,
									'code'                => $financial_mutation->code,
									'contra_account_name' => $financial_mutation->contra_account_name,
									'contra_account_number' => $financial_mutation->contra_account_number,
									'batch_reference'     => $financial_mutation->batch_reference,
									'offset'              => $financial_mutation->offset,
									'account_servicer_transaction_id' => $financial_mutation->account_servicer_transaction_id,
									'account_servicer_metadata' => $financial_mutation->account_servicer_metadata,
								]
							);
						},
						$financial_statement->financial_mutations
					),
				]
			),
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
