<?php
/**
 * Financial statement
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Financial statement class
 */
final class FinancialStatement {
	/**
	 * Financial account ID.
	 * 
	 * @var int
	 */
	public $financial_account_id;

	/**
	 * Reference.
	 * 
	 * @var string
	 */
	public $reference;

	/**
	 * Financial mutations attributes.
	 * 
	 * @var array
	 */
	public $financial_mutations_attributes = [];

	/**
	 * Construct financial statement.
	 * 
	 * @param int    $financial_account_id Financial account ID.
	 * @param string $reference            Reference.
	 */
	public function __construct( $financial_account_id, $reference ) {
		$this->financial_account_id = $financial_account_id;
		$this->reference            = $reference;
	}
}
