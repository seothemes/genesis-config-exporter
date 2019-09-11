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
	 * @var string
	 */
	public $image_dir;

	/**
	 * @var string
	 */
	public $template;

	/**
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Generator constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin    = $plugin;
		$this->template  = require_once $this->plugin->dir . 'config/template.php';
		$this->file_path = \get_stylesheet_directory() . '/config/onboarding.php';
		$this->image_dir = '/assets/img/';
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Associative array of args from WP CLI.
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function generate( $args ) {
		include_once ABSPATH . '/wp-admin/includes/file.php';
		\WP_Filesystem();

		/**
		 * Set up file system.
		 *
		 * @var \WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;

		$wp_filesystem->put_contents( $this->file_path, $this->replace() );
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function replace() {
		$template = $this->template;
		$theme    = \wp_get_theme();

		$replace = [
			'name'    => $theme->get( 'Name' ),
			'package' => $theme->get( 'Name' ),
			'author'  => $theme->get( 'Author' ),
			'link'    => $theme->get( 'ThemeURI' ),
			'plugins' => $this->get_plugins(),
			'content' => $this->get_content(),
			'navmenu' => $this->get_navmenu(),
			'widgets' => $this->get_widgets(),
		];

		foreach ( $replace as $from => $to ) {
			$template = str_replace( "{{{$from}}}", $to, $template );
		}

		return $template;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_plugins() {
		$plugins = '';
		$active  = \array_values( \get_option( 'active_plugins' ) );
		$count   = 0;

		if ( ( $key = array_search( $this->plugin->base, $active ) ) !== false ) {
			unset( $active[ $key ] );
		}

		foreach ( $active as $slug ) {
			$base = explode( DIRECTORY_SEPARATOR, $slug )[0];
			$name = ucwords( str_replace( '-', ' ', $base ) );
			$url  = "https://wordpress.org/plugins/$base/";

			$plugins .= 0 === $count++ ? "[" : "\n\t\t\t[";
			$plugins .= "
				'name'       => '{$name}',
				'slug'       => '{$slug}',
				'public_url' => '{$url}',
			],";
		}

		return $plugins;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_content() {
		$content = '';
		$posts   = \get_posts( [
			'numberposts' => -1,
			'post_type'   => 'any',
			//'post_status' => [ 'publish', 'inherit' ], // Include attachments.
		] );

		foreach ( $posts as $post ) {

			// Generate meta.
			$meta_input = '';
			$meta       = \get_post_meta( $post->ID, false, true );

			foreach ( $meta as $key => $value ) {
				if ( isset( $value[0] ) && ! empty( $value[0] ) ) {
					$original = $value[0];
					$value    = '_thumbnail_id' === $key ? \get_the_title( $original ) : $original;

					//if ( '_wp_attachment_metadata' === $key ) {
					//	$value = $this->get_attachment_meta( $original );
					//}

					$meta_input .= "\n\t\t\t\t'{$key}' => '{$value}',";
				}
			}

			$meta_input = ! empty( $meta_input ) ? "\n\t\t\t'meta_input'     => [ $meta_input \n\t\t\t]," : '';

			// Generate images.
			$featured_image = '';

			if ( isset( $meta['_thumbnail_id'][0] ) || 'attachment' === $post->post_type ) {
				$featured_image = $this->get_featured_image( $post, $meta );
			}

			// Generate content.
			$post_content = \str_replace( '\'', '&apos;', $post->post_content );
			$post_content = "\n\t\t\t'post_content'   => '{$post_content}',";

			$content .= "'{$post->post_name}' => [
			'post_title'     => '{$post->post_title}',
			'post_type'      => '{$post->post_type}',
			'post_status'    => '{$post->post_status}',
			'comment_status' => '{$post->comment_status}',
			'ping_status'    => '{$post->ping_status}',";

			$content .= $featured_image . $meta_input . $post_content;
			$content .= "\n\t\t],\n\t\t";
		}

		return $content;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param $post
	 * @param $meta
	 *
	 * @return string
	 */
	private function get_featured_image( $post, $meta ) {
		$image = [];

		if ( 'attachment' === $post->post_type ) {
			$image['path'] = \wp_get_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $meta['_wp_attached_file'][0];

		} else {
			$image['path'] = \get_attached_file( $meta['_thumbnail_id'][0] );
		}

		$image['dir']  = \get_stylesheet_directory() . $this->image_dir;
		$image['name'] = \basename( $image['path'] );
		$image['file'] = $image['dir'] . DIRECTORY_SEPARATOR . $image['name'];
		$image['url']  = "\\get_stylesheet_directory() . '{$this->image_dir}{$image['name']}'";

		if ( ! is_dir( $image['dir'] ) ) {
			\wp_mkdir_p( $image['dir'] );
		}

		if ( ! file_exists( $image['file'] ) ) {
			\copy( $image['path'], $image['file'] );
		}

		return "\n\t\t\t'featured_image' => {$image['url']},";
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param $meta_data
	 *
	 * @return string
	 */
	private function get_attachment_meta( $meta_data ) {
		$meta_data       = \unserialize( $meta_data );
		$attachment_meta = '[';
		$formatted_value = '';

		foreach ( $meta_data as $meta_name => $meta_value ) {
			if ( is_string( $meta_value ) ) {
				$formatted_value = "'{$meta_value}'";

			} elseif ( is_int( $meta_value ) ) {
				$formatted_value = "{$meta_value}";

			} elseif ( 'image_meta' === $meta_name && is_array( $meta_value ) ) {
				$formatted_value = "[";

				foreach ( $meta_value as $image_meta_name => $image_meta_value ) {
					if ( ! is_array( $image_meta_value ) ) {
						$formatted_value .= "\n\t\t\t\t\t\t'{$image_meta_name}' => '{$image_meta_value}',";
					}
				}

				$formatted_value .= "\n\t\t\t\t\t]";

			} elseif ( 'sizes' === $meta_name ) {
				$formatted_value = "[";

				foreach ( $meta_value as $size_key => $size_value ) {
					if ( is_array( $size_value ) ) {
						$formatted_value .= "\n\t\t\t\t\t\t'$size_key' => [";

						foreach ( $size_value as $size_name => $size ) {
							$formatted_value .= "\n\t\t\t\t\t\t\t'{$size_name}' => '{$size}',";
						}

						$formatted_value .= "\n\t\t\t\t\t\t],";
					}
				}

				$formatted_value .= "\n\t\t\t\t\t]";
			}

			$attachment_meta .= "\n\t\t\t\t\t'{$meta_name}' => {$formatted_value},";
		}

		$attachment_meta .= "\n\t\t\t\t]";

		return $attachment_meta;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_navmenu() {
		$menus     = '';
		$locations = \get_registered_nav_menus();

		foreach ( $locations as $location => $title ) {
			$menu_items = \wp_get_nav_menu_items( $title );

			if ( ! is_array( $menu_items ) ) {
				continue;
			}

			$menus .= "'$location' => [\n";

			foreach ( $menu_items as $menu_item ) {
				$url = \basename( $menu_item->url );

				$menu_item_slugs[ $menu_item->ID ] = $url;

				$menus .= "\t\t\t'{$url}' => [\n";
				$menus .= "\t\t\t\t'title'  => '{$menu_item->title}',\n";
				$menus .= "\t\t\t\t'id'     => '{$url}',\n";

				// If has a parent.
				if ( $menu_item->menu_item_parent ) {
					$menus .= "\t\t\t\t'parent' => '{$menu_item_slugs[$menu_item->menu_item_parent]}',\n";
				}

				$menus .= "\t\t\t],\n";
			}

			$menus .= "\t\t],";
		}

		return $menus;
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_widgets() {
		$widgets = '';

		$sidebars_widgets = \wp_get_sidebars_widgets();
		unset( $sidebars_widgets['wp_inactive_widgets'] );

		foreach ( $sidebars_widgets as $widget_area => $widgets_array ) {

			if ( empty( $widgets_array ) ) {
				continue;
			}

			$widgets .= "\t\t'$widget_area' => [\n";

			foreach ( $widgets_array as $widget ) {
				$type = substr( $widget, 0, strrpos( $widget, '-' ) );
				$id   = substr( $widget, strrpos( $widget, '-' ) + 1 );
				$args = \get_option( "widget_$type" )[ $id ];

				$widgets .= "\t\t\t[\n";
				$widgets .= "\t\t\t\t'type' => '$type',\n";

				if ( ! empty( $args ) ) {
					$widgets .= "\t\t\t\t'args' => [\n";

					foreach ( $args as $key => $value ) {
						$escaped = \str_replace( '\'', '&apos;', $value );
						$widgets .= "\t\t\t\t\t'$key' => '$escaped',\n";
					}

					$widgets .= "\t\t\t\t],";
				}

				$widgets .= "\n\t\t\t],";
			}

			$widgets .= "\n\t\t],\n";
		}

		return $widgets;
	}
}
