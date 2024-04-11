<?php
/**
 * Remote serializable
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Remote serializable class
 */
interface RemoteSerializable {
	/**
	 * Remote serialize.
	 * 
	 * @param string $context Context.
	 * @return mixed
	 */
	public function remote_serialize( $context = '' );
}
