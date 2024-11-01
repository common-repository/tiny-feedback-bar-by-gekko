<?php

function get_logo() {
	
	require(ABSPATH . '/wp-content/plugins/tiny-feedback-bar-by-gekko/includes/application_top.php');

	if (file_exists(ABSPATH . '/wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php')) {
	
		require(ABSPATH . '/wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php');
	
		if (file_exists(ABSPATH . '/wp-content/plugins/tiny-feedback-bar-by-gekko/custom/' . WEB_COMPANY_LOGO_BAR)) {
			
		} else {
			
		}
		if (WEB_COMPANY_LOGO_BAR != '' AND file_exists(ABSPATH . '/wp-content/plugins/tiny-feedback-bar-by-gekko/custom/' . WEB_COMPANY_LOGO_BAR) AND WEB_COMPANY_NAME != '') {
			
			if (WEB_COMPANY_WEBSITE != '') {
				$link_begin = '<a href="' . WEB_COMPANY_WEBSITE . '" target="_blank">';
				$link_end = '</a>';
			} else {
				$link_begin = '';
				$link_end = '';
			}
			$output = $link_begin . '<img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/tiny-feedback-bar-by-gekko/custom/' . WEB_COMPANY_LOGO_BAR . '" alt="' . WEB_COMPANY_NAME . '" style="display:block;padding:0px;margin:0px;" border="0" />' . $link_end;
		} else {
			$output = '<a href="http://www.tinyfeedbackbar.com/" title="Tiny Feedback Bar: feedback made easy" target="_blank"><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/tiny-feedback-bar-by-gekko/skin/images/logo-tiny-feedback-bar.png" style="display:block;padding:0px;margin:0px;" alt="Tiny Feedback Bar: feedback made easy" width="52" height="39" border="0" /></a>';
		}
	} else {
		$output = '<a href="http://www.tinyfeedbackbar.com/" title="Tiny Feedback Bar: feedback made easy" target="_blank"><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/tiny-feedback-bar-by-gekko/skin/images/logo-tiny-feedback-bar.png" style="display:block;padding:0px;margin:0px;" alt="Tiny Feedback Bar: feedback made easy" width="52" height="39" border="0" /></a>';
	}
	echo $output;
	die();
}

add_action( 'wp_ajax_getlogo', 'get_logo');

function get_branding() {
	
	require(ABSPATH . '/wp-content/plugins/tiny-feedback-bar-by-gekko/includes/application_top.php');

	if (file_exists(ABSPATH . '/wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php')) {
		
		require(ABSPATH . '/wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php');
		if (WEB_COMPANY_BRANDING != '') {
			
			if (WEB_COMPANY_SUPPORT_WEBSITE != '') {
				$link_begin = '<a href="' . WEB_COMPANY_SUPPORT_WEBSITE . '" target="_blank" class="tfb_blue">';
				$link_end = '</a>';
			} else {
				$link_begin = '';
				$link_end = '';
			}
			$output = $link_begin . WEB_COMPANY_BRANDING . $link_end;
		} else {
			$output = '';
		}
	} else {
		$output = '';
	}
	echo $output;
	die();
}

add_action( 'wp_ajax_getbranding', 'get_branding');

function get_todos() {
	
	global $wpdb, $_GET;
	
	$page_id = $_GET['page_id'];
	
	if ($page_id == '0') {
	
		$output = '<span style="color:#FF0000;">Attention: homepage ID is unknown</span> <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=tfb-settings" class="tfb_blue" target="_blank">Click here to correct</a>.';
	} else {
		
		$query_to_do = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do WHERE to_do_page_id = '" . $page_id . "' AND to_do_status != '300' AND to_do_status != '400' AND to_do_status != '500'";
		
		$query_to_do = str_replace('\n', '', $query_to_do);
		$query_to_do = str_replace('\r', '', $query_to_do);
		$query_to_do = str_replace('\t', '', $query_to_do);
		
		$sql_rows = $wpdb->get_results($query_to_do);
		$rijen = $wpdb->num_rows;
		
		if ($rijen == 1) {
			$items = '1 item';
		} else {
			$items = $rijen . ' items';
		}
		
		if ($rijen > 0) {
			$output = '<strong><a href="Javascript:tfb_exec(6,' . $page_id . ');" class="tfb_blue">' . $items . '</a></strong>';
		} else {
			$output = '0 items';
		}
	}
	echo $output;
	die();
}

add_action( 'wp_ajax_gettodos', 'get_todos');

