<?php
/**
 * Custom post type registration.
 *
 * @package Popupino
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Popup custom post type.
 */
final class DSPI_CPT {
	/**
	 * Register hooks.
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_filter( 'manage_' . DSPI_POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . DSPI_POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
	}

	/**
	 * Register popup CPT.
	 */
	public static function register_post_type(): void {
		$labels = array(
			'name'               => __( 'Popups', 'popupino' ),
			'singular_name'      => __( 'Popup', 'popupino' ),
			'menu_name'          => __( 'Popups', 'popupino' ),
			'name_admin_bar'     => __( 'Popup', 'popupino' ),
			'add_new'            => __( 'Add New', 'popupino' ),
			'add_new_item'       => __( 'Add New Popup', 'popupino' ),
			'new_item'           => __( 'New Popup', 'popupino' ),
			'edit_item'          => __( 'Edit Popup', 'popupino' ),
			'view_item'          => __( 'View Popup', 'popupino' ),
			'all_items'          => __( 'All Popups', 'popupino' ),
			'search_items'       => __( 'Search Popups', 'popupino' ),
			'not_found'          => __( 'No popups found.', 'popupino' ),
			'not_found_in_trash' => __( 'No popups found in Trash.', 'popupino' ),
		);

		register_post_type(
			DSPI_POST_TYPE,
			array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'query_var'           => false,
				'rewrite'             => false,
				'menu_icon'           => 'dashicons-format-status',
				'supports'            => array( 'title' ),
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'show_in_rest'        => false,
			)
		);
	}

	/**
	 * Add list table columns.
	 *
	 * @param array<string, string> $columns Columns.
	 * @return array<string, string>
	 */
	public static function columns( array $columns ): array {
		$new_columns = array();

		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;

			if ( 'title' === $key ) {
				$new_columns['dspi_active']   = __( 'Active', 'popupino' );
				$new_columns['dspi_template'] = __( 'Template', 'popupino' );
				$new_columns['dspi_shortcode']= __( 'Shortcode', 'popupino' );
			}
		}

		return $new_columns;
	}

	/**
	 * Render list table column content.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 */
	public static function column_content( string $column, int $post_id ): void {
		if ( 'dspi_active' === $column ) {
			echo '1' === (string) dspi_get_meta( $post_id, 'is_active' ) ? esc_html__( 'Yes', 'popupino' ) : esc_html__( 'No', 'popupino' );
			return;
		}

		if ( 'dspi_template' === $column ) {
			$templates = dspi_get_templates();
			$template  = (string) dspi_get_meta( $post_id, 'template' );
			echo esc_html( $templates[ $template ] ?? $template );
			return;
		}

		if ( 'dspi_shortcode' === $column ) {
			printf(
				'<code>[popupino_popup id="%d"]</code>',
				absint( $post_id )
			);
		}
	}
}
