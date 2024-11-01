<?php

function tfb_get_value( $val, $postid = '' ) {
	if ( empty($postid) ) {
		global $post;
		if (isset($post))
			$postid = $post->ID;
		else 
			return false;
	}
	$custom = get_post_custom($postid);
	if (!empty($custom['_tfb_'.$val][0]))
		return maybe_unserialize( $custom['_tfb_'.$val][0] );
	else
		return false;
}

function tfb_delete_to_do($to_do_id, $redirect = true) {
	
	global $wpdb;
	
	if ($to_do_id != '') {

		$query = "DELETE FROM " . $wpdb->prefix . "tfb_to_do WHERE to_do_id = '" . $to_do_id . "' LIMIT 1";
		$wpdb->query($query);
		
		$query = "DELETE FROM " . $wpdb->prefix . "tfb_to_do_messages WHERE to_do_id = '" . $to_do_id . "'";
		$wpdb->query($query);
		
		if ($redirect == true) {
			$admin_url = admin_url();
			header('Location: ' . $admin_url . '/admin.php?page=tfb-feedback');
			exit();
		}
	}
}

function tfb_edit_to_do() {
	
	global $wpdb, $_POST;
				
	if ($_POST['to_do_severity_id'] != $_POST['to_do_severity_id_new']
		OR $_POST['to_do_name'] != $_POST['to_do_name_new']
		OR $_POST['to_do_reproducibility_id'] != $_POST['to_do_reproducibility_id_new']
		OR $_POST['to_do_status_new'] != $_POST['to_do_status']) {
		
		$time = time();
		$wpdb->query("UPDATE " . $wpdb->prefix . "tfb_to_do SET to_do_severity_id = '".addslashes($_POST['to_do_severity_id_new'])."', to_do_name = '".addslashes($_POST['to_do_name_new'])."', to_do_reproducibility_id = '".addslashes($_POST['to_do_reproducibility_id_new'])."', to_do_status = '".addslashes($_POST['to_do_status_new'])."', to_do_update = '" . $time . "' WHERE to_do_id = '" . $_POST['to_do_id'] . "' LIMIT 1");
	}
	
	if ($_POST['to_do_status_new'] != $_POST['to_do_status']
		OR $_POST['tfb_to_do_messages'] != '') {
		
		$time = time();
		$current_user = wp_get_current_user();
		$wp_user_id = $current_user->data->ID;
		$wpdb->query("INSERT INTO " . $wpdb->prefix . "tfb_to_do_messages SET to_do_id = '" . $_POST['to_do_id'] . "', wp_user_id = '" . $wp_user_id . "', to_do_message_message = '" . $_POST['to_do_message_message_new'] . "', to_do_message_added = '" . $time . "'");
	}
	
	$admin_url = admin_url();
	header('Location: ' . $admin_url . '/admin.php?page=tfb-feedback');
	exit();
}

function tfb_set_value( $meta, $val, $postid ) {
	$oldmeta = get_post_meta($postid, '_tfb_'.$meta, true);
	if (!empty($oldmeta)) {
		delete_post_meta($postid, '_tfb_'.$meta, $oldmeta );
	}
	add_post_meta($postid, '_tfb_'.$meta, $val, true);
}

function get_tfb_options_arr() {
	$optarr = array('tfb','tfb_indexation', 'tfb_permalinks', 'tfb_titles', 'tfb_rss', 'tfb_internallinks', 'tfb_xml', 'tfb_social');
	return apply_filters( 'tfb_options', $optarr );
}

function get_tfb_options() {
	$options = array();
	foreach( get_tfb_options_arr() as $opt ) {
		$options = array_merge( $options, (array) get_option($opt) );
	}
	return $options;
}

function tfb_enqueue_scripts() {
	
	if ( is_user_logged_in() AND tfb_verify_ip_filter()) {
		
		$options = get_option('tfb');
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery_ui_position', plugin_dir_url(TFB_PLUGIN_NAME) . TFB_PLUGIN_NAME . '/js/jquery.ui.position.js', array('jquery'), NULL, false );
		if ($options['tfb_enable_point_click'] == 'yes') {
			wp_enqueue_script('jquery_contextMenu', plugin_dir_url(TFB_PLUGIN_NAME) . TFB_PLUGIN_NAME . '/js/jquery.contextMenu.js', array('jquery'), NULL, false );
		}
		wp_enqueue_script('tiny_feedback_bar', plugin_dir_url(TFB_PLUGIN_NAME) . TFB_PLUGIN_NAME . '/js/tiny_feedback_bar.js', array('jquery_ui_position', 'jquery'), NULL, false );
		if ($options['tfb_enable_point_click'] == 'yes') {
			wp_enqueue_script('jquery_point_click', plugin_dir_url(TFB_PLUGIN_NAME) . TFB_PLUGIN_NAME . '/js/point_click.js', array('jquery'), NULL, false );
		}
		wp_enqueue_script('delta_feedback', plugin_dir_url(TFB_PLUGIN_NAME) . TFB_PLUGIN_NAME . '/js/jquery.cluetip.js', array('jquery'), NULL, false );
	}
}

