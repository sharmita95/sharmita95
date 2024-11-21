<?php
//================================================================= Check And Create Table
include_once CCFV_DIR.'/vmt_subscription_core/plugin-table.php';
//================================================================= Remove Filters and Actions on Deactivation
include_once CCFV_DIR.'/vmt_subscription_core/plugin-deactivation.php';
//================================================================= Add Filters and Actions on Activation
include_once CCFV_DIR.'/vmt_subscription_core/plugin-activation.php';
//================================================================= Core Functions
include_once CCFV_DIR.'/vmt_subscription_core/core-action.php';
//================================================================= Plugin Menu Pages
include_once CCFV_DIR.'/vmt_subscription_core/menu-page.php';



//================================================================= Custom Mail Function
function custom_mail($to) {
	$cipher = 'AES-128-CBC';
	$key = 'vmt_viacon_subscription';
	$encrypted_mail   = strrev(base64_encode(openssl_encrypt($to, $cipher, $key, 0, substr(hash('sha256', $key), 0, openssl_cipher_iv_length($cipher)))));   
	$unsubscribe_link = site_url('/subscription-confirmation?status=unsubscribed&subscriber='.$encrypted_mail);

	$subject = get_option('vmt_mail_subject');
	$message = get_option('vmt_mail_message').'<br><div><p>__<br><a href="'.$unsubscribe_link.'">Click Here To Unsubscribe</a></p></div><br>';
	$from_name = get_option('vmt_mail_from_name');
	$from_mail = get_option('vmt_mail_from_mail');
	$reply_name = get_option('vmt_mail_reply_to_name');
	$reply_mail = get_option('vmt_mail_reply_to_mail');

	$headers = 'Content-Type: text/html; charset=UTF-8'.
				'From: '.$from_name.' <'.$from_mail.'>'."\r\n" .
				'Reply-To: '.$reply_name.' <' . $reply_mail.'>'."\r\n";

	$sent = wp_mail($to, $subject, $message, $headers);
	return $sent;
}
//================================================================= Custom Mail Confirmation Function
function custom_confirmation_mail($to) {
	$cipher = 'AES-128-CBC';
	$key = 'vmt_viacon_subscription';
	$encrypted_mail   = strrev(base64_encode(openssl_encrypt($to, $cipher, $key, 0, substr(hash('sha256', $key), 0, openssl_cipher_iv_length($cipher)))));   
	$confirmation_link = site_url('/subscription-confirmation?subscriber='.$encrypted_mail);
	$unsubscribe_link = site_url('/subscription-confirmation?status=unsubscribed&subscriber='.$encrypted_mail);

	$subject = get_option('vmt_conf_subject');
	$message = get_option('vmt_conf_message').'<br><a href="'.$confirmation_link.'">Click Here To Confirm</a><br><div><p>__<br><a href="'.$unsubscribe_link.'">Click Here To Unsubscribe</a></p></div><br>';
	$from_name = get_option('vmt_conf_from_name');
	$from_mail = get_option('vmt_conf_from_mail');
	$reply_name = get_option('vmt_conf_reply_to_name');
	$reply_mail = get_option('vmt_conf_reply_to_mail');

	$headers = 'Content-Type: text/html; charset=UTF-8'.
				'From: '.$from_name.' <'.$from_mail.'>'."\r\n" .
				'Reply-To: '.$reply_name.' <' . $reply_mail.'>'."\r\n";

	$sent = wp_mail($to, $subject, $message, $headers);
	return $sent;
}


