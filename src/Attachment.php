<?php
/**
 * Attachment
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Attachment class
 */
final class Attachment {
	/**
	 * Construct attachment.
	 * 
	 * @param string $filename Filename.
	 * @param string $contents Contents.
	 * @param string $type     Type.
	 */
	public function __construct(
		/**
		 * Filename.
		 */
		public $filename,
		/**
		 * Contents.
		 */
		public $contents,
		/**
		 * Type.
		 */
		public $type
	) {
	}
}
