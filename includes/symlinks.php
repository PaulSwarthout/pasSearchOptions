<?PHP

namespace search_options;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class symlinks {
	private static $class_initialized = false;
	public static $content_dir = "";

	public static function init() {
		if ( ! self::$class_initialized) {
			self::$content_dir = wp_normalize_path( WP_CONTENT_DIR );
			self::$class_initialized = true;
		}
	}
}
\search_options\symlinks::init();

/*
 * The PHP magic constant __FILE__ returns a fully resolved path to the current file as the server's operating system 'sees' it.
 * This is by design.
 * 
 * Unfortunately, when developers, like me, symbolically link their development folder to their WordPress installation,
 * the PHP magic constant __FILE__ returns the wrong path. The fully resolved path is outside the WordPress installation 
 * and many WordPress functions will return the wrong information, or simply crash.
 * 
 * I use symbolically linked folders for all of my plugins.
 * 
 * To resolve that issue, all of my plugins include this required file.
 * 
 * The functions plugin_dir_url() & plugin_dir_path() are overwritten in the plugin such that
 * the __FILE__ is converted to a path within the WordPress directory structure before being passed
 * to those two functions.
 * 
 * Once the correct folders and paths are defined, they will be used throughout this plugin instead of __FILE__.
 */
function is_symlink( $file ) {
	return ! strpos( wp_normalize_path( symlinks::$content_dir ), wp_normalize_path( \dirname( $file ) ) );
}
function get_webserver_view( $file ) {
	if (is_symlink( $file )) {
        $file = wp_normalize_path( $file );

        $file_parts = explode( '/', $file );
        if (array_search( 'plugins', $file_parts ) === false) {
            $plugins_path = symlinks::$content_dir . '/plugins';
        } else {
            $plugins_path = symlinks::$content_dir;
        }

        $build_a_path = wp_normalize_path($plugins_path);
        foreach ($file_parts as $part) {
            if (is_dir($build_a_path . '/' . $part)) {
                $build_a_path .= '/' . $part;
            } elseif (file_exists($build_a_path . '/' . $part)) {
                $build_a_path .= '/' . $part;
            } else {
                // continue
            }
        }
        return wp_normalize_path( $build_a_path );
	} else {
		return $file;
	}
}

function plugin_dir_path( $file ) {
	return \plugin_dir_path( get_webserver_view( $file ) );
}
function plugin_dir_url( $file ) {
	return \plugin_dir_url( get_webserver_view( $file ) );
}

// Add additional overrides here based upon what your plugin will need.
function pathinfo( $file, $part ) {
	return \pathinfo( get_webserver_view( $file ), $part );
}
function dirname( $file, $levels = 1 ) {
	return \dirname( get_webserver_view( $file ), $levels );
}