function get_search() {
	
	global $wpdb;
	
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	} else {
		$id = '';
	}
	
	if ($id != '0') {
	
		//
		//TICKETS EN to_do ZOEKEN
		////////////////////////
		if ($id == 'ccc') {
			
			$query_to_do = "SELECT b.* 
						FROM " . $wpdb->prefix . "tfb_to_do b 
						WHERE (
									b.to_do_name LIKE '%" . $id . "%' 
									OR b.to_do_id LIKE '%" . $id . "%' 
									OR b.ticket_id LIKE '%" . $id . "%' 
								) 
							AND b.to_do_status != '300'
							AND b.to_do_status != '400'
							AND b.to_do_status != '500'";
			
		} elseif ($id != '') {
			$query_to_do = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do WHERE to_do_page_id = '" . $id . "' AND to_do_status != '300' AND to_do_status != '400' AND to_do_status != '500'";
		} else {	
			$query_to_do = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do WHERE to_do_status != '300' AND to_do_status != '400' AND to_do_status != '500'";
		}
		
		$query_to_do = str_replace('\n', '', $query_to_do);
		$query_to_do = str_replace('\r', '', $query_to_do);
		$query_to_do = str_replace('\t', '', $query_to_do);
		
		$sql_rows = $wpdb->get_results($query_to_do);
		$rijen = $wpdb->num_rows;
		
		if ($rijen > 0) {
		
			foreach($sql_rows as $row_to_do) {
				
				$row_to_do = (array)$row_to_do;
				
				if ($row_to_do['to_do_type'] == '0') {
					$type = 'to_do';
				} else {
					$type = 'new_features';
				}
				if ($row_to_do['ticket_id'] != '' AND $row_to_do['ticket_id'] != '0') {
					
					if ($tree['under_tickets'][$row_to_do['ticket_id']]['info'] == '') {
						$tree['under_tickets'][$row_to_do['ticket_id']]['info'] = get_ticket_info($row_to_do['ticket_id']);
					}
					if ($row_to_do['to_do_id'] != '') {
						$tree['under_tickets'][$row_to_do['ticket_id']]['todos'][$row_to_do['to_do_id']] = $row_to_do;
					}
				} else {
					$tree['unassigned'][$row_to_do['to_do_id']] = $row_to_do;
				}
			}
		}
		
		$output = '<table width="100%" border="0" cellspacing="2" cellpadding="2">';
		
		if (isset($tree['under_tickets']) AND !is_array($tree['under_tickets']) AND !is_array($tree['unassigned'])) {
			
			$output .= '<tr><td><br></td></tr><tr><td><b>No items found</b></td></tr>';
		} else {
		
			if (isset($tree['under_tickets']) AND is_array($tree['under_tickets'])) {
				$output .= '<tr><td><br></td></tr><tr><td class="tableheader">Tickets</td><td class="tableheader">Status</td></tr>';
			}
			
			if (is_array($tree['unassigned'])) {
					
				if (is_array($tree['unassigned'])) {
			
					$output .= '<tr><td><br></td></tr><tr><td class="tableheader"><b>New Feature Requests, Tasks and Bugs:</b></td><td class="tableheader"><b>Status</b></td></tr>';
					foreach($tree['unassigned'] as $todo_id => $todo) {
		
						$status = tfb_return_to_do_status($todo['to_do_status']);
						//$output .= '<tr><td><a href="popup.php?status=1&to_do_id=' . $todo['to_do_id'] . '" class="black">#' . $todo['to_do_id'] . ' - ' . $todo['to_do_name'] . '</a></td><td>' . $status . '</td></tr>';
						$output .= '<tr><td><a href="admin-ajax.php?action=popup&status=1&to_do_id=' . $todo['to_do_id'] . '" class="black">#' . $todo['to_do_id'] . ' - ' . $todo['to_do_name'] . '</a></td><td>' . $status . '</td></tr>';
					}
				}
		
				$output .= '<tr><td height="2"></td></tr>';
			}
		}
			
		$output .= '</table>';
	}
	echo $output;
	die();
}

add_action( 'wp_ajax_getsearch', 'get_search');

function get_to_do() {
	
	global $wpdb, $current_user, $delta_config, $_GET;

	if ($_GET['get_todo'] != '') {
	
		if ($_GET['get_todo'] == '0') {
			
			$output = '<span style=\"color:#FF0000;\">Attention: homepage ID is unknown</span> <a href=\"' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=tfb-settings\" class=\"tfb_blue\" target=\"_blank\">Click here to correct</a>.';
		} else {
		
			$query_to_do = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do WHERE to_do_id = '" . $_GET['get_todo'] . "' LIMIT 1";
			
			$query_to_do = str_replace('\n', '', $query_to_do);
			$query_to_do = str_replace('\r', '', $query_to_do);
			$query_to_do = str_replace('\t', '', $query_to_do);
			
			$sql_rows = $wpdb->get_results($query_to_do);
			
			$rijen = $wpdb->num_rows;
			
			$x='1';
			
			if(!$sql_rows) {
				$error = 'Kon niet in de database kijken.';
			} else {
				
				if ($rijen == 1) {
				
					$to_do = (array)$sql_rows[0];
					
					if ($to_do['to_do_type'] == 'bug') {
						
						$text = 'Bug';
					} elseif ($to_do['to_do_type'] == 'task') {
						
						$text = 'Task';
					} elseif ($to_do['to_do_type'] == 'nfr') {
						
						$text = 'New Feature Request';
					}
					
					$status_out = '';
					if (is_array($delta_config['to_do']['status'])) {
						foreach($delta_config['to_do']['status'] as $key => $status) {
							if ($status['id'] == $to_do['to_do_status']) {
								$text .= ' with status \'' . $status['text'] . '\'';
							}
						}
					}
					
					$user = get_userdata($to_do['to_do_origin_wp_user_id']);
					if ($user->user_firstname != '') {
						$by = $user->user_firstname;
						if ($user->user_lastname != '') {
							$by .= ' ' . $user->user_lastname;
						}
					} else {
						$by = $user->user_login;
					}
					
					$output = '<strong><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=tfb-feedback&action=edit&to_do_id=' . $to_do['to_do_id'] . '" target="_blank">#' . $to_do['to_do_id'] . ' - ' . $to_do['to_do_name'] . '</a></strong><br />' . $text  . '<br />By ' . $by . '<br />';
				} else {
					
					$output = '';
				}
			}
			
			echo $output;
		}
	}
	die();
}

add_action( 'wp_ajax_gettodo', 'get_to_do');

