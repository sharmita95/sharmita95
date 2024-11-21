<?php
if ( ! function_exists( 'ccfv_contact_func' ) ) {
    function ccfv_contact_func() {
        $output = '';
        ob_start();
        
        $show_level = get_option('show_level');
        
        $ccfv_name_placeholder = get_option('ccfv_name_placeholder');
        if(!$ccfv_name_placeholder) 
        $ccfv_name_placeholder = "Your name..";

        $ccfv_email_placeholder = get_option('ccfv_email_placeholder');
        if(!$ccfv_email_placeholder)
        $ccfv_email_placeholder = "Your email..";
        
        $ccfv_subject_placeholder = get_option('ccfv_subject_placeholder');
        if(!$ccfv_subject_placeholder)
        $ccfv_subject_placeholder = "Your subject..";

        $show_ph_field = get_option('show_ph_field');
        $ccfv_ph_placeholder = get_option('ccfv_ph_placeholder');
        if(!$ccfv_ph_placeholder)
        $ccfv_ph_placeholder = "Your Phone Number..";

        $ccfv_message_placeholder = get_option('ccfv_message_placeholder');
        if(!$ccfv_message_placeholder)
        $ccfv_message_placeholder = "Your message..";

        $ccfv_form_wrapper_class = get_option('ccfv_form_wrapper_class');
        $ccfv_form_class = get_option('ccfv_form_class');
        $ccfv_input_class = get_option('ccfv_input_class');
        if(!$ccfv_input_class)
            $ccfv_input_class = "form-control";
            
        $ccfv_custom_css = get_option('ccfv_custom_css');
        $ccfv_button_html = get_option('ccfv_button_html');

        //Captcha
        $contact_site_key = get_option('contact_site_key');
        $show_on_captcha_cf = get_option('show_on_cf');
        $site_name = get_bloginfo( 'name' );
        if(empty($site_name)) {
            $site_name = "A Viacon Website";
        }

        
        ?>
        <div class="<?php echo $ccfv_form_wrapper_class; ?>" style="padding-bottom:25px;">
            <form name="ccvf_contact_form" method="post" id="ccvf_contact_form" action="" class="<?php echo $ccfv_form_class; ?>">
                <input type="hidden" name="action" value="tech_contact_process" />
                <input type="hidden" name="site_name" value="<?php echo $site_name; ?>" readonly/>
                <div class="form-group col-xs-12">
                    <?php if($show_level == 'yes') { ?>
                        <label class="form-label">  <?php echo $ccfv_name_placeholder; ?> *  </label>
                    <?php } ?>
                    <input class="<?php echo $ccfv_input_class; ?>" type="text" name="ccfv_name" value="" <?php if($show_level == 'no') { ?> placeholder="<?php echo $ccfv_name_placeholder; ?>" <?php } ?> required/>
                </div>                    
                <div class="form-group col-xs-12">
                    <?php if($show_level == 'yes') { ?>
                        <label class="form-label">  <?php echo $ccfv_email_placeholder; ?> *</label>
                    <?php } ?>
                    <input class="<?php echo $ccfv_input_class; ?>" type="email" name="ccfv_email" value="" <?php if($show_level == 'no') { ?> placeholder="<?php echo $ccfv_email_placeholder; ?>" <?php } ?> required/>
                </div>
                <div class="form-group col-xs-12">
                    <?php if($show_level == 'yes') { ?>
                        <label class="form-label">  <?php echo $ccfv_subject_placeholder; ?> *</label>
                    <?php } ?>
                    <input class="<?php echo $ccfv_input_class; ?>" type="text" name="ccfv_subject" value="" <?php if($show_level == 'no') { ?> placeholder="<?php echo $ccfv_subject_placeholder; ?>" <?php } ?> required/>
                </div>

                <?php if($show_ph_field == 'yes') { ?>
                <div class="form-group col-xs-12">
                    <?php if($show_level == 'yes') { ?>
                        <label class="form-label">  <?php echo $ccfv_ph_placeholder; ?></label>
                    <?php } ?>
                    <input class="<?php echo $ccfv_input_class; ?>" type="text" name="ccfv_phone" value="" <?php if($show_level == 'no') { ?> placeholder="<?php echo $ccfv_ph_placeholder; ?>" <?php } ?>/>
                </div>
                <?php } ?>


                <div class="form-group col-xs-12">
                    <?php if($show_level == 'yes') { ?>
                        <label class="form-label">  <?php echo $ccfv_message_placeholder; ?></label>
                    <?php } ?>
                    <textarea class="<?php echo $ccfv_input_class; ?>" name="ccfv_message" <?php if($show_level == 'no') { ?> placeholder="<?php echo $ccfv_message_placeholder; ?>" <?php } ?> ></textarea>
                </div>

                <?php if(($show_on_captcha_cf == 'yes') && !empty($contact_site_key)) { ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo $contact_site_key; ?>" style="margin-bottom: 1rem;"></div>
                <?php } ?>  

                <?php if($ccfv_button_html) {
                    echo $ccfv_button_html;
                } else { ?>
                    <input class="btn" type="submit" style="transform: translatey(85px);" value="Submit">
                <?php } ?>
            </form>
            <div class="success-msg" style="transform: translatey(40px);"></div>
            <div class="error-msg"></div>
            <div class="clear"></div>
        </div>
        
        <style>
            .smessage { border: 2px solid green; }
            .emessage { border: 2px solid red; }
            .smessage, .emessage { padding : 0.5rem;display: block;width: 100%;margin: 1rem 0;}
            label { display: block; }
            .form-group { padding: 0; }
            .g-recaptcha { display: inline-block; }
            <?php echo $ccfv_custom_css; ?>
        </style>
        
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}

add_action('wp_ajax_tech_contact_process', 'ajax_tech_contact_process');
add_action('wp_ajax_nopriv_tech_contact_process', 'ajax_tech_contact_process');
function ajax_tech_contact_process() {
    $response_arr = ['flag' => FALSE, 'msg' => NULL];
    
    $user_name = $_POST['ccfv_name'];
    $user_email = $_POST['ccfv_email'];
    $u_subject = $_POST['ccfv_subject'];
    $u_phone = $_POST['ccfv_phone'];
    $u_mnessage = $_POST['ccfv_message'];
    $captcha_resp = $_POST['g-recaptcha-response'];
    $site_name = $_POST['site_name'];

    $ccfv_email_to = get_option('ccfv_email_to');
    if(!$ccfv_email_to)
    $ccfv_email_to = 'sharmita.shee@viaconteam.com';

    $ccfv_email_cc = get_option('ccfv_email_cc');

    $ccfv_success_msg = get_option('ccfv_success_msg');
    if(!$ccfv_success_msg)
    $ccfv_success_msg = 'Thank you for your message. It has been sent';

    $contact_site_key = get_option('contact_site_key');
    $show_on_captcha_cf = get_option('show_on_cf');

    
    if(empty($user_name)) {
        $response_arr['msg'] = 'Enter your name.';
    } elseif(empty($user_email)) {
        $response_arr['msg'] = 'Enter your email address.';
    } elseif(empty($u_subject)) {
        $response_arr['msg'] = 'Enter subject.';
    } elseif(($show_on_captcha_cf == 'yes') && empty($contact_site_key)) {
        $response_arr['msg'] = 'Enter valid captcha details!!';
    } elseif(($show_on_captcha_cf == 'yes') && empty($captcha_resp)) {
        $response_arr['msg'] = 'Submit the captcha.';
    } else {
        
        $body = '<table class="mail-table" style="border: 1px solid #0a9e01; padding:20px; width: 100%;">
                    <h4 style="padding-bottom: 10px; width: 80%;">This e-mail was sent from a contact form of '.$site_name.'.</h4>
                    <tr>
                        <td>Name: ' .$user_name .'</td>
                    </tr>
                    <tr>
                        <td>Email: '. $user_email .'</td>
                    </tr>
                    <tr>
                        <td>Subject: ' . $u_subject. '</td>
                    </tr>
                    ' . ((!$u_phone=="") ? '
                    <tr>
                        <td>Phone: ' . $u_phone. '</td>
                    </tr>
                    ' : '') . '
                    ' . ((!$u_mnessage=="") ? '
                    <tr>
                        <td>Message: ' . $u_mnessage. '</td>
                    </tr>
                    ' : '') . '
                </table>';
        $headers = array('Content-Type: text/html; charset=UTF-8', 'Reply-To: ' .$user_name .' <' . $user_email. '>');

        if($ccfv_email_cc)
        $headers[] = 'Cc: '.$ccfv_email_cc;

        wp_mail( $ccfv_email_to, $u_subject , $body, $headers );
        
        $response_arr['msg'] = $ccfv_success_msg;
        $response_arr['flag'] = true;
    }
    
    
    echo json_encode($response_arr);
    exit;
}