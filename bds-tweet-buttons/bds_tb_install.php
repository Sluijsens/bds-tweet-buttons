<?php

// Define global variable to get database version
global $bds_tb_db_version;
$bds_tb_db_version = "1.0";

// Install or update database
function bds_tb_db_install() {
    // Needed for the function dbDelta()
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    // Get the new database version
    global $bds_tb_db_version, $wpdb;

    // Put the table in an own variable
    $prefix = $wpdb->prefix;
    $table = $prefix . "bds_tweet_buttons";
    
    // The query to create the table
    $query = "CREATE TABLE $table (
        id mediumint(9) not null auto_increment,
        title varchar(50) not null,
        own_style boolean not null default false,
        css_id varchar(50),
        css_class varchar(100),
        tweet_box boolean not null default false,
        twitter_options text not null,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id));";
    dbDelta($query); // Execute the query. If table already exists the update the table.
    
    // Add or update the current database version
    add_option("bds_tb_db_version", $bds_tb_db_version);
}

?>