function mark() {
	
	global $wpdb, $current_user, $_GET;

	$wp_user_id = $current_user->data->ID;
	$_GET['x'] = round($_GET['x'], 0);
	$_GET['y'] = round($_GET['y'], 0);
	$query = "INSERT INTO " . $wpdb->prefix . "tfb_to_do 
				SET to_do_origin_wp_user_id = '" . $wp_user_id . "', 
					to_do_type = '" . $_GET['type'] . "', 
					to_do_origin_method = 'feedback_bar', 
					to_do_origin_date = '" . time() . "', 
					to_do_page_id = '" . $_GET['page_id'] . "', 
					to_do_origin_x = '" . $_GET['x'] . "', 
					to_do_origin_y = '" . $_GET['y'] . "', 
					to_do_status = '50', 
					to_do_priority_id = '1', 
					to_do_severity_id = '', 
					to_do_added = '" . time() . "'";

	$sql = $wpdb->query($query);
	$to_do_id = $wpdb->insert_id;
	
	if (!$sql) {
		
	} else {
		
		if ($_GET['type'] == 'bug') {
			
			$text = 'Click here to start working on this Bug';
			$img = 'to_do_type_bug.png';
		} elseif ($_GET['type'] == 'task') {
			
			$text = 'Click here to start working on this Task';
			$img = 'to_do_type_task.png';
		} elseif ($_GET['type'] == 'nfr') {
			
			$text = 'Click here to start working on this New Feature Request';
			$img = 'to_do_type_new_feature.png';
		} else {
			$text = 'ELSE';
			$img = 'to_do_type_else.png';
		}
		
		$x = round($_GET['x'] + ($_GET['w'] / 2), 0) - 5;
		$_GET['y'] = $_GET['y'] - 15;
		
		$markings = '<div style="left:' . $x . 'px;top:' . $_GET['y'] .  'px;height:20px;width:20px;position:absolute;z-index:999990;" title="' . $text . '"><a id="sticky' . $to_do_id . '" href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=tfb-feedback&action=edit&to_do_id=' . $to_do_id . '" target="_blank" title="' . $text . '"><img src="' . plugin_dir_url(TFB_PLUGIN_NAME) . TFB_PLUGIN_NAME . '/skin/images/' . $img . '" border="0"></a></div>';
		
		$query_to_do = "SELECT b.* 
				FROM " . $wpdb->prefix . "tfb_to_do b 
				WHERE b.to_do_page_id = '" . $_GET['page_id'] . "' 
					AND b.to_do_status != '300' 
					AND b.to_do_status != '400' 
					AND b.to_do_status != '500' 
					AND b.to_do_origin_x != '' 
					AND b.to_do_origin_y != '' 
					AND b.to_do_id != '" . $to_do_id . "'";
		
		$sql_rows = $wpdb->get_results($query_to_do);
		$rijen = $wpdb->num_rows;
		if ($rijen > 0) {
			
			$teller = 0;
			foreach($sql_rows as $row_to_do) {
				
				$row_to_do = (array)$row_to_do;
				
				if ($row_to_do['to_do_type'] == 'bug') {
					$img = 'to_do_type_bug.png';
				} elseif ($row_to_do['to_do_type'] == 'task') {
					$img = 'to_do_type_task.png';
				} elseif ($row_to_do['to_do_type'] == 'nfr') {
					$img = 'to_do_type_new_feature.png';
				}
				
				$x = round($row_to_do['to_do_origin_x'] + ($_GET['w'] / 2), 0) - 5;
				$row_to_do['to_do_origin_y'] = $row_to_do['to_do_origin_y'] - 15;
				
				$markings .= '<div class="inpagetodos" style="left:' . $x . 'px;top:' . $row_to_do['to_do_origin_y'] .  'px;height:50px;width:50px;position:absolute;z-index:999990;style:block;" title="' . $row_to_do['text'] . '"><a id="sticky' . $row_to_do['to_do_id'] . '" class="cluetip" rel="' . get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=gettodo&get_todo=' . $row_to_do['to_do_id'] . '"><img src="' . plugin_dir_url(TFB_PLUGIN_NAME) . TFB_PLUGIN_NAME . '/skin/images/' . $img . '" border="0"></a></div>';
			}
		}
		
		echo $markings;
	}
	die();
}

add_action( 'wp_ajax_mark', 'mark');

function get_markings() {
	
	global $wpdb, $current_user, $_GET;
	
	$query_to_do = "SELECT b.* 
			FROM " . $wpdb->prefix . "tfb_to_do b 
			WHERE b.to_do_page_id = '" . $_GET['page_id'] . "' 
				AND b.to_do_status != '300' 
				AND b.to_do_status != '400' 
				AND b.to_do_status != '500' 
				AND b.to_do_origin_x != '' 
				AND b.to_do_origin_y != ''";
	
	$sql_rows = $wpdb->get_results($query_to_do);
	
	$rijen = $wpdb->num_rows;
	
	$markings = '';
	if ($rijen > 0) {
		
		foreach($sql_rows as $row_to_do) {
			
			$row_to_do = (array)$row_to_do;
			
			if (!isset($row_to_do['text'])) {
				$row_to_do['text'] = '';
			}
			
			if ($row_to_do['to_do_type'] == 'bug') {
				$img = 'to_do_type_bug.png';
			} elseif ($row_to_do['to_do_type'] == 'task') {
				$img = 'to_do_type_task.png';
			} elseif ($row_to_do['to_do_type'] == 'nfr') {
				$img = 'to_do_type_new_feature.png';
			}
			
			$x = round($row_to_do['to_do_origin_x'] + ($_GET['w'] / 2), 0) - 5;
			$row_to_do['to_do_origin_y'] = $row_to_do['to_do_origin_y'] - 15;
			
			$markings .= '<div class="inpagetodos" style="left:' . $x . 'px;top:' . $row_to_do['to_do_origin_y'] .  'px;height:50px;width:50px;position:absolute;z-index:999990;style:block;" title="' . $row_to_do['text'] . '"><a id="sticky' . $row_to_do['to_do_id'] . '" class="cluetip" rel="' . get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=gettodo&get_todo=' . $row_to_do['to_do_id'] . '"><img src="' . plugin_dir_url(TFB_PLUGIN_NAME) . TFB_PLUGIN_NAME . '/skin/images/' . $img . '" border="0"></a></div>';
		}
	}
	
	echo $markings;
	die();
}

add_action( 'wp_ajax_get_markings', 'get_markings');

