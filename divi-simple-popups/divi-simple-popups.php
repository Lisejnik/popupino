<?php
/**
 * Plugin Name: Divi Simple Popups
 * Plugin URI:  https://example.com/divi-simple-popups
 * Description: Simple, safe popups for Divi and WordPress sites without custom HTML, CSS, or JavaScript.
 * Version:     1.1.0
 * Author:      Divi Simple Popups
 * Text Domain: divi-simple-popups
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 *
 * @package DiviSimplePopups
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DSPI_VERSION', '1.1.0' );
define( 'DSPI_FILE', __FILE__ );
define( 'DSPI_PATH', plugin_dir_path( __FILE__ ) );
define( 'DSPI_URL', plugin_dir_url( __FILE__ ) );
define( 'DSPI_POST_TYPE', 'dspi_popup' );

require_once DSPI_PATH . 'includes/helpers.php';
require_once DSPI_PATH . 'includes/class-dspi-plugin.php';
require_once DSPI_PATH . 'includes/class-dspi-cpt.php';
require_once DSPI_PATH . 'includes/class-dspi-metaboxes.php';
require_once DSPI_PATH . 'includes/class-dspi-frontend.php';

register_activation_hook( __FILE__, array( 'DSPI_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'DSPI_Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'DSPI_Plugin', 'instance' ) );
