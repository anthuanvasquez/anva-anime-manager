<?php
/*
Plugin Name: Anva Anime Manager
Description: Manager the anime and episodes content.
Version: 1.0.0
Author: Anthuan Vásquez
Author URI: https://anthuanvasquez.net
License: GPL2
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Plugin Constans
define ( 'ANVA_ANIME_MANAGER_PLUGIN_VERSION', '1.0.0' );
define ( 'ANVA_ANIME_MANAGER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define ( 'ANVA_ANIME_MANAGER_PLUGIN_URI', plugin_dir_url( __FILE__ ) );

// Load dependencies
require_once( ANVA_ANIME_MANAGER_PLUGIN_DIR . '/includes/general.php' );
