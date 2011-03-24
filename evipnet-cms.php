<?php
/*
Plugin Name: EVIPNet Metadata
Description: Handles creation of EVIPNet metadata content.
Author: BIREME
Version: 0.1
*/

define('EVIP_VERSION', '0.1' );
define('EVIP_URL', WP_PLUGIN_URL . '/evipnet-cms/');
define('EVIP_PATH', dirname(__FILE__) );

// Load plugin files
require_once(EVIP_PATH . "/post_type.php");
require_once(EVIP_PATH . "/attach.php");

function evip_init() {

    wp_enqueue_script("jquery");    
    wp_enqueue_script("jquery-ui-core");
    wp_enqueue_script("jquery-ui-sortable");

    wp_enqueue_script("thickbox");
    wp_enqueue_style("thickbox");

    wp_enqueue_script('evip-edit', EVIP_URL . 'js/scripts.js');  
    wp_enqueue_style('evip-edit', EVIP_URL . 'css/styles.css');  
}
    


function evip_get_posts( $query ) {
	if ( is_home() )
		$query->set( 'post_type', array( 'post', 'page', 'evipnet','attachment' ) );

	return $query;
}


// display custom post types on wordpress homepage
add_filter('pre_get_posts', 'evip_get_posts' );
add_action('plugins_loaded','evip_init');

?>
