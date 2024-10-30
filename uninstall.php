<?php
	// if uninstall.php is not called by WordPress, die
	if(!defined('WP_UNINSTALL_PLUGIN')) {
		die;
	}
	
	$wp_query_ifb_tracking_form = new WP_Query([
		'post_type' => 'ifb_tracking_form',
	]);
	
	// The Loop
	if($wp_query_ifb_tracking_form->have_posts()) {
		while($wp_query_ifb_tracking_form->have_posts()) {
			$wp_query_ifb_tracking_form->the_post();
			
			// Delete post
			wp_delete_post(get_the_ID(), true);
		}
	}
	/* Restore original Post Data */
	wp_reset_postdata();
	
	// Delete infocob tracking options
	delete_option('infocob_tracking_settings');
	
	// Unregister all infocob tracking custom post type
	unregister_post_type('ifb_tracking_form');
