<?php
/**
 * Rewrite controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Rewrite controller class
 */
final class RewriteController {
	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		\add_filter( 'query_vars', [ $this, 'query_vars' ] );

		\add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Query vars.
	 * 
	 * @link https://developer.wordpress.org/reference/hooks/query_vars/
	 * @param string[] $query_vars Query vars.
	 * @return string[]
	 */
	public function query_vars( $query_vars ) {
		$query_vars[] = 'pronamic_moneybird_route';

		return $query_vars;
	}

	/**
	 * Initialize.
	 * 
	 * @link https://make.wordpress.org/core/2015/10/07/add_rewrite_rule-accepts-an-array-of-query-vars-in-wordpress-4-4/
	 * @return void
	 */
	public function init() {
		\add_rewrite_rule(
			'moneybird/sales-invoices/new/?$', 
			[
				'pronamic_moneybird_route' => 'new_sales_invoice',
			],
			'top'
		);

		\add_rewrite_rule(
			'moneybird/financial-statements/new/?$', 
			[
				'pronamic_moneybird_route' => 'new_financial_statement',
			],
			'top'
		);
	}
}
