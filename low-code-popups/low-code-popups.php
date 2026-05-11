<?php
/**
 * Plugin Name: Low Code Popups
 * Plugin URI:  https://github.com/Lisejnik/low-code-popups
 * Description: Simple, safe low-code popups for WordPress sites, with optional Divi-friendly workflows.
 * Version:     1.2.1
 * Author:      Low Code Popups
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: low-code-popups
 * Requires at least: 6.0
 * Requires PHP: 8.0
 *
 * @package LowCodePopups
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DSPI_VERSION', '1.2.1' );
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
