<?php

class BWCR{
  function __construct(){
    include plugin_dir_path( __FILE__ ) . "class-BWCR_Create.php";
  }
}

new BWCR();
