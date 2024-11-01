<?php

/**
 * Backend Class for use in all Yoast plugins
 * Version 0.2.1
 */

if ( !class_exists('TFB_Plugin_Admin') ) {
	class TFB_Plugin_Admin {

		var $hook 		= '';
		var $filename	= '';
		var $longname	= '';
		var $shortname	= '';
		var $ozhicon	= '';
		var $optionname = '';
		var $homepage	= '';
		var $feed		= 'http://www.tinyfeedbackbar.com/feed/';
		var $accesslvl	= 'manage_options';
		
		function __construct() {
		}
		
		function add_ozh_adminmenu_icon( $hook ) {
			if ($hook == $this->hook) 
				return TFB_URL.$this->ozhicon;
			return $hook;
		}
		
		function config_page_styles() {
			global $pagenow;
			if ( $pagenow == 'admin.php' && isset($_GET['page']) && in_array($_GET['page'], $this->adminpages) ) {
				wp_enqueue_style('dashboard');
				wp_enqueue_style('thickbox');
				wp_enqueue_style('global');
				wp_enqueue_style('wp-admin');
				wp_enqueue_style('tfb-admin-css', TFB_URL . 'skin/tfb_plugin_tools.css');
			}
		}

		function register_network_settings_page() {
			add_menu_page($this->longname, $this->shortname, 'delete_users', 'tfb_feedback', array(&$this,'network_config_page'), TFB_URL.'skin/images/tfb-icon.png');
		}
		
		function plugin_options_url() {
			return admin_url( 'admin.php?page=tfb-settings' );
		}
		
		/**
		 * Add a link to the settings page to the plugins list
		 */
		function add_action_link( $links, $file ) {
			static $this_plugin;
			if( empty($this_plugin) ) $this_plugin = $this->filename;
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Settings', 'tiny-feedback-bar-by-gekko' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}
		
		/**
		 * Create a Text input field
		 */
		function textinput($id, $label, $name, $value = '') {
			
			if (isset($value))
				$val = htmlspecialchars($value);
			
			return '<label class="textinput" for="'.$id.'">'.$label.':</label><input class="textinput" type="text" id="'.$id.'" name="'.$name.'" value="'.$val.'"/>' . '<br class="clear" />';
		}
		
		/**
		 * Create a small textarea
		 */
		function textarea($id, $label, $name, $value = '', $style = '') {
			
			if (isset($value))
				$val = esc_html($value);
			
			if ($style != '') {
				$style = 'style="' . $style . '"';
			} else {
				$style = 'style="height:100px;"';
			}
			
			return '<label class="textinput" for="'.$id.'">'.$label.':</label><textarea class="textinput" id="'.$id.'" name="'.$name.'"' . $style . '>' . $val . '</textarea>' . '<br class="clear" />';
		}
		
		/**
		 * Create a Select Box
		 */
		function select($id, $label, $name, $selected_value, $values) {
			
			$output = '<label class="select" for="'.$id.'">'.$label.':</label>';
			$output .= '<select class="select" name="'.$name.'" id="'.$id.'">';
			
			if (is_array($values)) {
				foreach($values as $value) {
					$sel = '';
					if (isset($selected_value) && $selected_value == $value['id'])
						$sel = 'selected="selected" ';
	
					if (!empty($value['text']))
						$output .= '<option '.$sel.'value="'.$value['id'].'">'.$value['text'].'</option>';
				}
			}
			$output .= '</select>';
			return $output . '<br class="clear"/>';
		}

		/**
		 * Create a postbox widget
		 */
		function postbox($id, $title, $content) {
		?>
			<div id="<?php echo $id; ?>" class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span><?php echo $title; ?></span></h3>
				<div class="inside">
					<?php echo $content; ?>
				</div>
			</div>
		<?php
		}


		/**
		 * Create a form table from an array of rows
		 */
		function form_table($rows) {
			$content = '<table class="form-table">';
			if (is_array($rows)) {
				foreach ($rows as $row) {
					$content .= '<tr><th valign="top" scrope="row">';
					if (isset($row['id']) && $row['id'] != '')
						$content .= '<label for="'.$row['id'].'">'.$row['label'].':</label>';
					else
						$content .= $row['label'];
					if (isset($row['desc']) && $row['desc'] != '')
						$content .= '<br/><small>'.$row['desc'].'</small>';
					$content .= '</th><td valign="top">';
					$content .= $row['content'];
					$content .= '</td></tr>'; 
				}
			}
			$content .= '</table>';
			return $content;
		}

		function text_limit( $text, $limit, $finish = '&hellip;') {
			if( strlen( $text ) > $limit ) {
		    	$text = substr( $text, 0, $limit );
				$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
				$text .= $finish;
			}
			return $text;
		}

		function fetch_rss_items( $num ) {
			include_once(ABSPATH . WPINC . '/feed.php');
			$rss = fetch_feed( $this->feed );
			
			// Bail if feed doesn't work
			if ( is_wp_error($rss) )
				return false;
			
			$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );
			
			// If the feed was erroneously 
			if ( !$rss_items ) {
				$md5 = md5( $this->feed );
				delete_transient( 'feed_' . $md5 );
				delete_transient( 'feed_mod_' . $md5 );
				$rss = fetch_feed( $this->feed );
				$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );
			}
			
			return $rss_items;
		}
		
		/**
		 * Box with latest news from Yoast.com for sidebar
		 */
		function news() {
			$rss_items = $this->fetch_rss_items( 5 );
			
			$content = '<ul>';
			if ( !$rss_items ) {
			    $content .= '<li class="tfb">'.__( 'No news items, feed might be broken...', 'tfb' ).'</li>';
			} else {
				if (is_array($rss_items)) {
				    foreach ( $rss_items as $item ) {
						$url = preg_replace( '/#.*/', '', esc_url( $item->get_permalink(), $protocolls=null, 'display' ) );
						$content .= '<li class="tfb">';
						$content .= '<a class="rsswidget" href="'.$url.'#utm_source=wpadmin&utm_medium=sidebarwidget&utm_term=newsitem&utm_campaign=wptfbplugin" target="_blank">'. esc_html( $item->get_title() ) .'</a> ';
						$content .= '</li>';
					}
				}
			}						
			$content .= '</ul>';
			$this->postbox('tfb_news', __( 'Follow the developments', 'tfb_news' ), $content);
		}

		function widget_setup() {
			$network = '';
			if ( function_exists('is_network_admin') && is_network_admin() )
				$network = '_network';

			$options = get_option('tfb_tfbdbwidget');
			//if ( !isset($options['removedbwidget'.$network]) || !$options['removedbwidget'.$network] )
	    		//wp_add_dashboard_widget( 'tfb_db_widget' , __( 'Laatste nieuws', 'tiny-feedback-bar-by-gekko' ) , array(&$this, 'db_widget') );
		}
		
		function widget_order( $arr ) {
			global $wp_meta_boxes;
			if ( function_exists('is_network_admin') && is_network_admin() ) {
				$plugins = $wp_meta_boxes['dashboard-network']['normal']['core']['dashboard_plugins'];
				unset($wp_meta_boxes['dashboard-network']['normal']['core']['dashboard_plugins']);
				$wp_meta_boxes['dashboard-network']['normal']['core'][] = $plugins;
			} else if ( is_admin() ) {
				if ( isset($wp_meta_boxes['dashboard']['normal']['core']['tfb_db_widget']) ) {
					$tfb_db_widget = $wp_meta_boxes['dashboard']['normal']['core']['tfb_db_widget'];
					unset($wp_meta_boxes['dashboard']['normal']['core']['tfb_db_widget']);
					if ( isset($wp_meta_boxes['dashboard']['side']['core']) ) {
						$begin = array_slice($wp_meta_boxes['dashboard']['side']['core'], 0, 1);
						$end = array_slice($wp_meta_boxes['dashboard']['side']['core'], 1, 5);
						$wp_meta_boxes['dashboard']['side']['core'] = $begin;
						$wp_meta_boxes['dashboard']['side']['core'][] = $tfb_db_widget;
						$wp_meta_boxes['dashboard']['side']['core'] += $end;
					} else {
						$wp_meta_boxes['dashboard']['side']['core'] = array();
						$wp_meta_boxes['dashboard']['side']['core'][] = $tfb_db_widget;
					}
				} 
			}
			return $arr;
		}
	}
}

