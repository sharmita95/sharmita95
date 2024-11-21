<?php
//================================================================= Include Custom Fields
include_once CCFV_DIR.'/vmt_subscription_core/custom-field.php';
//================================================================= Include Ajax
include_once CCFV_DIR.'/vmt_subscription_core/plugin-ajax.php';
//================================================================= Include Audiance Action Scripts
include_once CCFV_DIR.'/vmt_subscription_core/custom-script.php';



//==================================================================================================================Main Menu
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

			$old_table_name = $wpdb->prefix . 'ig_contacts';
			$old_table_status = $wpdb->prefix . 'ig_lists_contacts';
			$check_old_table_name = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $old_table_name ) );
			$check_old_table_status = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $old_table_status ) );
			if (($wpdb->get_var( $check_old_table_name ) == $old_table_name) && ($wpdb->get_var( $check_old_table_status ) == $old_table_status)) {
				echo '<p class="submit"><button id="import_old" class="button button-primary">Import Old Data</button></p>';
			}

            if(!empty($results)) {
				echo '<table class="widefat striped fixed" cellspacing="0">';
					echo '<div class="alignleft actions bulkactions" style="padding-bottom: 10px;">
							<select>
								<option selected>Bulk actions</option>
								<option value="delete">Delete</option>
								<option value="change">Change Status</option>
							</select>
							<button class="button action">Apply</button>
						</div>';
					echo '<p class="search-box" style="padding-bottom: 10px;">
						<input type="search" class="search-user" value="">
						<button class="button search-user">Search Posts</button>
					</p>';
					echo '<thead>
						<tr>
							<th id="check-box" class="manage-column column-check-box column-posts" scope="col">Check</th>
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
								<th scope="row"><input type="checkbox" value="'.$id.'"></th>
								<td style="text-align: left;">'.$id.'</td>
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
							<th id="check-box" class="manage-column column-check-box column-posts" scope="col">Check</th>
							<th id="index" class="manage-column column-index column-posts" scope="col">ID</th>
							<th class="manage-column column-email" scope="col">Email</th>
							<th class="manage-column column-status" scope="col">Status</th>
							<th class="manage-column column-date" scope="col">Date</th>
						</tr>
					</tfoot>';
                echo '</table>';
				// echo '<p class="submit"><button id="export" class="button button-primary">Export</button></p>';
				custom_script_menu_action();
            }else{
				echo 'No Data Found';
			}
	echo '</div>';
}
//==================================================================================================================Sub-Menu
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
//==================================================================================================================Sub-Menu-2
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
//==================================================================================================================Sub-Menu-3
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