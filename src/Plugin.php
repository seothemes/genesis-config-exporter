<?php

namespace SeoThemes\GenesisConfigExporter;

/**
 * Class Plugin
 *
 * @package \SeoThemes\GenesisConfigExporter
 */
class Plugin {

	/**
	 * @var string
	 */
	public $file;

	/**
	 * @var string
	 */
	public $dir;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $base;

	/**
	 * @var mixed
	 */
	public $name;

	/**
	 * @var mixed
	 */
	public $handle;

	/**
	 * Plugin constructor.
	 *
	 * @param $file
	 */
	public function __construct( $file ) {
		$this->file   = $file;
		$this->dir    = \trailingslashit( \dirname( $file ) );
		$this->url    = \trailingslashit( \plugin_dir_url( $file ) );
		$this->base   = \plugin_basename( $file );
		$this->handle = \basename( $file, '.php' );
		$this->name   = \ucwords( \str_replace( '-', ' ', $this->handle ) );
	}
}
