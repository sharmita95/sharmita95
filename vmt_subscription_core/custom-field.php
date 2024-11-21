<?php
function form_sections() {
	add_settings_section( 'form_content', 'Form', 'form_section_callback', 'subscribe_menu/form' );
}
add_action('admin_init', 'form_sections');
function form_section_callback() {
	echo '<p>Add Subscription Form</p>';  
}


function form_content_fields() {
	add_settings_field('form_class', 'Form Class', 'form_class_callback', 'subscribe_menu/form','form_content','vmt_subscribe_form_class');
	add_settings_field('form_content', 'Form', 'form_content_callback', 'subscribe_menu/form','form_content','vmt_subscribe_form');

	register_setting('subscribe_menu/form','vmt_subscribe_form', 'esc_attr');
	register_setting('subscribe_menu/form','vmt_subscribe_form_class', 'esc_attr');
}
add_action('admin_init', 'form_content_fields');
function form_content_callback($form) {
	echo '<textarea rows="20" cols="100" id="'.$form.'" name="'.$form.'" placeholder="Add Your Fields Here">'.get_option($form).'</textarea>';
}
function form_class_callback($class) {
	echo '<input class="regular-text ltr" id="'.$class.'" name="'.$class.'" placeholder="Add Form Class Here" value="'.get_option($class).'" />';
}



function mail_sections() {
	add_settings_section( 'non_confirm_mail_contents', 'Before Confirmation Messages', '', 'subscribe_menu/mail' );
	add_settings_section( 'confirm_mail_contents', 'After Confirmation Messages', '', 'subscribe_menu/mail' );
	add_settings_section( 'mail_settings', 'Settings', '', 'subscribe_menu/mail' );
}
add_action('admin_init', 'mail_sections');

function mail_content_fields() {
	add_settings_field('mail_header_from_name', 'From Name', 'mail_form_callback', 'subscribe_menu/mail', 'non_confirm_mail_contents','vmt_mail_from_name');
	add_settings_field('mail_header_from_mail', 'From Mail', 'mail_form_callback', 'subscribe_menu/mail', 'non_confirm_mail_contents','vmt_mail_from_mail');
	add_settings_field('mail_subject', 'Subject', 'mail_form_callback', 'subscribe_menu/mail', 'non_confirm_mail_contents','vmt_mail_subject');
	add_settings_field('mail_message', 'Message body', 'mail_message_callback', 'subscribe_menu/mail', 'non_confirm_mail_contents','vmt_mail_message');
	add_settings_field('mail_header_reply_to_name', 'Reply To Name', 'mail_form_callback', 'subscribe_menu/mail', 'non_confirm_mail_contents','vmt_mail_reply_to_name');
	add_settings_field('mail_header_reply_to_mail', 'Reply To Mail', 'mail_form_callback', 'subscribe_menu/mail', 'non_confirm_mail_contents','vmt_mail_reply_to_mail');
	add_settings_field('mail_confirm_from_name', 'From Name', 'mail_form_callback', 'subscribe_menu/mail', 'confirm_mail_contents','vmt_conf_from_name');
	add_settings_field('mail_confirm_from_mail', 'From Mail', 'mail_form_callback', 'subscribe_menu/mail', 'confirm_mail_contents','vmt_conf_from_mail');
	add_settings_field('mail_confirm_subject', 'Subject', 'mail_form_callback', 'subscribe_menu/mail', 'confirm_mail_contents','vmt_conf_subject');
	add_settings_field('mail_confirm_message', 'Message body', 'mail_message_callback', 'subscribe_menu/mail', 'confirm_mail_contents','vmt_conf_message');
	add_settings_field('mail_confirm_reply_to_name', 'Reply To Name', 'mail_form_callback', 'subscribe_menu/mail', 'confirm_mail_contents','vmt_conf_reply_to_name');
	add_settings_field('mail_confirm_reply_to_mail', 'Reply To Mail', 'mail_form_callback', 'subscribe_menu/mail', 'confirm_mail_contents','vmt_conf_reply_to_mail');

	register_setting('subscribe_menu/mail','vmt_mail_subject', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_mail_message', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_mail_from_name', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_mail_from_mail', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_mail_reply_to_name', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_mail_reply_to_mail', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_conf_from_name', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_conf_from_mail', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_conf_subject', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_conf_message', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_conf_reply_to_name', 'esc_attr');
	register_setting('subscribe_menu/mail','vmt_conf_reply_to_mail', 'esc_attr');
}
add_action('admin_init', 'mail_content_fields');

