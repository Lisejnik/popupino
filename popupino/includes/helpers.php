<?php
/**
 * Shared helper functions for Popupino.
 *
 * @package Popupino
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return default popup meta values.
 *
 * @return array<string, mixed>
 */
function dspi_get_default_meta(): array {
	return array(
		'heading'        => '',
		'text'           => '',
		'date_line'      => '',
		'content_mode'   => 'builder',
		'custom_html'    => '',
		'button_text'    => '',
		'button_url'     => '',
		'button_target'  => 'same',
		'background_id'  => 0,
		'background_size'=> 'cover',
		'text_color'     => '#ffffff',
		'button_color'   => '#2ea3f2',
		'overlay_color'  => '#000000',
		'overlay_opacity'=> '0.45',
		'font_family'    => 'inherit',
		'heading_size'   => 26,
		'heading_bold'   => '1',
		'heading_italic' => '0',
		'text_size'      => 16,
		'text_bold'      => '0',
		'text_italic'    => '0',
		'button_size'    => 15,
		'button_bold'    => '1',
		'button_italic'  => '0',
		'popup_width'    => '',
		'popup_width_unit' => 'px',
		'popup_height'   => '',
		'popup_height_unit' => 'px',
		'is_active'      => '0',
		'auto_display'   => '1',
		'display_mode'   => 'all',
		'page_ids'       => '',
		'delay'          => 0,
		'frequency'      => 'always',
		'custom_days'    => 7,
		'start_date'     => '',
		'end_date'       => '',
		'template'       => 'bottom-right',
	);
}

/**
 * Available content editing modes.
 *
 * @return array<string, string>
 */
function dspi_get_content_modes(): array {
	return array(
		'builder' => __( 'Visual builder fields', 'popupino' ),
		'html'    => __( 'Custom HTML / CSS / JS', 'popupino' ),
	);
}

/**
 * Get a popup meta value with a safe default.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key without the dspi_ prefix.
 * @return mixed
 */
function dspi_get_meta( int $post_id, string $key ) {
	$defaults = dspi_get_default_meta();
	$value    = get_post_meta( $post_id, '_dspi_' . $key, true );

	if ( '' === $value && array_key_exists( $key, $defaults ) ) {
		return $defaults[ $key ];
	}

	return $value;
}

/**
 * Available button link target options.
 *
 * @return array<string, string>
 */
function dspi_get_button_target_options(): array {
	return array(
		'same'  => __( 'Open in the same tab', 'popupino' ),
		'blank' => __( 'Open in a new tab', 'popupino' ),
	);
}

/**
 * Available font family options.
 *
 * @return array<string, string>
 */
function dspi_get_font_family_options(): array {
	return array(
		'inherit'   => __( 'Theme default', 'popupino' ),
		'arial'     => 'Arial',
		'georgia'   => 'Georgia',
		'tahoma'    => 'Tahoma',
		'trebuchet' => 'Trebuchet MS',
		'verdana'   => 'Verdana',
		'system'    => __( 'System UI', 'popupino' ),
	);
}

/**
 * CSS font-family values for the option keys.
 *
 * @return array<string, string>
 */
function dspi_get_font_family_css_values(): array {
	return array(
		'inherit'   => 'inherit',
		'arial'     => 'Arial, Helvetica, sans-serif',
		'georgia'   => 'Georgia, serif',
		'tahoma'    => 'Tahoma, Geneva, sans-serif',
		'trebuchet' => '"Trebuchet MS", Helvetica, sans-serif',
		'verdana'   => 'Verdana, Geneva, sans-serif',
		'system'    => '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
	);
}

/**
 * Available background image sizing options.
 *
 * @return array<string, string>
 */
function dspi_get_background_size_options(): array {
	return array(
		'cover'   => __( 'Cover area', 'popupino' ),
		'contain' => __( 'Fit entire image', 'popupino' ),
		'stretch' => __( 'Stretch to popup', 'popupino' ),
		'auto'    => __( 'Original size', 'popupino' ),
	);
}

/**
 * CSS background-size values for the option keys.
 *
 * @return array<string, string>
 */
function dspi_get_background_size_css_values(): array {
	return array(
		'cover'   => 'cover',
		'contain' => 'contain',
		'stretch' => '100% 100%',
		'auto'    => 'auto',
	);
}

/**
 * Units allowed for manual popup dimensions.
 *
 * @return array<string, string>
 */
function dspi_get_dimension_units(): array {
	return array(
		'px' => 'px',
		'vw' => 'vw',
		'%'  => '%',
	);
}

/**
 * Available display modes.
 *
 * @return array<string, string>
 */
function dspi_get_display_modes(): array {
	return array(
		'all'     => __( 'All pages', 'popupino' ),
		'include' => __( 'Only selected pages by ID', 'popupino' ),
		'hide'    => __( 'Hide on selected pages by ID', 'popupino' ),
	);
}