function popup() {

	if ( !is_user_logged_in() ) {
		?>
		<html>
		<head>
		<title>Tiny Feedback Bar - Login please</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="skin/popup.css" rel="stylesheet" type="text/css">
		</head>
		
		<body>
		<table width="100%" border="0" cellspacing="2" cellpadding="2">
		  <tr>
			<td><img src="<? echo plugin_dir_path(TFB_PLUGIN_NAME); ?>skin/images/logo-popup-tiny-feedback-bar.png" width="115" height="83" /></td>
			<td align="right"><span class="orange">Tiny</span> Feedback Bar&nbsp;&reg;</td>
		  </tr>
		</table>
		<table width="100%" border="0" cellspacing="2" cellpadding="2">
		  <tr>
			<td><h1>Tiny Feedback Bar</h1></td>
		  </tr>
		  <tr>
			<td><b>You have to be logged in to be able to use Tiny Feedback Bar</b>/td>
		  </tr>
		</table>
		</body>
		</html>
		<?php
		exit();
	}
	
	$popup_script = '<script type="text/javascript">
	function tfb_ajax(todo, target, pageid) {
		
		var target = \'#\' + target;
		var values = \'?action=\' + todo;
		if (pageid != \'\') {
			values = values + \'&page_id=\' + pageid;
		}
		jQuery.ajax({
			type: "get", url: "' . get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php" + values,
			success: function(html){
				jQuery(target).html(html);
			}
		});
	}
	</script>';
	
	?>
	<html>
	<head>
	<title>Tiny Feedback Bar - Popup</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="../wp-content/plugins/tiny-feedback-bar-by-gekko/skin/popup.css" rel="stylesheet" type="text/css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<?php echo $popup_script; ?>
	<script language="javascript"> 
	<!-- 
		function start(page_id) {
			tfb_ajax('getsearch', 'output', page_id, '', '', '', '');
		}
		function myFunction(element){ 
			liArray=document.getElementById("tabitems").childNodes; 
			i=0; 
			while(liArray[i]){ 
				liArray[i].id=""; 
				i++; 
			}
			element.id="current"; 
		} 
	 
	    function get(page_id) {
	 		
			/*
			var email;
			id = document.search.id.value;
			var poststr = "id=" + encodeURI( id );
			tfbMakePostRequest('ajax/search.php', poststr);
			*/
			
			tfb_ajax('getsearch', 'output', page_id, '', '', '', '');
	    }
		function showdiv(tabid, prefix) {
			
			document.getElementById('newfeature').style.display = "none";
			document.getElementById('task').style.display = "none";
			document.getElementById('bug').style.display = "none";
			document.getElementById('status').style.display = "none";
			document.getElementById('statustodo').style.display = "none";
			document.getElementById('contact').style.display = "none";
			
			document.getElementById("tabnewfeature").className = "begin";
			document.getElementById("tabtask").className = "";
			document.getElementById("tabbug").className = "";
			<?php
			if (file_exists('../wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php')) {
				
				require_once('../wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php');
			
				if (WEB_COMPANY_DESCRIPTION != '') {
					?>
			document.getElementById("tabstatus").className = "";
			document.getElementById("tabcontact").className = "end";
					<?php
				} else {
					?>
			
					<?php
				}
			} else {
				?>
			document.getElementById("tabstatus").className = "end";
				<?
			}
			?>
			
			var element = document.getElementById(tabid);
			if(element.style.display != "block") {
				element.style.display = "block";
			} else {
				element.style.display = "none";
			}
			document.getElementById("tab" + tabid).className = prefix + "current";
		}
		var cleared = false;
		function clickclear(thisfield) {
			
			if (cleared == false) {
				thisfield.value = "";
				cleared = true;
			}
		}
	//-->
	</script>
	</head>
	
	<body>
	<?php
	if (file_exists('../wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php')) {
		
		require_once('../wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php');
	
		if (WEB_COMPANY_LOGO_POPUP != '') {
			$company_logo = '<img src="../wp-content/plugins/tiny-feedback-bar-by-gekko/custom/' . WEB_COMPANY_LOGO_POPUP . '" />';
		} else {
			$company_logo = '<img src="../wp-content/plugins/' . TFB_PLUGIN_NAME . '/skin/images/logo-popup-tiny-feedback-bar.png" width="115" height="83" />';
		}
	} else {
		$company_logo = '<img src="../wp-content/plugins/' . TFB_PLUGIN_NAME . '/skin/images/logo-popup-tiny-feedback-bar.png" width="115" height="83" />';
	}
	$current_user = wp_get_current_user();
	$user_info = get_userdata($current_user->data->ID);
	?>
	<table width="480" border="0" cellspacing="2" cellpadding="2">
	  <tr>
		<td height="83"><?php echo $company_logo; ?></td>
		<td height="83" align="right">Hi <?php echo $user_info->data->user_nicename; ?></td>
	  </tr>
	</table>
	<div id="tabopsommng">
		<div id="tabs">
			<ul>
				<li id="tabtask" class="begin<?php if ($_GET['type'] == '3' OR ($_GET['type'] == '' AND $_GET['status'] != '1' AND $_GET['contact'] != '1')) { echo ' current'; } ?>"><a href="javascript:showdiv('task', 'begin ');">Create Task</a></li>
				<li id="tabbug" class="<?php if ($_GET['type'] == '2') { echo 'current'; } ?>"><a href="javascript:showdiv('bug', 'begin ');">Report Bug</a></li>
				<li id="tabnewfeature" class="<?php if ($_GET['type'] == '1') { echo 'current'; } ?>"><a href="javascript:showdiv('newfeature', '');">New Feature Request</a></li>
                <li id="tabstatus" class="<?php if ($_GET['status'] == '1') { echo 'current'; } ?>"><a href="javascript:showdiv('status', 'end ');">Status</a></li>
				<?php
				if (file_exists('../wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php')) {
					
					require_once('../wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php');
				
					if (WEB_COMPANY_DESCRIPTION != '') {
						?>
				<li id="tabcontact" class="end"><a href="javascript:showdiv('contact', 'end ');">Contact</a></li>
						<?php
					}
				}
				?>
			</ul>
		</div>
	</div>
	<?php if (strstr($_SERVER['HTTP_USER_AGENT'], 'Firefox/')) { ?><br><br><br><?php } ?>
	<div id="task" style="display:<?php if (isset($_GET['type']) AND $_GET['type'] == '3') { echo 'block'; } else { echo 'none'; } ?>">
	<?php
	if (isset($_GET['page_id']) AND $_GET['page_id'] != '') {
		
		$title = get_the_title($_GET['page_id']);
	}
	?>
	<table width="480" border="0" cellspacing="2" cellpadding="2">
	 <form id="task" name="task" method="post" action="admin-ajax.php">
	  <tr>
		<td colspan="3"><h1>Create Task</h1></td>
	  </tr>
      <? if (isset($_GET['msg']) AND $_GET['msg'] != '') { ?>
	  <tr>
		<td colspan="3"><div class="succes"><? echo $_GET['msg']; ?></div></td>
	  </tr>
      <? } ?>
	  <tr>
		<td><b>URL</b></td>
		<td width="2"><b>:</b></td>
		<td><?php if (!isset($_GET['url'])) { echo '-'; } else { echo str_replace('http://', '', $_GET['url']); } ?></td>
	  </tr>
	  <tr>
		<td width="110"><b>Page Title</b></td>
		<td width="2"><b>:</b></td>
		<td width="370"><?php if (!isset($title)) { echo '-'; } else { echo $title; } ?></td>
	  </tr>
	  <tr>
		<td><b>Name</b></td>
		<td width="2"><b>:</b></td>
		<td><input type="text" name="titel" id="titel" maxlength="25" class="invoer" /></td>
	  </tr>
	  <tr>
		<td><b>Comments</b></td>
		<td><b>:</b></td>
		<td></td>
	  </tr>
	  <tr>
		<td colspan="3">Keep it brief, please</td>
	  </tr>
	  <tr>
		<td colspan="3"><textarea name="opmerkingen" id="opmerkingen" rows="13" class="invoer"></textarea></td>
	  </tr>
	  <tr>
		<td colspan="3"><input name="task_type" type="hidden" value="3" />
		  <input name="action" type="hidden" value="newtask" />
          <input name="page_id" type="hidden" value="<?php echo $_GET['page_id']; ?>" />
		  <input name="url" type="hidden" value="<?php echo htmlspecialchars($_GET['url']); ?>" />
		  <input name="uid" type="hidden" value="<?php echo $_SESSION['userinfo']['id']; ?>" />
		  <input type="submit" name="Save" value="save" />&nbsp;
		  <input type="reset" name="button" value="reset" /></td>
	  </tr>
	 </form>
	</table>
	</div>
	<div id="bug" style="display:<?php if (isset($_GET['type']) AND $_GET['type'] == '2') { echo 'block'; } else { echo 'none'; } ?>">
	<?php
	if (isset($_GET['page_id']) AND $_GET['page_id'] != '') {
		
		$title = get_the_title($_GET['page_id']);
	}
	?>
	<table width="480" border="0" cellspacing="2" cellpadding="2">
	 <form id="bug" name="bug" method="post" action="admin-ajax.php">
	  <tr>
		<td colspan="3"><h1>Report Bug</h1></td>
	  </tr>
      <? if (isset($_GET['msg']) AND $_GET['msg'] != '') { ?>
	  <tr>
		<td colspan="3"><div class="succes"><? echo $_GET['msg']; ?></div></td>
	  </tr>
      <? } ?>
	  <tr>
		<td><b>URL</b></td>
		<td width="2"><b>:</b></td>
		<td><?php if (!isset($_GET['url'])) { echo '-'; } else { echo str_replace('http://', '', $_GET['url']); } ?></td>
	  </tr>
	  <tr>
		<td width="110"><b>Page Title</b></td>
		<td width="2"><b>:</b></td>
		<td width="370"><?php if (!isset($title)) { echo '-'; } else { echo $title; } ?></td>
	  </tr>
	  <tr>
		<td><b>Name</b></td>
		<td width="2"><b>:</b></td>
		<td><input type="text" name="titel" id="titel" maxlength="25" class="invoer" /></td>
	  </tr>
	  <tr>
		<td><b>Impact</b></td>
		<td width="2"><b>:</b></td>
		<td><select name="to_do_severity_id" class="invoer">
				<option value="100">Content</option>
				<option value="200">Tweak</option>
				<option value="300">Small</option>
				<option value="400">Big</option>
				<option value="500">Crash</option>
			</select></td>
	  </tr>
	  <tr>
		<td><b>Reproducibility</b></td>
		<td width="2"><b>:</b></td>
		<td><select name="to_do_reproducibility_id" class="invoer">
				<option value="100">-</option>
				<option value="200">Not reproducable</option>
				<option value="300">Haven't tried</option>
				<option value="400">Random</option>
				<option value="500">Sometimes</option>
				<option value="600">Always</option>
			</select></td>
	  </tr>
	  <tr>
		<td><b>Comments</b></td>
		<td><b>:</b></td>
		<td></td>
	  </tr>
	  <tr>
		<td colspan="3">Example: write down the steps needed to reproduce the bug</td>
	  </tr>
	  </tr>
	  <tr>
		<td colspan="3"><textarea name="opmerkingen" id="opmerkingen" rows="13" class="invoer"></textarea></td>
	  </tr>
	  <tr>
		<td colspan="3"><input name="to_do_type" type="hidden" value="2" />
		  <input name="action" type="hidden" value="newbug" />
          <input name="page_id" type="hidden" value="<?php echo $_GET['page_id']; ?>" />
		  <input name="url" type="hidden" value="<?php echo htmlspecialchars($_GET['url']); ?>" />
		  <input name="uid" type="hidden" value="<?php echo $_SESSION['userinfo']['id']; ?>" />
		  <input type="submit" name="Save" value="save" />&nbsp;
		  <input type="reset" name="button" value="reset" /></td>
	  </tr>
	 </form>
	</table>
	</div>
	<div id="newfeature" style="display:<?php 
		if (isset($_GET['type']) AND $_GET['type'] == '1') { 
			echo 'block'; 
		} else { 
			echo 'none'; 
		} 
		?>">
	<?php
	if (isset($_GET['page_id']) AND $_GET['page_id'] != '') {
		$title = get_the_title($_GET['page_id']);
	}
	?>
	<table width="480" border="0" cellspacing="2" cellpadding="2">
	 <form id="newfeature" name="newfeature" method="post" action="admin-ajax.php">
	  <tr>
		<td colspan="3"><h1>New Feature Request</h1></td>
	  </tr>
      <? if (isset($_GET['msg']) AND $_GET['msg'] != '') { ?>
	  <tr>
		<td colspan="3"><div class="succes"><? echo $_GET['msg']; ?></div></td>
	  </tr>
      <? } ?>
	  <tr>
		<td><b>URL</b></td>
		<td width="2"><b>:</b></td>
		<td><?php if (!isset($_GET['url'])) { echo '-'; } else { echo str_replace('http://', '', $_GET['url']); } ?></td>
	  </tr>
	  <tr>
		<td width="110"><b>Page Title</b></td>
		<td width="2"><b>:</b></td>
		<td width="370"><?php if (!isset($title)) { echo '-'; } else { echo $title; } ?></td>
	  </tr>
	  <tr>
		<td><b>Name</b></td>
		<td width="2"><b>:</b></td>
		<td><input type="text" name="titel" id="titel" maxlength="25" class="invoer" /></td>
	  </tr>
	  <tr>
		<td><b>Comments</b></td>
		<td><b>:</b></td>
		<td></td>
	  </tr>
	  <tr>
		<td colspan="3"><textarea name="opmerkingen" id="opmerkingen" rows="15" class="invoer"></textarea></td>
	  </tr>
	  <tr>
		<td colspan="3"><input name="to_do_type" type="hidden" value="1" />
		  <input name="action" type="hidden" value="newnfr" />
          <input name="page_id" type="hidden" value="<?php echo $_GET['page_id']; ?>" />
          <input name="url" type="hidden" value="<?php echo urlencode($_GET['url']); ?>" />
		  <input name="uid" type="hidden" value="<?php echo $_SESSION['userinfo']['id']; ?>" />
		  <input type="submit" name="Save" value="save" />&nbsp;
		  <input type="reset" name="button" value="reset" /></td>
	  </tr>
	 </form>
	</table>
	</div>
	<div id="feedback" style="display:<?php if (isset($_GET['type']) AND $_GET['type'] == '0') { echo 'block'; } else { echo 'none'; } ?>">
	<?php
	if (isset($_GET['page_id']) AND $_GET['page_id'] != '') {
		
		$title = get_the_title($_GET['page_id']);
	}
	?>
	<table width="480" border="0" cellspacing="2" cellpadding="2">
	 <form id="feedback" name="feedback" method="post" action="admin-ajax.php">
	  <tr>
		<td colspan="3"><h1>Report Feedback</h1></td>
	  </tr>
      <? if (isset($_GET['msg']) AND $_GET['msg'] != '') { ?>
	  <tr>
		<td colspan="3"><div class="succes"><? echo $_GET['msg']; ?></div></td>
	  </tr>
      <? } ?>
	  <tr>
		<td><b>URL</b></td>
		<td width="2"><b>:</b></td>
		<td><?php if (!isset($_GET['url'])) { echo '-'; } else { echo str_replace('http://', '', $_GET['url']); } ?></td>
	  </tr>
	  <tr>
		<td width="110"><b>Page Title</b></td>
		<td width="2"><b>:</b></td>
		<td width="370"><?php if (!isset($title)) { echo '-'; } else { echo $title; } ?></td>
	  </tr>
	  <tr>
		<td width="100"><b>Name</b></td>
		<td width="2"><b>:</b></td>
		<td width="370"><input type="text" name="titel" id="titel" maxlength="25" class="invoer" /></td>
	  </tr>
	  <tr>
		<td><b>Comments</b></td>
		<td><b>:</b></td>
		<td></td>
	  </tr>
	  <tr>
		<td colspan="3"><textarea name="opmerkingen" id="opmerkingen" rows="15" class="invoer"></textarea></td>
	  </tr>
	  <tr>
		<td colspan="3"><input name="to_do_type" type="hidden" value="0" />
		  <input name="action" type="hidden" value="newfeedback" />
          <input name="page_id" type="hidden" value="<?php if (isset($_GET['page_id'])) { echo $_GET['page_id']; } ?>" />
		  <input name="url" type="hidden" value="<?php echo htmlspecialchars($_GET['url']); ?>" />
		  <input name="uid" type="hidden" value="<?php echo $_SESSION['userinfo']['id']; ?>" />
		  <input type="submit" name="Save" value="save" />&nbsp;
		  <input type="reset" name="button" value="reset" /></td>
	  </tr>
	 </form>
	</table>
	</div>
	<div id="status" style="display:<?php if (isset($_GET['status']) AND $_GET['status'] == '1' AND !isset($_GET['ticket_id']) AND !isset($_GET['to_do_id']) AND !isset($_GET['task_id'])) { echo 'block'; } else { echo 'none'; } ?>">
	<?php
	if (isset($_GET['page_id']) AND $_GET['page_id'] != '') {
		
		$title = ' for:<br /> ' . get_the_title($_GET['page_id']);
		$page_id = $_GET['page_id'];
	} else {
		$page_id = '';
	}
	?>
	<table width="480" border="0" cellspacing="2" cellpadding="2">
	 <form name="search" method="post" action="javascript:get(document.getElementById('search'));">
	  <tr>
		<td><h1>Open To Do's<?php if (isset($title)) { echo $title; } ?></h1></td>
	  </tr>
      <?php /* ?>
	  <tr>
		<td><input type="text" onClick="clickclear(this);" onKeyUp="document.search.submit();" value="Search for New Feature Requests, Tasks &amp; Bugs" name="id" id="id" style="width:335px" class="invoer" />&nbsp;&nbsp;<input type="submit" name="submitbuttontop" value="search"></td>
	  </tr>
	  <?php */ ?>
	 </form>
	</table>
	 <span name="output" id="output">
	  <table width="100%" border="0" cellspacing="2" cellpadding="2"><tr><td><br></td></tr><tr><td><b>Retrieving latest information, one moment please..</b></td></tr></table>
	 </span>
	 <script language="javascript"> 
	 start(<?php echo $page_id; ?>);
	 </script>
	</div>
	<div id="statustodo" style="display:<?php if (isset($_GET['status']) AND $_GET['status'] == '1' AND $_GET['to_do_id'] != '') { echo 'block'; } else { echo 'none'; } ?>">
	<?php
	if (isset($_GET['to_do_id'])) {
		$to_do = tfb_get_to_do_info($_GET['to_do_id']);

		if ($to_do['to_do_type'] == 'task') {
			$type = 'Task';
		} elseif ($to_do['to_do_type'] == 'nfr') {
			$type = 'New Feature Request';
		} else {
			$type = 'Bug';
		}
		?>
	<table width="480" border="0" cellspacing="2" cellpadding="2">
	  <tr>
		<td colspan="3"><h2><?php echo $type; ?> #<?php echo $to_do['to_do_id']; ?></h2><br><h1>'<?php echo $to_do['to_do_name']; ?>'</h1></td>
	  </tr>
	  <tr>
		<td colspan="3">&nbsp;</td>
	  </tr>
	  <tr>
		<td colspan="3" height="5"></td>
	  </tr>
	  <tr>
		<td width="110"><b>Status</b></td>
		<td><b>:</b></td>
		<td width="370"><?php if ($to_do['to_do_status'] == '0') { echo '-'; } else { echo tfb_return_to_do_status($to_do['to_do_status']); } ?></td>
	  </tr>
	  <tr>
		<td><b>Priority</b></td>
		<td><b>:</b></td>
		<td><?php if ($to_do['to_do_priority_id'] == '') { echo '-'; } else { echo tfb_return_to_do_priority($to_do['to_do_priority_id']); } ?></td>
	  </tr>
	  <?php if ($to_do['to_do_page_id'] != '0') { ?>
	  <tr>
		<td><b>Page</b></td>
		<td><b>:</b></td>
		<td><?php echo get_the_title($to_do['to_do_page_id']); ?></td>
	  </tr>
	  <?php } ?>
	  <?php if ($to_do['to_do_severity_id'] != '0') { ?>
	  <tr>
		<td><b>Impact</b></td>
		<td><b>:</b></td>
		<td><?php echo tfb_return_to_do_severity($to_do['to_do_severity_id']); ?></td>
	  </tr>
	  <?php } ?>
	  <?php if ($to_do['to_do_reproducibility_id'] != '0') { ?>
	  <tr>
		<td><b>Reproducibility</b></td>
		<td><b>:</b></td>
		<td><?php echo tfb_return_to_do_reproducibility($to_do['to_do_reproducibility_id']); ?></td>
	  </tr>
	  <?php } ?>
	  <tr>
		<td><b>Made by</b></td>
		<td><b>:</b></td>
		<td><?php echo tfb_return_date($to_do['to_do_date']); ?> by <?php echo $to_do['origin']['name']; ?></td>
	  </tr>
	  <tr>
		<td><b>Comments</b></td>
		<td><b>:</b></td>
		<td></td>
	  </tr>
	  <tr>
		<td colspan="3"><?php if ($to_do['to_do_descr'] == '') { echo '-'; } else { echo nl2br(html_entity_decode($to_do['to_do_descr'])); } ?></td>
	  </tr>
	</table>
		<?
	}
	?>
    </div>
	<div id="contact" style="display:<?php if (isset($_GET['contact']) AND $_GET['contact'] == '1') { echo 'block'; } else { echo 'none'; } ?>">
	<table width="480" border="0" cellspacing="2" cellpadding="2">
	  <tr>
		<td colspan="3"><h1>Contact us</h1></td>
	  </tr>
	  <tr>
		<td colspan="3" height="5"></td>
	  </tr>
	  <tr>
		<td colspan="3"></td>
	  </tr>
	  <?php 
	  if (file_exists(ABSPATH . '/wp-content/plugins/tiny-feedback-bar-by-gekko/custom/company-info.php')) {
	  	if (WEB_COMPANY_PHONE != '') { 
		?>
	  <tr>
		<td width="110"><b>Phone:</b></td>
		<td><b>:</b></td>
		<td width="370"><?php echo WEB_COMPANY_PHONE; ?></td>
	  </tr>
		<?php
	  	}
	  ?>
	  <?php 
	  	if (WEB_COMPANY_EMAIL != '') { 
		?>
	  <tr>
		<td width="110"><b>E-mail:</b></td>
		<td><b>:</b></td>
		<td width="370"><?php echo '<a href="mailto:' . WEB_COMPANY_EMAIL . '" target="_blank">', WEB_COMPANY_EMAIL, '</a>'; ?></td>
	  </tr>
		<?php
	  	}
	  ?>
	  <?php 
	  	if (WEB_COMPANY_DESCRIPTION != '') { 
		?>
	  <tr>
		<td colspan="3"><?php echo nl2br(WEB_COMPANY_DESCRIPTION); ?></td>
	  </tr>
		<?php
	  	}
	  }
	  ?>
	</table>
	</div>
	</body>
	</html>
    <?php
	die();
}

