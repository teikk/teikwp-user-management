<?php 

add_action( 'plugins_loaded',array('FWPUM_Login','init') );
class FWPUM_Login{
	protected static $instance;

	public $errors = '';
	/**
	 * Pobierz instancję obiektu
	 * Singleton method
	 * @return object Instancja obiektu
	 */
	public static function get_instance() {
		// create an object
		NULL === self::$instance and self::$instance = new self;

		return self::$instance; // return the object
	}
	public function __construct(){
		$this->errors = new WP_Error;
	}
	public static function init(){
		$self = self::get_instance();
		add_action( 'fwpum/login/errors', array( $self, 'debug') );
		add_action( 'fwpum/password-change/errors', array( $self, 'debug') );
		add_action( 'fwpum/password-reset/errors', array( $self, 'debug') );

		add_action( 'wp', array( $self,'loginUser') );
		add_action( 'login_form_lostpassword', array( $self,'passwordReset') );
		add_action( 'wp', array( $self,'passwordChange') );
		add_action( 'wp', array( $self,'passwordChangeAfterReset') );

		add_action( 'login_form', array( $self, 'doPasswordReset' ),1 );
		add_action( 'login_form_resetpass', array( $self, 'doPasswordReset' ),1 );
	}

	public function validate($data){
		$this->errors = new WP_Error;
		if( !isset($data['user_name']) ) {
			$this->errors->add( 'username_empty',__("Username cannot be empty") );
		} else {
			if( username_exists( $data['user_name'] ) ) {
				$user = get_user_by( 'login', $data['user_name'] );
				if( isset($data['password']) ) {
					$login = wp_check_password( $data['password'], $user->data->user_pass, $user->ID );
					if( !$login ) {
						$this->errors->add( 'error',__("Something went wrong") );
					}
				} else {
					$this->errors->add( 'password_empty',__("Password cannot be empty") );					
				}
			} else {
				$this->errors->add( 'user_no_exists',__("That user doesn't exist") );
			}
		}		

		do_action( 'fwpum/login/validation', $data );
		if( $this->errors->get_error_codes() ) {
			return false;
		} else {
			return true;
		}
	}

	private function createLoginData($data){
		return array(
			'user_login'    => $data['user_name'],
			'user_password' => $data['password'],
			'remember'      => ( isset($data['remember']) ) ? true : false
			);
	}

	public function loginUser(){
		if( !isset( $_POST['_fwpum_login'] ) ) {
			return;
		}
		if( !wp_verify_nonce( $_POST['_fwpum_login'],'login' ) ) {
			return;
		}

		if( $this->validate($_POST) ) {
			$user = wp_signon( $this->createLoginData($_POST) );
		} else {
			$user = new WP_Error;
		}
		if( !is_wp_error( $user ) ) {
			wp_redirect( apply_filters( 'fwpum/login/redirect', home_url() ), 302 );
		}
	}

	public function passwordReset(){
		if( !isset( $_POST['_fwpum_reset-password'] ) ) {
			return;
		}
		if( !wp_verify_nonce( $_POST['_fwpum_reset-password'], 'reset-password' ) ) {
			return;
		}
		$this->errors = new WP_Error;
		if( !isset( $_POST['user_login'] ) ) return;
		if( empty( $_POST['user_login'] ) ) return;


		$user = trim( $_POST['user_login'] );
		if( username_exists( $user ) ) {
			$userData = get_user_by( 'login', $user );
		} else {
			if( email_exists( $user ) ) {
				$userData = get_user_by( 'email', $user );
			} else {				
				$this->errors->add('invalid_user',__("Invalid user credentials"));
			}
		}

		if( !$this->errors->get_error_codes() ) {
			$resetPassword = retrieve_password();
			$redirect = apply_filters( 'fwpum/password-reset/redirect', home_url() );
			$redirect = add_query_arg('checkmail','1',$redirect);
			if( !is_wp_error( $resetPassword ) ) {
				wp_redirect( $redirect, 302 );
				exit();
			}
		}
	}

