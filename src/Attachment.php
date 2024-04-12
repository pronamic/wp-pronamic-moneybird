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
	 * Filename.
	 * 
	 * @var string
	 */
	public $filename;

	/**
	 * Contents.
	 * 
	 * @var string
	 */
	public $contents;

	/**
	 * Type.
	 * 
	 * @var string
	 */
	public $type;

	/**
	 * Construct attachment.
	 * 
	 * @param string $filename Filename.
	 * @param string $contents Contents.
	 * @param string $type     Type.
	 */
	public function __construct( $filename, $contents, $type ) {
		$this->filename = $filename;
		$this->contents = $contents;
		$this->type     = $type;
	}
}