add_action( 'wp_ajax_popup', 'popup');

function new_nfr() {
	
	global $wpdb, $_POST;
	
	//echo 'function new_nfr<br>';
	//die();
	
	//Informatie voorbereiden
	$page_id = esc_html($_POST['page_id']);
	$to_do_name = esc_html($_POST['titel']);
	$to_do_descr = esc_html($_POST['opmerkingen']);
	$to_do_type = 'nfr';
	$to_do_status = '50';
	$current_user = wp_get_current_user();
	
	$wp_user_id = $current_user->data->ID;
	if ($_SESSION['userinfo']['project_id'] != '') {
		$project = ", project_id = '" . $_SESSION['userinfo']['project_id'] . "'";
	}
	$time = time();
	
	//Informatie opslaan in database
	$values = "to_do_name = '$to_do_name', to_do_descr = '$to_do_descr'" . $project . ", to_do_status = '$to_do_status', to_do_page_id = '$page_id', to_do_type = '$to_do_type', to_do_origin_wp_user_id = '$wp_user_id', to_do_origin_method = 'feedback_bar', to_do_origin_date = '$time', to_do_date = '$time', to_do_added = '$time'";
	$query = "INSERT INTO " . $wpdb->prefix . "tfb_to_do SET ". $values;
	
	$sql = $wpdb->query($query);
	$to_do_id = $wpdb->insert_id;
	
	$values = "to_do_id = '$to_do_id', to_do_message_message = '$to_do_descr', wp_user_id = '$wp_user_id', to_do_message_added = '$time'";
	$query = "INSERT INTO " . $wpdb->prefix . "tfb_to_do_messages SET ". $values;
	$sql = $wpdb->query($query);
	
	if ($to_do_id == '') {
		$_SESSION['feedback']['feedback']['error'] = 'Your feedback couldn\'t be stored, contact ' . YOUR_COMPANY . ' at ' . YOUR_TELEPHONE . ' for more information.';
	} else {
		$_SESSION['feedback']['newfeature']['succes'] = 'Saved as #' . $to_do_id . ' - ' . $to_do_name . '';
	}
	
	//Redirect naar popup.php met het juiste tabblad
	header('Location: admin-ajax.php?action=popup&url=' . $_POST['url'] . '&type=1&page_id=' . $page_id . '&msg=' . urlencode('New Feature Request Succesfully saved'));
	die();
}

