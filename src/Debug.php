<?php

namespace SeoThemes\GenesisConfigExporter;

/**
 * Class Debug
 *
 * @package \SeoThemes\GenesisConfigExporter
 */
class Debug {

	/**
	 * @var Generator
	 */
	public $generator;

	/**
	 * @var bool
	 */
	const DEBUG = false;

	/**
	 * Debug constructor.
	 *
	 * @param Generator $generator
	 */
	public function __construct( Generator $generator ) {
		$this->generator = $generator;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dump() {
		if ( $this->is_in_debug() ) {
			\var_dump( $this->generator );
		}
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_in_debug() {
		return defined( 'WP_DEBUG' ) && WP_DEBUG && self::DEBUG ?: false;
	}
}
