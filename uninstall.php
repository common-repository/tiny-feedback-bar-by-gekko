<?php

if (WP_UNINSTALL_PLUGIN) {
	
	global $wpdb;
	
	//REMOVE OPTIONS
	//delete_option('TFB_version');
	
	//REMOVE DATABASES
	//$wpdb->query("DROP TABLE IF EXISTS '" . $wpdb->prefix . "tfb_to_do_messages'");
	//$wpdb->query("DROP TABLE IF EXISTS '" . $wpdb->prefix . "tfb_to_do_messages'");
}

?>