//================================================================= Main Subscription Function
function subscribe(){
	$subscription_site_key = get_option('subscription_site_key');
	$spinner_svg = get_option('vmt_spinner_svg');
	if(empty($spinner_svg)){
		$spinner_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="25px" height="25px" viewBox="0 0 100 100"><circle cx="50" cy="50" fill="none" stroke="#ffffff" stroke-width="10" r="40" stroke-dasharray="150 50"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>';
	}
	$spinner_gap = get_option('vmt_spinner_gap');
	$spinner_position = get_option('vmt_spinner_position');
	if(empty($spinner_position)){
		$spinner_position = 'Middle';
	}
	$spinner_align = get_option('vmt_spinner_align');
	if(empty($spinner_align)){
		$spinner_align = 'Center';
	}
	?><form onsubmit="return false;" method="post" class="subscribe-us-form <?php echo get_option('vmt_subscribe_form_class'); ?>">
		<?php echo htmlspecialchars_decode(get_option('vmt_subscribe_form')); ?>
		<div id="subscription-captcha" class="g-recaptcha" style="margin-bottom: 1rem;"></div>
		<div class="vmt-spinner" style="display: none;" data-position="<?php echo $spinner_position; ?>" data-align="<?php echo $spinner_align; ?>" <?php echo (!empty($spinner_gap))?'data-gap="'.$spinner_gap.'"':''; ?>><?php echo htmlspecialchars_decode($spinner_svg); ?></div>
		<?php if(!empty($subscription_site_key)) { 
			?>
			<script>
				jQuery(document).ready(function($) {
					$('#subscription-captcha').insertBefore('form.subscribe-us-form button, form.subscribe-us-form input[type="submit"]');
				});
			</script>
			<script type="text/javascript">
				var onloadCallback = function() {
					grecaptcha.render('subscription-captcha', {
					'sitekey' : '<?php echo $subscription_site_key; ?>'
					});
				};
			</script>
			<?php
		} ?>
	</form>
	
	<script>
		jQuery.fn.subscribe_function = function(param) {
			var subscribers_data = {};
			var check_required = 1;
			var tatol_captcha = $('.g-recaptcha').length;
			var response = grecaptcha.getResponse(tatol_captcha-1);
			$('form.subscribe-us-form input[required]').each(function(){
				if(!$(this).val()){
					check_required = 0;
				}
			});
			$('form.subscribe-us-form input[name=email][required], form.subscribe-us-form input[name=mail][required], form.subscribe-us-form input[type=email][required]').each(function(){
				var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				var email = $(this).val();
				if (email === '' || !regex.test(email)) {
					check_required = 0;
                }
			});
			if(check_required){
				var check_empty = 0;
				$('form.subscribe-us-form input, form.subscribe-us-form textarea').each(function(){
					var id = $(this).attr('name');
					var value = $(this).val();
					subscribers_data[id] = value;
					if(value){
						check_empty = 1;
					}
				});
				$('form.subscribe-us-form .empty-message').remove();
				if(check_empty){
					if (response.length === 0) {
						$('form.subscribe-us-form').append('<span class="empty-message" style="color: red;">Please complete the reCAPTCHA challenge</span>');
					}else {
						var spinner_position = $('form.subscribe-us-form div.vmt-spinner').data('position');
						var spinner_gap = $('form.subscribe-us-form div.vmt-spinner').data('gap');
						if(!spinner_gap){
							spinner_gap = 0;
						}
						var spinner_align = $('form.subscribe-us-form div.vmt-spinner').data('align');
						jQuery.ajax({
							url: '<?php echo admin_url('admin-ajax.php'); ?>',
							data: { action: 'subscribe_function', subscribers_data: subscribers_data},
							beforeSend: function(){
								if($('form.subscribe-us-form button').length && spinner_position === 'Middle'){
									$("form.subscribe-us-form button").height($('form.subscribe-us-form button').height()).width($('form.subscribe-us-form button').width()).empty().append($('form.subscribe-us-form div.vmt-spinner').contents()).find('svg').css({"position":"absolute", "display":"flex", "top":"50%", "left":"50%", "height":"fit-content", "width":"fit-content", "transform":"translateY(-50%) translateX(-50%)"});
									$("form.subscribe-us-form div.vmt-spinner").remove();
								}else{
									if(spinner_position === 'Middle'){
										spinner_position = 'Bottom';
									}
									$("form.subscribe-us-form div.vmt-spinner").show();
									if($('form.subscribe-us-form input[type="submit"]').length || $('form.subscribe-us-form button').length){
										if($('form.subscribe-us-form button').length){
											$("form.subscribe-us-form button").wrap('<div class="submit-wrapper" style="gap:'+spinner_gap+';display: flex;"/>');
											$("form.subscribe-us-form div.vmt-spinner").insertAfter('form.subscribe-us-form button');
										}else{
											$('form.subscribe-us-form input[type="submit"]').wrap('<div class="submit-wrapper" style="gap:'+spinner_gap+';display: flex;"/>');
											$("form.subscribe-us-form div.vmt-spinner").insertAfter('form.subscribe-us-form input[type="submit"]');
										}
									}else{
										$("form.subscribe-us-form div.vmt-spinner").wrap('<div class="submit-wrapper" style="gap:'+spinner_gap+';display: flex;"/>');
									}
									if(spinner_position === 'Bottom'){
										$("form.subscribe-us-form div.submit-wrapper").css("flex-direction", "column");
									}
									if(spinner_position === 'Left'){
										$("form.subscribe-us-form div.submit-wrapper").css("flex-direction", "row-reverse");
									}
									if(spinner_position === 'Top'){
										$("form.subscribe-us-form div.submit-wrapper").css("flex-direction", "column-reverse");
									}
									if(spinner_position === 'Right'){
										$("form.subscribe-us-form div.submit-wrapper").css("flex-direction", "row");
									}
									if(spinner_align === 'Start'){
										$("form.subscribe-us-form div.submit-wrapper").css("align-items", "flex-start");
									}
									if(spinner_align === 'Center'){
										$("form.subscribe-us-form div.submit-wrapper").css("align-items", "center");
									}
									if(spinner_align === 'End'){
										$("form.subscribe-us-form div.submit-wrapper").css("align-items", "flex-end");
									}
								}
							},
							success: function(data) {
								$("form.subscribe-us-form").empty().html( data );
							}
						});
					}
				}else{
					$('form.subscribe-us-form').append('<span class="empty-message" style="color: red;">Empty Data Cannot Be Submitted</span>');
				}
			}
		}
		jQuery(document).ready(function($) {
			$('form.subscribe-us-form input[name=email], form.subscribe-us-form input[name=mail], form.subscribe-us-form input[type=email]').attr("required", true).attr({required: "true", type: "email"});
			$('form.subscribe-us-form button, form.subscribe-us-form input[type="submit"]').click(function(){
				$(this).subscribe_function();
			});
			$('form.subscribe-us-form').keypress(function(event){
				var keycode = (event.keyCode ? event.keyCode : event.which);
				if(keycode == '13'){
					$(this).subscribe_function();
				}
			});
		});
	</script>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
<?php
}
add_action('wp_ajax_subscribe_function' , 'subscribe_function');
add_action('wp_ajax_nopriv_subscribe_function','subscribe_function');
function subscribe_function(){
	$subscribers_data = $_GET['subscribers_data'];
	$email = $subscribers_data['email'];
	$sent_confirmation = get_option('vmt_sent_confirmation');
	if(empty($sent_confirmation)){
		$sent_confirmation = 'Yes';
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'vmt_subscription';
	$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE email_id='$email'" );
	$date = gmdate( 'Y-m-d H:i:s');
	
	if(is_array($results) && count($results)>0){
		$result = $wpdb->get_results( "SELECT `status` FROM $table_name WHERE email_id='$email'" );
		$status = $result[0]->status;
		if($status == 'subscribed'){
			$span_class = get_option('vmt_subscribe_exist_class');
			$span_msg = get_option('vmt_subscribe_exist');
			echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: green;">';
				echo (!empty($span_msg))?$span_msg:'You Are Already A Member.';
			echo '</span>';
		}elseif($status == 'unconfirmed'){
			if($sent_confirmation == 'Yes'){
				$reconfirm = custom_confirmation_mail($email);
				$status = 'unconfirmed';
			}else{
				$reconfirm = custom_mail($email);
				$status = 'subscribed';
			}
			if($reconfirm){
				if($sent_confirmation == 'Yes'){
					$span_class = get_option('vmt_subscribe_confirmation_class');
					$span_msg = get_option('vmt_subscribe_confirmation');
					echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: yellow;">';
						echo (!empty($span_msg))?$span_msg:'A confirmation Mail Has Been Sent. Please Click On The Link Sent To Your Mail To Confirm';
					echo '</span>';
				}else{
					$wpdb->query("UPDATE $table_name SET `status`='$status' WHERE email_id='$email'");

					$span_class = get_option('vmt_subscribe_successful_class');
					$span_msg = get_option('vmt_subscribe_successful');
					echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: green;">';
						echo (!empty($span_msg))?$span_msg:'Successfully Submitted.';
					echo '</span>';
				}
			}else{
				$span_class = get_option('vmt_subscribe_error_class');
				$span_msg = get_option('vmt_subscribe_error');
				echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: red;">';
					echo (!empty($span_msg))?$span_msg:'It looks like some error has occurred.';
				echo '</span>';
			}
		}elseif($status == 'unsubscribed'){
			if($sent_confirmation == 'Yes'){
				$reconfirm = custom_confirmation_mail($email);
				$status = 'unconfirmed';
			}else{
				$reconfirm = custom_mail($email);
				$status = 'subscribed';
			}
			if($reconfirm){
				$wpdb->query("UPDATE $table_name SET `status`='$status' WHERE email_id='$email'");

				if($sent_confirmation == 'Yes'){
					$span_class = get_option('vmt_subscribe_resubscribe_class');
					$span_msg = get_option('vmt_subscribe_resubscribe');
					echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: green;">';
						echo (!empty($span_msg))?$span_msg:'You Are Not A Subscriber. Please Click On The Link Sent To Your Mail To Subscribe';
					echo '</span>';
				}else{
					$span_class = get_option('vmt_subscribe_successful_class');
					$span_msg = get_option('vmt_subscribe_successful');
					echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: green;">';
						echo (!empty($span_msg))?$span_msg:'Successfully Submitted.';
					echo '</span>';
				}
			}else{
				$span_class = get_option('vmt_subscribe_error_class');
				$span_msg = get_option('vmt_subscribe_error');
				echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: red;">';
					echo (!empty($span_msg))?$span_msg:'It looks like some error has occurred.';
				echo '</span>';
			}
		}else{
			$span_class = get_option('vmt_subscribe_error_class');
			$span_msg = get_option('vmt_subscribe_error');
			echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: red;">';
				echo (!empty($span_msg))?$span_msg:'It looks like some error has occurred.';
			echo '</span>';
		}
	}else{
		if($sent_confirmation == 'Yes'){
			$sent = custom_confirmation_mail($email);
			$status = 'unconfirmed';
		}else{
			$sent = custom_mail($email);
			$status = 'subscribed';
		}
		$wpdb->query("INSERT INTO $table_name(`email_id`, `status`, `time`) VALUES ('$email','$status','$date')");
		if($sent){
			$span_class = ($sent_confirmation == 'Yes')?get_option('vmt_subscribe_confirmation_class'):get_option('vmt_subscribe_successful_class');
			$span_msg = ($sent_confirmation == 'Yes')?get_option('vmt_subscribe_confirmation'):get_option('vmt_subscribe_successful');
			echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: green;">';
				echo (!empty($span_msg))?$span_msg:'Successfully Submitted.';
			echo '</span>';
		}else{
			$span_class = get_option('vmt_subscribe_error_class');
			$span_msg = get_option('vmt_subscribe_error');
			echo (!empty($span_class))?'<span class="'.$span_class.'">':'<span style="color: red;">';
				echo (!empty($span_msg))?$span_msg:'It looks like some error has occurred.';
			echo '</span>';
		}
	}

	die;
}