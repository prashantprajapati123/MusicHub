=== Plugin Name ===
Contributors: eschnack
Tags: discography, music, bands, bandcamp, custom post types
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: trunk

Creates a Discography Custom Post Type and allows you to import your albums from Bandcamp.

== Description ==

Musopress Discography creates a Discography Custom Post Type to display and organize your albums on your site. 

If you use Bandcamp, it lets you import your music through the click of a button. This way you can use Bandcamp's awesom features and integrate them effortlessly with your site. Any changes you make can be easily updated by simply re-importing the album(s) in question.

An optional Artist taxonomy is included, useful for labels and artists with many bands/projects.

There's also a widget to display your latest releases.


== Installation ==

You can install it through the admin interface or manually:

1. Upload `musopress-discography` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

*Plugin Setup*
1. Create a page and add the [discography] shortcode.
2. If you want to use the Artist taxonomy, create a copy of your current theme's 'archive.php' file and rename it 'taxonomy-artist.php'. Replace the title with * and the Loop with *. 

== Frequently Asked Questions ==

= I'm getting an error when trying to import from Bandcamp =

This is usually because of a timeout issue. Try again later, or if you're importing a lot of albums, try importing them in smaller batches.

= I'm getting 404 errors on the album pages =

Go to Setting->Permalinks and save.

= I'm still getting 404 errors =

This is most likely a conflict with a plugin or theme. Try switching to a different theme and re-saving the permalinks. If that doesn't fix it, try going through each plugin, deactivating it, re-saving the permalinks, and seeing if that fixes it. 


== Screenshots ==

1. Discography Settings page
2. Bandcamp Import page
3. Discography index page
4. Single Album view

== Changelog ==

= 0.5.1 =
* Now flushes rewrite rules on activation and deactivation to minimize permalink problem.
* Disabled has_archive for the album post type to prevent conflicts with the discography slug.

= 0.5 =
* Initial release.

