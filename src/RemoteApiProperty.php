<?php
/**
 * Remote API property
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Attribute;

/**
 * Remote API property class
 * 
 * @link https://www.php.net/manual/en/language.attributes.php
 * @link https://stitcher.io/blog/attributes-in-php-8
 */
#[Attribute( Attribute::TARGET_PROPERTY )]
final class RemoteApiProperty {
	/**
	 * Construct property.
	 * 
	 * @param string $name        Name.
	 * @param string $create_name Create name.
	 */
	public function __construct(
		/**
		 * Name.
		 */
		public string $name,
		/**
		 * Create name.
		 */
		public ?string $create_name = null
	) {
	}

	/**
	 * Get name.
	 * 
	 * @param string $context Context.
	 * @return string
	 */
	public function get_name( $context = '' ) {
		if ( 'create' === $context && null !== $this->create_name ) {
			return $this->create_name;
		}

		return $this->name;
	}
}
