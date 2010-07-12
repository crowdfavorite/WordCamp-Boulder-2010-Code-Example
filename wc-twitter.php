<?php
/*
Plugin Name: WordCamp Twitter
Plugin URI: http://crowdfavorite.com/wordpress/
Description: Demonstration plugin for WordCamp Boulder. The purpose of this plugin is to demonstrate the basics of creating widgets and accessing remote rss feeds.
Version: 0.1
Author: Alex King, Shawn Parker
Author URI: http://crowdfavorite.com 
*/

/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
class wp_twitter_Widget extends WP_Widget {
	
    /**
     * Constructor
     *
     * @return void
     **/
	function wp_twitter_Widget() {
		$widget_ops = array( 'classname' => 'wc-twitter', 'description' => 'WordCamp Twitter Example' );
		$this->WP_Widget( 'wc-twitter', 'WC Twitter', $widget_ops );
	}

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme 
     * @param array  An array of settings for this widget instance 
     * @return void Echoes it's output
     **/
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		echo $before_widget.$before_title;
		echo 'WC Twitter'; // Can set this with a widget option, or omit altogether
		echo $after_title;

		// make sure that we have an account to fetch before we do anything
		if (!empty($instance['account'])) {

			/**
			 * See if we can pull transient data first
			 * More about transients here: http://codex.wordpress.org/Transients_API
			 */
			if (!($tweets = get_transient('wc-twitter'))) {
				
				// fetch the rss feed
				$url = 'http://twitter.com/statuses/user_timeline/'.$instance['account'].'.rss';
				$tweets = fetch_feed($url);
				
				// store the tweets in transient cache for an hour
				if (!is_wp_error($tweets)) {
					set_transient('wc-twitter', $tweets, 60*60);
				}
			}

			/**
			 * iterate over tweets and echo the output
			 * format out put as necessary for your site
			 */
			if ($tweets && $tweets->get_item_quantity()) {
				echo '<ul>';
				foreach($tweets->get_items() as $item) {
					echo '<li><a href="'.esc_url($item->get_link()).'">'.esc_html($item->get_title()).'</a></li>';
				}
				echo '</ul>';
			}
		}

		echo $after_widget;
	}

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings 
     * @return array The validated and (if necessary) amended settings
     **/
	function update( $new_instance, $old_instance ) {
		// update logic goes here
		$updated_instance = $new_instance;
		return $updated_instance;
	}

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
	function form( $instance ) {
		// here we'll set up our input 
		$instance = wp_parse_args( (array) $instance, array() );
		
		echo '<label for="'.$this->get_field_id('account').'">Account</label>
			<input type="text" name="'.$this->get_field_name('account').'" id="'.$this->get_field_id('account').'" value="'.
			(!empty($instance['account']) ? esc_html($instance['account']) : '').'">';
	}
}

/**
 * Register the widget
 */
function wc_boulder_register_widget() {
	register_widget('wp_twitter_Widget');
}
add_action( 'widgets_init', 'wc_boulder_register_widget' );


?>