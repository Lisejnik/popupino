<?php
/**
 * Frontend rendering and shortcode.
 *
 * @package DiviSimplePopups
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend output.
 */
final class DSPI_Frontend {
	/**
	 * Register hooks.
	 */
	public static function init(): void {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render_auto_popups' ) );
		add_shortcode( 'dspi_popup', array( __CLASS__, 'shortcode' ) );
	}

	/**
	 * Enqueue frontend assets.
	 */
	public static function enqueue_assets(): void {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_style( 'dspi-frontend', DSPI_URL . 'assets/css/frontend.css', array(), DSPI_VERSION );
		wp_enqueue_script( 'dspi-frontend', DSPI_URL . 'assets/js/frontend.js', array(), DSPI_VERSION, true );
	}

	/**
	 * Render automatically displayed popups.
	 */
	public static function render_auto_popups(): void {
		$query = new WP_Query(
			array(
				'post_type'              => DSPI_POST_TYPE,
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => false,
				'orderby'                => 'menu_order date',
				'order'                  => 'ASC',
			)
		);

		if ( ! $query->have_posts() ) {
			return;
		}

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = (int) get_the_ID();

			if ( dspi_should_render_popup( $post_id, false ) ) {
				echo self::render_popup( $post_id, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		wp_reset_postdata();
	}

	/**
	 * Shortcode callback.
	 *
	 * @param mixed $atts Shortcode attributes.
	 * @return string
	 */
	public static function shortcode( $atts ): string {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			(array) $atts,
			'dspi_popup'
		);

		$post_id = absint( $atts['id'] );
		if ( ! $post_id || DSPI_POST_TYPE !== get_post_type( $post_id ) ) {
			return '';
		}

		return self::render_popup( $post_id, true );
	}

	/**
	 * Render one popup.
	 *
	 * @param int  $post_id          Popup post ID.
	 * @param bool $manual_insertion Whether shortcode/manual rendering is being used.
	 * @return string
	 */
	public static function render_popup( int $post_id, bool $manual_insertion = false ): string {
		if ( ! dspi_should_render_popup( $post_id, $manual_insertion ) ) {
			return '';
		}

		$template      = (string) dspi_get_meta( $post_id, 'template' );
		$templates     = dspi_get_templates();
		$template      = array_key_exists( $template, $templates ) ? $template : 'bottom-right';
		$content_mode  = (string) dspi_get_meta( $post_id, 'content_mode' );
		$custom_html   = (string) dspi_get_meta( $post_id, 'custom_html' );
		$heading       = (string) dspi_get_meta( $post_id, 'heading' );
		$text          = (string) dspi_get_meta( $post_id, 'text' );
		$date_line     = (string) dspi_get_meta( $post_id, 'date_line' );
		$button_text   = (string) dspi_get_meta( $post_id, 'button_text' );
		$button_url    = (string) dspi_get_meta( $post_id, 'button_url' );
		$button_target = (string) dspi_get_meta( $post_id, 'button_target' );
		$frequency     = (string) dspi_get_meta( $post_id, 'frequency' );
		$custom_days   = max( 1, absint( dspi_get_meta( $post_id, 'custom_days' ) ) );
		$delay         = max( 0, absint( dspi_get_meta( $post_id, 'delay' ) ) );
		$background_id = absint( dspi_get_meta( $post_id, 'background_id' ) );
		$background    = $background_id ? wp_get_attachment_image_url( $background_id, 'large' ) : '';
		$overlay       = dspi_hex_to_rgba( (string) dspi_get_meta( $post_id, 'overlay_color' ), dspi_get_meta( $post_id, 'overlay_opacity' ) );
		$font_values   = dspi_get_font_family_css_values();
		$bg_sizes      = dspi_get_background_size_css_values();
		$font_key      = (string) dspi_get_meta( $post_id, 'font_family' );
		$bg_size_key   = (string) dspi_get_meta( $post_id, 'background_size' );
		$width         = (string) dspi_get_meta( $post_id, 'popup_width' );
		$height        = (string) dspi_get_meta( $post_id, 'popup_height' );
		$width_unit    = (string) dspi_get_meta( $post_id, 'popup_width_unit' );
		$height_unit   = (string) dspi_get_meta( $post_id, 'popup_height_unit' );

		$style = sprintf(
			'--dspi-text-color:%s;--dspi-button-color:%s;--dspi-overlay-color:%s;--dspi-font-family:%s;--dspi-title-size:%dpx;--dspi-title-weight:%s;--dspi-title-style:%s;--dspi-text-size:%dpx;--dspi-text-weight:%s;--dspi-text-style:%s;--dspi-button-size:%dpx;--dspi-button-weight:%s;--dspi-button-style:%s;--dspi-bg-size:%s;%s%s%s',
			dspi_sanitize_hex_color( (string) dspi_get_meta( $post_id, 'text_color' ), '#ffffff' ),
			dspi_sanitize_hex_color( (string) dspi_get_meta( $post_id, 'button_color' ), '#2ea3f2' ),
			$overlay,
			$font_values[ $font_key ] ?? 'inherit',
			absint( dspi_get_meta( $post_id, 'heading_size' ) ),
			'1' === (string) dspi_get_meta( $post_id, 'heading_bold' ) ? '800' : '400',
			'1' === (string) dspi_get_meta( $post_id, 'heading_italic' ) ? 'italic' : 'normal',
			absint( dspi_get_meta( $post_id, 'text_size' ) ),
			'1' === (string) dspi_get_meta( $post_id, 'text_bold' ) ? '700' : '400',
			'1' === (string) dspi_get_meta( $post_id, 'text_italic' ) ? 'italic' : 'normal',
			absint( dspi_get_meta( $post_id, 'button_size' ) ),
			'1' === (string) dspi_get_meta( $post_id, 'button_bold' ) ? '700' : '400',
			'1' === (string) dspi_get_meta( $post_id, 'button_italic' ) ? 'italic' : 'normal',
			$bg_sizes[ $bg_size_key ] ?? 'cover',
			$background ? '--dspi-bg-image:url("' . esc_url_raw( $background ) . '");' : '',
			$width ? '--dspi-popup-width:' . (float) $width . ( array_key_exists( $width_unit, dspi_get_dimension_units() ) ? $width_unit : 'px' ) . ';' : '',
			$height ? '--dspi-popup-height:' . (float) $height . ( array_key_exists( $height_unit, dspi_get_dimension_units() ) ? $height_unit : 'px' ) . ';' : ''
		);

		ob_start();
		?>
		<div
			id="dspi-popup-<?php echo esc_attr( $post_id ); ?>"
			class="dspi-popup dspi-popup--<?php echo esc_attr( $template ); ?> dspi-popup--hidden"
			data-dspi-popup
			data-popup-id="<?php echo esc_attr( $post_id ); ?>"
			data-frequency="<?php echo esc_attr( $frequency ); ?>"
			data-custom-days="<?php echo esc_attr( $custom_days ); ?>"
			data-delay="<?php echo esc_attr( $delay ); ?>"
			data-manual="<?php echo esc_attr( $manual_insertion ? '1' : '0' ); ?>"
			role="dialog"
			aria-modal="<?php echo esc_attr( in_array( $template, array( 'center-modal', 'fullscreen' ), true ) ? 'true' : 'false' ); ?>"
			aria-labelledby="dspi-popup-title-<?php echo esc_attr( $post_id ); ?>"
		>
			<div class="dspi-popup__panel" style="<?php echo esc_attr( $style ); ?>">
				<button type="button" class="dspi-popup__close" data-dspi-close aria-label="<?php esc_attr_e( 'Close popup', 'divi-simple-popups' ); ?>">×</button>
				<div class="dspi-popup__content">
					<?php if ( 'html' === $content_mode && $custom_html ) : ?>
						<div class="dspi-popup__custom-html">
							<?php echo $custom_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php else : ?>
						<?php if ( $date_line ) : ?>
							<p class="dspi-popup__date"><?php echo esc_html( $date_line ); ?></p>
						<?php endif; ?>

						<?php if ( $heading ) : ?>
							<h2 id="dspi-popup-title-<?php echo esc_attr( $post_id ); ?>" class="dspi-popup__title"><?php echo esc_html( $heading ); ?></h2>
						<?php endif; ?>

						<?php if ( $text ) : ?>
							<div class="dspi-popup__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
						<?php endif; ?>

						<?php if ( $button_text && $button_url ) : ?>
							<a
								class="dspi-popup__button"
								href="<?php echo esc_url( $button_url ); ?>"
								<?php if ( 'blank' === $button_target ) : ?>
									target="_blank"
									rel="noopener noreferrer"
								<?php endif; ?>
							><?php echo esc_html( $button_text ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php

		return (string) ob_get_clean();
	}
}
