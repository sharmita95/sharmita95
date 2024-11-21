<?php
if(!function_exists('deactivate_subscrition_plugin')) {
	function deactivate_subscrition_plugin() {
		// Remove Sub-Menu 1 page
		remove_action('subscribe_form','');
		// Remove Sub-Menu 2 page
		remove_action('subscribe_mail','');
		// Remove Sub-Menu 3 page
		remove_action('subscribe_message','');
		// Remove Menu page
		remove_action('subscribe_menu','');
		// Remove Shortcode
    	remove_shortcode( 'viacon_subscrition' );
		// Unset Page Template
		remove_filter( 'page_template', '' );
		// Remove Page Template
		remove_filter( 'theme_page_templates', '', 10 );
		// Remove Confirmation Page
		$found_post_title = get_page_by_title( 'Subscription Confirmation', OBJECT, 'page' );
    	wp_delete_post($found_post_title->ID);
	}
}