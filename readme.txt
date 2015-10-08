=== GP Integration ===
Contributors: GregRoss
Plugin URI: http://toolstack.com/gp-integration
Author URI: http://toolstack.com
Tags: glotpress, admin
Requires at least: 3.9
Tested up to: 4.3
Stable tag: 1.5
License: GPLv2

Integrates GlotPress with your WordPress installation.

== Description ==

GlotPress is a great way to manage and work collaborative on translations for your projects and with GP Integration it becomes even better!

Don't have GlotPress installed?  Want a simpler way to do it?  Check out [Glot-O-Matic](http://glot-o-matic.com)!

GP Integration adds the missing features you need to run GlotPress effectively:

* Manage admin users
* Add and delete and reset passwords for users in the local GlotPress user table
* Delete projects and translation sets
* View your GlotPress site right in the WordPress Admin interface
* Shortcode to embed your GlotPress installation right in to your WordPress front end
* Support for both integrated (your using the WordPress database) and standalone (your using another database on the same server)

= License =
	
This code is released under the GPL v2, see license.txt for details.

== Installation ==

1. Extract the archive file into your plugins directory in the gp-integration folder.
2. Activate the plugin in the Plugin options.
3. To to the GlotPress->Settings menu.
4. Configure your database name (leave blank to use your WordPress database).
5. Configure your table prefix (by default "gp_" is used).
6. Configure the path to GlotPress (fully qualified is best).

== Frequently Asked Questions ==

= I don't have GlotPress installed, can I still use GP Integration? =

No, you have to have a copy of GlotPress installed for GP Integration to be useful.  

However you can use (Glot-O-Matic)[http://glot-o-matic.com] and get all the features of GP Integration with a complete copy of GlotPress included!

= In your screen shots your GlotPress logo background matches the background of the WordPress admin area, how did you do that? =

The GlotPress logo is a PNG, but it doesn't have a transparent background set.  You can find it in your GlotPress install under "img/glotpress-logo.png", use your faviorite image editor and remove the background with the magic wand!

= What is the shortcode name? =

[gp-integration] will embed GlotPress.
[gp-integration-link] will create a link to your GlotPress install.
[gp-integration-translator-list] will create a formated table of your translators.

= How does the shortcode work? =

The shortcode creats an iFrame along with a bit of JavaScript.  The JavaScript will resize the iFrame to match the height of the GlotPress page being displayed.  The JavaScript fires once a second so you may see a slight delay in the iFrame being resized.

Also note that external links, like the "Proudly powered by GlotPress" in the footer, if clicked, will break the resizing script.

== Screenshots ==

1. GlotPress inside of WordPress Admin.
2. GlotPress on the front end.
3. Configuration screen.
4. Translation set managmenet	.
5. Project management.
6. User management.
7. Admin management.

== Changelog ==
= 1.5 =
* Release date: October 8, 2015
* Added gp-integration-translator-list short code.
* Added translation support.
* Fixed warning messages during install if GP_DEBUG is enabled.
* Fixed missing define.
* Fixed bug with unchecked options not being saved.
* Updated settings/about page.
* Updated utility library.

= 1.0 =
* Release date: May 15, 2015
* Added gp-integration-link short code.
* Updated options system.

= 0.5 =
* Release date: January 26, 2014
* Initial release.

== Upgrade Notice ==
= 1.5 =
* None.
