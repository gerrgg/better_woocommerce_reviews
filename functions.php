<?php

/*
Plugin Name:  Better Woocommerce Reviews
Description:  Making woocommerce better, one review at a time.
Version:      0.1
Author:       Gregory Bastianelli
Author URI:   drunk.kiwi
License:      GPL

Better Woocommerce Reviews is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Better Woocommerce Reviews is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// runs the show
include plugin_dir_path( __FILE__ ) . "class-BWCR.php";
// admin options
include plugin_dir_path( __FILE__ ) . "options.php";
// create review html

add_action( 'wp_enqueue_scripts', 'bwcr_enqueue' );
function bwcr_enqueue(){
  wp_enqueue_style( 'bwcr-style', plugin_dir_url( __FILE__ ) . 'style.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'style.css'), false );
  wp_enqueue_script( 'bwcr-script', plugin_dir_url( __FILE__ ) . 'script.js', array(), filemtime(plugin_dir_path( __FILE__ ) . 'script.js'), false );
  wp_enqueue_style( 'font-awe', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css', array(),  '5.6.3', false );
}

add_shortcode( 'createReview', 'bwcr_create_review_form' );

function bwcr_create_review_form(){
  if( isset( $_GET['p_ids'] ) ){
    $form = new BWCR_Create( explode( ',', $_GET['p_ids'] ) );
    $form->get_form();
  } else {
    echo 'No ID in url';
  }

}
