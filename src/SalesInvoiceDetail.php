<?php
/**
 * Sales invoice detail
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Sales invoice detail class
 * 
 * @link https://developer.moneybird.com/api/sales_invoices/#post_sales_invoices
 */
final class SalesInvoiceDetail implements RemoteSerializable {
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
	 * Product ID.
	 * 
	 * @var int|null
	 */
	#[RemoteApiProperty( 'product_id' )]
	public $product_id;

	/**
	 * Project ID.
	 * 
	 * @var int|null
	 */
	#[RemoteApiProperty( 'project_id' )]
	public $project_id;
}
