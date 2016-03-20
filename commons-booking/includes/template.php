<?php

/**
 * Load template files of the plugin also include a filter pn_get_template_part<br>
 * Based on WooCommerce function<br>
 *
 * @package   Commons_Booking
 * @author  Florian Egermann
 * @license   GPL-2.0+
 * @copyright 2016
 * @since    0.6
 */

function cb_get_template_part( $template = '', $attributes = null, $buffer = FALSE ) {
	$path = plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . 'templates/';
	$plugin_slug = 'commons-booking';
	
	// Look in yourtheme/slug-name.php and yourtheme/plugin-name/slug-name.php
  if ( ! $attributes ) {
    $attributes = array();
  }

  $attributes['plugin_slug'] = $plugin_slug; // add plugin-slug to the attributes

  if ( $buffer ) { 
  	ob_start(); 
  	include( $path . $template . '.php');
  	$content = ob_get_contents();
		ob_end_clean();
		return  $content;
	} else {
	  include ( $path . $template . '.php' );
	}

}
