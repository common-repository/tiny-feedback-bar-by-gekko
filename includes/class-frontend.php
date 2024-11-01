<?php 

class TFB_Frontend {

	function __construct() {
		
		$options = get_tfb_options();

		add_action( 'wp_head', array(&$this, 'head'), 1, 1 );
		add_action( 'wp_footer', array(&$this, 'footer'), 1, 1 );
		add_action( 'wp_enqueue_scripts', 'tfb_enqueue_scripts');
	}
	
	function multisite_body_classes($classes) {
		$classes[] = 'context-menu-one';
		return $classes;
	}
	
	function head() {
		$options = get_tfb_options();

		global $wp_query, $tfb_page_on_front;
		
		if ( 'posts' == get_option( 'show_on_front') && $wp_query->is_home() ) {
			
			$options = get_option('tfb');
			if ($options['tfb_homepage_page_id'] != '') {
				$tfb_page_id = $options['tfb_homepage_page_id'];
			} else {

			}
		} elseif ( 'page' == get_option( 'show_on_front') && get_option( 'page_on_front' ) && $wp_query->is_page( get_option( 'page_on_front' ) ) ) {
			$tfb_page_id = get_the_ID();
		} else {
			$tfb_page_id = get_the_ID();
		}
		
		$this->tfb_page_id = $tfb_page_id;
		
		if ( is_user_logged_in() AND $this->check_ip_filter()) {
			echo '<script type="text/javascript">
function tfb_ajax(todo, target, pageid, type, w, x, y) {
	
	var target = \'#\' + target;
	var values = \'?action=\' + todo;
	if (pageid != \'\') {
		values = values + \'&page_id=\' + pageid;
	}
	if (type != \'\') {
		values = values + \'&type=\' + type;
	}
	if (w != \'\') {
		values = values + \'&w=\' + w;
	}
	if (x != \'\') {
		values = values + \'&x=\' + x;
	}
	if (y != \'\') {
		values = values + \'&y=\' + y;
	}
	var mark_url = "', get_bloginfo('wpurl'), '/wp-admin/admin-ajax.php" + values;
	jQuery.ajax({
		type: "get", url: mark_url,
		success: function(html){
			jQuery(target).html(html);
		}
	});
}
function tfb_exec(set, page_id) {
	
	var set;
	var page_id;
	var width;
	var height;
	var left;
	var top;
	var url;
	var go_to;
	
	width = 500;
	height = 600;
	url = location.href;
	left = ((window.screenX||window.screenLeft) + 5);
	top = ((window.screenY||window.screenTop) + 5);
	
	if (set == 1) {
		//go_to = u + \'/popup.php?url=\' + url + \'&type=0&page_id=\' + page_id;
		go_to = \'', get_bloginfo('wpurl'), '/wp-admin/admin-ajax.php?action=popup&url=\' + url + \'&type=0&page_id=\' + page_id;
	} else if (set == 2) { //New Feature Request
		//go_to = u + \'/popup.php?url=\' + url + \'&type=1&page_id=\' + page_id;
		go_to = \'', get_bloginfo('wpurl'), '/wp-admin/admin-ajax.php?action=popup&url=\' + url + \'&type=1&page_id=\' + page_id;
	} else if (set == 3) { //Create Task
		//go_to = u + \'/popup.php?url=\' + url + \'&type=3&page_id=\' + page_id;
		go_to = \'', get_bloginfo('wpurl'), '/wp-admin/admin-ajax.php?action=popup&url=\' + url + \'&type=3&page_id=\' + page_id;
	} else if (set == 4) { // Report Bug
		//go_to = u + \'/popup.php?url=\' + url + \'&type=2&page_id=\' + page_id;
		go_to = \'', get_bloginfo('wpurl'), '/wp-admin/admin-ajax.php?action=popup&url=\' + url + \'&type=2&page_id=\' + page_id;
	} else if (set == 5) {
		//go_to = u + \'/popup.php?url=\' + url + \'&status=1&page_id=\' + page_id;
		go_to = \'', get_bloginfo('wpurl'), '/wp-admin/admin-ajax.php?action=popup&url=\' + url + \'&status=1&page_id=\' + page_id;
	} else if (set == 6) {
		//go_to = u + \'/popup.php?url=\' + url + \'&status=1&page_id=\' + page_id;
		go_to = \'', get_bloginfo('wpurl'), '/wp-admin/admin-ajax.php?action=popup&url=\' + url + \'&status=1&page_id=\' + page_id;
	}
	
	window.open(go_to,null,\'toolbar=no,location=no,menubar=no,resizable=no,scrollbars=yes,width=\' + width + \',height=\' + height + \',left=\' + left + \',top=\' + top + \'\');
}
</script>';
		}
	}
	
	function check_ip_filter() {
		
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
	
	function footer() {
		$options = get_tfb_options();

		global $wp_query;
		
		if ( is_user_logged_in() AND $this->check_ip_filter()) {
			
			if ($this->tfb_page_id == '') {
				$this->tfb_page_id = '0';
			}
			
			$options = get_option('tfb');
			
			echo '<!-- Tiny Feedback Bar begin -->
';
			
			if ($options['tfb_enable_point_click'] == 'yes') {
				$markings = '';
			
				echo '
<link href="', plugin_dir_url(TFB_PLUGIN_NAME), TFB_PLUGIN_NAME, '/skin/jquery.contextMenu.css" rel="stylesheet" type="text/css">
';
			}
			
			echo '
<link href="', plugin_dir_url(TFB_PLUGIN_NAME), TFB_PLUGIN_NAME, '/skin/tiny-feedback-bar.css" rel="stylesheet" type="text/css">
<link href="', plugin_dir_url(TFB_PLUGIN_NAME), TFB_PLUGIN_NAME, '/custom/style.css" rel="stylesheet" type="text/css">
';
			
			if ($options['tfb_enable_point_click'] == 'yes') {
				echo '
<script language="javascript">
function tfb_markthespot(type) {
	
	var w = document.getElementById(\'tfbw\').value;
	var x = document.getElementById(\'tfbx\').value - 20;
	var y = document.getElementById(\'tfby\').value - 10;
	
	var tfbx = x - (w / 2);
	
	tfb_ajax(\'mark\', \'tfbmarkings\', ', $this->tfb_page_id, ', type, w, tfbx, y);
}
function tfb_get_markings() {
	var w = document.getElementById(\'tfbw\').value;
	tfb_ajax(\'get_markings\', \'tfbmarkings\', ', $this->tfb_page_id, ', \'\', w, \'\', \'\');
}

jQuery(function(){
    
	jQuery("body").addClass("context-menu-one");
	
	jQuery.contextMenu({
        selector: \'.context-menu-one\', 
        callback: function(key, options) {
    		tfbProcess(key);
        },
        items: {
            "task": {name: "Create Task", icon: "tfb-task-icon"},
            "bug": {name: "Report Bug", icon: "tfb-bug-icon"},
            "nfr": {name: "New Feature Request", icon: "tfb-nfr-icon"}
        }
    });
	
	jQuery(\'a.cluetip\').cluetip({cluetipClass: \'cluetip-rounded\', dropShadow: false, activation: \'click\'});
});

jQuery(document).ready(function() {
	jQuery(\'a.cluetip\').cluetip();
	jQuery(\'#tfbw\').val(jQuery(window).width());
	tfb_get_markings();
});
</script>
<div name="tfbmarkings" id="tfbmarkings" style="position:absolute;left:0px;top:0px;">', $markings, '</div>
';
			}
			
			echo '
<script language="javascript">tfb_build(\'', get_bloginfo('wpurl'), '/wp-content/plugins/tiny-feedback-bar-by-gekko\',\'', $this->tfb_page_id, '\');</script>
<div class="tfb_bottom_space"></div>
<!-- Tiny Feedback Bar end -->';
		}
	}
}

$tfb_front = new TFB_Frontend;
