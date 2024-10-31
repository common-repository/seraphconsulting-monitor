=== SeraphConsulting monitor ===
Contributors: smilight
Donate link: https://seraphconsulting.net
Tags: monitor, api, info
Requires at least: 5.6
Version: 1.0.4
Tested up to: 5.6
Stable tag: 1.0.4
Requires PHP: 5.5 or higher
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple plugin to show wp and installed plugins info

== Description ==

Simple plugin to show wp and installed plugins info by url http://YOUR_WEBSITE/seraph-monitor/v1/info/
Will be useful for external WP dashboards and wp monitoring services.

Plugin will show:
* all installed plugins on your site with short info
* outdated plugins and boolean near plugin that needs update
* wordpress version and boolean if wp needs update
* all installed themes with short info
* outdated themes and boolean near theme that needs update
* php version installed on server
* mysql version installed on server


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/seraph-monitor` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. That's it! Website info is now accessible from URL http://YOUR_WEBSITE/seraph-monitor/v1/info/

== Frequently Asked Questions ==

= Who need this plugin? =

Its for people who have few WP based websites and needs to keep it up to date from one place

= How to use it? =

Install this plugin and do `curl http://YOUR_WEBSITE/seraph-monitor/v1/info/` and you will get JSON response with website info.
