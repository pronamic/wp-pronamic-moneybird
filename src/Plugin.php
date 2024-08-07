<?php
/**
 * Plugin
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Plugin class
 */
final class Plugin {
	/**
	 * Instance.
	 * 
	 * @var self
	 */
	private static $instance;

	/**
	 * Instance.
	 * 
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		$controllers = [
			new AuthorizationPostTypeController(),
			new FinancialStatementsController(),
			new PostTypeSupportController(),
			new RewriteController(),
			new SalesInvoicesController(),
			new SettingsController(),
			new TemplateController(),
			new UsersController(),
			new WooCommerceController(),
		];

		if ( \is_admin() ) {
			$controllers[] = new AdminController();
		}

		foreach ( $controllers as $controller ) {
			$controller->setup();
		}
	}
}
