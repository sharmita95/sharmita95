<?php
if(!function_exists('my_plugin_create_table')) {
	function my_plugin_create_table() {
		//IF DATABASE NOT EXISTS - PENDING
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'vmt_subscription';
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,		
			email_id text(255) NOT NULL,
			status text(20) NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}

if(!function_exists('my_plugin_add_prev_data')) { //Add old table data
	function my_plugin_add_prev_data() {
		global $wpdb;
		$old_table_name = $wpdb->prefix . 'ig_contacts';
		$old_table_status = $wpdb->prefix . 'ig_lists_contacts';
		$new_table_name = $wpdb->prefix . 'vmt_subscription';

		$old_table_results = $wpdb->get_results( "SELECT * FROM $old_table_name");
		if(!empty($old_table_results)) { 
			foreach($old_table_results as $old_row_data) {
				$wpdb->query("INSERT INTO $new_table_name(`id`, `email_id`, `time`) 
				VALUES ('$old_row_data->id', '$old_row_data->email','$old_row_data->created_at')");
			}
		}
		$old_table_status_results = $wpdb->get_results( "SELECT * FROM $old_table_status");
		if(!empty($old_table_status_results)) { 
			foreach($old_table_status_results as $old_status_data) {
				$wpdb->query("UPDATE $new_table_name SET `status`='$old_status_data->status' WHERE id=$old_status_data->contact_id");
			}
		}
	}
}


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


if(!function_exists('activate_subscrition_plugin')) {
	function activate_subscrition_plugin() {	
		// Add Shortcode
		add_shortcode('viacon_subscrition', 'subscribe');
		// Set Page Template
		add_filter( 'page_template', 'subscription_page_template' );
		function subscription_page_template( $page_template ){
			if ( get_page_template_slug() == 'subscription-temp.php' ) {
				$page_template = dirname( __FILE__ ) . '/subscription-temp.php';
			}
			return $page_template;
		}
		// Add Page Template
		add_filter( 'theme_page_templates', 'add_template', 10, 4 );
		function add_template( $post_templates, $wp_theme, $post, $post_type ) {
			$post_templates['subscription-temp.php'] = __('Subscription Message');
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
								'page_template'	=> 'subscription-temp.php',
								'post_name'		=> 'subscription-confirmation' ));
		}else{
			if($found_post_title->post_status != 'publish'){
				$get_post = get_post( $found_post_title->ID, 'ARRAY_A' );
				$get_post['post_status'] = 'publish';
				wp_update_post($get_post);
			}
			update_post_meta( $found_post_title->ID , '_wp_page_template', 'subscription-temp.php' );
		}
	}
}



//=================================Ajax Delete Function==================================
add_action('wp_ajax_vmt_delete' , 'vmt_delete');
add_action('wp_ajax_nopriv_vmt_delete','vmt_delete');
function vmt_delete(){
	$id = $_GET['vmt_id'];
	if(empty($id)){
		$id = $_POST['vmt_id'];
	}
	if(!empty($id)){
		delete_subscriber($id);
	}
}
//==================================Ajax Edit Function===================================
add_action('wp_ajax_vmt_edit' , 'vmt_edit');
add_action('wp_ajax_nopriv_vmt_edit','vmt_edit');
function vmt_edit(){
	$id = $_GET['vmt_id'];
	if(empty($id)){
		$id = $_POST['vmt_id'];
	}
	$email = $_GET['vmt_email'];
	if(empty($email)){
		$email = $_POST['vmt_email'];
	}
	if(!empty($id) && !empty($email)){
		edit_subscriber($id, $email);
	}
}
//=================================Ajax Change Function==================================
add_action('wp_ajax_vmt_change' , 'vmt_change');
add_action('wp_ajax_nopriv_vmt_change','vmt_change');
function vmt_change(){
	$id = $_GET['vmt_id'];
	if(empty($id)){
		$id = $_POST['vmt_id'];
	}
	$status = $_GET['vmt_status'];
	if(empty($status)){
		$status = $_POST['vmt_status'];
	}
	if(!empty($id) && !empty($status)){
		change_subscriber($id, $status);
	}
}
//===========================Ajax Resend Confirmation Function===========================
add_action('wp_ajax_vmt_resend' , 'vmt_resend');
add_action('wp_ajax_nopriv_vmt_resend','vmt_resend');
function vmt_resend(){
	$email = $_GET['vmt_email'];
	if(empty($email)){
		$email = $_POST['vmt_email'];
	}
	if(!empty($email)){
		$confirmation = custom_confirmation_mail($email);
		echo $confirmation;
	}
}
//===========================Ajax Export Function===========================
add_action('wp_ajax_vmt_export' , 'vmt_export');
add_action('wp_ajax_nopriv_vmt_export','vmt_export');
function vmt_export(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'vmt_subscription';
	$results = $wpdb->get_results( "SELECT * FROM $table_name");
	if($results->num_rows > 0){ 
		$delimiter = ","; 
		$filename = "members-data_" . date('Y-m-d') . ".csv"; 
		
		// Create a file pointer 
		$f = fopen('php://memory', 'w'); 
		
		// Set column headers 
		$fields = array('ID', 'EMAIL', 'STATUS', 'TIME'); 
		fputcsv($f, $fields, $delimiter); 
		
		// Output each row of the data, format line as csv and write to file pointer
		foreach($results as $row){
			$lineData = array($row->id, $row->email_id, $row->status, $row->time); 
			fputcsv($f, $lineData, $delimiter); 
		}

		// Move back to beginning of file 
		fseek($f, 0); 
		
		// Set headers to download file rather than displayed 
		header('Content-Type: text/csv'); 
		header('Content-Disposition: attachment; filename="' . $filename . '";'); 
		
		//output all remaining data on a file pointer 
		fpassthru($f); 
	} 
	exit; 
}


