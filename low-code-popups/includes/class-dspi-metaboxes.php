<?php
/**
 * Admin metaboxes and meta persistence.
 *
 * @package LowCodePopups
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Popup metaboxes.
 */
final class DSPI_Metaboxes {
	/**
	 * Register hooks.
	 */
	public static function init(): void {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_metaboxes' ) );
		add_action( 'save_post_' . DSPI_POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
	}

	/**
	 * Add metaboxes.
	 */
	public static function add_metaboxes(): void {
		add_meta_box(
			'dspi_content',
			__( 'Popup Content', 'low-code-popups' ),
			array( __CLASS__, 'render_content_metabox' ),
			DSPI_POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'dspi_display',
			__( 'Display Rules', 'low-code-popups' ),
			array( __CLASS__, 'render_display_metabox' ),
			DSPI_POST_TYPE,
			'normal',
			'default'
		);

		add_meta_box(
			'dspi_design',
			__( 'Design Settings', 'low-code-popups' ),
			array( __CLASS__, 'render_design_metabox' ),
			DSPI_POST_TYPE,
			'normal',
			'default'
		);

		add_meta_box(
			'dspi_template',
			__( 'Template', 'low-code-popups' ),
			array( __CLASS__, 'render_template_metabox' ),
			DSPI_POST_TYPE,
			'side',
			'default'
		);

		add_meta_box(
			'dspi_preview',
			__( 'Live Preview', 'low-code-popups' ),
			array( __CLASS__, 'render_preview_metabox' ),
			DSPI_POST_TYPE,
			'normal',
			'low'
		);
	}

	/**
	 * Enqueue admin CSS/JS only on popup edit screens.
	 *
	 * @param string $hook Current admin hook.
	 */
	public static function enqueue_admin_assets( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || DSPI_POST_TYPE !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'dspi-admin', DSPI_URL . 'assets/css/admin.css', array(), DSPI_VERSION );
		wp_enqueue_script( 'dspi-admin', DSPI_URL . 'assets/js/admin.js', array(), DSPI_VERSION, true );
		wp_localize_script(
			'dspi-admin',
			'dspiAdmin',
			array(
				'mediaTitle'  => __( 'Choose a background image', 'low-code-popups' ),
				'mediaButton' => __( 'Use this image', 'low-code-popups' ),
				'defaults'    => array(
					'heading'        => __( 'Popup headline', 'low-code-popups' ),
					'text'           => __( 'Your popup text will appear here.', 'low-code-popups' ),
					'button'         => __( 'Button', 'low-code-popups' ),
					'date'           => __( 'Optional date or note', 'low-code-popups' ),
					'textColor'      => '#ffffff',
					'buttonColor'    => '#2ea3f2',
					'overlayColor'   => '#000000',
					'overlayOpacity' => '0.45',
				),
			)
		);
	}

