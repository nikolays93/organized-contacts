=== Organized Contacts ===

Contributors: Nikolays93
Donate link: https://vk.com/nikolays_93
Tags: organize, contacts
Requires at least: 4.6
Tested up to: 4.8.3
Stable tag: 4.8.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The plugin allows you to organize information about your companies / organization


== Description ==

Plugin will add customizer settings and [company] shortcode.
Company shortcode has attrs:
    "id" (for muliple contacts) may be:
        primary, secondary, tertiary, quaternary, fivefold

    "field" may be
        name, image, address, numbers, email, time_work, socials

    "filter" (default as 'the_content')
        Set none for disable default filter

    "before"
        The some custom html

    "after"
        The some custom html

for example:
    [company id="secondary" field="address" filter="none" before="<span class='label'>Our address:</span>"]
        for muliple, or
    [company field="email"]
        for primary only

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Appearance > Customize screen to configure the contacts

== Changelog ==

1.1
    - Add field "image"

1.2
    - Add sanitize "image" field - from relative to absolute