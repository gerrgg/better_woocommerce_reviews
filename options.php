<?php

/*
Adds the options page to the backend.
*/

add_action( 'admin_menu', 'bwcr_menu' );
if( ! class_exists('WP_List_Table')){
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
include plugin_dir_path( __FILE__ ) . "class-Link-List-Table.php";

function bwcr_menu(){
  add_comments_page( 'Better Woocommerce Reviews Options', 'Better Woocommerce Reviews', 'manage_options', 'bwcr-menu', 'bwcr_options' );
}

function bwcr_options(){
  if( !current_user_can( 'manage_options' ) ){
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  ?>
  <div class="wrap">
    <h1>Better Woocommerce Reviews Options</h1>
    <?php
    //Our class extends the WP_List_Table class, so we need to make sure that it's there
    //Prepare Table of elements
    $wp_list_table = new Links_List_Table();
    $wp_list_table->prepare_items();
    $wp_list_table->display();
    ?>
  </div>
  <?php
}
