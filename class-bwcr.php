<?php

class BWCR{
  function __construct(){
    register_activation_hook( __FILE__, 'bwcr_install' );
    $this->include_our_files();
  }



  function include_our_files(){
    // admin options
    include plugin_dir_path( __FILE__ ) . "options.php";
    include plugin_dir_path( __FILE__ ) . "class-BWCR_Create.php";
  }

  function bwcr_enqueue(){
    wp_enqueue_style( 'bwcr-style', plugin_dir_url( __FILE__ ) . 'style.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'style.css'), false );
    wp_enqueue_script( 'bwcr-script', plugin_dir_url( __FILE__ ) . 'script.js', array(), filemtime(plugin_dir_path( __FILE__ ) . 'script.js'), false );
    wp_enqueue_style( 'font-awe', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css', array(),  '5.6.3', false );
  }

  function bwcr_install(){
    // bwcr_create_features_table();
    bwcr_alter_comments_table();
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



  function bwcr_insert_features( $key, $data ){
    global $wpdb;
    $get_from_table = $wpdb->prefix . 'comments';
    $row = $wpdb->query( "SELECT comment_ID
                          FROM $get_from_table
                          WHERE comment_post_ID = $key
                          AND user_id = " );
  }
}

new BWCR();