	public function doPasswordReset(){
		if( !isset($_GET['action']) ){
			return;
		}
		if( $_GET['action'] != 'rp' && $_GET['action'] != 'resetpass'){
			return;
		}
		$user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
		if( !is_wp_error( $user ) ){
			$redirect = apply_filters( 'fwpum/password-reset/redirect', home_url() );
			$redirect = add_query_arg('key',$_REQUEST['key'],$redirect);
			$redirect = add_query_arg('login',$_REQUEST['login'],$redirect);
		} else {
			if( $user->get_error_code() === 'expired_key' ){
				$redirect = apply_filters( 'fwpum/password-reset/redirect/fail', home_url() );
				$redirect = add_query_arg('error',$user->get_error_code(),$redirect);
			} else {
				$redirect = apply_filters( 'fwpum/password-reset/redirect/fail', home_url() );
				$redirect = add_query_arg('error',$user->get_error_code(),$redirect);
			}
		}
		wp_redirect( $redirect, 302 );
		exit();
	}

	public function passwordChangeAfterReset(){
		if( !isset( $_POST['_fwpum_reset-password-final'] ) ) {
			return;
		}
		if( !wp_verify_nonce( $_POST['_fwpum_reset-password-final'], 'reset-password' ) ) {
			return;
		}
		$this->errors = new WP_Error;

		if( empty( $_POST['password'] ) ) {
			$this->errors->add( 'pass_empty',__("Password cannot be empty") );
		} else {
			if( !empty( $_POST['password'] ) || !empty( $_POST['confirm_password'] ) ) {
				if( $_POST['password'] !== $_POST['confirm_password'] ) {
					$this->errors->add( 'pass_no_match',__("Passwords don't match") );
				}
			} else {
				$this->errors->add( 'pass_empty',__("Password cannot be empty") );
			}		
		}
		if( username_exists( $_POST['login'] ) ) {
			$user = get_user_by( 'login', $_POST['login'] );
		} elseif( email_exists( $_POST['login'] ) ) {
			$user = get_user_by( 'email', $_POST['login'] );
		} else {
			$this->errors->add( 'user_no_exists',__("This user doesn't exist") );
		}

		
		if( !$this->errors->get_error_codes() ) {
			$updated = wp_update_user( array(
				'ID' => $user->ID,
				'user_pass' => $_POST['password']
				) );
			if( !is_wp_error( $updated ) ) {
				$redirect = apply_filters( 'fwpum/password-change/redirect', home_url() );
				$redirect = add_query_arg('passchanged','1',$redirect);
				wp_redirect( $redirect, 302 );
				exit();
			}
		}
	}

	public function passwordChange(){
		if( !isset( $_POST['_fwpum_change-password'] ) ) {
			return;
		}
		if( wp_verify_nonce( '_fwpum_change-password', $_POST['_fwpum_change-password'] ) ) {
			return;
		}		
		$this->errors = new WP_Error;
		$user = wp_get_current_user();
		if( empty( $_POST['old_password'] ) ) {
			$this->errors->add( 'pass_empty',__("Password cannot be empty") );
		} else {
			$oldPass = wp_check_password( $_POST['old_password'], $user->data->user_pass, $user->ID );
			var_dump($oldPass);
			if( $oldPass ) {
				if( !empty( $_POST['new_password'] ) || !empty( $_POST['confirm_password'] ) ) {
					if( $_POST['new_password'] !== $_POST['confirm_password'] ) {
						$this->errors->add( 'pass_no_match',__("Passwords don't match") );
					}
				} else {
					$this->errors->add( 'pass_empty',__("Password cannot be empty") );
				}
			} else {
				$this->errors->add( 'pass_empty',__("Something went wrong.") );
			}			
		}
		if( !$this->errors->get_error_codes() ) {
			$updated = wp_update_user( array(
				'ID' => $user->ID,
				'user_pass' => $_POST['new_password']
				) );
			if( !is_wp_error( $updated ) ) {
				$redirect = apply_filters( 'fwpum/password-change/redirect', home_url() );
				$redirect = add_query_arg('passchanged','1',$redirect);
				wp_logout();
				wp_redirect( $redirect, 302 );
				exit();
			}
		}
	}

	public function debug(){
		if( !empty( $this->errors ) || isset( $_GET['checkmail'] ) ) {
			set_query_var( 'fwpum_errors', $this->errors );
			fwpum_template('errors');
		}
	}
}