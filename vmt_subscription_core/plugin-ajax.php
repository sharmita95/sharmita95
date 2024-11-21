<?php
//=================================================================Delete Function
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
//=================================================================Edit Function
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
//=================================================================Change Function
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
//=================================================================Resend Confirmation Function
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
//=================================================================Export Function
add_action('wp_ajax_vmt_export' , 'vmt_export');
add_action('wp_ajax_nopriv_vmt_export','vmt_export');
function vmt_export(){

}
//=================================================================Import Data From Old Table Function
add_action('wp_ajax_vmt_import_old' , 'vmt_import_old');
add_action('wp_ajax_nopriv_vmt_import_old','vmt_import_old');
function vmt_import_old(){
	vmt_add_prev_data();
}