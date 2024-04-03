<?php
/**
 * Administration endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Administration endpoint class
 */
final class AdministrationEndpoint extends Endpoint {
	/**
	 * Administration ID.
	 * 
	 * @var string
	 */
	public $administration_id;

	/**
	 * Construct administration endpoint.
	 * 
	 * @param Client $client            Client.
	 * @param string $administration_id Administration ID.
	 */
	public function __construct( $client ) {
		parent::__construct( $client );

		$this->administration_id = $administration_id;
	}

	/**
	 * Get financial statements endpoint.
	 * 
	 * @return FinancialStatementsEndpoint
	 */
	public function get_financial_statements_endpoint() {
		return new FinancialStatementsEndpoint( $this->administration_id );
	}

	/**
	 * Get sales invoices endpoint.
	 * 
	 * @return SalesInvoicesEndpoint
	 */
	public function get_sales_invoices_endpoint() {
		return new SalesInvoicesEndpoint( $this->administration_id );
	}
}
