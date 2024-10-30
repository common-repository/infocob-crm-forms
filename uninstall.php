<?php
	// if uninstall.php is not called by WordPress, die
	if(!defined('WP_UNINSTALL_PLUGIN')) {
		die;
	}
	
	// Delete ifb_crm_forms
	$wp_query_ifb_crm_forms = new WP_Query([
		'post_type' => 'ifb_crm_forms',
		'posts_per_page' => -1,
	]);
	
	// The Loop
	if($wp_query_ifb_crm_forms->have_posts()) {
		while($wp_query_ifb_crm_forms->have_posts()) {
			$wp_query_ifb_crm_forms->the_post();
			
			// Delete post
			wp_delete_post(get_the_ID(), true);
		}
	}
	/* Restore original Post Data */
	wp_reset_postdata();
	
	// Delete ifb_recipients
	$wp_query_ifb_recipients = new WP_Query([
		'post_type' => 'ifb_recipients',
		'posts_per_page' => -1,
	]);
	
	// The Loop
	if($wp_query_ifb_recipients->have_posts()) {
		while($wp_query_ifb_recipients->have_posts()) {
			$wp_query_ifb_recipients->the_post();
			
			// Delete post
			wp_delete_post(get_the_ID(), true);
		}
	}
	/* Restore original Post Data */
	wp_reset_postdata();
	
	// Delete infocob tracking options
	delete_option('infocob_crm_forms_settings');
	delete_option('INFOCOB_CRM_FORMS_UPGRADE_VERSION');
	
	// Unregister all infocob tracking custom post type
	unregister_post_type('infocob_crm_forms_settings');
