<?php
/*
Plugin Name: Digest Post
Plugin URI: http://fredericiana.com/
Description: Daily fetches a given RSS feed and posts a list of links as a digest post to your blog.
Version: 0.1
Author: Frederic Wenzel
Author URI: http://fredericiana.com
*/

/*  Copyright 2007  Frederic Wenzel <fwenzel@mozilla.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Digest_Post {
    var $feed_uri;
    var $post_settings;
    var $preamble;
    
    /**
     * PHP5 constructor
     */
    function __construct() {
        $this->Digest_Post();
    }
    
    /**
     * Constructor, hooking up the plugin to action and filter hooks
     */
    function Digest_Post() {
        /**
         * Settings
         * @TODO Admin interface for this
         */
        $this->feed_uri = 'http://fredericiana.com/feed';
        /*
          settings to be applied to all posts posted;
          some are commented out but kept for documentation purposes and
          possible later extension.
        */
        $this->post_settings = array(
            'post_author'       => 1,   // default: admin
            //'post_date'		=> $post_dt,
            //'post_date_gmt'	=> $post_dt,
            //'post_modified'	=> $post_modified_gmt,
            //'post_modified_gmt'	=> $post_modified_gmt,
            'post_title'	=> 'Digest for %s', // %s will be replaced by current date
            //'post_content'	=> $post_content,
            //'post_excerpt'	=> $post_excerpt,
            'post_status'	=> 'publish', // or 'draft' or 'private'
            //'post_name'		=> $post_title,
            'post_type'         => 'post',  // or 'page'
            //'comment_status'	=> $comment_status_map[$post_open_comment],
            //'ping_status'	=> $comment_status_map[$post_open_tb],
            //'comment_count'	=> $post_nb_comment + $post_nb_trackback,
            //'post_category'     => array(1), // category array
        );
        $this->preamble = ''; // text to be printed before the digest list, if any
        /***** END OF SETTINGS *****/
        
        
        // register plugin activation and deactivation hooks
        $filename = __FILE__;
        register_activation_hook($filename, array($this, '_install'));
        register_deactivation_hook($filename, array($this, '_uninstall'));
        
        // hook up the digest post functionality to the daily cron job
        add_action('digest_post_run', array($this, 'digest_post_run'));
    }
    
    /**
     * install plugin into wordpress
     */
    function _install() {
        // schedule a daily event
        wp_schedule_event( time(), 'often', 'digest_post_run');
        
        // add options to database
        add_option('digest_post_last_post', null, 'Timestamp of last posted digest');
    }
    
    /**
     * uninstall plugin from wordpress
     */
    function _uninstall() {
        // unschedule daily event
        remove_action('digest_post_run', array($this, 'digest_post_run'));
        wp_clear_scheduled_hook('digest_post_run');
        
        // delete options from database
        delete_option('digest_post_last_post');
    }
    
    /**
     * Main function: fetch RSS, make digest, and post it
     */
    function digest_post_run() {
        $rssdata = $this->fetch_feed();
        if (!($rssdata) || empty($rssdata->items)) return false; // if there's nothing for us to post, do nothing.
        
        $this->post_digest($this->create_digest($rssdata));
    }
    
    /**
     * Fetch the RSS feed that is set up and store it into the local feed variable,
     * then only return the ones that are more current than the last digest post.
     */
    function fetch_feed() {
        include_once(ABSPATH . WPINC . '/rss.php');
        $rssdata = fetch_rss($this->feed_uri);
        
        // filter out already posted items, if necessary
        if ($lastpost = get_option('digest_post_last_post')) {
            $datefilter = create_function('$item', 'return ('.$lastpost.' < strtotime($item[\'pubdate\']));');
            $rssdata->items = array_filter($rssdata->items, $datefilter);
        }
        
        return $rssdata;
    }

    /**
     * Create digest list from RSS data
     */
    function create_digest($rssdata) {
        $digest = "<div>" . $this->preamble;
        
        $digest .= "<ul>\n";
        foreach ($rssdata->items as $item) {
            $digest .= '<li><a href="' . $item['link'] .'">'
                . $item['title'] ."</a></li>\n";
         }
         $digest .= "</ul></div>\n";
         
         return $digest;
    }
    
    /**
     * Post supplied data as a new post on the blog
     */
    function post_digest($content) {
        // make array with post settings and content
        $digest = (PHP_VERSION < 5) ? $this->post_settings : clone($this->post_settings);
        $digest['post_title'] = sprintf($digest['post_title'], strftime('%x'));
        $digest['post_content'] = $content;
        
        // publish this post now.
        wp_insert_post($digest);
        
        // update last post timestamp
        update_option('digest_post_last_post', time());
    }
}

// for development: run every few seconds
add_filter('cron_schedules', 'cronjob_often');
function cronjob_often() {return array('often' => array('interval' => 20, 'display' => 'Often'));}
  

/* instantiate our plugin object to let the magic happen */
$digest_post = new Digest_Post();