	/**
	 * Render content metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function render_content_metabox( WP_Post $post ): void {
		wp_nonce_field( 'dspi_save_popup', 'dspi_nonce' );

		$background_id  = absint( dspi_get_meta( $post->ID, 'background_id' ) );
		$background_url = $background_id ? wp_get_attachment_image_url( $background_id, 'medium' ) : '';
		$button_target  = (string) dspi_get_meta( $post->ID, 'button_target' );
		$content_mode   = (string) dspi_get_meta( $post->ID, 'content_mode' );
		?>
		<div class="dspi-admin-grid">
			<fieldset class="dspi-fieldset">
				<legend><strong><?php esc_html_e( 'Content editing mode', 'low-code-popups' ); ?></strong></legend>
				<div class="dspi-radio-row">
					<?php foreach ( dspi_get_content_modes() as $value => $label ) : ?>
						<label>
							<input type="radio" name="dspi_meta[content_mode]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $content_mode, $value ); ?>>
							<span><?php echo esc_html( $label ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<p class="description"><?php esc_html_e( 'Use the visual fields for the standard popup builder, or switch to custom code for advanced layouts.', 'low-code-popups' ); ?></p>
			</fieldset>

			<div class="dspi-builder-fields">
			<p>
				<label for="dspi_heading"><strong><?php esc_html_e( 'Popup headline', 'low-code-popups' ); ?></strong></label>
				<input type="text" id="dspi_heading" name="dspi_meta[heading]" class="widefat dspi-preview-input" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'heading' ) ); ?>">
			</p>

			<p>
				<label for="dspi_text"><strong><?php esc_html_e( 'Popup text', 'low-code-popups' ); ?></strong></label>
				<textarea id="dspi_text" name="dspi_meta[text]" rows="5" class="widefat dspi-preview-input"><?php echo esc_textarea( dspi_get_meta( $post->ID, 'text' ) ); ?></textarea>
				<span class="description"><?php esc_html_e( 'Basic safe HTML is allowed, such as links or emphasis.', 'low-code-popups' ); ?></span>
			</p>

			<p>
				<label for="dspi_date_line"><strong><?php esc_html_e( 'Date / supporting line', 'low-code-popups' ); ?></strong></label>
				<input type="text" id="dspi_date_line" name="dspi_meta[date_line]" class="widefat dspi-preview-input" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'date_line' ) ); ?>">
			</p>

			<div class="dspi-admin-columns dspi-button-settings-row">
				<p>
					<label for="dspi_button_text"><strong><?php esc_html_e( 'Button text', 'low-code-popups' ); ?></strong></label>
					<input type="text" id="dspi_button_text" name="dspi_meta[button_text]" class="widefat dspi-preview-input" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'button_text' ) ); ?>">
				</p>
				<p>
					<label for="dspi_button_url"><strong><?php esc_html_e( 'Button URL', 'low-code-popups' ); ?></strong></label>
					<input type="url" id="dspi_button_url" name="dspi_meta[button_url]" class="widefat" value="<?php echo esc_url( dspi_get_meta( $post->ID, 'button_url' ) ); ?>" placeholder="https://">
				</p>
				<p>
					<label for="dspi_button_target"><strong><?php esc_html_e( 'Button link behavior', 'low-code-popups' ); ?></strong></label>
					<select id="dspi_button_target" name="dspi_meta[button_target]" class="widefat">
						<?php foreach ( dspi_get_button_target_options() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $button_target, $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
			</div>

			<div class="dspi-media-field">
				<label><strong><?php esc_html_e( 'Background image', 'low-code-popups' ); ?></strong></label>
				<input type="hidden" id="dspi_background_id" name="dspi_meta[background_id]" value="<?php echo esc_attr( $background_id ); ?>">
				<div class="dspi-image-preview" data-empty-text="<?php esc_attr_e( 'No image selected yet.', 'low-code-popups' ); ?>">
					<?php if ( $background_url ) : ?>
						<img src="<?php echo esc_url( $background_url ); ?>" alt="">
					<?php endif; ?>
				</div>
				<p>
					<button type="button" class="button dspi-upload-image"><?php esc_html_e( 'Choose image', 'low-code-popups' ); ?></button>
					<button type="button" class="button dspi-remove-image"><?php esc_html_e( 'Remove', 'low-code-popups' ); ?></button>
				</p>
			</div>

			<div class="dspi-admin-columns dspi-color-row">
				<p>
					<label for="dspi_text_color"><strong><?php esc_html_e( 'Text color', 'low-code-popups' ); ?></strong></label>
					<input type="color" id="dspi_text_color" name="dspi_meta[text_color]" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'text_color' ) ); ?>" data-dspi-default="#ffffff">
				</p>
				<p>
					<label for="dspi_button_color"><strong><?php esc_html_e( 'Button color', 'low-code-popups' ); ?></strong></label>
					<input type="color" id="dspi_button_color" name="dspi_meta[button_color]" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'button_color' ) ); ?>" data-dspi-default="#2ea3f2">
				</p>
				<p>
					<label for="dspi_overlay_color"><strong><?php esc_html_e( 'Overlay color', 'low-code-popups' ); ?></strong></label>
					<input type="color" id="dspi_overlay_color" name="dspi_meta[overlay_color]" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'overlay_color' ) ); ?>" data-dspi-default="#000000">
				</p>
				<p>
					<label for="dspi_overlay_opacity"><strong><?php esc_html_e( 'Overlay opacity', 'low-code-popups' ); ?></strong></label>
					<input type="number" id="dspi_overlay_opacity" name="dspi_meta[overlay_opacity]" min="0" max="1" step="0.05" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'overlay_opacity' ) ); ?>" data-dspi-default="0.45">
				</p>
			</div>
			<p class="dspi-color-reset-row">
				<button type="button" class="button dspi-reset-colors"><?php esc_html_e( 'Reset colors to defaults', 'low-code-popups' ); ?></button>
				<span class="description"><?php esc_html_e( 'Restores the original text, button, overlay color, and overlay opacity values.', 'low-code-popups' ); ?></span>
			</p>
			</div>

			<div class="dspi-custom-html-fields">
				<p>
					<label for="dspi_custom_html"><strong><?php esc_html_e( 'Custom HTML / CSS / JS', 'low-code-popups' ); ?></strong></label>
					<textarea id="dspi_custom_html" name="dspi_meta[custom_html]" rows="12" class="widefat code"><?php echo esc_textarea( dspi_get_meta( $post->ID, 'custom_html' ) ); ?></textarea>
					<span class="description">
						<?php esc_html_e( 'Advanced users can write full custom markup here. Users with the unfiltered_html capability can include CSS and JavaScript; other users will have unsafe tags removed on save.', 'low-code-popups' ); ?>
					</span>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render design settings metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function render_design_metabox( WP_Post $post ): void {
		$font_family      = (string) dspi_get_meta( $post->ID, 'font_family' );
		$background_size  = (string) dspi_get_meta( $post->ID, 'background_size' );
		$popup_width_unit = (string) dspi_get_meta( $post->ID, 'popup_width_unit' );
		$popup_height_unit = (string) dspi_get_meta( $post->ID, 'popup_height_unit' );
		?>
		<div class="dspi-admin-grid">
			<div class="dspi-admin-columns">
				<p>
					<label for="dspi_font_family"><strong><?php esc_html_e( 'Font family', 'low-code-popups' ); ?></strong></label>
					<select id="dspi_font_family" name="dspi_meta[font_family]" class="widefat dspi-preview-style-input">
						<?php foreach ( dspi_get_font_family_options() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $font_family, $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p>
					<label for="dspi_background_size"><strong><?php esc_html_e( 'Background image size', 'low-code-popups' ); ?></strong></label>
					<select id="dspi_background_size" name="dspi_meta[background_size]" class="widefat dspi-preview-style-input">
						<?php foreach ( dspi_get_background_size_options() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $background_size, $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
			</div>

			<div class="dspi-admin-columns dspi-typography-row">
				<p>
					<label for="dspi_heading_size"><strong><?php esc_html_e( 'Headline size', 'low-code-popups' ); ?></strong></label>
					<input type="number" id="dspi_heading_size" name="dspi_meta[heading_size]" min="10" max="120" step="1" value="<?php echo esc_attr( absint( dspi_get_meta( $post->ID, 'heading_size' ) ) ); ?>" class="dspi-preview-style-input">
					<label class="dspi-inline-check"><input type="checkbox" name="dspi_meta[heading_bold]" value="1" <?php checked( '1', dspi_get_meta( $post->ID, 'heading_bold' ) ); ?> class="dspi-preview-style-input"> <?php esc_html_e( 'Bold', 'low-code-popups' ); ?></label>
					<label class="dspi-inline-check"><input type="checkbox" name="dspi_meta[heading_italic]" value="1" <?php checked( '1', dspi_get_meta( $post->ID, 'heading_italic' ) ); ?> class="dspi-preview-style-input"> <?php esc_html_e( 'Italic', 'low-code-popups' ); ?></label>
				</p>
				<p>
					<label for="dspi_text_size"><strong><?php esc_html_e( 'Text size', 'low-code-popups' ); ?></strong></label>
					<input type="number" id="dspi_text_size" name="dspi_meta[text_size]" min="10" max="80" step="1" value="<?php echo esc_attr( absint( dspi_get_meta( $post->ID, 'text_size' ) ) ); ?>" class="dspi-preview-style-input">
					<label class="dspi-inline-check"><input type="checkbox" name="dspi_meta[text_bold]" value="1" <?php checked( '1', dspi_get_meta( $post->ID, 'text_bold' ) ); ?> class="dspi-preview-style-input"> <?php esc_html_e( 'Bold', 'low-code-popups' ); ?></label>
					<label class="dspi-inline-check"><input type="checkbox" name="dspi_meta[text_italic]" value="1" <?php checked( '1', dspi_get_meta( $post->ID, 'text_italic' ) ); ?> class="dspi-preview-style-input"> <?php esc_html_e( 'Italic', 'low-code-popups' ); ?></label>
				</p>
				<p>
					<label for="dspi_button_size"><strong><?php esc_html_e( 'Button text size', 'low-code-popups' ); ?></strong></label>
					<input type="number" id="dspi_button_size" name="dspi_meta[button_size]" min="10" max="60" step="1" value="<?php echo esc_attr( absint( dspi_get_meta( $post->ID, 'button_size' ) ) ); ?>" class="dspi-preview-style-input">
					<label class="dspi-inline-check"><input type="checkbox" name="dspi_meta[button_bold]" value="1" <?php checked( '1', dspi_get_meta( $post->ID, 'button_bold' ) ); ?> class="dspi-preview-style-input"> <?php esc_html_e( 'Bold', 'low-code-popups' ); ?></label>
					<label class="dspi-inline-check"><input type="checkbox" name="dspi_meta[button_italic]" value="1" <?php checked( '1', dspi_get_meta( $post->ID, 'button_italic' ) ); ?> class="dspi-preview-style-input"> <?php esc_html_e( 'Italic', 'low-code-popups' ); ?></label>
				</p>
			</div>

			<div class="dspi-admin-columns dspi-dimensions-row">
				<p>
					<label for="dspi_popup_width"><strong><?php esc_html_e( 'Popup width', 'low-code-popups' ); ?></strong></label>
					<span class="dspi-unit-field">
						<input type="number" id="dspi_popup_width" name="dspi_meta[popup_width]" min="0" step="1" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'popup_width' ) ); ?>" class="dspi-preview-style-input" placeholder="<?php esc_attr_e( 'Default', 'low-code-popups' ); ?>">
						<select name="dspi_meta[popup_width_unit]" id="dspi_popup_width_unit" class="dspi-preview-style-input">
							<?php foreach ( dspi_get_dimension_units() as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $popup_width_unit, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</span>
				</p>
				<p>
					<label for="dspi_popup_height"><strong><?php esc_html_e( 'Popup height', 'low-code-popups' ); ?></strong></label>
					<span class="dspi-unit-field">
						<input type="number" id="dspi_popup_height" name="dspi_meta[popup_height]" min="0" step="1" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'popup_height' ) ); ?>" class="dspi-preview-style-input" placeholder="<?php esc_attr_e( 'Default', 'low-code-popups' ); ?>">
						<select name="dspi_meta[popup_height_unit]" id="dspi_popup_height_unit" class="dspi-preview-style-input">
							<?php foreach ( dspi_get_dimension_units() as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $popup_height_unit, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</span>
				</p>
			</div>
			<p class="description"><?php esc_html_e( 'Leave width or height empty to use the selected template default. The popup will still respect the visitor screen size.', 'low-code-popups' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render display metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function render_display_metabox( WP_Post $post ): void {
		$display_mode = (string) dspi_get_meta( $post->ID, 'display_mode' );
		$display_mode = 'exclude' === $display_mode ? 'hide' : $display_mode;
		$frequency    = (string) dspi_get_meta( $post->ID, 'frequency' );
		?>
		<div class="dspi-admin-grid">
			<label class="dspi-checkbox">
				<input type="checkbox" name="dspi_meta[is_active]" value="1" <?php checked( '1', dspi_get_meta( $post->ID, 'is_active' ) ); ?>>
				<span><?php esc_html_e( 'Active popup', 'low-code-popups' ); ?></span>
			</label>

			<label class="dspi-checkbox">
				<input type="checkbox" name="dspi_meta[auto_display]" value="1" <?php checked( '1', dspi_get_meta( $post->ID, 'auto_display' ) ); ?>>
				<span><?php esc_html_e( 'Display automatically on the site', 'low-code-popups' ); ?></span>
			</label>

			<p>
				<label for="dspi_display_mode"><strong><?php esc_html_e( 'Display mode', 'low-code-popups' ); ?></strong></label>
				<select id="dspi_display_mode" name="dspi_meta[display_mode]" class="widefat">
					<?php foreach ( dspi_get_display_modes() as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $display_mode, $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p>
				<label for="dspi_page_ids"><strong><?php esc_html_e( 'Page IDs separated by commas', 'low-code-popups' ); ?></strong></label>
				<input type="text" id="dspi_page_ids" name="dspi_meta[page_ids]" class="widefat" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'page_ids' ) ); ?>" placeholder="12, 24, 35">
			</p>

			<div class="dspi-admin-columns">
				<p>
					<label for="dspi_delay"><strong><?php esc_html_e( 'Delay in seconds', 'low-code-popups' ); ?></strong></label>
					<input type="number" id="dspi_delay" name="dspi_meta[delay]" min="0" step="1" value="<?php echo esc_attr( absint( dspi_get_meta( $post->ID, 'delay' ) ) ); ?>">
				</p>
				<p>
					<label for="dspi_frequency"><strong><?php esc_html_e( 'After closing, when should this popup appear again?', 'low-code-popups' ); ?></strong></label>
					<select id="dspi_frequency" name="dspi_meta[frequency]" class="widefat">
						<?php foreach ( dspi_get_frequency_options() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $frequency, $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p>
					<label for="dspi_custom_days"><strong><?php esc_html_e( 'Custom days before showing again', 'low-code-popups' ); ?></strong></label>
					<input type="number" id="dspi_custom_days" name="dspi_meta[custom_days]" min="1" step="1" value="<?php echo esc_attr( max( 1, absint( dspi_get_meta( $post->ID, 'custom_days' ) ) ) ); ?>">
					<span class="description"><?php esc_html_e( 'Used only when the custom-days option is selected. Example: 7 means the popup can appear again 7 days after the visitor closes it.', 'low-code-popups' ); ?></span>
				</p>
			</div>

			<div class="dspi-help-box">
				<strong><?php esc_html_e( 'How display frequency works', 'low-code-popups' ); ?></strong>
				<ul>
					<li><?php esc_html_e( 'Show every time: the popup can appear again after every page load or refresh, even if the visitor closed it before.', 'low-code-popups' ); ?></li>
					<li><?php esc_html_e( 'Show once options: after the visitor closes the popup, the browser remembers it and hides it for the selected period.', 'low-code-popups' ); ?></li>
					<li><?php esc_html_e( 'Custom days does not disable the popup permanently. It only controls how long to wait before showing it again to the same visitor in the same browser.', 'low-code-popups' ); ?></li>
				</ul>
			</div>

			<div class="dspi-admin-columns">
				<p>
					<label for="dspi_start_date"><strong><?php esc_html_e( 'Show only from date', 'low-code-popups' ); ?></strong></label>
					<input type="date" id="dspi_start_date" name="dspi_meta[start_date]" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'start_date' ) ); ?>">
				</p>
				<p>
					<label for="dspi_end_date"><strong><?php esc_html_e( 'Show only until date', 'low-code-popups' ); ?></strong></label>
					<input type="date" id="dspi_end_date" name="dspi_meta[end_date]" value="<?php echo esc_attr( dspi_get_meta( $post->ID, 'end_date' ) ); ?>">
				</p>
			</div>

			<p class="description">
				<?php esc_html_e( 'Manual opening via a link still works even after the popup has been closed automatically for the selected period.', 'low-code-popups' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render template metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function render_template_metabox( WP_Post $post ): void {
		$template = (string) dspi_get_meta( $post->ID, 'template' );
		?>
		<p>
			<label for="dspi_template_select"><strong><?php esc_html_e( 'Choose a template', 'low-code-popups' ); ?></strong></label>
			<select id="dspi_template_select" name="dspi_meta[template]" class="widefat">
				<?php foreach ( dspi_get_templates() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $template, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="description">
			<?php
			printf(
				/* translators: 1: current popup ID for CSS class, 2: current popup ID for shortcode. */
				esc_html__( 'Manual opening: use the CSS class lcp-open-popup-%1$d or the shortcode [lcp_popup id="%2$d"].', 'low-code-popups' ),
				absint( $post->ID ),
				absint( $post->ID )
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render live preview metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function render_preview_metabox( WP_Post $post ): void {
		$template      = (string) dspi_get_meta( $post->ID, 'template' );
		$background_id = absint( dspi_get_meta( $post->ID, 'background_id' ) );
		$background    = $background_id ? wp_get_attachment_image_url( $background_id, 'large' ) : '';
		$overlay       = dspi_hex_to_rgba( (string) dspi_get_meta( $post->ID, 'overlay_color' ), dspi_get_meta( $post->ID, 'overlay_opacity' ) );
		$font_values   = dspi_get_font_family_css_values();
		$bg_sizes      = dspi_get_background_size_css_values();
		$font_key      = (string) dspi_get_meta( $post->ID, 'font_family' );
		$bg_size_key   = (string) dspi_get_meta( $post->ID, 'background_size' );
		$width         = (string) dspi_get_meta( $post->ID, 'popup_width' );
		$height        = (string) dspi_get_meta( $post->ID, 'popup_height' );
		$width_unit    = (string) dspi_get_meta( $post->ID, 'popup_width_unit' );
		$height_unit   = (string) dspi_get_meta( $post->ID, 'popup_height_unit' );
		$style         = sprintf(
			'--dspi-preview-text:%s;--dspi-preview-button:%s;--dspi-preview-overlay:%s;--dspi-preview-font-family:%s;--dspi-preview-bg-size:%s;--dspi-preview-title-size:%dpx;--dspi-preview-title-weight:%s;--dspi-preview-title-style:%s;--dspi-preview-text-size:%dpx;--dspi-preview-text-weight:%s;--dspi-preview-text-style:%s;--dspi-preview-button-size:%dpx;--dspi-preview-button-weight:%s;--dspi-preview-button-style:%s;%s%s%s',
			dspi_sanitize_hex_color( (string) dspi_get_meta( $post->ID, 'text_color' ), '#ffffff' ),
			dspi_sanitize_hex_color( (string) dspi_get_meta( $post->ID, 'button_color' ), '#2ea3f2' ),
			$overlay,
			$font_values[ $font_key ] ?? 'inherit',
			$bg_sizes[ $bg_size_key ] ?? 'cover',
			absint( dspi_get_meta( $post->ID, 'heading_size' ) ),
			'1' === (string) dspi_get_meta( $post->ID, 'heading_bold' ) ? '800' : '400',
			'1' === (string) dspi_get_meta( $post->ID, 'heading_italic' ) ? 'italic' : 'normal',
			absint( dspi_get_meta( $post->ID, 'text_size' ) ),
			'1' === (string) dspi_get_meta( $post->ID, 'text_bold' ) ? '700' : '400',
			'1' === (string) dspi_get_meta( $post->ID, 'text_italic' ) ? 'italic' : 'normal',
			absint( dspi_get_meta( $post->ID, 'button_size' ) ),
			'1' === (string) dspi_get_meta( $post->ID, 'button_bold' ) ? '700' : '400',
			'1' === (string) dspi_get_meta( $post->ID, 'button_italic' ) ? 'italic' : 'normal',
			$background ? '--dspi-preview-bg:url("' . esc_url_raw( $background ) . '");' : '',
			$width ? '--dspi-preview-width:' . (float) $width . ( array_key_exists( $width_unit, dspi_get_dimension_units() ) ? $width_unit : 'px' ) . ';' : '',
			$height ? '--dspi-preview-height:' . (float) $height . ( array_key_exists( $height_unit, dspi_get_dimension_units() ) ? $height_unit : 'px' ) . ';' : ''
		);
		?>
		<div class="dspi-live-preview" data-template="<?php echo esc_attr( $template ); ?>" style="<?php echo esc_attr( $style ); ?>">
			<div class="dspi-live-preview__toolbar">
				<strong><?php esc_html_e( 'Preview', 'low-code-popups' ); ?></strong>
				<span><?php esc_html_e( 'Updates while you edit the popup settings.', 'low-code-popups' ); ?></span>
			</div>
			<div class="dspi-live-preview__viewport">
				<div class="dspi-live-preview__site">
					<span></span>
					<span></span>
					<span></span>
				</div>
				<div class="dspi-live-preview__popup">
					<button type="button" aria-label="<?php esc_attr_e( 'Close popup', 'low-code-popups' ); ?>">×</button>
					<p class="dspi-live-preview__date"><?php echo esc_html( dspi_get_meta( $post->ID, 'date_line' ) ?: __( 'Optional date or note', 'low-code-popups' ) ); ?></p>
					<strong class="dspi-live-preview__heading"><?php echo esc_html( dspi_get_meta( $post->ID, 'heading' ) ?: __( 'Popup headline', 'low-code-popups' ) ); ?></strong>
					<div class="dspi-live-preview__text"><?php echo esc_html( wp_strip_all_tags( dspi_get_meta( $post->ID, 'text' ) ) ?: __( 'Your popup text will appear here.', 'low-code-popups' ) ); ?></div>
					<span class="dspi-live-preview__button"><?php echo esc_html( dspi_get_meta( $post->ID, 'button_text' ) ?: __( 'Button', 'low-code-popups' ) ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save popup meta.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public static function save( int $post_id, WP_Post $post ): void {
		if ( ! isset( $_POST['dspi_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dspi_nonce'] ) ), 'dspi_save_popup' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( DSPI_POST_TYPE !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$raw      = isset( $_POST['dspi_meta'] ) && is_array( $_POST['dspi_meta'] ) ? wp_unslash( $_POST['dspi_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Each field is sanitized below according to its expected type.
		$defaults = dspi_get_default_meta();
		$units    = dspi_get_dimension_units();
		$custom_html = (string) ( $raw['custom_html'] ?? '' );
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$custom_html = wp_kses_post( $custom_html );
		}

		$meta = array(
			'heading'         => sanitize_text_field( (string) ( $raw['heading'] ?? '' ) ),
			'text'            => wp_kses_post( (string) ( $raw['text'] ?? '' ) ),
			'date_line'       => sanitize_text_field( (string) ( $raw['date_line'] ?? '' ) ),
			'content_mode'    => array_key_exists( (string) ( $raw['content_mode'] ?? '' ), dspi_get_content_modes() ) ? (string) $raw['content_mode'] : $defaults['content_mode'],
			'custom_html'     => $custom_html,
			'button_text'     => sanitize_text_field( (string) ( $raw['button_text'] ?? '' ) ),
			'button_url'      => esc_url_raw( (string) ( $raw['button_url'] ?? '' ) ),
			'button_target'   => array_key_exists( (string) ( $raw['button_target'] ?? '' ), dspi_get_button_target_options() ) ? (string) $raw['button_target'] : $defaults['button_target'],
			'background_id'   => absint( $raw['background_id'] ?? 0 ),
			'background_size' => array_key_exists( (string) ( $raw['background_size'] ?? '' ), dspi_get_background_size_options() ) ? (string) $raw['background_size'] : $defaults['background_size'],
			'text_color'      => dspi_sanitize_hex_color( (string) ( $raw['text_color'] ?? $defaults['text_color'] ), $defaults['text_color'] ),
			'button_color'    => dspi_sanitize_hex_color( (string) ( $raw['button_color'] ?? $defaults['button_color'] ), $defaults['button_color'] ),
			'overlay_color'   => dspi_sanitize_hex_color( (string) ( $raw['overlay_color'] ?? $defaults['overlay_color'] ), $defaults['overlay_color'] ),
			'overlay_opacity' => (string) max( 0, min( 1, (float) ( $raw['overlay_opacity'] ?? $defaults['overlay_opacity'] ) ) ),
			'font_family'     => array_key_exists( (string) ( $raw['font_family'] ?? '' ), dspi_get_font_family_options() ) ? (string) $raw['font_family'] : $defaults['font_family'],
			'heading_size'    => max( 10, min( 120, absint( $raw['heading_size'] ?? $defaults['heading_size'] ) ) ),
			'heading_bold'    => isset( $raw['heading_bold'] ) ? '1' : '0',
			'heading_italic'  => isset( $raw['heading_italic'] ) ? '1' : '0',
			'text_size'       => max( 10, min( 80, absint( $raw['text_size'] ?? $defaults['text_size'] ) ) ),
			'text_bold'       => isset( $raw['text_bold'] ) ? '1' : '0',
			'text_italic'     => isset( $raw['text_italic'] ) ? '1' : '0',
			'button_size'     => max( 10, min( 60, absint( $raw['button_size'] ?? $defaults['button_size'] ) ) ),
			'button_bold'     => isset( $raw['button_bold'] ) ? '1' : '0',
			'button_italic'   => isset( $raw['button_italic'] ) ? '1' : '0',
			'popup_width'     => self::sanitize_dimension_value( (string) ( $raw['popup_width'] ?? '' ) ),
			'popup_width_unit'=> array_key_exists( (string) ( $raw['popup_width_unit'] ?? '' ), $units ) ? (string) $raw['popup_width_unit'] : $defaults['popup_width_unit'],
			'popup_height'    => self::sanitize_dimension_value( (string) ( $raw['popup_height'] ?? '' ) ),
			'popup_height_unit'=> array_key_exists( (string) ( $raw['popup_height_unit'] ?? '' ), $units ) ? (string) $raw['popup_height_unit'] : $defaults['popup_height_unit'],
			'is_active'       => isset( $raw['is_active'] ) ? '1' : '0',
			'auto_display'    => isset( $raw['auto_display'] ) ? '1' : '0',
			'display_mode'    => array_key_exists( (string) ( $raw['display_mode'] ?? '' ), dspi_get_display_modes() ) ? (string) $raw['display_mode'] : $defaults['display_mode'],
			'page_ids'        => dspi_sanitize_page_ids( (string) ( $raw['page_ids'] ?? '' ) ),
			'delay'           => absint( $raw['delay'] ?? 0 ),
			'frequency'       => array_key_exists( (string) ( $raw['frequency'] ?? '' ), dspi_get_frequency_options() ) ? (string) $raw['frequency'] : $defaults['frequency'],
			'custom_days'     => max( 1, absint( $raw['custom_days'] ?? $defaults['custom_days'] ) ),
			'start_date'      => self::sanitize_date( (string) ( $raw['start_date'] ?? '' ) ),
			'end_date'        => self::sanitize_date( (string) ( $raw['end_date'] ?? '' ) ),
			'template'        => array_key_exists( (string) ( $raw['template'] ?? '' ), dspi_get_templates() ) ? (string) $raw['template'] : $defaults['template'],
		);

		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, '_dspi_' . $key, $value );
		}
	}

	/**
	 * Sanitize YYYY-MM-DD date values.
	 *
	 * @param string $date Raw date.
	 * @return string
	 */
	private static function sanitize_date( string $date ): string {
		$date = sanitize_text_field( $date );

		if ( '' === $date ) {
			return '';
		}

		$parsed = date_create_from_format( 'Y-m-d', $date );

		return $parsed && $parsed->format( 'Y-m-d' ) === $date ? $date : '';
	}

	/**
	 * Sanitize an optional numeric dimension.
	 *
	 * @param string $value Raw dimension value.
	 * @return string
	 */
	private static function sanitize_dimension_value( string $value ): string {
		if ( '' === trim( $value ) ) {
			return '';
		}

		return (string) max( 0, (float) $value );
	}
}
