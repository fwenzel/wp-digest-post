=== Digest Post ===
Contributors: fwenzel
Donate link: http://fredericiana.com/
Tags: digest, post, daily, rss
Requires at least: 2.1
Tested up to: 2.3.1
Stable tag: 0.1

Daily fetches an RSS feed and posts a list of links as a digest post to your
blog. (Note that this is in pre-ALPHA development).

== Description ==

Digest Post fetches an RSS feed on a regular basis (usually daily) and posts a
list of links to the newest articles as a digest post to your blog.

In order to run daily, it leverages the Wordpress "cron" facility which was
introduced in Wordpress 2.1. Therefore, this plugin won't work with any versions
older than that.

Please note that this plugin is in a very early stage of development. It
currently has no Admin pages to configure it with, instead you need to go into
the plugin's PHP code to set it up. However it is planned to have it completely
configurable through a Wordpress Admin page.

== Installation ==

Installation is pretty easy: 

1. Just drop the plugin file into your `/wp-content/plugins/` directory
1. Configure it (BEFORE activating it, see below)
1. Activate the plugin through the 'Plugins' menu in WordPress

== Configuration ==

At the moment, you need to edit the plugin's PHP file in order to set it up
correctly for your blog. To do so, use an editor of your choice and edit the
file digest-post.php. Scroll down a little, then you'll find a section enclosed
by the following comments:

`/***** SETTINGS, YOU MAY EDIT THIS *****/`
and eventually...
`/***** END OF SETTINGS *****/`

Inside, you'll find some settings for you to change, most notably the "feed_uri"
which is where you should put the web address of the RSS feed you would like to
aggregate, and possibly the time of day you would like the digest post to show
up.

You can also set a preamble for the digest posts and a title for the posts.

== Frequently Asked Questions ==

= Why can't I just have an admin interface for the settings? =

That's what I am wondering too ;) No, seriously, this is high up on the list of
things that this plugin needs.

= How can I help? =

If you want to help making this plugin more useful, feel free to drop me a line
at fwenzel at mozilla dot com. If you are "PHP-savvy", you may also change some
of the code you find in the SVN repository
(`http://svn.wp-plugins.org/digest-post/trunk/`) and send me a patch. I very
much appreciate it!

= Will it re-post items it already fetched once? =

No, the plugin stores the time stamp it last posted a digest, and the next time
it runs, it will only include items in the digest that have a time stamp *after*
the last post.

= Will it fetch the RSS feed periodically and cache its contents? =

No, not at the moment. But if you'd like to add this feature, you are very
welcome to help me out! (See the section on how you can help above.)

== Screenshots ==

1. A screen shot of my development blog having posted two digests from my regular blog.
