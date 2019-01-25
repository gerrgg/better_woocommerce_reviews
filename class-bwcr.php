<?php

class BWCR{
  function __construct(){
    $this->include_our_files();
  }

  function include_our_files(){
    // admin options
    include plugin_dir_path( __FILE__ ) . "options.php";
    include plugin_dir_path( __FILE__ ) . "class-BWCR_Create.php";
  }


}

new BWCR();