//===========================Database User Delete Function===========================
function delete_subscriber($id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'vmt_subscription';
	$wpdb->query("DELETE FROM $table_name WHERE id='$id'");
}
//===========================Database Email Edit Function============================
function edit_subscriber($id, $email){
	global $wpdb;
	$table_name = $wpdb->prefix . 'vmt_subscription';
	$wpdb->query("UPDATE $table_name SET `email_id`='$email' WHERE id='$id'");
}
//==========================Database Status Change Function==========================
function change_subscriber($id, $status){
	global $wpdb;
	$table_name = $wpdb->prefix . 'vmt_subscription';
	$wpdb->query("UPDATE $table_name SET `status`='$status' WHERE id='$id'");
}





//==========================Main Menu==========================

add_action('admin_menu', 'subscribe_menu');
function subscribe_menu(){
	add_menu_page( "Theme Menu", "Subscribe Us", 'manage_options', 'subscribe_menu', 'subscribe_us_callback','', 6);
}
function subscribe_us_callback(){
	echo '<div class="wrap">';
		echo '<h1>Audience</h1>';
			global $wpdb;
			$table_name = $wpdb->prefix . 'vmt_subscription';

            $results = $wpdb->get_results( "SELECT * FROM $table_name");
            if(!empty($results)) {
				echo '<table class="widefat striped fixed" cellspacing="0">';
					echo '<thead>
						<tr>
							<th id="index" class="manage-column column-index column-posts" scope="col">ID</th>
							<th id="email" class="manage-column column-email" scope="col">Email</th>
							<th id="status" class="manage-column column-status" scope="col">Status</th>
							<th id="date" class="manage-column column-date" scope="col">Date</th>
						</tr>
					</thead>';
                    echo '<tbody>'; 
                        foreach($results as $row){
							$id = $row->id;
							$email = $row->email_id;
							$status = $row->status;
							$time = $row->time;
							$subscribed = ($status == 'subscribed')?'selected':'';
							$unconfirmed = ($status == 'unconfirmed')?'selected':'';
							$unsubscribed = ($status == 'unsubscribed')?'selected':'';
                            echo '<tr id="'.$id.'" class="column-posts" valign="top">
								<th scope="row">'.$id.'</th>
								<td style="text-align: left;">
									<span>'.$email.'</span>
									<input type="email" value="'.$email.'" disabled style="color: black;display: none;width: 100%;">
									<div class="row-actions">
										<span><a href="#edit" class="subscriber-edit" data-id="'.$id.'">Edit</a> |</span>
										<span class="delete"><a href="#delete" class="subscriber-delete" data-id="'.$id.'">Delete</a> |</span>
										<span><a href="#resend-confirmation" class="resend-confirmation">Resend Confirmation</a></span>
									</div>
								</td>
								<td style="text-align: left;">
									<span>'.$status.'</span>
									<select class="subscriber-change" disabled style="color: black;display: none;" data-id="'.$id.'">
										<option value="subscribed" '.$subscribed.'>Subscribed</option>
										<option value="unconfirmed" '.$unconfirmed.'>Unconfirmed</option>
										<option value="unsubscribed" '.$unsubscribed.'>Unsubscribed</option>
									</select>
									<div class="row-actions">
										<span><a href="#change" class="subscriber-change">Change</a></span>
									</div>
								</td>
								<td style="text-align: left;">'.$time.'</td>
							</tr>';
                        }
                    echo '</tbody>';
					echo '<tfoot>
						<tr>
							<th class="manage-column column-index column-posts" scope="col">ID</th>
							<th class="manage-column column-email" scope="col">Email</th>
							<th class="manage-column column-status" scope="col">Status</th>
							<th class="manage-column column-date" scope="col">Date</th>
						</tr>
					</tfoot>';
                echo '</table>';
				echo '<p class="submit"><button id="export" class="button button-primary">Export</button></p>'
				?><script>
					jQuery(document).ready(function($) {
						//=================================================================Delete Ajax=================================================================
						$(".subscriber-delete").click(function(){
							selected = $(this);
							var vmt_id = selected.data('id');
							jQuery.ajax({
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								data: { action: 'vmt_delete', vmt_id: vmt_id },
								beforeSend: function(){
								},
								success: function(data) {
									$('#'+vmt_id).fadeOut();
									setTimeout(function () {
										$('#'+vmt_id).remove();
									}, 400);
								}
							});
						});
						//==================================================================Edit Ajax==================================================================
						$(".subscriber-edit").click(function(){
							selected = $(this);
							if(selected.parent().parent().siblings('input').prop('disabled') == false){
								var vmt_email = selected.parent().parent().siblings('input').val();
								var vmt_id = selected.data('id');
								jQuery.ajax({
									url: '<?php echo admin_url('admin-ajax.php'); ?>',
									data: { action: 'vmt_edit', vmt_id: vmt_id, vmt_email: vmt_email },
									beforeSend: function(){
										selected.parent().parent().siblings('input').attr('disabled', 'disabled');
										selected.fadeOut().fadeIn();
										setTimeout(function () {
											selected.text("Editing...");
										}, 400);
									},
									success: function(data){
										selected.parent().parent().siblings('input').fadeOut();
										setTimeout(function () {
											selected.parent().parent().siblings('span').fadeIn().text(vmt_email);
										}, 400);
										selected.fadeOut().fadeIn();
										setTimeout(function () {
											selected.text("Done").delay(2000).fadeOut().fadeIn();
										}, 400);
										setTimeout(function () {
											selected.text("Edit");
										}, 3200);
									}
								});
							}else{
								selected.fadeOut().fadeIn();
								setTimeout(function () {
									selected.text("Save");
								}, 400);
								selected.parent().parent().siblings('span').fadeOut();
								setTimeout(function () {
									selected.parent().parent().siblings('input').removeAttr('disabled').fadeIn();
								}, 400);
							}
						});
						//=================================================================Change Ajax=================================================================
						$("a.subscriber-change").click(function(){
							if($(this).parent().parent().siblings('select').prop('disabled') == true){
								$(this).parent().parent().siblings('span').fadeOut();
								$(this).parent().parent().siblings('select').removeAttr('disabled').delay(400).fadeIn();
							}
						});
						$('select.subscriber-change').on('change', function() {
							selected = $(this);
							var vmt_status = selected.val();
							var vmt_id = selected.data('id');
							jQuery.ajax({
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								data: { action: 'vmt_change', vmt_id: vmt_id, vmt_status: vmt_status },
								beforeSend: function(){
									setTimeout(function () {
										selected.attr('disabled', 'disabled');
									}, 200);
								},
								success: function(data) {
									selected.fadeOut();
									selected.siblings('span').text(selected.val()).delay(400).fadeIn();
								}
							});
						});
						//=================================================================Resend Ajax=================================================================
						$(".resend-confirmation").click(function(){
							selected = $(this);
							var vmt_email = selected.parent().parent().siblings('input').val();
							jQuery.ajax({
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								data: { action: 'vmt_resend', vmt_email: vmt_email },
								beforeSend: function(){
									selected.fadeOut().fadeIn();
									setTimeout(function () {
										selected.text("Sending...").addClass('resend-confirmation').parent().removeClass('delete');
									}, 400);
								},
								success: function(data) {
									selected.fadeOut().fadeIn();
									setTimeout(function () {
										if(data == 0){
											selected.text("Error(Try Again)").parent().addClass('delete');
										}else{
											selected.text("Confirmation Sent").removeClass('resend-confirmation');
										}
									}, 400);
								}
							});
						});
						//=================================================================Export Ajax=================================================================
						$("#export").click(function(){
							jQuery.ajax({
								url: '<?php echo admin_url('admin-ajax.php'); ?>',
								data: { action: 'vmt_export' },
								beforeSend: function(){
								},
								success: function(data) {
								}
							});
						});
					});
				</script><?php
            }else{
				echo 'No Data Found';
			}
	echo '</div>';
}
//==========================Sub-Menu===========================
add_action('admin_menu', 'subscribe_form');
function subscribe_form(){
	add_submenu_page('subscribe_menu', 'Form', 'Form', 'manage_options', 'subscribe_menu/form', 'subscribe_form_callback');
}
function subscribe_form_callback(){ ?>
	<div class="wrap">
		<h1>Custom Form</h1>
		<form method="post" action="options.php" novalidate="novalidate">
			<?php settings_fields('subscribe_menu/form'); ?>
			<table class="form-table" role="presentation">
				<?php do_settings_sections( 'subscribe_menu/form', 'default' );
				do_settings_fields('subscribe_menu/form', 'default') ?>
			</table>
			<?php submit_button();?>
		</form>
	</div><?php
}
//=========================Sub-Menu-2==========================
add_action('admin_menu', 'subscribe_mail');
function subscribe_mail(){
	add_submenu_page('subscribe_menu', 'Mails', 'Mails', 'manage_options', 'subscribe_menu/mail', 'subscribe_mail_callback');
}
function subscribe_mail_callback(){ ?>
	<div class="wrap">
		<h1>Mail Settings</h1>
		<form method="post" action="options.php" novalidate="novalidate">
			<?php settings_fields('subscribe_menu/mail'); ?>
			<table class="form-table" role="presentation">
				<?php do_settings_sections( 'subscribe_menu/mail', 'default' );
				do_settings_fields('subscribe_menu/mail', 'default') ?>
			</table>
			<?php submit_button();?>
		</form>
	</div><?php
}
//=========================Sub-Menu-3==========================
add_action('admin_menu', 'subscribe_message');
function subscribe_message(){
	add_submenu_page('subscribe_menu', 'Messages', 'Messages', 'manage_options', 'subscribe_menu/message', 'subscribe_message_callback');
}
function subscribe_message_callback(){ ?>
	<div class="wrap">
		<h1>Subscribe Messages</h1>
		<form method="post" action="options.php" novalidate="novalidate">
			<?php settings_fields('subscribe_menu/message'); ?>
			<table class="form-table" role="presentation">
				<?php do_settings_sections( 'subscribe_menu/message', 'default' );
				do_settings_fields('subscribe_menu/message', 'default') ?>
			</table>
			<?php submit_button();?>
		</form>
	</div><?php
}



/*----------------------------------------------------------------------------------Custom Section and Field--------------------------------------------------------*/
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



function subscribe(){
	$site_key = get_option('site_key');
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
		<?php if(!empty($site_key)) { ?>
			<div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>" style="margin-bottom: 1rem;"></div>
		<?php } ?> 
		<div class="vmt-spinner" style="display: none;" data-position="<?php echo $spinner_position; ?>" data-align="<?php echo $spinner_align; ?>" <?php echo (!empty($spinner_gap))?'data-gap="'.$spinner_gap.'"':''; ?>><?php echo htmlspecialchars_decode($spinner_svg); ?></div>
	</form>
	<script>
		jQuery.fn.subscribe_function = function(param) {
			var subscribers_data = {};
			var check_required = 1;
			var response = grecaptcha.getResponse();
			
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
	</script><?php	
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