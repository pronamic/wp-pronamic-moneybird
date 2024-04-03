<?php
/**
 * Sales invoice
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Sales invoice class
 */
final class SalesInvoice {
	/**
	 * Contact ID.
	 * 
	 * @var string|null
	 */
	public $contact_id;

	/**
	 * Reference.
	 * 
	 * @var string|null
	 */
	public $reference;

	/**
	 * Details attributes.
	 * 
	 * @var array
	 */
	public $details_attributes = [];
}
