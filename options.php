<?php

/*
Adds the options page to the backend.
*/

add_action( 'admin_menu', 'bwcr_menu' );

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
    
    ?>
  </div>
  <?php
}
