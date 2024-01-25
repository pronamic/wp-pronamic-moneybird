<?php
/**
 * Settings controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Settings controller class
 */
class SettingsController {
	/**
	 * Setup.
	 */
	public function setup() {
		\add_action( 'init', [ $this, 'init' ] );

		\add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	/**
	 * Initialize.
	 */
	public function init() {
		\register_setting(
			'pronamic_moneybird',
			'pronamic_moneybird_authorization_post_id',
			[
				'type' => 'integer',
			]
		);
	}

	/**
	 * Admin initialize.
	 */
	public function admin_init() {
		\add_settings_section(
			'pronamic_moneybird_general',
			\__( 'General', 'pronamic-moneybird' ),
			function () { },
			'pronamic_moneybird'
		);

		\add_settings_field(
			'pronamic_moneybird_authorization_post_id',
			\__( 'Default authorization', 'pronamic-moneybird' ),
			[ $this, 'input_page' ],
			'pronamic_moneybird',
			'pronamic_moneybird_general',
			[
				'post_type'        => 'pronamic_moneybird_a',
				'show_option_none' => \__( '— Select authorization —', 'pronamic-moneybird' ),
				'label_for'        => 'pronamic_moneybird_authorization_post_id',
			]
		);
	}

	/**
	 * Input page.
	 *
	 * @param array $args Arguments.
	 * @return void
	 */
	public function input_page( $args ) {
		$name = $args['label_for'];

		$selected = \get_option( $name, '' );

		if ( false === $selected ) {
			$selected = '';
		}

		\wp_dropdown_pages(
			[
				'name'             => \esc_attr( $name ),
				'post_type'        => \esc_attr( $args['post_type'] ?? 'page' ),
				'selected'         => \esc_attr( $selected ),
				'show_option_none' => \esc_attr( $args['show_option_none'] ?? __( '— Select a page —', 'pronamic-moneybird' ) ),
				'class'            => 'regular-text',
			]
		);
	}
}
