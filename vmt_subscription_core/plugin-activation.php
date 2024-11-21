<?php
if(!function_exists('activate_subscrition_plugin')) {
	function activate_subscrition_plugin() {
		//Check and Create Plugin Table
		vmt_create_table();
		// Add Shortcode
		add_shortcode('viacon_subscrition', 'subscribe');
		// Set Page Template
		add_filter( 'page_template', 'subscription_page_template' );
		function subscription_page_template( $page_template ){
			if ( get_page_template_slug() == 'vmt_subscription_core/subscription-temp.php' ) {
				$page_template = CCFV_DIR . '/vmt_subscription_core/subscription-temp.php';
			}
			return $page_template;
		}
		// Add Page Template
		add_filter( 'theme_page_templates', 'add_template', 10, 4 );
		function add_template( $post_templates, $wp_theme, $post, $post_type ) {
			$post_templates['vmt_subscription_core/subscription-temp.php'] = __('Subscription Message');
			return $post_templates;
		}
		// Create Confirmation Page
		$found_post_title = get_page_by_title( 'Subscription Confirmation', OBJECT, 'page' );
		if ($found_post_title == null || !get_post_status($found_post_title->ID)){
			wp_insert_post(array('post_type'	=> 'page',
								'post_title'	=> 'Subscription Confirmation',
								'post_content'	=> '',
								'post_status'	=> 'publish',
								'post_author'	=> 1,
								'page_template'	=> 'vmt_subscription_core/subscription-temp.php',
								'post_name'		=> 'subscription-confirmation' ));
		}else{
			if($found_post_title->post_status != 'publish'){
				$get_post = get_post( $found_post_title->ID, 'ARRAY_A' );
				$get_post['post_status'] = 'publish';
				wp_update_post($get_post);
			}
			update_post_meta( $found_post_title->ID , '_wp_page_template', 'vmt_subscription_core/subscription-temp.php' );
		}
	}
}