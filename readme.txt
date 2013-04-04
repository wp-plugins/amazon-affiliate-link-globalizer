=== Amazon Affiliate Link Globalizer ===
Contributors: guruz
Tags: amazon, amazon affiliate, amazon associates, amazon partner, amazon localizer
Requires at least: 2.8
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Rewrites Amazon.com/Amzn.com and forwards the visitor to 'their' country specific Amazon store (using IP Geolocation). 

== Description ==

The plugin will install an output filter that checks for links to Amazon.com and Amzn.com. It then rewrites those links 
inside your posts to point to the [A-FWD](http://a-fwd.com/ "A-FWD") service. 
A-FWD will do the country lookup for you. You can specify your Affiliate IDs for all the country sites in the plugin's Wordpress settings.

* Can rewrite single title text links (with ASIN) of various URL formats
* Supports Amazon.com and Amzn.com links
* Can forward to the correct Amazon store (Canada, UK, ...)
* The plugin does not change your database, so if you deactivate it you will get your unchanged Amazon links back.

In contrast to similar plugins, this one does not use Javascript.

== Installation ==

1. Upload `amazon-link-globalizer.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.0 =
* Initial release
