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
include plugin_dir_path( __FILE__ ) . "class-bwcr.php";
// admin options
include plugin_dir_path( __FILE__ ) . "options.php";
// create review html
include plugin_dir_path( __FILE__ ) . "create-review.php";
