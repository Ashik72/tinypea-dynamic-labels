<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));


  require_once( plugin_dir_path( __FILE__ ) . '/inc/class.tinypea_dynamic_labels.php' );
  require_once( plugin_dir_path( __FILE__ ) . '/inc/class.admin_options.php' );

  require_once( 'titan-framework-checker.php' );
  require_once( 'titan-framework-options.php' );

  require_once( plugin_dir_path( __FILE__ ) . '/pdf/create.php' );

  add_action( 'plugins_loaded', function () {

		TinypeaDynamicLabels::get_instance();

  } );

 ?>
