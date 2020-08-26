=== Organized Contacts ===

Contributors: Nikolays93
Donate link: https://vk.com/nikolays_93
Tags: organize, contacts
Requires at least: 4.6
Tested up to: 4.9.5
Stable tag: 4.9.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The plugin allows you to organize information about your companies / organization


== Description ==

Plugin will add customizer settings and [contact] shortcode.

How to use:
```
    <!-- Print contact name -->
    [contact field="name"]
    <!-- Print contact image -->
    [contact_image]
    <!-- Print contact location -->
    [contact field="city, address"]
    <!-- Print contact first phone number (split by ,) -->
    [contact field="phone" del="," part="1"]
    <!-- Print contact second number -->
    [contact field="phone" part="2"]
    <!-- Print contact mail address -->
    [contact field="email" before="Email: "]
    <!-- Print contact work time -->
    [contact field="work_time"]
    <!-- Print contact socials -->
    [contact field="socials"]
```

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/organized-contacts` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Appearance > Customize screen to configure the contacts

== Changelog ==
2.0
    * Fully code review

1.6
    * Reorganize code
    * Add Schema.org support
    * Explode address
    * Add multiple field with delimiter
    * Add primary image home url filter

1.5
    * Global refactoring (warning: tertiary, quaternary, fivefold excluded )

1.4
    * Add custom fields action with сonvenient class
    * Set control priorities

1.3
    * Unlimited companies (It helped for me, and I think you will maybe find useful)

1.2
    * Add sanitize "image" field - from relative to absolute

1.1
    * Add field "image"
