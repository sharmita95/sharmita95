<?php
//==========================================================================================Check And Create Table
if(!function_exists('vmt_create_table')) {
	function vmt_create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'vmt_subscription';

		$check_new_table = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
		if ( ! $wpdb->get_var( $check_new_table ) == $table_name ) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,		
				email_id text(255) NOT NULL,
				status text(20) NOT NULL,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			//Add Data From Previous Table If Exists
			vmt_add_prev_data();
		}
	}
}
//==========================================================================================Check And Import Data From old Table to New Table
if(!function_exists('vmt_add_prev_data')) {
	function vmt_add_prev_data() {
		global $wpdb;
		$old_table_name = $wpdb->prefix . 'ig_contacts';
		$old_table_status = $wpdb->prefix . 'ig_lists_contacts';
		$check_old_table_name = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $old_table_name ) );
		$check_old_table_status = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $old_table_status ) );
		if (($wpdb->get_var( $check_old_table_name ) == $old_table_name) && ($wpdb->get_var( $check_old_table_status ) == $old_table_status)) {
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
}