<?php
/*
Plugin Name: Custom Forms and Captcha Plugin
Description: Custom contact Form, Subscription Form & Captcha (contact, comment & subscription form)
Version: 0.1.3
Author: Viacon
Text Domain: ccfv-plugin-textdomain
*/

//https://stackoverflow.com/questions/45279935/google-recaptcha-in-custom-form

define('CCFV_DIR', plugin_dir_path( __FILE__ ));
define('CCFV_URL', plugin_dir_url( __FILE__ ));




/* Incliude Files */
include_once CCFV_DIR.'/admin-page.php';
include_once CCFV_DIR.'/forms/contact.php';
include_once CCFV_DIR.'/comment-form-captcha.php';

include_once CCFV_DIR.'/admin-pages/test-mail-temp.php'; 
include_once CCFV_DIR.'/admin-pages/settings.php'; 
include_once CCFV_DIR.'/admin-pages/dashboard.php';

include_once CCFV_DIR.'/vmt-subscription.php';

// include_once CCFV_DIR.'/subscription-functions.php';

/* Activate the plugin */
register_activation_hook( __FILE__, 'ccfv_plugin_activate_func' );
function ccfv_plugin_activate_func() { 

	after_activation_jobs_ccfv(); 

	flush_rewrite_rules(); // Clear the permalinks after the post type has been registered.

}

/* Deactivation hook */
register_deactivation_hook( __FILE__, 'ccfv_plugin_deactivate_func' );
function ccfv_plugin_deactivate_func() {

	after_deactivation_jobs_ccfv();
	
	flush_rewrite_rules(); // Clear the permalinks to remove our post type's rules from the database.

}


/*********************** Functions for Activation & Deactivation ************************/
add_action('init', 'after_activation_jobs_ccfv');
function after_activation_jobs_ccfv() {
    
    add_shortcode('ccvf_contact_form', 'ccfv_contact_func');
    ccfv_plugin_menu_pages();
    
    //add settings after pugin activation
	add_filter( 'plugin_action_links', 'ccfv_settings_plugin_link', 10, 2 );


    //FROM SUBSCRIOTION PLUGIN
    // my_plugin_create_table();
	// my_plugin_add_prev_data();
	// subscribe_menu();
	activate_subscrition_plugin();
	
    
}
function after_deactivation_jobs_ccfv() {
    
    remove_shortcode( 'ccvf_contact_form' );

    //FROM SUBSCRIOTION PLUGIN
    // remove_menu_page('subscribe_us_callback');	
    deactivate_subscrition_plugin();
}

function ccfv_settings_plugin_link( $links, $file ) {
    
    //Insert the link at the beginning
    $in = '<a href="admin.php?page=mail-settings-page">' . __('Settings','ccfv-plugin-textdomain') . '</a>';
    array_unshift($links, $in);

    //Insert at the end
    // $links[] = '<a href="options-general.php?page=many-tips-together">'.__('Settings','ccfv-plugin-textdomain').'</a>';
    
    return $links;
}



/****************** Add Script *********************/
function ccfv_contactform_add_script() {
    
    wp_enqueue_script('ccfv-captcha', 'https://www.google.com/recaptcha/api.js', array(), false, false);
    
    
    wp_enqueue_script('ccfv', plugin_dir_url( __FILE__ ) .  '/js/custom_cfv.js', array(), false, true);
	$jsData = [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'test' => 'ccfv',
        'test1' => 'Viacon',
        ];

    wp_localize_script('ccfv', 'Front', $jsData);
    
}
add_action('wp_enqueue_scripts', 'ccfv_contactform_add_script');


function wpdocs_selectively_enqueue_admin_script( $hook ) {
    wp_enqueue_script('ccfv-captcha-test', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js', array(), false, false);
}
add_action( 'admin_enqueue_scripts', 'wpdocs_selectively_enqueue_admin_script' );



add_filter( 'wp_mail_from_name', 'sender_name' );
function sender_name( $original_sender_name ) {
    
    $site_name = get_bloginfo( 'name' );
    if(empty($site_name)) {
        $site_name = "A Viacon Website";
    }
    return $site_name;
}