/**
 * Available frequency options.
 *
 * @return array<string, string>
 */
function dspi_get_frequency_options(): array {
	return array(
		'always'  => __( 'Show every time', 'popupino' ),
		'session' => __( 'Show once per browser session', 'popupino' ),
		'day'     => __( 'Show again after 1 day', 'popupino' ),
		'custom'  => __( 'Show again after a custom number of days', 'popupino' ),
	);
}

/**
 * Available popup templates.
 *
 * @return array<string, string>
 */
function dspi_get_templates(): array {
	return array(
		'bottom-right' => __( 'Small popup at bottom right', 'popupino' ),
		'bottom-left'  => __( 'Small popup at bottom left', 'popupino' ),
		'center-modal' => __( 'Centered modal', 'popupino' ),
		'fullscreen'   => __( 'Fullscreen popup', 'popupino' ),
		'top-bar'      => __( 'Top bar', 'popupino' ),
		'bottom-bar'   => __( 'Bottom bar', 'popupino' ),
	);
}

/**
 * Sanitize comma-separated page IDs.
 *
 * @param string $value Raw value.
 * @return string
 */
function dspi_sanitize_page_ids( string $value ): string {
	$ids = array_filter(
		array_map(
			'absint',
			preg_split( '/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY )
		)
	);

	return implode( ',', array_unique( $ids ) );
}

/**
 * Convert a comma-separated ID string to integers.
 *
 * @param string $value Comma-separated IDs.
 * @return array<int>
 */
function dspi_parse_page_ids( string $value ): array {
	if ( '' === trim( $value ) ) {
		return array();
	}

	return array_values(
		array_filter(
			array_map( 'absint', explode( ',', $value ) )
		)
	);
}

/**
 * Sanitize a hex color value.
 *
 * @param string $value    Raw color.
 * @param string $fallback Fallback hex color.
 * @return string
 */
function dspi_sanitize_hex_color( string $value, string $fallback = '#000000' ): string {
	$color = sanitize_hex_color( $value );

	return $color ? $color : $fallback;
}

/**
 * Build a CSS rgba color from a hex color and opacity.
 *
 * @param string $hex     Hex color.
 * @param mixed  $opacity Opacity between 0 and 1.
 * @return string
 */
function dspi_hex_to_rgba( string $hex, $opacity ): string {
	$hex     = ltrim( dspi_sanitize_hex_color( $hex ), '#' );
	$opacity = max( 0, min( 1, (float) $opacity ) );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	$red   = hexdec( substr( $hex, 0, 2 ) );
	$green = hexdec( substr( $hex, 2, 2 ) );
	$blue  = hexdec( substr( $hex, 4, 2 ) );

	return sprintf( 'rgba(%d, %d, %d, %.2F)', $red, $green, $blue, $opacity );
}

/**
 * Check whether a popup is within its date range.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function dspi_is_within_date_range( int $post_id ): bool {
	$now        = current_time( 'timestamp' );
	$start_date = dspi_get_meta( $post_id, 'start_date' );
	$end_date   = dspi_get_meta( $post_id, 'end_date' );

	if ( $start_date ) {
		$start_timestamp = strtotime( $start_date . ' 00:00:00' );
		if ( $start_timestamp && $now < $start_timestamp ) {
			return false;
		}
	}

	if ( $end_date ) {
		$end_timestamp = strtotime( $end_date . ' 23:59:59' );
		if ( $end_timestamp && $now > $end_timestamp ) {
			return false;
		}
	}

	return true;
}

/**
 * Check whether a popup should be rendered on the current request.
 *
 * Frequency is evaluated in JavaScript, because it depends on browser storage.
 *
 * @param int  $post_id          Popup post ID.
 * @param bool $manual_insertion Whether shortcode/manual rendering is being used.
 * @return bool
 */
function dspi_should_render_popup( int $post_id, bool $manual_insertion = false ): bool {
	if ( 'publish' !== get_post_status( $post_id ) ) {
		return false;
	}

	if ( '1' !== (string) dspi_get_meta( $post_id, 'is_active' ) ) {
		return false;
	}

	if ( ! dspi_is_within_date_range( $post_id ) ) {
		return false;
	}

	if ( $manual_insertion ) {
		return true;
	}

	if ( '1' !== (string) dspi_get_meta( $post_id, 'auto_display' ) ) {
		return false;
	}

	$display_mode = (string) dspi_get_meta( $post_id, 'display_mode' );
	$page_ids     = dspi_parse_page_ids( (string) dspi_get_meta( $post_id, 'page_ids' ) );
	$current_id   = (int) get_queried_object_id();

	if ( 'include' === $display_mode ) {
		return $current_id > 0 && in_array( $current_id, $page_ids, true );
	}

	if ( 'hide' === $display_mode || 'exclude' === $display_mode ) {
		return ! ( $current_id > 0 && in_array( $current_id, $page_ids, true ) );
	}

	return true;
}
