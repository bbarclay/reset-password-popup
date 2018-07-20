<?php
/*
Plugin Name: Business Blueprint ResetPass Popup
Plugin URI: http://www.julycabigas.com
Description:  A plugin that allows user to reset their passwords
Version: 1.6.2
Author: BusinessBlueprint

*/


if ( ! defined('ABSPATH') ) {
        die('Please do not load this file directly.');
}

define('RPP_VERSION','1.0.0');
define('RPP_PATH', plugin_dir_path(__FILE__ ) );


/**
* Action and Filters
*/
add_action('wp_enqueue_scripts', 'rpp_enqueue_scripts');
add_action('wp_ajax_nopriv_rpp_update_display', 'rpp_update_display');
add_action('wp_ajax_rpp_update_display', 'rpp_update_display');
add_shortcode('rpp_popup', 'rpp_popup_shortcode');



function rpp_enqueue_scripts() {

	 wp_enqueue_script( 'reset-password-popup', plugin_dir_url( __FILE__ ) . 'assets/js/reset-password-popup.js', array(), false, true );

    wp_localize_script( 'reset-password-popup', 'rpp_ajax', array( 
    	'ajaxurl' => admin_url( 'admin-ajax.php' ), 
    	'security' => wp_create_nonce('rpp_security'),
    ) );
}


function rpp_update_display() {

	$security = $_POST['security'];
	$is_hide = sanitize_text_field( $_POST['is_hide'] );

	if( ! wp_verify_nonce( $security, 'rpp_security')   ) {
		die('issue on verifying nonce');
	}	

	if($is_hide) {

		$id = rpp_get_userId();

		if( $id && current_user_can('subscriber') || current_user_can('administrator') || current_user_can('editor') || current_user_can('author') ) {
			update_user_meta( $id, 'hide_reset_password_popup', true );

			wp_send_json_success(array(
				'status' => 'saved',
				'id' => $id
			));
		}
		
		exit;
	}

}

function rpp_popup_shortcode() {

	$is_visible = rpp_is_visible();

    if( $is_visible == true ) {

	   ob_start();

	   require_once RPP_PATH . 'templates/template-popup.php';

      $output_string = ob_get_contents();
      if( ob_get_contents() ) ob_end_clean();
      return $output_string;
	
    }
   	
}

/**
* Helpers
*/

function rpp_get_userId() {
	$user = get_current_user_id();

	return $user;
}

function rpp_is_visible() {


	$id = rpp_get_userId();

	if(!$id) {
		return false;
	}

	$hide_pass 		 = get_user_meta( $id, 'hide_reset_password_popup', true );
	$user  		     = get_userdata($id);
	$last_date 	 	 = strtotime( '-7 day', current_time('timestamp') );
	$registered_date = strtotime( $user->user_registered );

	if( $registered_date < $last_date || $hide_pass ) {
		return false;
	}
	
	return true;


}