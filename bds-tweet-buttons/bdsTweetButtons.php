<?php

/*
 * Plugin Name: BDS Tweet Buttons
 * Plugin URI: http://www.bryan-slop.nl
 * Description: A simple Twitter Button plugin to manage custom Tweet Buttons
 * Version: 1.0
 * Author: Bryan Slop
 * Author URI: http://www.bryan-slop.nl
 * License: GPL
 * 
 */

// Define the root directory for the plugin
define("BDS_TB_ROOT", basename(dirname(__FILE__)));

// Check if the listtable class exists. If not, then include it
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// Include all (needed) php files
include_once 'php/classes/class.Widget.inc';
include_once 'php/classes/class.BDS_TB_ListTable.inc';
include_once 'php/functions/bds_tb_admin.php';
include_once 'bds_tb_install.php';
include_once 'php/functions/main_functions.php';

// Check if database version differs from the new setting. If so, run install again
if (get_option("bds_tb_db_version") > 0 || !get_option("bds_tb_db_version")) {
    bds_tb_db_install();
}

// Add shortcode so it can be executed
add_shortcode("bds_tb", "bds_tb_shortcode");

// Load all admin scripts and styles
add_action("admin_enqueue_scripts", "bds_tb_load_admin_scripts");
function bds_tb_load_admin_scripts() {
    // Register styles
    wp_register_style("bds_tb_css_admin", WP_PLUGIN_URL . "/" . BDS_TB_ROOT . "/css/admin.css");

    // Load styles
    wp_enqueue_style("bds_tb_css_admin");
    
    // Load wp scripts and styles
    bds_tb_load_scripts();
}

// Only load scripts and styles for front-end
add_action("wp_enqueue_scripts", "bds_tb_load_scripts");
function bds_tb_load_scripts() {
    // Register styles
    wp_register_style("bds_tb_css_main", WP_PLUGIN_URL . "/" . BDS_TB_ROOT . "/css/main.css");

    // Register the scripts
    wp_register_script("bds_tb_js_main", WP_PLUGIN_URL . "/" . BDS_TB_ROOT . "/js/main.js", array("jquery"));

    // Load styles
    wp_enqueue_style("bds_tb_css_main");

    // Load the scripts
    wp_enqueue_script("bds_tb_js_main");
}

// Register the widget
add_action( "widgets_init", "bds_register_widgets");
function bds_register_widgets() {
     register_widget("BDS_Tweet_Button");
}
?>