function mail_form_callback($msg) {
	echo '<input class="regular-text ltr" size="100" id="'.$msg.'" name="'.$msg.'" placeholder="Add '.ucfirst(substr($msg,9)).'" value="'.get_option($msg).'" />';
}
function mail_message_callback($msg) {
	echo '<textarea rows="15" cols="50" id="'.$msg.'" name="'.$msg.'" placeholder="Add '.ucfirst(substr($msg,9)).'">'.get_option($msg).'</textarea>';
}

function mail_setting_fields() {
	add_settings_field('mail_confirmation_setting', 'Sent Confirmation', 'mail_setting_callback', 'subscribe_menu/mail', 'mail_settings','vmt_sent_confirmation');
	
	register_setting('subscribe_menu/mail','vmt_sent_confirmation', 'esc_attr');
}
add_action('admin_init', 'mail_setting_fields');
function mail_setting_callback($conf) {
	$mail_confirm = get_option($conf);
	if(empty($mail_confirm)){
		$mail_confirm = 'Yes';
	}
	?><fieldset>
		<label>Yes</label>
		<input type="radio" value="Yes" name="<?php echo $conf; ?>" <?php checked( $mail_confirm, 'Yes' ); checked( $mail_confirm, '' ); ?> style="margin-right: 30px;">
		<label>No</label>
		<input type="radio" value="No" name="<?php echo $conf; ?>" <?php checked( $mail_confirm, 'No' ); ?> style="margin-right: 30px;">
	</fieldset><?php
}



function message_sections() {
	add_settings_section( 'message_contents', 'Messages', '', 'subscribe_menu/message' );
    // add_settings_section( 'message_settings', 'Messages Settings', '', 'subscribe_menu/message' );
	add_settings_section( 'spinner_settings', 'Spinner Settings', '', 'subscribe_menu/message' );
}
add_action('admin_init', 'message_sections');

