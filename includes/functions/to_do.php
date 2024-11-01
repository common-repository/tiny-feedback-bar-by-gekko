<?php

function tfb_get_to_do_info($to_do_id) {
	
	global $delta_config, $wpdb, $current_user;
	
	$test = 0;
	
	if ($test == '1') { echo 'tfb_get_to_do_info<br>'; }
	
	if ($to_do_id != '') {
	
		$query = "SELECT *, b.to_do_id FROM " . $wpdb->prefix . "tfb_to_do b WHERE b.to_do_id = '" . $to_do_id . "' LIMIT 1";
		
		if ($test == '1') { echo '11<br>'; }
		$sql_to_dos = $wpdb->get_results($query);
		
		$to_do = (array)$sql_to_dos[0];
		
		if ($to_do['to_do_origin_date'] != '') {
			
			if ($test == '1') { echo '22<br>'; }
			$to_do['origin']['type'] = $to_do['to_do_origin_method'];
			$to_do['origin']['since'] = $to_do['to_do_origin_date'];
			
			if ($to_do['to_do_origin_wp_user_id'] != '') {
				if ($test == '1') { echo '33<br>'; }
				$user = get_userdata(get_current_user_id());
				$to_do['origin']['id'] = $user->data->ID;
				$to_do['origin']['name'] = $user->data->user_nicename;
			}
		}
		
		if ($test == '1') { echo '55<br>'; }
	}
	
	return $to_do;
}

function tfb_return_to_do_status($to_do_status) {
	
	global $delta_config;
	
	if (is_array($delta_config['to_do']['status'])) {
		foreach($delta_config['to_do']['status'] as $key => $value) {
			
			if ($value['id'] == $to_do_status) {
				$output = $value['text'];
			}
		}
	}
	
	return $output;
}

function tfb_return_to_do_severity($to_do_severity_id) {
	
	global $delta_config;
	
	if (is_array($delta_config['to_do']['severity'])) {
		foreach($delta_config['to_do']['severity'] as $key => $value) {
		
			if ($value['id'] == $to_do_severity_id) {
				$output = $value['text'];
			}
		}
	}
	
	return $output;
}

function tfb_return_to_do_reproducibility($to_do_reproducibility_id) {
	
	global $delta_config;
	
	if (is_array($delta_config['to_do']['reproducibility'])) {
		foreach($delta_config['to_do']['reproducibility'] as $key => $value) {
			
			if ($value['id'] == $to_do_reproducibility_id) {
				$output = $value['text'];
			}
		}
	}
	
	return $output;
}

function tfb_return_to_do_priority($to_do_priority_id) {
	
	global $delta_config, $wpdb;
	
	$query = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do_priority WHERE to_do_priority_id = '$to_do_priority_id' LIMIT 1";
	$sql = $wpdb->get_results($query);
	$row = (array)$sql[0];
	$x='1';
	
	if(!$sql) {
		$error = 'Kon niet in de database kijken.';
		return '-';
	} else {
	
		return $row['to_do_priority_name'];
	}
}

function tfb_get_to_do_priority_icon($to_do_priority_id) {
	
	global $delta_config;
	
	if (is_array($delta_config['to_do']['priority'])) {
		foreach($delta_config['to_do']['priority'] as $key => $value) {
			
			if ($value['id'] == $to_do_priority_id) {
				$output['img'] = $value['icon'];
				$output['text'] = $value['text'];
			}
		}
	}
	return $output;
}

?>