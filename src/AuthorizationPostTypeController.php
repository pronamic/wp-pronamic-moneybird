<?php
/**
 * Authorization post type controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Authorization post type controller class
 */
class AuthorizationPostTypeController {
	/**
	 * Post type key.
	 * 
	 * @var string
	 */
	public const POST_TYPE = 'pronamic_moneybird_a';

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'init', [ $this, 'init' ] );

		\add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 10, 2 );

		\add_action( 'save_post_' . self::POST_TYPE, [ $this, 'save_post' ] );
		\add_action( 'save_post_' . self::POST_TYPE, [ $this, 'maybe_set_default_authorization' ] );

		\add_action( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );
	}

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	public function init() {
		\register_post_type(
			self::POST_TYPE,
			[
				'label'        => \__( 'Moneybird Authorizations', 'pronamic-moneybird' ),
				'labels'       => [
					'name'                  => \_x( 'Authorizations', 'post type general name', 'pronamic-moneybird' ),
					'singular_name'         => \_x( 'Authorization', 'post type singular name', 'pronamic-moneybird' ),
					'add_new'               => \_x( 'Add New', 'moneybird authorizations', 'pronamic-moneybird' ),
					'add_new_item'          => \__( 'Add New Authorization', 'pronamic-moneybird' ),
					'edit_item'             => \__( 'Edit Authorization', 'pronamic-moneybird' ),
					'new_item'              => \__( 'New Authorization', 'pronamic-moneybird' ),
					'view_item'             => \__( 'View Authorization', 'pronamic-moneybird' ),
					'view_items'            => \__( 'View Authorizations', 'pronamic-moneybird' ),
					'search_items'          => \__( 'Search Authorizations', 'pronamic-moneybird' ),
					'not_found'             => \__( 'No authorizations found.', 'pronamic-moneybird' ),
					'not_found_in_trash'    => \__( 'Not authorizations found in Trash.', 'pronamic-moneybird' ),
					'parent_item_colon'     => \__( 'Parent Authorization:', 'pronamic-moneybird' ),
					'all_items'             => \__( 'All Authorizations', 'pronamic-moneybird' ),
					'archives'              => \__( 'Authorization Archives', 'pronamic-moneybird' ),
					'attributes'            => \__( 'Authorization Attributes', 'pronamic-moneybird' ),
					'insert_into_item'      => \__( 'Insert into authorization', 'pronamic-moneybird' ),
					'uploaded_to_this_item' => \__( 'Uploaded to this authorization', 'pronamic-moneybird' ),
					'featured_image'        => \__( 'Featured image', 'pronamic-moneybird' ),
					'set_featured_image'    => \__( 'Set featured image', 'pronamic-moneybird' ),
					'remove_featured_image' => \__( 'Remove featured image', 'pronamic-moneybird' ),
					'use_featured_image'    => \__( 'Use as featured image', 'pronamic-moneybird' ),
					'filter_items_list'     => \__( 'Filter authorizations list', 'pronamic-moneybird' ),
					'filter_by_date'        => \__( 'Filter by date', 'pronamic-moneybird' ),
					'items_list_navigation' => \__( 'Authorizations list navigation', 'pronamic-moneybird' ),
					'items_list'            => \__( 'Authorizations list', 'pronamic-moneybird' ),
					'menu_name'             => \__( 'Authorizations', 'pronamic-moneybird' ),
					'name_admin_bar'        => \_x( 'Moneybird Authorization', 'add new from admin bar', 'pronamic-moneybird' ),
				],
				'public'       => true,
				/**
				 * Hierarchical is required for usage in `wp_dropdown_pages`.
				 * 
				 * @link https://developer.wordpress.org/reference/functions/register_post_type/#hierarchical
				 * @link https://developer.wordpress.org/reference/functions/wp_dropdown_pages/
				 */
				'hierarchical' => true,
				'show_in_menu' => false,
				'supports'     => [
					'title',
				],
			]
		);
	}

	/**
	 * Add meta boxes.
	 * 
	 * @param string  $post_type Post type.
	 * @param WP_Post $post      Post object.
	 * @return void
	 */
	public function add_meta_boxes( $post_type, $post ) {
		if ( self::POST_TYPE !== $post_type ) {
			return;
		}

		\add_meta_box(
			'pronamic_moneybird_authorization_settings',
			\__( 'Authorization', 'pronamic-moneybird' ),
			[ $this, 'meta_box_authorization_settings' ],
			$post_type,
			'normal',
			'high'
		);

		/**
		 * Authentication.
		 */
		$api_token = \get_post_meta( $post->ID, '_pronamic_moneybird_api_token', true );

		if ( '' !== $api_token ) {
			\add_meta_box(
				'pronamic_moneybird_authentication',
				\__( 'Authentication', 'pronamic-moneybird' ),
				[ $this, 'meta_box_authentication' ],
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Save authorization settings.
	 * 
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_post( $post_id ) {
		if ( \defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! \array_key_exists( 'pronamic_moneybird_nonce', $_POST ) ) {
			return;
		}

		$nonce = \sanitize_key( $_POST['pronamic_moneybird_nonce'] );

		if ( ! \wp_verify_nonce( $nonce, 'pronamic_moneybird_save_authorization_settings' ) ) {
			return;
		}

		$api_token         = \array_key_exists( '_pronamic_moneybird_api_token', $_POST ) ? \sanitize_text_field( \wp_unslash( $_POST['_pronamic_moneybird_api_token'] ) ) : '';
		$administration_id = \array_key_exists( '_pronamic_moneybird_administration_id', $_POST ) ? \sanitize_text_field( \wp_unslash( $_POST['_pronamic_moneybird_administration_id'] ) ) : '';

		\update_post_meta( $post_id, '_pronamic_moneybird_api_token', $api_token );
		\update_post_meta( $post_id, '_pronamic_moneybird_administration_id', $administration_id );
	}

	/**
	 * Maybe set default authorization.
	 * 
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/GatewayPostType.php#L42
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/GatewayPostType.php#L103-L124
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function maybe_set_default_authorization( $post_id ) {
		// Don't set the default authorization if the post is not published.
		if ( 'publish' !== \get_post_status( $post_id ) ) {
			return;
		}

		// Don't set the default gateway if there is already a published gateway set.
		$id = \get_option( 'pronamic_moneybird_authorization_post_id' );

		if ( ! empty( $id ) && 'publish' === \get_post_status( $id ) ) {
			return;
		}

		// Update.
		\update_option( 'pronamic_moneybird_authorization_post_id', $post_id );
	}

	/**
	 * Display post states.
	 *
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/Admin/AdminGatewayPostType.php#L68
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/Admin/AdminGatewayPostType.php#L215-L233
	 * @param array    $post_states Post states.
	 * @param \WP_Post $post        Post.
	 * @return array
	 */
	public function display_post_states( $post_states, $post ) {
		if ( self::POST_TYPE !== \get_post_type( $post ) ) {
			return $post_states;
		}

		if ( (int) \get_option( 'pronamic_moneybird_authorization_post_id' ) === $post->ID ) {
			$post_states['pronamic_moneybird_authorization_post_id'] = \__( 'Default', 'pronamic-moneybird' );
		}

		return $post_states;
	}

	/**
	 * Meta box authorization settings.
	 * 
	 * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
	 * @param WP_Post $post Post.
	 * @return void
	 */
	public function meta_box_authorization_settings( // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Used in include.
		$post
	) {
		\wp_nonce_field( 'pronamic_moneybird_save_authorization_settings', 'pronamic_moneybird_nonce' );

		include __DIR__ . '/../admin/meta-box-authorization-settings.php';
	}

	/**
	 * Meta box authentication.
	 * 
	 * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
	 * @param WP_Post $post Post.
	 * @return void
	 */
	public function meta_box_authentication(  // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Used in include.
		$post
	) {
		include __DIR__ . '/../admin/meta-box-authentication.php';
	}
}
