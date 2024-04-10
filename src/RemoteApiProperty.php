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
	 * Name.
	 * 
	 * @var string
	 */
	public string $name;

	/**
	 * Construct property.
	 * 
	 * @param string $name Name.
	 */
	public function __construct( $name ) {
		$this->name = $name;
	}
}
