<?php

class TFB_Pointers {

	function __construct() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue' ) );
	}
	
	function enqueue() {
		wp_enqueue_style( 'wp-pointer' ); 
		wp_enqueue_script( 'jquery-ui' ); 
		wp_enqueue_script( 'wp-pointer' ); 
		wp_enqueue_script( 'utils' );
	}
}

$tfb_pointers = new TFB_Pointers;
