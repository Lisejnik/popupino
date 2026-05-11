# Divi Simple Popups

Divi Simple Popups is a lightweight WordPress plugin for creating simple popups without adding custom HTML, CSS, or JavaScript to a Divi Code module. It works outside Divi as well, but the workflow is designed with Divi and Divi Theme Builder users in mind.

## Features

- Custom Post Type `dspi_popup` for managing popups.
- Metaboxes for content, display rules, template selection, and live preview.
- Templates: bottom right, bottom left, centered modal, fullscreen, top bar, and bottom bar.
- Design controls for fonts, bold/italic styles, text sizes, popup dimensions, and background image sizing.
- Advanced custom HTML mode for users who want to write their own markup.
- Automatic display based on page rules, delay, date range, and frequency.
- Frequency options explain whether the popup should show every time or stay hidden for a selected period after the visitor closes it.
- Standalone frontend JavaScript with no jQuery dependency.
- Standalone frontend and admin CSS.
- WordPress media uploader for the background image.
- Button links can open in the same tab or a new tab.
- Shortcode `[dspi_popup id="123"]`.
- Manual opening via `.dspi-open-popup-123`, `data-dspi-open-popup="123"`, or `#dspi-popup-123`.

## Installation

1. Upload the `divi-simple-popups` folder to `wp-content/plugins/`.
2. Activate the plugin in the WordPress admin.
3. Go to **Popups** and create a new popup.
4. Fill in the content, enable **Active popup**, and adjust the display rules if needed.

## Using With Divi

Popups are rendered automatically in the site footer. To open a popup when a Divi button or link is clicked, add this CSS class to the element:

```text
dspi-open-popup-123
```

Replace the number with the ID of your popup.

You can also insert a popup manually with:

```text
[dspi_popup id="123"]
```

## Notes

- Disable **Display automatically on the site** if you only want to use the popup with a shortcode or click trigger.
- Display frequency is stored in the browser with `sessionStorage` and `localStorage`.
- The plugin does not use Composer, a build step, or paid libraries.

## Compatibility

- WordPress 6.0+
- PHP 8.0+
