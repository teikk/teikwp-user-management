<?php
/*
Plugin Name: FWP User Management
Plugin URI: 
Description: 
Version: 0.1
Author: #
Author URI: 
Copyright: 2017
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function fwpum_template($slug,$name=''){
	if(!empty($name)){
		$name = '-'.$name;
	}
	$template = $slug.$name;
	if ( $overridden_template = locate_template( 'fwpum/'.$template.'.php',false,false ) ) {
		$load = $overridden_template;
	} else {
		$load = FWPUM_DIR . 'templates/'.$template.'.php';
	}
	do_action( 'fwpum_load_template_'.$template );
	load_template( $load, false );
}

function fwpum_parse($string){
	$data = array();
	parse_str($string,$data);
	return $data;
}

/**
 * Stałe do głównego katalogu wtyczki
 */
define( 'FWPUM_DIR' , plugin_dir_path(__FILE__) );
define( 'FWPUM_URI', plugin_dir_url( __FILE__ ) );


function fwpum_file( $file ) {
	require_once( FWPUM_DIR . '/'. $file );
}

register_activation_hook( __FILE__, array('FWPUM','onActivate') );
register_deactivation_hook( __FILE__, array('FWPUM','onDeactivate') );

add_action( 'plugins_loaded', array('FWPUM','init') );
class FWPUM {
	protected static $instance;

	private function __construct() {}

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
	public static function init(){
		$instance = self::get_instance();
		add_action( 'wp_enqueue_scripts', array($instance,'assets') );
	}
	public static function onActivate(){
	}
	public static function onDeactivate(){
	}

	public function assets(){
		wp_enqueue_script( 'fwpum-parsley', FWPUM_URI . '/assets/parsley.min.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'fwpum-app', FWPUM_URI . '/assets/app.js', array( 'jquery' ), false, true );
		wp_localize_script( 'fwpum-app', 'fwpa', array(
			'ajax' => admin_url('admin-ajax.php')
			) );

		wp_enqueue_style( 'fwpum-main', FWPUM_URI . '/assets/main.css' );
	}
}


require_once( FWPUM_DIR . '/load.php' );