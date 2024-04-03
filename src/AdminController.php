<?php
/**
 * Admin controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Admin controller class
 */
final class AdminController {
	/**
	 * Setup.
	 */
	public function setup() {
		\add_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	/**
	 * Get menu icon URL.
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 * @return string
	 * @throws \Exception Throws exception when retrieving menu icon fails.
	 */
	private function get_menu_icon_url() {
		/**
		 * Icon URL.
		 *
		 * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme.
		 * This should begin with 'data:image/svg+xml;base64,'.
		 *
		 * We use a SVG image with default fill color #A0A5AA from the default admin color scheme:
		 * https://github.com/WordPress/WordPress/blob/5.2/wp-includes/general-template.php#L4135-L4145
		 *
		 * The advantage of this is that users with the default admin color scheme do not see the repaint:
		 * https://github.com/WordPress/WordPress/blob/5.2/wp-admin/js/svg-painter.js
		 *
		 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
		 */
		$file = __DIR__ . '/../images/dist/moneybird-icon-wp-admin-fresh-base.svg';

		if ( ! \is_readable( $file ) ) {
			throw new \Exception(
				\sprintf(
					'Could not read WordPress admin menu icon from file: %s.',
					\esc_html( $file )
				)
			);
		}

		$svg = \file_get_contents( $file, true );

		if ( false === $svg ) {
			throw new \Exception(
				\sprintf(
					'Could not read WordPress admin menu icon from file: %s.',
					\esc_html( $file )
				)
			);
		}

		$icon_url = \sprintf(
			'data:image/svg+xml;base64,%s',
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			\base64_encode( $svg )
		);

		return $icon_url;
	}

	/**
	 * Admin menu.
	 */
	public function admin_menu() {
		try {
			$menu_icon_url = $this->get_menu_icon_url();
		} catch ( \Exception $e ) {
			/**
			 * If retrieving the menu icon URL fails we will
			 * fallback to the WordPress site dashicon.
			 *
			 * @link https://developer.wordpress.org/resource/dashicons/#admin-site-alt3
			 */
			$menu_icon_url = 'dashicons-admin-site-alt3';
		}

		\add_menu_page(
			\__( 'Moneybird', 'pronamic-moneybird' ),
			\__( 'Moneybird', 'pronamic-moneybird' ),
			'manage_options',
			'pronamic-moneybird',
			function () {
				include __DIR__ . '/../admin/page-dashboard.php';
			},
			$menu_icon_url
		);

		\add_submenu_page(
			'pronamic-moneybird',
			\__( 'Moneybird Authorizations', 'pronamic-moneybird' ),
			\__( 'Authorizations', 'pronamic-moneybird' ),
			'manage_options',
			\add_query_arg( 'post_type', 'pronamic_moneybird_a', 'edit.php' ),
			'',
			10
		);

		add_submenu_page(
			'pronamic-moneybird',
			\__( 'Moneybird Settings', 'pronamic-moneybird' ),
			\__( 'Settings', 'pronamic-moneybird' ),
			'manage_options',
			'pronamic-moneybird-settings',
			function () {
				include __DIR__ . '/../admin/page-settings.php';
			},
			20
		);
	}
}
