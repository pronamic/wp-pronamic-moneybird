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
class SalesInvoice {
	/**
	 * Contact ID.
	 * 
	 * @var string
	 */
	public $contact_id = '';

	/**
	 * Details attributes.
	 * 
	 * @var array
	 */
	public $details_attributes = [];
}