add_action( 'wp_ajax_newnfr', 'new_nfr');

function new_task() {
	
	global $wpdb, $_POST;
	
	//Informatie voorbereiden
	$page_id = esc_html($_POST['page_id']);
	$to_do_name = esc_html($_POST['titel']);
	$to_do_descr = esc_html($_POST['opmerkingen']);
	$to_do_severity_id = $_POST['to_do_severity_id'];
	$to_do_reproducibility_id = $_POST['to_do_reproducibility_id'];
	$to_do_type = 'task';
	$to_do_status = '50';
	$current_user = wp_get_current_user();
	
	$wp_user_id = $current_user->data->ID;
	$time = time();
	
	//Informatie opslaan in database
	$values = "to_do_name = '$to_do_name', to_do_descr = '$to_do_descr', to_do_status = '$to_do_status', to_do_page_id = '$page_id', to_do_type = '$to_do_type', to_do_origin_wp_user_id = '$wp_user_id', to_do_origin_method = 'feedback_bar', to_do_origin_date = '$time', to_do_date = '$time', to_do_added = '$time'";
	$query = "INSERT INTO " . $wpdb->prefix . "tfb_to_do SET ". $values;
	
	$sql = $wpdb->query($query);
	$to_do_id = $wpdb->insert_id;
	
	$values = "to_do_id = '$to_do_id', to_do_message_message = '$to_do_descr', wp_user_id = '$wp_user_id', to_do_message_added = '$time'";
	$query = "INSERT INTO " . $wpdb->prefix . "tfb_to_do_messages SET ". $values;
	$sql = $wpdb->query($query);
	
	if ($to_do_id == '') {
		$_SESSION['feedback']['feedback']['error'] = 'Your feedback couldn\'t be stored, contact ' . YOUR_COMPANY . ' at ' . YOUR_TELEPHONE . ' for more information.';
	} else {
		$_SESSION['feedback']['task']['succes'] = 'Saved as #' . $to_do_id . ' - ' . $to_do_name . '';
	}
	
	//Redirect naar popup.php met het juiste tabblad
	header('Location: admin-ajax.php?action=popup&url=' . $_POST['url'] . '&type=3&page_id=' . $page_id . '&msg=' . urlencode('Task succesfully saved'));
	exit();
}

