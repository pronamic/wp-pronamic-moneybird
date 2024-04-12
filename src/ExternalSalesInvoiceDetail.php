<?php
/**
 * External sales invoice detail
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * External sales invoice detail class
 * 
 * @link https://developer.moneybird.com/api/sales_invoices/#post_sales_invoices
 */
final class ExternalSalesInvoiceDetail implements RemoteSerializable {
	/**
	 * ID.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'id' )]
	public $id;

	/**
	 * Description.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'description' )]
	public $description;

	/**
	 * Period.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'period' )]
	public $period;

	/**
	 * Price.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'price' )]
	public $price;

	/**
	 * Amount.
	 * 
	 * @var string|null
	 */
	#[RemoteApiProperty( 'amount' )]
	public $amount;

	/**
	 * Tax rate ID.
	 * 
	 * @var int|null
	 */
	#[RemoteApiProperty( 'tax_rate_id' )]
	public $tax_rate_id;

	/**
	 * Ledger account ID.
	 * 
	 * @var int|null
	 */
	#[RemoteApiProperty( 'ledger_account_id' )]
	public $ledger_account_id;

	/**
	 * Project ID.
	 * 
	 * @var int|null
	 */
	#[RemoteApiProperty( 'project_id' )]
	public $project_id;

	/**
	 * Row order.
	 * 
	 * @var int|null
	 */
	#[RemoteApiProperty( 'row_order' )]
	public $row_order;

	/**
	 * Remote serialize.
	 * 
	 * @link https://developer.moneybird.com/api/external_sales_invoices/#post_external_sales_invoices
	 * @param string $context Context.
	 * @return mixed
	 */
	public function remote_serialize( $context = '' ) {
		$serializer = new RemoteSerializer( $context );

		return $serializer->serialize( $this );
	}
}
