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

register_activation_hook( __FILE__, 'bwcr_install' );
// runs the show
include plugin_dir_path( __FILE__ ) . "class-BWCR.php";
// admin options
include plugin_dir_path( __FILE__ ) . "options.php";
// create review html

add_action( 'wp_enqueue_scripts', 'bwcr_enqueue' );
add_action( 'admin_post_process_review', 'bwcr_process_review' );
add_action( 'admin_post_nopriv_process_review', 'bwcr_process_review' );

//https://stackoverflow.com/questions/21330932/add-new-column-to-wordpress-database
function bwcr_install(){
  bwcr_create_features_table();
  bwcr_alter_comments_table();
}

function bwcr_create_features_post_type(){
  register_post_type( 'bwcr_features',
    array(
      'labels' => array(
        'name' => __( 'Features' ),
        'singular_name' => __( 'Product' )
      ),
      'public' => true,
      'exclude_from_search' => true,
      'has_archive' => true,
      'publicly_queryable' => true,
    )
  );
}
add_action( 'init', 'bwcr_create_features_post_type' );

function bwcr_enqueue(){
  wp_enqueue_style( 'bwcr-style', plugin_dir_url( __FILE__ ) . 'style.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'style.css'), false );
  wp_enqueue_script( 'bwcr-script', plugin_dir_url( __FILE__ ) . 'script.js', array(), filemtime(plugin_dir_path( __FILE__ ) . 'script.js'), false );
  wp_enqueue_style( 'font-awe', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css', array(),  '5.6.3', false );
}
add_shortcode( 'createReview', 'bwcr_create_review_form' );

function bwcr_create_features_table(){
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  // connection details
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  // Create the features table
  $table_name = $wpdb->prefix . 'bwcr_features';
  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          connection_id mediumint(9) NOT NULL,
          question text NOT NULL,
          awnsers text NOT NULL,
          type text NOT NULL,
          created timestamp DEFAULT CURRENT_TIMESTAMP NULL,
          PRIMARY KEY  (id)
  ) $charset_collate;";
  dbDelta( $sql );

  // Create the features_meta table
  $table_name2 = $wpdb->prefix . 'bwcr_features_meta';
  $sql2 = "CREATE TABLE IF NOT EXISTS $table_name2 (
           id mediumint(9) NOT NULL AUTO_INCREMENT,
           comment_id mediumint(9) NOT NULL,
           q_id mediumint(9) NOT NULL,
           meta_value text NOT NULL,
           PRIMARY KEY  (id)
  ) $charset_collate;";
  dbDelta( $sql2 );

}

function bwcr_alter_comments_table(){
  global $wpdb;
  $table_to_alter = $wpdb->prefix . 'comments';
  $row = $wpdb->get_results( "SELECT COLUMN_NAME
                              FROM INFORMATION_SCHEMA.COLUMNS
                              WHERE table_name = $table_to_alter
                              AND column_name = comment_headline");

  if( empty( $row ) ) $wpdb->query( "ALTER TABLE $table_to_alter
                                     ADD comment_headline TEXT NOT NULL" );
}

function bwcr_process_review(){
  $items_reviewed = array();
  foreach( $_POST as $key => $data ){
    if( $key != "username" && $key != "user_id" && $key != 'action' ){

      // store items just reviewed for redirect
      array_push( $items_reviewed, $key );

      bwcr_insert_comment( $key, $data );
      //bwcr_insert_features( $key, $data );
    }
  }
  // wp_redirect( '/review?p_ids=' . impode( ',', $items_reviewed ) . '&action=thankyou' );

}

function bwcr_insert_features( $key, $data ){
  global $wpdb;
  $get_from_table = $wpdb->prefix . 'comments';
  $row = $wpdb->query( "SELECT comment_ID
                        FROM $get_from_table
                        WHERE comment_post_ID = $key
                        AND user_id = " );
}

function bwcr_insert_comment( $key, $data ){
  global $wpdb;
  $user = wp_get_current_user();

  pre_dump( $data );

  $args = array(
    'comment_post_ID' => $key,
    'comment_author' => ( ! empty( $user ) ) ? $user->display_name : 'Anonymous',
    'comment_author_email' => ( ! empty( $user ) ) ? $user->user_email : '',
    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
    'comment_date' => current_time( 'mysql', $gmt = 0 ),
    'comment_date_gmt' => current_time( 'mysql', $gmt = 1 ),
    'comment_headline' => $data['headline'],
    'comment_content' =>  $data['review'],
    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
    'comment_approved' => 0,
    'user_id' => ( ! empty( $user ) ) ? get_current_user_id() : 0,
  );

    $wpdb->insert(
      $wpdb->prefix . 'comments',
      $args
    );

}


function bwcr_create_review_form(){
  if( isset( $_GET['p_ids'], $_GET['action']) && $_GET['action'] == 'create' ){
    $form = new BWCR_Create( explode( ',', $_GET['p_ids'] ) );
    $form->get_form();
  } else {
    echo 'No ID in url';
  }

}
