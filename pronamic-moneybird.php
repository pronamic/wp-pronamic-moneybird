<?php
/**
 * Pronamic Moneybird
 *
 * @package   Pronamic\Moneybird
 * @author    Pronamic
 * @copyright 2024 Pronamic
 * @license   GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Pronamic Moneybird
 * Plugin URI:        https://wp.pronamic.directory/plugins/pronamic-moneybird/
 * Description:       This WordPress plugin provides the link between your WordPress website and your Moneybird administration.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * Author:            Pronamic
 * Author URI:        https://www.pronamic.eu/
 * Text Domain:       pronamic-moneybird
 * Domain Path:       /languages/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://wp.pronamic.directory/plugins/pronamic-moneybird/
 * GitHub URI:        https://github.com/pronamic/pronamic-moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Autoload.
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

/**
 * Bootstrap.
 */
\add_action(
	'plugins_loaded',
	function () {
		\load_plugin_textdomain( 'pronamic-moneybird', false, \dirname( \plugin_basename( __FILE__ ) ) . '/languages' );

		Plugin::instance()->setup();
	}
);

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	} 
);
