<?php
/**
 * Template controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Template controller class
 */
class TemplateController {
	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		\add_filter( 'template_include', [ $this, 'template_include' ] );
	}

	/**
	 * Template include.
	 * 
	 * @param string $template Template.
	 * @return string
	 */
	public function template_include( $template ) {
		$route = \get_query_var( 'pronamic_moneybird_route', null );

		if ( null === $route ) {
			return $template;
		}

		switch ( $route ) {
			case 'new_financial_statement':
				return $this->template_include_new_financial_statement( $template );
			case 'new_sales_invoice':
				return $this->template_include_new_sales_invoice( $template );
			default:
				return $template;
		}
	}

	/**
	 * Template include new financial statement.
	 * 
	 * @param string $template Template.
	 * @return string
	 */
	private function template_include_new_financial_statement( $template ) {
		$template = __DIR__ . '/../templates/financial-statement-new.php';

		return $template;
	}

	/**
	 * Template include new sales invoice.
	 * 
	 * @param string $template Template.
	 * @return string
	 */
	private function template_include_new_sales_invoice( $template ) {
		$template = __DIR__ . '/../templates/sales-invoice-new.php';

		return $template;
	}
}
