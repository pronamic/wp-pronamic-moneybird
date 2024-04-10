<?php
/**
 * Remote API create property
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Attribute;

/**
 * Remote API create property class
 * 
 * @link https://www.php.net/manual/en/language.attributes.php
 * @link https://stitcher.io/blog/attributes-in-php-8
 */
#[Attribute( Attribute::TARGET_PROPERTY )]
final class RemoteApiCreateProperty extends RemoteApiProperty {

}
