<?php

namespace SeoThemes\GenesisConfigExporter;

/**
 * Class Generator
 *
 * @package \SeoThemes\GenesisConfigExporter
 */
class Generator {

	/**
	 * @var string
	 */
	public $file_path;

	/**
	 * @var mixed
	 */
	public $data;

	/**
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * @var Replacer
	 */
	private $replacer;

	/**
	 * Generator constructor.
	 *
	 * @param Plugin   $plugin
	 * @param Replacer $replacer
	 *
	 * @return void
	 */
	public function __construct( Plugin $plugin, Replacer $replacer ) {
		$this->plugin    = $plugin;
		$this->replacer  = $replacer;
		$this->file_path = \get_stylesheet_directory() . '/config/onboarding.php';
		$this->data      = $this->replacer->replace();
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
	 *
	 * @throws \Exception
	 */
	public function generate( $args, $assoc_args ) {
		include_once ABSPATH . '/wp-admin/includes/file.php';
		\WP_Filesystem();

		/**
		 * Set up file system.
		 *
		 * @var \WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;

		$wp_filesystem->put_contents( $this->file_path, $this->data );
	}
}
