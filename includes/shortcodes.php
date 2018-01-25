<?php 

add_shortcode( 'fwpum_register', 'fwpum_register' );
function fwpum_register(){
	if( is_user_logged_in() ) {
		return;
	}
	ob_start();
	fwpum_template('register/form');
	return ob_get_clean();
}

add_shortcode( 'fwpum_login', 'fwpum_login' );
function fwpum_login(){
	if( is_user_logged_in() ) {
		return;
	}
	ob_start();
	fwpum_template('login/form');
	return ob_get_clean();
}

add_shortcode( 'fwpum_change_password', 'fwpum_change_password' );
function fwpum_change_password(){
	if( !is_user_logged_in() ) {
		return;
	}
	ob_start();
	fwpum_template('password-change/form');
	return ob_get_clean();
}

add_shortcode( 'fwpum_new_password', 'fwpum_new_password' );
function fwpum_new_password(){
	if( is_user_logged_in() ) {
		return;
	}
	ob_start();
	fwpum_template('new-password/form');
	return ob_get_clean();
}