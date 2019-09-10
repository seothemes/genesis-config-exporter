<?php

namespace SeoThemes\GenesisConfigExporter;

/**
 * Class Command
 *
 * @package \SeoThemes\GenesisConfigExporter
 */
class Command {

	/**
	 * @var Generator
	 */
	protected $generator;

	/**
	 * Command constructor.
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
	 * @param $args
	 * @param $assoc_args
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function export( $args, $assoc_args ) {
		if ( ! isset( $assoc_args['force'] ) ) {
			\WP_CLI::confirm( 'This will overwrite your current child theme\'s onboarding config file. Proceed?' );
		}

		if ( ! isset( $assoc_args['dry'] ) ) {
			$this->generator->generate( $args, $assoc_args );
		}

		if ( isset( $assoc_args['dry'] ) ) {
			echo $this->generator->data;
		}

		\WP_CLI::success( 'Generated ' . $this->generator->file_path );
	}
}
