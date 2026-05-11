(function () {
	'use strict';

	function getDefaults() {
		return (window.dspiAdmin && window.dspiAdmin.defaults) ? window.dspiAdmin.defaults : {
			heading: 'Popup headline',
			text: 'Your popup text will appear here.',
			button: 'Button',
			date: 'Optional date or note',
			textColor: '#ffffff',
			buttonColor: '#2ea3f2',
			overlayColor: '#000000',
			overlayOpacity: '0.45',
			fontFamily: 'inherit',
			backgroundSize: 'cover'
		};
	}

	function getFontFamilyValue(value) {
		var fonts = {
			inherit: 'inherit',
			arial: 'Arial, Helvetica, sans-serif',
			georgia: 'Georgia, serif',
			tahoma: 'Tahoma, Geneva, sans-serif',
			trebuchet: '"Trebuchet MS", Helvetica, sans-serif',
			verdana: 'Verdana, Geneva, sans-serif',
			system: '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif'
		};

		return fonts[value] || fonts.inherit;
	}

	function getBackgroundSizeValue(value) {
		var sizes = {
			cover: 'cover',
			contain: 'contain',
			stretch: '100% 100%',
			auto: 'auto'
		};

		return sizes[value] || sizes.cover;
	}

	function hexToRgba(hex, opacity) {
		var value = (hex || '#000000').replace('#', '');
		var alpha = Math.max(0, Math.min(1, parseFloat(opacity || '0.45')));
		var red;
		var green;
		var blue;

		if (value.length === 3) {
			value = value[0] + value[0] + value[1] + value[1] + value[2] + value[2];
		}

		red = parseInt(value.substring(0, 2), 16);
		green = parseInt(value.substring(2, 4), 16);
		blue = parseInt(value.substring(4, 6), 16);

		return 'rgba(' + red + ', ' + green + ', ' + blue + ', ' + alpha + ')';
	}

	function stripTags(value) {
		var wrapper = document.createElement('div');
		wrapper.innerHTML = value || '';
		return wrapper.textContent || wrapper.innerText || '';
	}

	function setText(selector, value, fallback) {
		var target = document.querySelector(selector);
		if (target) {
			target.textContent = value || fallback;
		}
	}

	function updatePreview() {
		var preview = document.querySelector('.dspi-live-preview');
		var template = document.getElementById('dspi_template_select');
		var heading = document.getElementById('dspi_heading');
		var text = document.getElementById('dspi_text');
		var dateLine = document.getElementById('dspi_date_line');
		var button = document.getElementById('dspi_button_text');
		var textColor = document.getElementById('dspi_text_color');
		var buttonColor = document.getElementById('dspi_button_color');
		var overlayColor = document.getElementById('dspi_overlay_color');
		var overlayOpacity = document.getElementById('dspi_overlay_opacity');
		var fontFamily = document.getElementById('dspi_font_family');
		var backgroundSize = document.getElementById('dspi_background_size');
		var headingSize = document.getElementById('dspi_heading_size');
		var headingBold = document.querySelector('input[name="dspi_meta[heading_bold]"]');
		var headingItalic = document.querySelector('input[name="dspi_meta[heading_italic]"]');
		var textSize = document.getElementById('dspi_text_size');
		var textBold = document.querySelector('input[name="dspi_meta[text_bold]"]');
		var textItalic = document.querySelector('input[name="dspi_meta[text_italic]"]');
		var buttonSize = document.getElementById('dspi_button_size');
		var buttonBold = document.querySelector('input[name="dspi_meta[button_bold]"]');
		var buttonItalic = document.querySelector('input[name="dspi_meta[button_italic]"]');
		var popupWidth = document.getElementById('dspi_popup_width');
		var popupWidthUnit = document.getElementById('dspi_popup_width_unit');
		var popupHeight = document.getElementById('dspi_popup_height');
		var popupHeightUnit = document.getElementById('dspi_popup_height_unit');
		var defaults = getDefaults();

		if (!preview) {
			return;
		}

		if (template) {
			preview.setAttribute('data-template', template.value);
		}

		setText('.dspi-live-preview__heading', heading ? heading.value : '', defaults.heading);
		setText('.dspi-live-preview__text', text ? stripTags(text.value) : '', defaults.text);
		setText('.dspi-live-preview__date', dateLine ? dateLine.value : '', defaults.date);
		setText('.dspi-live-preview__button', button ? button.value : '', defaults.button);

		if (textColor) {
			preview.style.setProperty('--dspi-preview-text', textColor.value);
		}

		if (buttonColor) {
			preview.style.setProperty('--dspi-preview-button', buttonColor.value);
		}

		if (overlayColor && overlayOpacity) {
			preview.style.setProperty('--dspi-preview-overlay', hexToRgba(overlayColor.value, overlayOpacity.value));
		}

		if (fontFamily) {
			preview.style.setProperty('--dspi-preview-font-family', getFontFamilyValue(fontFamily.value));
		}

		if (backgroundSize) {
			preview.style.setProperty('--dspi-preview-bg-size', getBackgroundSizeValue(backgroundSize.value));
		}

		if (headingSize) {
			preview.style.setProperty('--dspi-preview-title-size', Math.max(10, parseInt(headingSize.value || '25', 10)) + 'px');
		}

		preview.style.setProperty('--dspi-preview-title-weight', headingBold && headingBold.checked ? '800' : '400');
		preview.style.setProperty('--dspi-preview-title-style', headingItalic && headingItalic.checked ? 'italic' : 'normal');

		if (textSize) {
			preview.style.setProperty('--dspi-preview-text-size', Math.max(10, parseInt(textSize.value || '15', 10)) + 'px');
		}

		preview.style.setProperty('--dspi-preview-text-weight', textBold && textBold.checked ? '700' : '400');
		preview.style.setProperty('--dspi-preview-text-style', textItalic && textItalic.checked ? 'italic' : 'normal');

		if (buttonSize) {
			preview.style.setProperty('--dspi-preview-button-size', Math.max(10, parseInt(buttonSize.value || '14', 10)) + 'px');
		}

		preview.style.setProperty('--dspi-preview-button-weight', buttonBold && buttonBold.checked ? '700' : '400');
		preview.style.setProperty('--dspi-preview-button-style', buttonItalic && buttonItalic.checked ? 'italic' : 'normal');

		if (popupWidth && popupWidth.value) {
			preview.style.setProperty('--dspi-preview-width', popupWidth.value + (popupWidthUnit ? popupWidthUnit.value : 'px'));
		} else {
			preview.style.removeProperty('--dspi-preview-width');
		}

		if (popupHeight && popupHeight.value) {
			preview.style.setProperty('--dspi-preview-height', popupHeight.value + (popupHeightUnit ? popupHeightUnit.value : 'px'));
		} else {
			preview.style.removeProperty('--dspi-preview-height');
		}
	}

	function updateContentMode() {
		var selected = document.querySelector('input[name="dspi_meta[content_mode]"]:checked');
		var mode = selected ? selected.value : 'builder';

		document.body.classList.toggle('dspi-content-mode-html', mode === 'html');
		document.body.classList.toggle('dspi-content-mode-builder', mode !== 'html');
	}

	function updatePreviewBackground(url) {
		var livePreview = document.querySelector('.dspi-live-preview');

		if (!livePreview) {
			return;
		}

		if (url) {
			livePreview.style.setProperty('--dspi-preview-bg', 'url("' + url + '")');
		} else {
			livePreview.style.removeProperty('--dspi-preview-bg');
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		var uploadButton = document.querySelector('.dspi-upload-image');
		var removeButton = document.querySelector('.dspi-remove-image');
		var resetColorsButton = document.querySelector('.dspi-reset-colors');
		var imageId = document.getElementById('dspi_background_id');
		var preview = document.querySelector('.dspi-image-preview');
		var frame;
		var defaults = getDefaults();

		updateContentMode();

		if (uploadButton && imageId && preview && window.wp && window.wp.media) {
			uploadButton.addEventListener('click', function (event) {
				event.preventDefault();

				if (frame) {
					frame.open();
					return;
				}

				frame = window.wp.media({
					title: window.dspiAdmin ? window.dspiAdmin.mediaTitle : 'Choose image',
					button: {
						text: window.dspiAdmin ? window.dspiAdmin.mediaButton : 'Use this image'
					},
					multiple: false
				});

				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

					imageId.value = attachment.id;
					preview.innerHTML = '<img src="' + url + '" alt="">';
					updatePreviewBackground(attachment.url);
				});

				frame.open();
			});
		}

		if (removeButton && imageId && preview) {
			removeButton.addEventListener('click', function (event) {
				event.preventDefault();
				imageId.value = '';
				preview.innerHTML = '';
				updatePreviewBackground('');
			});
		}

		if (resetColorsButton) {
			resetColorsButton.addEventListener('click', function (event) {
				var textColor = document.getElementById('dspi_text_color');
				var buttonColor = document.getElementById('dspi_button_color');
				var overlayColor = document.getElementById('dspi_overlay_color');
				var overlayOpacity = document.getElementById('dspi_overlay_opacity');

				event.preventDefault();

				if (textColor) {
					textColor.value = textColor.getAttribute('data-dspi-default') || defaults.textColor;
				}

				if (buttonColor) {
					buttonColor.value = buttonColor.getAttribute('data-dspi-default') || defaults.buttonColor;
				}

				if (overlayColor) {
					overlayColor.value = overlayColor.getAttribute('data-dspi-default') || defaults.overlayColor;
				}

				if (overlayOpacity) {
					overlayOpacity.value = overlayOpacity.getAttribute('data-dspi-default') || defaults.overlayOpacity;
				}

				updatePreview();
			});
		}

		Array.prototype.forEach.call(document.querySelectorAll('.dspi-preview-input, .dspi-preview-style-input, #dspi_template_select, #dspi_text_color, #dspi_button_color, #dspi_overlay_color, #dspi_overlay_opacity'), function (field) {
			field.addEventListener('input', updatePreview);
			field.addEventListener('change', updatePreview);
		});

		Array.prototype.forEach.call(document.querySelectorAll('input[name="dspi_meta[content_mode]"]'), function (field) {
			field.addEventListener('change', function () {
				updateContentMode();
				updatePreview();
			});
		});

		updatePreview();
	});
}());
