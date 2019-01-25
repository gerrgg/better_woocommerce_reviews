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

// alter comments table
register_activation_hook( __FILE__, 'bwcr_install' );

//enqueue scripts
add_action( 'wp_enqueue_scripts', 'bwcr_enqueue' );

// setup admin post to process reviews
add_action( 'admin_post_process_review', 'bwcr_process_review' );
add_action( 'admin_post_nopriv_process_review', 'bwcr_process_review' );

// show the form on a specific page
add_shortcode( 'createReview', 'bwcr_create_review_form' );


function bwcr_enqueue(){
  /*
  Enqueue our scripts
  */
  wp_enqueue_style( 'bwcr-style', plugin_dir_url( __FILE__ ) . 'style.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'style.css'), false );
  wp_enqueue_script( 'bwcr-script', plugin_dir_url( __FILE__ ) . 'script.js', array(), filemtime(plugin_dir_path( __FILE__ ) . 'script.js'), false );
  wp_enqueue_style( 'font-awe', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css', array(),  '5.6.3', false );
}

function bwcr_install(){
  bwcr_alter_comments_table();
}

function bwcr_alter_comments_table(){
  global $wpdb;
  $columns_to_add = array( 'headline', 'fit', 'comfort', 'durability', 'value' );
  $table_to_alter = $wpdb->prefix . 'comments';
  // pre_dump( $wpdb );
  foreach( $columns_to_add as $column ){
    $row = $wpdb->get_results( "SELECT COLUMN_NAME
                                FROM INFORMATION_SCHEMA.COLUMNS
                                WHERE TABLE_SCHEMA LIKE '$wpdb->dbname'
                                AND TABLE_NAME = '$table_to_alter'
                                AND COLUMN_NAME = 'comment_$column'");

    if( empty( $row ) ) $wpdb->query( "ALTER TABLE $table_to_alter
                                       ADD comment_$column TEXT NOT NULL" );
  }
}

function bwcr_process_review(){
  /*
  Function runs once the user submits a review at example.com/reviews?p_ids=1,2,3&action=create
  */
  $items_reviewed = array();
  foreach( $_POST as $key => $data ){
    if( $key != "username" && $key != "user_id" && $key != 'action' ){

      // store items just reviewed for redirect
      array_push( $items_reviewed, $key );
      bwcr_insert_comment( $key, $data );
      // bwcr_process_the_ratings( $key, $data );
    }
  }
  // wp_redirect( '/review?p_ids=' . impode( ',', $items_reviewed ) . '&action=thankyou' );
}
//https://stackoverflow.com/questions/52122275/add-a-product-review-with-ratings-programmatically-in-woocommerce
function bwcr_insert_comment( $key, $data ){
  global $wpdb;
  $user = wp_get_current_user();
  pre_dump( $data );
  $comment_id = wp_insert_comment(
      array(
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
      )
    );

    echo $comment_id;
    update_comment_meta( $comment_id, 'rating', $data['product'] );
    update_comment_meta( $comment_id, 'bwcr_fit', $data['product'] );
    update_comment_meta( $comment_id, 'bwcr_durability', $data['product'] );
    update_comment_meta( $comment_id, 'bwcr_comfort', $data['product'] );
}

function bwcr_create_review_form(){
  if( isset( $_GET['p_ids'], $_GET['action'] ) ){
    if( $_GET['action'] == 'create' ){
      $form = new BWCR_Create( explode( ',', $_GET['p_ids'] ) );
      $form->get_form();
    }
    if( $_GET['action'] == 'thankyou' ){

    }
  }
}



//https://stackoverflow.com/questions/21330932/add-new-column-to-wordpress-database
// function bwcr_create_features_post_type(){
//   register_post_type( 'bwcr_features',
//     array(
//       'labels' => array(
//         'name' => __( 'Features' ),
//         'singular_name' => __( 'Product' )
//       ),
//       'public' => true,
//       'exclude_from_search' => true,
//       'has_archive' => true,
//       'publicly_queryable' => true,
//     )
//   );
// }
// add_action( 'init', 'bwcr_create_features_post_type' );

// function bwcr_create_features_table(){
//   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//   // connection details
//   global $wpdb;
//   $charset_collate = $wpdb->get_charset_collate();
//
//   // Create the features table
//   $table_name = $wpdb->prefix . 'bwcr_features';
//   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
//           id mediumint(9) NOT NULL AUTO_INCREMENT,
//           connection_id mediumint(9) NOT NULL,
//           question text NOT NULL,
//           awnsers text NOT NULL,
//           type text NOT NULL,
//           created timestamp DEFAULT CURRENT_TIMESTAMP NULL,
//           PRIMARY KEY  (id)
//   ) $charset_collate;";
//   dbDelta( $sql );
//
//   // Create the features_meta table
//   $table_name2 = $wpdb->prefix . 'bwcr_features_meta';
//   $sql2 = "CREATE TABLE IF NOT EXISTS $table_name2 (
//            id mediumint(9) NOT NULL AUTO_INCREMENT,
//            comment_id mediumint(9) NOT NULL,
//            q_id mediumint(9) NOT NULL,
//            meta_value text NOT NULL,
//            PRIMARY KEY  (id)
//   ) $charset_collate;";
//   dbDelta( $sql2 );
// }
