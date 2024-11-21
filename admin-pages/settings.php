<?php 
if ( ! function_exists( 'mail_settings_sub_menu_admin_page_contents' ) ) {
    function mail_settings_sub_menu_admin_page_contents() {
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        if (! empty ($_POST['update'])
            && ! empty($_POST['cf7sr_nonce'])
            && wp_verify_nonce($_POST['cf7sr_nonce'],'ccfv_update_settings' )
        ) {
            
            // echo '<pre>';
            // print_r($_POST);
            // echo '</pre>';
            
            $ccfv_email_to = ! empty ($_POST['ccfv_email_to']) ? sanitize_text_field($_POST['ccfv_email_to']) : '';
            update_option('ccfv_email_to', $ccfv_email_to);

            $ccfv_email_cc = ! empty ($_POST['ccfv_email_cc']) ? sanitize_text_field($_POST['ccfv_email_cc']) : '';
            update_option('ccfv_email_cc', $ccfv_email_cc);

            $ccfv_success_msg = ! empty ($_POST['ccfv_success_msg']) ? sanitize_text_field($_POST['ccfv_success_msg']) : '';
            update_option('ccfv_success_msg', $ccfv_success_msg);

            //Form Settings
            $show_level =  isset($_POST["show_level"]) ? "yes" : "no";
            update_option('show_level', $show_level);
            if($show_level == 'yes') { $checked = 'checked';} else {$checked = '';}

            $ccfv_name_placeholder = ! empty ($_POST['ccfv_name_placeholder']) ? sanitize_text_field($_POST['ccfv_name_placeholder']) : '';
            update_option('ccfv_name_placeholder', $ccfv_name_placeholder);

            $ccfv_email_placeholder = ! empty ($_POST['ccfv_email_placeholder']) ? sanitize_text_field($_POST['ccfv_email_placeholder']) : '';
            update_option('ccfv_email_placeholder', $ccfv_email_placeholder);

            $ccfv_subject_placeholder = ! empty ($_POST['ccfv_subject_placeholder']) ? sanitize_text_field($_POST['ccfv_subject_placeholder']) : '';
            update_option('ccfv_subject_placeholder', $ccfv_subject_placeholder);

            $show_ph_field =  isset($_POST["show_ph_field"]) ? "yes" : "no";
            update_option('show_ph_field', $show_ph_field);
            if($show_ph_field == 'yes') { $checked_ph_field = 'checked';} else {$checked_ph_field = '';}

            $ccfv_ph_placeholder = ! empty ($_POST['ccfv_ph_placeholder']) ? sanitize_text_field($_POST['ccfv_ph_placeholder']) : '';
            update_option('ccfv_ph_placeholder', $ccfv_ph_placeholder);

            $ccfv_message_placeholder = ! empty ($_POST['ccfv_message_placeholder']) ? sanitize_text_field($_POST['ccfv_message_placeholder']) : '';
            update_option('ccfv_message_placeholder', $ccfv_message_placeholder);


            $ccfv_form_wrapper_class = ! empty ($_POST['ccfv_form_wrapper_class']) ? sanitize_text_field($_POST['ccfv_form_wrapper_class']) : '';
            update_option('ccfv_form_wrapper_class', $ccfv_form_wrapper_class);

            $ccfv_form_class = ! empty ($_POST['ccfv_form_class']) ? sanitize_text_field($_POST['ccfv_form_class']) : '';
            update_option('ccfv_form_class', $ccfv_form_class);

            $ccfv_input_class = ! empty ($_POST['ccfv_input_class']) ? sanitize_text_field($_POST['ccfv_input_class']) : '';
            update_option('ccfv_input_class', $ccfv_input_class);

            $ccfv_custom_css = ! empty ($_POST['ccfv_custom_css']) ? $_POST['ccfv_custom_css'] : '';
            $ccfv_custom_css = str_replace('\"','"',$ccfv_custom_css);
            $ccfv_custom_css = str_replace('=\"','="',$ccfv_custom_css);
            update_option('ccfv_custom_css', $ccfv_custom_css);
            
            $ccfv_button_html = ! empty ($_POST['ccfv_button_html']) ? $_POST['ccfv_button_html'] : '';
            $ccfv_button_html = str_replace('\"','"',$ccfv_button_html);
            $ccfv_button_html = str_replace('=\"','="',$ccfv_button_html);
            update_option('ccfv_button_html', $ccfv_button_html);
           

            //Captcha settings
                // Contact
                    $contact_site_key = ! empty ($_POST['contact_site_key']) ? sanitize_text_field($_POST['contact_site_key']) : '';
                    update_option('contact_site_key', $contact_site_key);

                    $contact_secret_key = ! empty ($_POST['contact_secret_key']) ? sanitize_text_field($_POST['contact_secret_key']) : '';
                    update_option('contact_secret_key', $contact_secret_key);

                // Commnet
                    $comment_site_key = ! empty ($_POST['comment_site_key']) ? sanitize_text_field($_POST['comment_site_key']) : '';
                    update_option('comment_site_key', $comment_site_key);

                    $comment_secret_key = ! empty ($_POST['comment_secret_key']) ? sanitize_text_field($_POST['comment_secret_key']) : '';
                    update_option('comment_secret_key', $comment_secret_key);

                // Subscription
                    $subscription_site_key = ! empty ($_POST['subscription_site_key']) ? sanitize_text_field($_POST['subscription_site_key']) : '';
                    update_option('subscription_site_key', $subscription_site_key);

                    $subscription_secret_key = ! empty ($_POST['subscription_secret_key']) ? sanitize_text_field($_POST['subscription_secret_key']) : '';
                    update_option('subscription_secret_key', $subscription_secret_key);

            $show_on_cf =  isset($_POST["show_on_cf"]) ? "yes" : "no";
            update_option('show_on_cf', $show_on_cf);
            if($show_level == 'yes') { $checked_capt_cf = 'checked';} else {$checked_capt_cf = '';}

            $show_on_comment =  isset($_POST["show_on_comment"]) ? "yes" : "no";
            update_option('show_on_comment', $show_on_comment);
            if($show_on_comment == 'yes') { $checked_capt_comment = 'checked';} else {$checked_capt_comment = '';}

            $updated = 1;

            wp_admin_notice( 'Saved', [ 'type' => 'success', 'dismissible'        => true ] );

        } else {
            $ccfv_email_to = get_option('ccfv_email_to');
            $ccfv_email_cc = get_option('ccfv_email_cc');
            $ccfv_success_msg = get_option('ccfv_success_msg');

            //Form Settings
            $show_level = get_option('show_level');
            if($show_level == 'yes') { $checked = 'checked';} else {$checked = '';}

            $ccfv_name_placeholder = get_option('ccfv_name_placeholder');
            $ccfv_email_placeholder = get_option('ccfv_email_placeholder');
            $ccfv_subject_placeholder = get_option('ccfv_subject_placeholder');
            $show_ph_field = get_option('show_ph_field');
            if($show_ph_field == 'yes') { $checked_ph_field = 'checked';} else {$checked_ph_field = '';}
            $ccfv_ph_placeholder = get_option('ccfv_ph_placeholder');
            $ccfv_message_placeholder = get_option('ccfv_message_placeholder');

            $ccfv_form_wrapper_class = get_option('ccfv_form_wrapper_class');
            $ccfv_form_class = get_option('ccfv_form_class');
            $ccfv_input_class = get_option('ccfv_input_class');
            $ccfv_custom_css = get_option('ccfv_custom_css');
            $ccfv_button_html = get_option('ccfv_button_html');

            //Captcha settings
            $contact_site_key = get_option('contact_site_key');
            $contact_secret_key = get_option('contact_secret_key');

            $comment_site_key = get_option('comment_site_key');
            $comment_secret_key = get_option('comment_secret_key');

            $subscription_site_key = get_option('subscription_site_key');
            $subscription_secret_key = get_option('subscription_secret_key');

            $show_on_cf = get_option('show_on_cf');
            if($show_on_cf == 'yes') { $checked_capt_cf = 'checked';} else {$checked_capt_cf = '';}

            $show_on_comment = get_option('show_on_comment');
            if($show_on_comment == 'yes') { $checked_capt_comment = 'checked';} else {$checked_capt_comment = '';}
        }
        
        $tab = isset($_GET['tab']) ? $_GET['tab'] : ''; ?>
        
        
        <nav class="nav-tab-wrapper">  
          <a href="?page=mail-settings-page" class="nav-tab <?php if($tab==null):?>nav-tab-active<?php endif; ?>">Email Settings</a>  
          <a href="?page=mail-settings-page&tab=form-fields" class="nav-tab <?php if($tab==='form-fields'):?>nav-tab-active<?php endif; ?>">Form Fields</a>  
          <a href="?page=mail-settings-page&tab=form-design-settings" class="nav-tab <?php if($tab==='form-design-settings'):?>nav-tab-active<?php endif; ?>">Form Design</a>  
          <a href="?page=mail-settings-page&tab=captcha-settings" class="nav-tab <?php if($tab==='captcha-settings'):?>nav-tab-active<?php endif; ?>">Captcha</a>  
        </nav>  
        
        
        <form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="POST">
            
            
            <input type="hidden" value="1" name="update">
            <?php wp_nonce_field( 'ccfv_update_settings', 'cf7sr_nonce' ); ?>

            <div style="display: flex;">
                <div style="width: 90%;">
                    <h1><?php esc_html_e('Contact Form Settings', 'ccfv-plugin-textdomain'); ?></h1>
                    <p>To add the Contact Form, add <strong>[ccvf_contact_form]</strong> in your desired page content</p>
                </div>
                <input type="submit" class="button-primary admin-btn" style="margin: 1.5rem 0;" value="Save Settings">
            </div>

                <div class="tab-content" style="margin-right: 12px;">  
                
                    
                    <table class="sec-wrap <?php if($tab === '') { ?>show<?php } ?>" id="one">                
                        <tbody>
                            <tr>
                                <th>Mail To</th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_email_to); ?>" name="ccfv_email_to">
                                </td>
                            </tr>
                            <tr>
                                <th>Mail Cc</th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_email_cc); ?>" name="ccfv_email_cc">
                                </td>
                            </tr>
                            <tr>
                                <th>Success Message</th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_success_msg); ?>" name="ccfv_success_msg"> 
                                </td>               
                            </tr>
                        </tbody>
                    </table>
                    
                    
                    <table class="sec-wrap <?php if($tab === 'form-fields') { ?>show<?php } ?>"  id="two">
                        <tbody>
                            <tr>
                                <th>Show Form label <span>(remove the placeholder)</span></th>
                                <td> 
                                    <input type="checkbox" id="show_level" name="show_level" <?php echo $checked; ?>> 
                                </td>
                            </tr>
                            <tr>
                                <th>Name Text <span>(placeholder / label)</span></th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_name_placeholder); ?>" name="ccfv_name_placeholder"> 
                                </td>
                            </tr>    
                            <tr>
                                <th>Email Text <span>(placeholder / label)</span></th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_email_placeholder); ?>" name="ccfv_email_placeholder"> 
                                </td>    
                            </tr>
                            <tr>
                                <th>Subject Text <span>(placeholder / label)</span></th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_subject_placeholder); ?>" name="ccfv_subject_placeholder"> 
                                </td>    
                            </tr>
                            <tr>
                                <th>Show Phone Number Field <span></span></th>
                                <td> 
                                    <input type="checkbox" id="show_ph_field" name="show_ph_field" <?php echo $checked_ph_field; ?>> 
                                </td>
                            </tr>
                            <tr>
                                <th>Phone Number Text <span>(placeholder / label)</span></th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_ph_placeholder); ?>" name="ccfv_ph_placeholder"> 
                                </td>    
                            </tr>
                            <tr>
                                <th>Message Text <span>(placeholder / label)</span></th>
                                <td> 
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_message_placeholder); ?>" name="ccfv_message_placeholder"> 
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                    
                    
                    <table class="sec-wrap <?php if($tab === 'form-design-settings') { ?>show<?php } ?>" id="three">
                        <tbody>
                            <tr>
                                <th>Form Wrapper class <span>(optional)</span></th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_form_wrapper_class); ?>" name="ccfv_form_wrapper_class"> 
                                </td>
                            </tr>
                            <tr>
                                <th>Form Class <span>(optional)</span></th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_form_class); ?>" name="ccfv_form_class"> 
                                </td>
                            </tr>
                            <tr>
                                <th>Input Fields Class <span>(optional)</span></th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($ccfv_input_class); ?>" name="ccfv_input_class"> 
                                </td>
                            </tr>
                            <tr>
                                <th>Custom CSS <span></span>
                                <td>
                                    <textarea name="ccfv_custom_css" rows="4" cols="55"><?php echo esc_attr($ccfv_custom_css); ?></textarea>      
                                </td>               
                            </tr>
                            <tr>
                                <th>Submit Button HTML <span>(optional)</span>
                                <td>
                                    <textarea name="ccfv_button_html" rows="4" cols="90"><?php echo esc_attr($ccfv_button_html); ?></textarea>      
                                </td>               
                            </tr>
                        </tbody>
                    </table>
                    
                    
                    <table class="sec-wrap <?php if($tab === 'captcha-settings') { ?>show<?php } ?>" id="four">
                        <tbody>
                            <tr>
                                <th>Contact Site key</th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($contact_site_key); ?>" name="contact_site_key"> 
                                </td>
                            </tr>
                            <tr>
                                <th>Contact Secret key </th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($contact_secret_key); ?>" name="contact_secret_key"> 
                                </td>
                            </tr>
                            <tr>
                                <th>Comment Site key</th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($comment_site_key); ?>" name="comment_site_key"> 
                                </td>
                            </tr>
                            <tr>
                                <th>Comment Secret key </th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($comment_secret_key); ?>" name="comment_secret_key"> 
                                </td>
                            </tr>
                            <tr>
                                <th>subscription Site key</th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($subscription_site_key); ?>" name="subscription_site_key"> 
                                </td>
                            </tr>
                            <tr>
                                <th>subscription Secret key </th>
                                <td>
                                    <input type="text" style="width: 370px;" value="<?php echo esc_attr($subscription_secret_key); ?>" name="subscription_secret_key"> 
                                </td>
                            </tr>
                            <tr>
                                <th>Show on contact Form </th>
                                <td>
                                    <input type="checkbox" id="show_on_cf" name="show_on_cf" <?php echo $checked_capt_cf; ?>>
                                </td>
                            </tr>
                            <tr>
                                <th>Show on comment Form </th>
                                <td>
                                    <input type="checkbox" id="show_on_comment" name="show_on_comment" <?php echo $checked_capt_comment; ?>> 
                                </td>
                            </tr>                        
                            <tr>
                                <th>You can generate Site key and Secret key <a target="_blank" href="https://www.google.com/recaptcha/admin" rel="noopener noreferrer nofollow">here</a></th>
                                <td>
                                    <strong style="color:red">Choose reCAPTCHA v2 -&gt; Checkbox</strong><br>
                                    <p>This plugin implements "I'm not a robot" checkbox.</p>
                                    <img src="<?php echo CCFV_URL.'captcha.jpg'; ?>" style="width:400px;float: left;"/>
                                </td>
                            </tr>
                        </tbody> 
                    </table> 
                        
                </div>  
    
            </form>
        
            <style>
                input, textarea {
                    border: 1px solid #8c8f945c !important;
                }
                th {
                    vertical-align: top;
                    text-align: left;
                    padding: 18px 10px 20px 0;
                    width: 300px;
                    font-weight: 600;
                }
                .not-used { color: red; }
                span { font-size: 12px; }
                .sec-wrap {
                    display: none;
                    width: 100%;
                    background-color: rgb(255, 255, 255);
                    color: rgb(30, 30, 30);
                    padding: 1rem 2rem;
                    position: relative;
                    box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 0px 1px;
                    outline: none;
                    border-radius: 6px;
                }
                .show {
                    display: block;
                }
                td h3 { border-bottom: 1px solid #000; padding-bottom: 4px; display: inline; }
            </style>

        <?php
    }
}