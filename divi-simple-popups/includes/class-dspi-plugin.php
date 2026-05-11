<?php
/**
 * Main plugin coordinator.
 *
 * @package DiviSimplePopups
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
final class DSPI_Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var DSPI_Plugin|null
	 */
	private static ?DSPI_Plugin $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return DSPI_Plugin
	 */
	public static function instance(): DSPI_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot plugin modules.
	 */
	private function __construct() {
		load_plugin_textdomain( 'divi-simple-popups', false, dirname( plugin_basename( DSPI_FILE ) ) . '/languages' );

		DSPI_CPT::init();
		DSPI_Metaboxes::init();
		DSPI_Frontend::init();
	}

	/**
	 * Activation callback.
	 */
	public static function activate(): void {
		DSPI_CPT::register_post_type();
		flush_rewrite_rules();
	}

	/**
	 * Deactivation callback.
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
