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
	public function __construct( $client, $administration_id ) {
		parent::__construct( $client );

		$this->administration_id = $administration_id;
	}

	/**
	 * Get contacts endpoint.
	 *
	 * @return ContactsEndpoint
	 */
	public function get_contacts_endpoint() {
		return new ContactsEndpoint( $this->client, $this->administration_id );
	}

	/**
	 * Get external sales invoices endpoint.
	 *
	 * @return ExternalSalesInvoicesEndpoint
	 */
	public function get_external_sales_invoices_endpoint() {
		return new ExternalSalesInvoicesEndpoint( $this->client, $this->administration_id );
	}

	/**
	 * Get financial mutations endpoint.
	 *
	 * @return FinancialMutationsEndpoint
	 */
	public function get_financial_mutations_endpoint() {
		return new FinancialMutationsEndpoint( $this->client, $this->administration_id );
	}

	/**
	 * Get financial statements endpoint.
	 *
	 * @return FinancialStatementsEndpoint
	 */
	public function get_financial_statements_endpoint() {
		return new FinancialStatementsEndpoint( $this->client, $this->administration_id );
	}

	/**
	 * Get sales invoices endpoint.
	 *
	 * @return SalesInvoicesEndpoint
	 */
	public function get_sales_invoices_endpoint() {
		return new SalesInvoicesEndpoint( $this->client, $this->administration_id );
	}
}
