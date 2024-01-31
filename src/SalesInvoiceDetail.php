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
class SalesInvoiceDetail {
	/**
	 * Description.
	 * 
	 * @var string|null
	 */
	public $description;

	/**
	 * Period.
	 * 
	 * @var string|null
	 */
	public $period;

	/**
	 * Price.
	 * 
	 * @var string|null
	 */
	public $price;

	/**
	 * Amount.
	 * 
	 * @var string|null
	 */
	public $amount;

	/**
	 * Product ID.
	 * 
	 * @var int|null
	 */
	public $product_id;
}
