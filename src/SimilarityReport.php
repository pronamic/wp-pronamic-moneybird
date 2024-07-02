<?php
/**
 * Similarity report
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Similarity report class
 */
final class SimilarityReport {
	/**
	 * Property similarities
	 * 
	 * @var array<string, float>
	 */
	public $property_similarities;

	/**
	 * Get average similarity percentage.
	 * 
	 * @return float
	 */
	public function get_average_similarity() {
		return \array_sum( $this->property_similarities ) / \count( $this->property_similarities );
	}

	/**
	 * Is perfect match.
	 * 
	 * @return bool
	 */
	public function is_perfect_match(): bool {
		return ( 100.0 === $this->get_average_similarity() );
	}
}