function message_content_fields() {
	add_settings_field('messages_successful', 'Successful Message', 'messages_content_callback', 'subscribe_menu/message','message_contents',array('vmt_subscribe_successful','vmt_subscribe_successful_class'));
	add_settings_field('messages_confirmation', 'Confirmation Message', 'messages_content_callback', 'subscribe_menu/message','message_contents',array('vmt_subscribe_confirmation','vmt_subscribe_confirmation_class'));
	add_settings_field('messages_exist', 'Exist Message', 'messages_content_callback', 'subscribe_menu/message','message_contents',array('vmt_subscribe_exist','vmt_subscribe_exist_class'));
	add_settings_field('messages_unsubscribe', 'Unsubscribe Message', 'messages_content_callback', 'subscribe_menu/message','message_contents',array('vmt_subscribe_unsubscribe','vmt_subscribe_unsubscribe_class'));
	add_settings_field('messages_resubscribe', 'Resubscribe Message', 'messages_content_callback', 'subscribe_menu/message','message_contents',array('vmt_subscribe_resubscribe','vmt_subscribe_resubscribe_class'));
	add_settings_field('messages_error', 'Error Message', 'messages_content_callback', 'subscribe_menu/message','message_contents',array('vmt_subscribe_error','vmt_subscribe_error_class'));

	register_setting('subscribe_menu/message','vmt_subscribe_successful', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_confirmation', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_exist', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_unsubscribe', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_resubscribe', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_error', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_successful_class', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_confirmation_class', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_exist_class', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_unsubscribe_class', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_resubscribe_class', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_subscribe_error_class', 'esc_attr');
}
add_action('admin_init', 'message_content_fields');

function messages_content_callback($msg) {
	echo '<div style="display: flex; gap: 10px;">';
		echo '<div>';
			echo '<textarea rows="5" cols="100" id="'.$msg[0].'" name="'.$msg[0].'" placeholder="'.ucfirst(substr($msg[0],14)).' Message">'.get_option($msg[0]).'</textarea>';
		echo '</div>';
		echo '<div>';
			echo '<p class="description">Add '.ucfirst(substr($msg[0],14)).' Message Span Class Here</p>';
			echo '<input class="regular-text ltr" id="'.$msg[1].'" name="'.$msg[1].'" placeholder="Add '.ucfirst(substr($msg[0],14)).' Message Span Class Here" value="'.get_option($msg[1]).'" />';
		echo '</div>';
	echo '</div>';
}



function message_setting_fields() {
	add_settings_field('spinner_svg', 'Spinner SVG', 'spinner_callback', 'subscribe_menu/message','spinner_settings','vmt_spinner_svg');
	add_settings_field('spinner_gap', 'Spinner Gap', 'spinner_gap_callback', 'subscribe_menu/message','spinner_settings','vmt_spinner_gap');
	add_settings_field('spinner_position', 'Spinner Position', 'spinner_position_callback', 'subscribe_menu/message','spinner_settings','vmt_spinner_position');
	add_settings_field('spinner_align', 'Spinner Alignment', 'spinner_align_callback', 'subscribe_menu/message','spinner_settings','vmt_spinner_align');

	register_setting('subscribe_menu/message','vmt_spinner_svg', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_spinner_gap', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_spinner_position', 'esc_attr');
	register_setting('subscribe_menu/message','vmt_spinner_align', 'esc_attr');
}
add_action('admin_init', 'message_setting_fields');

function spinner_callback($svg) {
	$spinner_svg = get_option($svg);
	if(empty($spinner_svg)){
		$spinner_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="25px" height="25px" viewBox="0 0 100 100"><circle cx="50" cy="50" fill="none" stroke="#ffffff" stroke-width="10" r="40" stroke-dasharray="150 50"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>';
	}
	?><input type="text" size="100" placeholder="Add Only SVG Code" value='<?php echo $spinner_svg; ?>' name="<?php echo $svg; ?>">
	<?php echo htmlspecialchars_decode($spinner_svg);
}
function spinner_gap_callback($gap) {
	?><input type="text" placeholder="Add Gap With Suffix" value='<?php echo get_option($gap); ?>' name="<?php echo $gap; ?>"><?php
}
function spinner_position_callback($pos) {
	$position = get_option($pos);
	?><fieldset>
		<label>Top</label>
		<input type="radio" value="Top" name="<?php echo $pos; ?>" <?php checked( $position, 'Top' ); ?> style="margin-right: 30px;">
		<label>Left</label>
		<input type="radio" value="Left" name="<?php echo $pos; ?>" <?php checked( $position, 'Left' ); ?> style="margin-right: 30px;">
		<label>Bottom</label>
		<input type="radio" value="Bottom" name="<?php echo $pos; ?>" <?php checked( $position, 'Bottom' ); ?> style="margin-right: 30px;">
		<label>Right</label>
		<input type="radio" value="Right" name="<?php echo $pos; ?>" <?php checked( $position, 'Right' ); ?> style="margin-right: 30px;">
		<label>Middle</label>
		<input type="radio" value="Middle" name="<?php echo $pos; ?>" <?php checked( $position, 'Middle' ); checked( $position, '' ); ?>>
		<span class="description">(Replaces Button Text with Spinner)</span>
	</fieldset><?php
}
function spinner_align_callback($align) {
	$spinner_align = get_option($align);
	if(empty($spinner_align)){
		$spinner_align = 'Start';
	}
	?><fieldset>
		<label>Start</label>
		<input type="radio" value="Start" name="<?php echo $align; ?>" <?php checked( $spinner_align, 'Start' ); checked( $spinner_align, '' ); ?> style="margin-right: 30px;">
		<label>Center</label>
		<input type="radio" value="Center" name="<?php echo $align; ?>" <?php checked( $spinner_align, 'Center' ); ?> style="margin-right: 30px;">
		<label>End</label>
		<input type="radio" value="End" name="<?php echo $align; ?>" <?php checked( $spinner_align, 'End' ); ?>>
	</fieldset><?php
}