<?php 

add_action( 'plugins_loaded',array('FWPUM_Register','init') );
class FWPUM_Register{
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
	public function __construct(){}
	public static function init(){
		$self = self::get_instance();
		add_action( 'fwpum/register/errors', array( $self, 'debug') );

		add_action( 'wp', array( $self,'registerUser') );
	}

	public function validate($data){
		$this->errors = new WP_Error;

		if( !isset($data['user_name']) ) {
			$this->errors->add('username_empty',__("Username cannot be empty"));
		} else {
			if( username_exists( $data['user_name'] ) ) {
				$this->errors->add('user_exists',__("Username already exists"));
			}
		}
		if( !isset($data['password']) ) {
			$this->errors->add('password_empty',__("Password cannot be empty"));
		} else {
			if( $data['password'] !== $data['confirm_password'] ) {
				$this->errors->add('pw_no_match',__("Passwords don't match"));
			}
		}		
		if( !isset($data['email']) ) {
			$this->errors->add('email_empty',__("Email cannot be empty"));
		} else {
			if( email_exists( $data['email'] ) ){
				$this->errors->add('email_exists',__("E-mail address already exists"));
			}
		}
		
		// if( !isset($data['accept']) ) {
		// 	$this->errors->add('accept',__("You have to accept terms of service"));
		// }
		
		do_action( 'fwpum/register/validation', $data );

		if( $this->errors->get_error_codes() ) {
			return false;
		} else {
			return true;
		}
	}

	public function createUserData($data){
		$userArgs = array(
			'user_login'  =>  $data['user_name'],
			'user_pass'  =>  $data['password'],
			'user_email' => $data['email']
			);
		return $userArgs;
	}

	public function registerUser(){
		if( !isset( $_POST['_fwpum_register'] ) ) {
			return;
		}
		if( !wp_verify_nonce( $_POST['_fwpum_register'], 'register' ) ) {
			return;
		}

		if( $this->validate($_POST) ) {
			$user = wp_insert_user( $this->createUserData($_POST) );
		} else {
			$user = new WP_Error;
		}
		if( !is_wp_error( $user ) ) {			
			wp_redirect( apply_filters( 'fwpum/register/redirect', home_url() ), 302 );
		}

	}
	public function debug(){
		if( !empty( $this->errors ) ) {
			set_query_var( 'fwpum_errors', $this->errors );
			fwpum_template('errors');
		}
	}
}