function tfb_verify_ip_filter() {
		
	global $_SERVER;
	
	$options = get_option('tfb');
	
	if ($options['tfb_ip_filter'] != '') {
		
		if (strstr($options['tfb_ip_filter'], ';')) {
			
			$ip_array = explode(';', $options['tfb_ip_filter']);
		} else {
			
			$ip_array[0] = trim($options['tfb_ip_filter']);
		}
		
		if (!in_array($_SERVER['REMOTE_ADDR'], $ip_array)) {
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}
	
function tfb_create_database_table() {
	global $wpdb;
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	//TO DO-DB
	$sql_to_do_db = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "tfb_to_do` (
			  `to_do_id` int(15) NOT NULL AUTO_INCREMENT,
			  `client_id` int(15) NOT NULL DEFAULT '0',
			  `ticket_id` int(15) NOT NULL DEFAULT '0',
			  `project_id` int(15) NOT NULL,
			  `user_id` int(15) NOT NULL DEFAULT '0',
			  `webuser_id` int(15) NOT NULL DEFAULT '0',
			  `parent_to_do_id` int(15) NOT NULL,
			  `to_do_name` varchar(255) NOT NULL DEFAULT '',
			  `to_do_descr` text NOT NULL,
			  `to_do_cat_id` int(15) NOT NULL,
			  `to_do_priority_id` int(5) NOT NULL DEFAULT '1',
			  `to_do_type` enum('bug','task','nfr') NOT NULL DEFAULT 'task',
			  `to_do_severity_id` int(5) NOT NULL,
			  `to_do_reproducibility_id` int(5) NOT NULL,
			  `assigned_user_ids` varchar(255) NOT NULL DEFAULT '',
			  `to_do_origin_wp_user_id` int(15) NOT NULL,
			  `to_do_origin_user_id` int(15) NOT NULL,
			  `to_do_origin_webuser_id` varchar(255) NOT NULL,
			  `to_do_origin_method` enum('developer','feedback_bar') NOT NULL DEFAULT 'developer',
			  `to_do_page_id` int(15) NOT NULL,
			  `to_do_origin_url` varchar(255) NOT NULL,
			  `to_do_origin_x` varchar(5) NOT NULL,
			  `to_do_origin_y` varchar(5) NOT NULL,
			  `to_do_origin_date` int(15) NOT NULL,
			  `to_do_status` int(2) NOT NULL DEFAULT '1',
			  `to_do_status_old` varchar(255) NOT NULL,
			  `to_do_time_est` varchar(5) NOT NULL,
			  `to_do_permission` enum('0','1') NOT NULL DEFAULT '0',
			  `to_do_cat` int(5) NOT NULL DEFAULT '0',
			  `to_do_url` varchar(255) NOT NULL DEFAULT '',
			  `to_do_domain_id` int(15) NOT NULL,
			  `to_do_calculated_time_before` int(10) NOT NULL DEFAULT '0',
			  `to_do_calculated_time_after` int(10) NOT NULL DEFAULT '0',
			  `to_do_est` int(10) NOT NULL DEFAULT '0',
			  `to_do_max_hours` int(5) NOT NULL,
			  `to_do_date` int(15) NOT NULL DEFAULT '0',
			  `to_do_time` int(15) NOT NULL DEFAULT '0',
			  `to_do_pauze` int(15) NOT NULL DEFAULT '0',
			  `to_do_korting` int(15) NOT NULL,
			  `to_do_korting_remark` text NOT NULL,
			  `to_do_added` int(15) NOT NULL DEFAULT '0',
			  `to_do_update` int(15) NOT NULL DEFAULT '0',
			  UNIQUE KEY `to_do_id` (`to_do_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	dbDelta($sql_to_do_db);
	
	//TO DO MESSAGES-DB
	$sql_to_do_messages_db = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "tfb_to_do_messages` (
			  `to_do_message_id` int(15) NOT NULL AUTO_INCREMENT,
			  `to_do_id` int(15) NOT NULL,
			  `client_id` int(15) NOT NULL,
			  `wp_user_id` int(15) NOT NULL,
			  `user_id` int(15) NOT NULL,
			  `webuser_id` int(15) NOT NULL,
			  `to_do_message_message` varchar(255) NOT NULL,
			  `to_do_message_origin` varchar(30) NOT NULL,
			  `to_do_message_added` int(15) NOT NULL,
			  `to_do_message_update` int(15) NOT NULL,
			  PRIMARY KEY (`to_do_message_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	dbDelta($sql_to_do_messages_db);
	
	//TO DO PRIORITY MESSAGES-DB
	$sql_to_do_messages_db = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "tfb_to_do_priority` (
			  `to_do_priority_id` int(11) NOT NULL AUTO_INCREMENT,
			  `to_do_priority_name` varchar(50) NOT NULL,
			  `to_do_priority_order` int(5) NOT NULL,
			  `to_do_priority_default` enum('1','0') NOT NULL,
			  `to_do_priority_update` int(15) NOT NULL,
			  KEY `bug_priority_id` (`to_do_priority_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
	dbDelta($sql_to_do_messages_db);
	
	//PRIORITY-DB VULLEN
	$query = "DELETE FROM " . $wpdb->prefix . "tfb_to_do_priority";
	$wpdb->query($query);
	$query = "INSERT INTO `" . $wpdb->prefix . "tfb_to_do_priority` VALUES (1, 'Normaal', 3, '1', 0);";
	dbDelta($query);
	$query = "INSERT INTO `" . $wpdb->prefix . "tfb_to_do_priority` VALUES (2, 'Hoog', 2, '0', 0);";
	dbDelta($query);
	$query = "INSERT INTO `" . $wpdb->prefix . "tfb_to_do_priority` VALUES (4, 'Laag', 4, '0', 0);";
	dbDelta($query);
	$query = "INSERT INTO `" . $wpdb->prefix . "tfb_to_do_priority` VALUES (5, 'Urgent', 1, '0', 0);";
	dbDelta($query);
	
	$options['tfb_enable_point_click'] = 'yes';
	update_option( 'tfb', $options );
}

?>