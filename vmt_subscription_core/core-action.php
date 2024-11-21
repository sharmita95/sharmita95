<?php
//=======================================================Database User Delete Function
function delete_subscriber($id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'vmt_subscription';
	$wpdb->query("DELETE FROM $table_name WHERE id='$id'");
}
//=======================================================Database Email Edit Function
function edit_subscriber($id, $email){
	global $wpdb;
	$table_name = $wpdb->prefix . 'vmt_subscription';
	$wpdb->query("UPDATE $table_name SET `email_id`='$email' WHERE id='$id'");
}
//=======================================================Database Status Change Function
function change_subscriber($id, $status){
	global $wpdb;
	$table_name = $wpdb->prefix . 'vmt_subscription';
	$wpdb->query("UPDATE $table_name SET `status`='$status' WHERE id='$id'");
}