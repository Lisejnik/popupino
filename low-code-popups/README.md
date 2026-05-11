=== Low Code Popups ===
Contributors: lisejnik
Tags: popups, modal, marketing, shortcode, low-code
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple responsive popup builder for WordPress sites, with low-code controls and optional Divi-friendly triggers.

== Description ==

Low Code Popups is a lightweight WordPress plugin for creating simple popups without manually adding HTML, CSS, or JavaScript. It works with any WordPress theme and includes Divi-friendly trigger classes and workflows for users who build with Divi or Divi Theme Builder.

= Features =

* Custom Post Type `dspi_popup` for managing popups.
* Metaboxes for content, display rules, template selection, and live preview.
* Templates: bottom right, bottom left, centered modal, fullscreen, top bar, and bottom bar.
* Design controls for fonts, bold/italic styles, text sizes, popup dimensions, and background image sizing.
* Advanced custom HTML mode for users who want to write their own markup.
* Automatic display based on page rules, delay, date range, and frequency.
* Frequency options explain whether the popup should show every time or stay hidden for a selected period after the visitor closes it.
* Standalone frontend JavaScript with no jQuery dependency.
* Standalone frontend and admin CSS.
* WordPress media uploader for the background image.
* Button links can open in the same tab or a new tab.
* Shortcode `[lcp_popup id="123"]`.
* Manual opening via `.lcp-open-popup-123`, `data-lcp-open-popup="123"`, or `#lcp-popup-123`.
* Backward-compatible aliases: `[dspi_popup]`, `.dspi-open-popup-123`, and `#dspi-popup-123`.

== Installation ==

1. Upload the `low-code-popups` folder to `wp-content/plugins/`.
2. Activate the plugin in the WordPress admin.
3. Go to **Popups** and create a new popup.
4. Fill in the content, enable **Active popup**, and adjust the display rules if needed.

== Frequently Asked Questions ==

= How do I open a popup from a Divi button? =

Add this CSS class to the Divi button or link:

`lcp-open-popup-123`

Replace `123` with the ID of your popup.

= Can I insert a popup manually? =

Yes. Use the shortcode:

`[lcp_popup id="123"]`

= Can the popup be used without automatic display? =

Yes. Disable **Display automatically on the site** and use a shortcode or click trigger.

== Changelog ==

= 1.2.1 =
* Update the readme compatibility header to WordPress 6.9.

= 1.2.0 =
* Rename plugin to Low Code Popups and update the distribution slug to avoid trademark issues.
* Add branded shortcode and trigger aliases while keeping the original aliases compatible.

= 1.1.1 =
* Fix Plugin Checker issues for readme metadata, plugin URI, translation placeholders, and translation loading.

= 1.1.0 =
* Add typography, popup dimensions, background image sizing, and advanced custom HTML mode.

= 1.0.0 =
* Initial release.
