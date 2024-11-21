<?php
function ccfv_plugin_menu_pages () {
    
    add_action('admin_menu', 'ccfv_addmenu');
    
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ccfv_add_action_links', 10, 2 );
}


function ccfv_addmenu() {
    
    add_menu_page(
        'Contact Form', 
        'Contact Form', 
        'manage_options', 
        'custom-menu', 
        'ccvf_dashboard', 
        'dashicons-email-alt',
        5 
    );
    add_submenu_page(
        'custom-menu', 
        'Submenu Page Title', 
        'Dashboard', 
        'manage_options', 
        'custom-menu' 
    );
    add_submenu_page(
        'custom-menu', 
        'Submenu Page Title2', 
        'Settings', 
        'manage_options', 
        'mail-settings-page', 
        'mail_settings_sub_menu_admin_page_contents' 
    );
    add_submenu_page(
        'custom-menu', 
        'Mail Test', 
        'Mail Test', 
        'manage_options', 
        'mail-test-page', 
        'ccvf_mail_test_page_contents' 
    );
    
}


function ccfv_add_action_links($links) {
    array_unshift($links , '<a href="' . admin_url( 'options-general.php?page=ccfv_edit' ) . '">Settings</a>');
    return $links;
}