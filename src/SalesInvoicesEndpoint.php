<?php
/**
 * Sales invoices endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Exception;

/**
 * Sales invoices endpoint class
 */
final class SalesInvoicesEndpoint extends ResourceEndpoint {
	/**
	 * Create sales invoice.
	 * 
	 * @param SalesInvoice $sales_invoice Sales invoice.
	 * @return
	 */
	public function create( SalesInvoice $sales_invoice ) {
		$url = $this->get_api_url( 'sales_invoices' );

		$data = [
			'sales_invoice' => $sales_invoice,
		];

		$response = $this->client->post( $url, $data );

		$response_status = (string) $response->status();
		$response_data   = $response->json();

		if ( '201' !== $response_status ) {
			$http_exception = new Exception( 'Unexpected HTTP response: ' . $response_status, (int) $response_status );

			throw Error::from_response_object( $response_data, (int) $response_status, $http_exception );
		}
	}
}