add_action( 'wp_ajax_newtask', 'new_task');

function new_bug() {
	
	global $wpdb, $_POST;
	
	//Informatie voorbereiden
	$page_id = esc_html($_POST['page_id']);
	$to_do_name = esc_html($_POST['titel']);
	$to_do_descr = esc_html($_POST['opmerkingen']);
	$to_do_severity_id = $_POST['to_do_severity_id'];
	$to_do_reproducibility_id = $_POST['to_do_reproducibility_id'];
	$to_do_type = 'bug';
	$to_do_status = '50';
	$project_id = $_SESSION['userinfo']['project_id'];
	
	$current_user = wp_get_current_user();
	
	$wp_user_id = $current_user->data->ID;
	
	$time = time();
	
	//Informatie opslaan in database
	$values = "to_do_name = '$to_do_name', to_do_descr = '$to_do_descr', to_do_status = '$to_do_status', to_do_page_id = '$page_id', to_do_type = '$to_do_type', to_do_severity_id = '$to_do_severity_id', to_do_reproducibility_id = '$to_do_reproducibility_id', to_do_origin_wp_user_id = '$wp_user_id', to_do_origin_method = 'feedback_bar', to_do_origin_date = '$time', to_do_date = '$time', to_do_added = '$time'";
	$query = "INSERT INTO " . $wpdb->prefix . "tfb_to_do SET ". $values;
	
	$sql = $wpdb->query($query);
	$to_do_id = $wpdb->insert_id;
	
	$values = "to_do_id = '$to_do_id', to_do_message_message = '$to_do_descr', wp_user_id = '$wp_user_id', to_do_message_added = '$time'";
	$query = "INSERT INTO " . $wpdb->prefix . "tfb_to_do_messages SET ". $values;
	$sql = $wpdb->query($query);
	
	
	if ($to_do_id == '') {
		$_SESSION['feedback']['feedback']['error'] = 'Your feedback couldn\'t be stored, contact ' . YOUR_COMPANY . ' at ' . YOUR_TELEPHONE . ' for more information.';
	} else {
		$_SESSION['feedback']['to_do']['succes'] = 'Saved as #' . $to_do_id . ' - ' . $to_do_name . '';
	}
	
	//Redirect naar popup.php met het juiste tabblad
	header('Location: admin-ajax.php?action=popup&url=' . $_POST['url'] . '&type=2&page_id=' . $page_id . '&msg=' . urlencode('Bug succesfully saved'));
	exit();
}

add_action( 'wp_ajax_newbug', 'new_bug');