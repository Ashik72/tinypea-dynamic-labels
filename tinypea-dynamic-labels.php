<?php
/*
Plugin Name: Tinypea Dynamic Labels
Plugin URI: http://uk.fiverr.com/wp_expert_
Description: Tinypea Dynamic Labels Plugin
Version: 0.0.1
Author: Ashik72
Author URI: https://www.upwork.com/freelancers/~01353e37a21e977904
License: GPLv2 or later
Text Domain: tinypea_dynamic_labels
*/

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

if (file_exists(__DIR__ . '/vendor/autoload.php'))
  require __DIR__ . '/vendor/autoload.php';

  if (!function_exists('d')) {

  	function d($data) {

  		ob_start();
  		var_dump($data);
  		$output = ob_get_clean();
  		echo $output;
  	}
  }


	define( 'tinypea_dynamic_labels_PLUGIN_DIR', dirname( __FILE__ ).DIRECTORY_SEPARATOR );
  define( 'tinypea_dynamic_labels_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

  if ( ! defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

  if (file_exists( plugin_dir_path( __FILE__ ) . 'plugin_loader.php' ))
    require_once( plugin_dir_path( __FILE__ ) . 'plugin_loader.php' );
