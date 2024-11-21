<?php 
/** 
 * Google reCAPTCHA: Add widget before the submit button 
 */ 

$comment_site_key = get_option('comment_site_key');
$comment_secret_key = get_option('comment_secret_key');
$show_on_comment = get_option('show_on_comment');

// if (!is_user_logged_in()) { 
if($comment_site_key && $comment_secret_key && ($show_on_comment == 'yes')) {
   add_filter('comment_form_defaults', 'add_google_recaptcha'); 
}
// }
function add_google_recaptcha($submit_field) { 
   global $comment_site_key;

   $submit_field['submit_field'] = '<div style="margin-bottom: 1rem;" class="g-recaptcha" data-sitekey="'.$comment_site_key.'"></div>'.$submit_field['submit_field']; 
   return $submit_field; 
} 

 

/** 
* Google reCAPTCHA: verify response and validate comment submission 
*/ 
function is_valid_captcha_response($captcha) { 
    
    global $comment_secret_key;
    $captcha_postdata = http_build_query( 
       array( 
           'secret' => $comment_secret_key, 
           'response' => $captcha, 
           'remoteip' => $_SERVER['REMOTE_ADDR'] 
       ) 
    ); 
    $captcha_opts = array( 
       'http' => array( 
           'method'  => 'POST', 
           'header'  => 'Content-type: application/x-www-form-urlencoded', 
           'content' => $captcha_postdata 
       ) 
    ); 
    $captcha_context  = stream_context_create($captcha_opts); 
    $captcha_response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify", false, $captcha_context), true); 
    if(!empty($captcha_response['success'])){ 
       return true; 
    }else{ 
       return false; 
    } 
} 

function verify_google_recaptcha() { 
   $recaptcha = $_POST['g-recaptcha-response']; 
   if(empty($recaptcha)){ 
       wp_die(__("<b>ERROR: </b><b>Please click the captcha checkbox.</b><p><a href='javascript:history.back()'>« Back</a></p>")); 
   }elseif(!is_valid_captcha_response($recaptcha)){ 
       wp_die(__("<b>Sorry, spam detected!</b>")); 
   } 
} 

// if (!is_user_logged_in()) { 
if($comment_site_key && $comment_secret_key && ($show_on_comment == 'yes')) {
   add_action('pre_comment_on_post', 'verify_google_recaptcha'); 
}
// }