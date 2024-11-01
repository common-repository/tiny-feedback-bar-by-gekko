<?php
if ( ! class_exists( 'TFB_Admin' ) ) {
	
	class TFB_Admin extends TFB_Plugin_Admin {

		var $hook 			= 'tiny-feedback-bar-by-gekko';
		var $filename		= 'tiny-feedback-bar-by-gekko/tiny-feedback-bar-by-gekko.php';
		var $longname;
		var $shortname;
		var $currentoption 	= 'tfb';
		var $ozhicon		= 'tag.png';
		
		function TFB_Admin() {
			$this->longname = __( 'Tiny Feedback Bar Settings', 'tiny-feedback-bar-by-gekko' );
			$this->shortname = __( 'TFB', 'tiny-feedback-bar-by-gekko' );
			//$this->multisite_defaults();
			add_action( 'init', array(&$this, 'init') );
		}
		
		function init() {
			
			add_action('admin_menu', array(&$this,'tfb_menu_pages'));
			$plugin = plugin_basename( __FILE__ );
			
			if ( is_admin() ) {
				wp_enqueue_style('tfb-admin-css', TFB_URL . 'skin/tfb_plugin_tools_admin.css');
			} else {
				wp_enqueue_style('tfb-admin-css', TFB_URL . 'skin/tfb_plugin_tools.css');
			}
			
			add_filter( 'plugin_action_links', array(&$this, 'add_action_link'), 10, 2 );
			
			add_action( 'wp_dashboard_setup', array(&$this,'widget_setup'));	
			add_action( 'wp_network_dashboard_setup', array(&$this,'widget_setup'));	
			add_filter( 'wp_dashboard_widgets', array(&$this, 'widget_order'));
			add_filter( 'wp_network_dashboard_widgets', array(&$this, 'widget_order'));
		}
		
		function tfb_install() {
			
			tfb_create_database_table();
		}

		function tfb_menu_pages() {
			
			$capability = 'manage_options';
			
			// Add the top-level admin menu
			$page_title = 'Tiny Feedback Bar Settings';
			$menu_title = 'Tiny Feedback';
			$menu_slug = 'tfb-feedback';
			$function = array($this, 'tfb_feedback');
			$icon = TFB_URL.'skin/images/tfb-icon.png';
			add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon);
		
			// Feedback-page
			$submenu_page_title = 'Feedback';
			$submenu_title = 'Feedback';
			$submenu_slug = 'tfb-feedback';
			$submenu_function = array($this, 'tfb_feedback');
			add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
			
			// Settings-page
			$submenu_page_title = 'Settings';
			$submenu_title = 'Settings';
			$submenu_slug = 'tfb-settings';
			$submenu_function = array($this, 'tfb_settings');
			add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
			
			// About-page
			$submenu_page_title = 'About';
			$submenu_title = 'About';
			$submenu_slug = 'tfb-about';
			$submenu_function = array($this, 'tfb_about');
			add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
			
			// Help-page
			$submenu_page_title = 'Tiny Feedback Bar Help';
			$submenu_title = 'Help';
			$submenu_slug = 'tfb-help';
			$submenu_function = array($this, 'tfb_help');
			add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
			
			// Report Back-page
			$submenu_page_title = 'Report Back to us';
			$submenu_title = 'Report Back';
			$submenu_slug = 'tfb-report-back';
			$submenu_function = array($this, 'tfb_report_back');
			add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
		}
		
		function tfb_plugin_action_links($links, $file) {
			static $this_plugin;
		
			if (!$this_plugin) {
				$this_plugin = plugin_basename(__FILE__);
			}
		
			if ($file == $this_plugin) {
				// The "page" query string value must be equal to the slug
				// of the Settings admin page we defined earlier, which in
				// this case equals "myplugin-settings".
				$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=tfb-settings">Settings</a>';
				array_unshift($links, $settings_link);
			}
		
			return $links;
		}
		
		function tfb_settings() {
			if (!current_user_can('manage_options')) {
				wp_die('You do not have sufficient permissions to access this page.');
			}
		
			// Here is where you could start displaying the HTML needed for the settings
			// page, or you could include a file that handles the HTML output for you.
			
			if (isset($_POST['action']) AND $_POST['action'] == 'edit') {

				$options['tfb_enable_point_click'] = $_POST['tfb_enable_point_click'];
				$options['tfb_ip_filter'] = $_POST['tfb_ip_filter'];
				$options['tfb_homepage_page_id'] = $_POST['tfb_homepage_page_id'];
				update_option( 'tfb', $options );
			}
			
			echo '<div class="wrap">';
			
			echo $this->to_do_settings_page();
		}
		
		function tfb_about() {
		
			// Here is where you could start displaying the HTML needed for the settings
			// page, or you could include a file that handles the HTML output for you.
			
			echo '<div class="wrap">';
			
			echo $this->to_do_about_page();
		}

		function custom_bulk_admin_notices() {
		
			global $post_type, $pagenow;
			
			if($pagenow == 'edit.php' && $post_type == 'post' && isset($_REQUEST['exported']) && (int) $_REQUEST['exported']) {
				
				$message = sprintf( _n( 'Post exported.', '%s posts exported.', $_REQUEST['exported'] ), number_format_i18n( $_REQUEST['exported'] ) );
				
				echo "
				{$message}
				
				";
			}
		}
		
		function tfb_feedback() {
			
			global $wpdb, $_GET, $_POST, $delta_config;
			
			if (!current_user_can('manage_options')) {
				wp_die('You do not have sufficient permissions to access this page.');
			}
			
			if (isset($_GET['action']) AND $_GET['action'] == 'edit' AND $_GET['to_do_id'] != '') {
				
				echo '<div class="wrap">';
				echo $this->to_do_edit_page($_GET['to_do_id']);
				
			} else {
				
				$this->admin_header('Feedback', true, true, 'tfb_feedback', 'tfb_feedback');
				
				if (is_array($_GET)) {
					
					$gets = '';
					$gets_url = '';
					foreach($_GET as $key => $value) {
						if ($key != 'filter_status' AND $key != 'page') {
							$gets .= '<input name="' . $key . '" type="hidden" value="' . $value . '" />';
							$gets_url .= '&' . $key . '=' . $value;
						}
					}
				}
				
				$types = '<ul class="subsubsub">';
				if (isset($_GET['filter_status']) AND $_GET['filter_status'] == '') {
					$class = ' class="current"';
				} else {
					$class = '';
				}
				$query_to_do = "SELECT COUNT(*) as total FROM " . $wpdb->prefix . "tfb_to_do 
									WHERE to_do_status != '300'
										AND to_do_status != '400'
										AND to_do_status != '500'";
				$sql_rows = $wpdb->get_results($query_to_do);
				$total = (array)$sql_rows[0];
				
				$types .= '<li><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=tfb-feedback&filter_status="' . $class . '>All open items <span class="count">(' . $total['total'] . ')</span></a>' . '&nbsp;|&nbsp;</li>';
				foreach($delta_config['to_do']['status'] as $status) {
					
					if (isset($_GET['filter_status']) AND $status['id'] == $_GET['filter_status']) {
						$selected = ' selected';
						$class = ' class="current"';
					} else {
						$selected = '';
						$class = '';
					}
					
					$query_to_do = "SELECT COUNT(*) as total FROM " . $wpdb->prefix . "tfb_to_do 
										WHERE to_do_status = '" . $status['id'] . "'";
					$sql_rows = $wpdb->get_results($query_to_do);
					$total = (array)$sql_rows[0];
					
					$types .= '<li><a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=tfb-feedback&filter_status=' . $status['id'] . '"' . $class . '>' . $status['text'] . ' <span class="count">(' . $total['total'] . ')</span></a>' . '&nbsp;|&nbsp;</li>';
				}
				
				$types = substr($types, 0, strlen($types)-18) . '</li></ul>';
				echo $types;
				
				echo '<div class="wrap">';
				$feedbackTable = new TFB_Feedback_Table();
				$feedbackTable->prepare_items();
				$feedbackTable->display();
			}
		}
		
		function tfb_report_back() {
			
			if (!current_user_can('manage_options')) {
				wp_die('You do not have sufficient permissions to access this page.');
			}
			
			echo '<div class="wrap">';
			
			if (isset($_POST['action']) AND $_POST['action'] == 'send') {
				
				//SEND MAIL
				$to = 'Tiny Feedback Bar - Support <info@tinyfeedbackbar.com>';
				$headers = 'From: Tiny Feedback Bar - Support <info@tinyfeedbackbar.com>' . "\r\n";
				if (isset($_POST['from_email']) AND $_POST['from_email'] != '') {
					$headers .= 'Cc: ' . $_POST['from_name'] . ' <' . $_POST['from_email'] . '>';
				}
				
				$subject = 'Report Back to Tiny Feedback Bar';
				$message = 'Your name: ' . $_POST['from_name'] . '
Your e-mail: ' . $_POST['from_email'] . '
Feedback type: ' . $_POST['feedback_type'] . '
Description: ' . nl2br($_POST['description']) . '
Happyness today: ' . $_POST['happyness'] . '';
				wp_mail( $to, $subject, $message, $headers );
				
				add_settings_error('tfb_report_back','success', __('Your feedback has been sent') , 'updated');
				
			}
				
			echo $this->to_do_report_back_page();
		}
		
		function tfb_help() {
			
			if (!current_user_can('manage_options')) {
				wp_die('You do not have sufficient permissions to access this page.');
			}
			
			if (isset($_POST['action']) AND $_POST['action'] == 'edit') {

				$options['tfb_ip_filter'] = $_POST['tfb_ip_filter'];
				update_option( 'tfb', $options );
			}
			
			echo '<div class="wrap">';
			
			echo $this->to_do_help_page();
		}

		function to_do_settings_page() {
			
			global $wpdb, $TFB_Admin, $delta_config, $_POST;
			
			$options = get_option('tfb');
			
			$this->admin_header('Settings', true, true, 'tfb_settings', 'tfb_settings');
			
			$content_now = '';
			
			$content_now .= '<form action="" method="post" id="tfb-settings">';
			
			$content_now .= '<input name="action" type="hidden" value="edit" />';
			
			echo $content_now;
			
			$content = '';
			
			$content .= '<span class="text5"><b>Use Point &amp; Click?</b><br />Point &amp; Click enables you and your customer to litterally point to a certain position in any page and add a new Tast, Bug or New Feature Request. In some cases Tiny Feedback Bar might not be able to operate due to a combination of different plugins. If that\'s the case you can disable Point &amp; Click for this site. Customers will still be able to enter new Tasks, etc. They can use the Bar itself for that.</span><br class="clear" />';
			
			$choices = array();
			$choices[] = array('id' => 'yes', 'text' => 'Yes, enable');
			$choices[] = array('id' => 'no', 'text' => 'No, disable');
			
			$content .= $this->select('tfb_enable_point_click', 'Enable Point &amp; Click', 'tfb_enable_point_click', $options['tfb_enable_point_click'], $choices);
			
			$content .= '<span class="text5"><b>Option: only show Tiny Feedback Bar when viewed from specific ip-addresses</b><br />Want to make Tiny Feedback Bar visible from certain ip-addresses, than write down these ip-addresses. Seperate ip-addresses by a semi-colon (;).</span><br class="clear" />';
			
			$content .= $this->textinput('tfb_ip_filter', 'IP-filter', 'tfb_ip_filter', $options['tfb_ip_filter']);
			
			$content .= '<br /><span class="text5"><b>Display posts on homepage?</b><br />Do you display posts on your homepage, than select the homepage in this selectbox. Otherwise Tiny Feedback Bar won\'t be able to tell if you\'re watching the homepage or one of the posts.</span><br class="clear" />';
			
			$args = array(
				'sort_order' => 'ASC',
				'sort_column' => 'menu_order',
				'hierarchical' => 1,
				'exclude' => '',
				'include' => '',
				'meta_key' => '',
				'meta_value' => '',
				'authors' => '',
				'child_of' => 0,
				'parent' => 0,
				'exclude_tree' => '',
				'number' => '',
				'offset' => 0,
				'post_type' => 'page',
				'post_status' => 'publish'
			); 
			$all_pages = get_pages($args); 
			
			if (is_array($all_pages) AND count($all_pages)) {
				
				$pages[] = array('id' => '', 'text' => 'Which is the homepage?');
				foreach($all_pages as $page) {
					
					$pages[] = array('id' => $page->ID, 'text' => get_the_title($page->ID));
				}
				
				$content .= $this->select('tfb_homepage_page_id', 'Homepage', 'tfb_homepage_page_id', $options['tfb_homepage_page_id'], $pages);
			} else {
				
				$content .= 'First: create some pages. Then select your homepage in this box.';
			}
			
			$this->postbox('titleshelp',__('Settings', 'feedback-settings'), $content);
			
			echo '<div class="submit"><input type="submit" class="button-primary" name="submit" value="Save settings" /></div>';
			
			echo '</form>
						</div>
					</div>
				</div>
			</div>
			';
			
			$this->settings_sidebar();
		}

		function to_do_edit_page($to_do_id) {
			
			global $wpdb, $TFB_Admin, $delta_config, $_POST;
			
			//FEEDBACK INFORMATION
			$query_to_do = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do 
								WHERE to_do_id = '" . $to_do_id . "'
								LIMIT 1";
			$sql_rows = $wpdb->get_results($query_to_do);
			$to_do = (array)$sql_rows[0];
			
			$this->admin_header('Feedback edit', true, true, 'tfb_feedback', 'tfb_feedback');
			
			$content_now = '';
			
			$content_now .= '<form action="" method="post" id="tfb-edit">';
			
			$content_now .= '<input name="action" type="hidden" value="edit" />';
			$content_now .= '<input name="to_do_id" type="hidden" value="' . $to_do_id . '" />';
			
			//Current values
			$content_now .= '<input name="to_do_type" type="hidden" value="' . $to_do['to_do_type'] . '" />';
			$content_now .= '<input name="to_do_name" type="hidden" value="' . $to_do['to_do_name'] . '" />';
			if ($to_do['to_do_type'] == 'bug') {
				$content_now .= '<input name="to_do_severity_id" type="hidden" value="' . $to_do['to_do_severity_id'] . '" />';
				$content_now .= '<input name="to_do_reproducibility_id" type="hidden" value="' . $to_do['to_do_reproducibility_id'] . '" />';
			}
			
			echo $content_now;
			
			if ($to_do['to_do_type'] == 'bug') {
				$type = 'Bug';
			} elseif ($to_do['to_do_type'] == 'task') {
				$type = 'Task';
			} elseif ($to_do['to_do_type'] == 'nfr') {
				$type = 'New Feature Request';
			}
			
			$content = '';
			$content .= '<label class="text">Type:</label><span class="text5">' . $type . '</span><br class="clear" />';
			
			$date = tfb_return_date($to_do['to_do_added']);
			$content .= '<label class="text">Date made:</label><span class="text5">' . $date . '</span><br class="clear" />';
			
			if ($to_do['to_do_origin_wp_user_id'] != '0') {
				$user_info = get_userdata($to_do['to_do_origin_wp_user_id']);
				
				$content .= '<label class="text">Made by:</label><span class="text5"><a href="profile.php?user_id=' . $user_info->data->ID . '">' . $user_info->data->user_nicename . '</a></span><br class="clear" />';
			}
			
			if ($to_do['to_do_page_id'] != '0') {
				
				$page = get_page($to_do['to_do_page_id'], 'ARRAY_A');

				$permalink = get_permalink( $to_do['to_do_page_id'] );
				$title = $page['post_title'];
				$url_begin = '<a href="' . $permalink . '" target="_blank">';
				$url_end = '</a>';
			} else {
				$title = '-';
				$url_begin = '';
				$url_end = '';
			}
			$content .= '<label class="text">URL:</label><span class="text5">' . $url_begin . '' . $title . $url_end . '</span><br class="clear" />';
			
			$status = tfb_return_to_do_status($to_do['to_do_status']);
			$content .= '<label class="text">Current status:</label><span class="text5">' . $status . '</span><br class="clear" />';
			
			$content .= $this->textinput('to_do_name', 'Name', 'to_do_name_new', $to_do['to_do_name']);
			
			if ($to_do['to_do_type'] == 'bug') {
				$content .= $this->select('to_do_severity_id', 'Impact', 'to_do_severity_id_new', $to_do['to_do_severity_id'], $delta_config['to_do']['severity']);
				
				$content .= $this->select('to_do_reproducibility_id', 'Reproducibility', 'to_do_reproducibility_id_new', $to_do['to_do_reproducibility_id'], $delta_config['to_do']['reproducibility']);
			}
			
			$content .= '<br class="clear"/>';
			
			$this->postbox('titleshelp',__('Feedback information', 'feedback-info'), $content);
			
			//CONVERSATIE
			
			$content = '';
			$content .= $this->textarea('to_do_message_message_new', 'Write an update', 'to_do_message_message_new', '');
			$content .= $this->select('to_do_status_new', 'Option: new status', 'to_do_status_new', $to_do['to_do_status'], $delta_config['to_do']['status']);
					
			$query_to_do_messages = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do_messages 
								WHERE to_do_id = '" . $to_do_id . "'
								ORDER BY to_do_message_added DESC";
			$sql_rows = $wpdb->get_results($query_to_do_messages);
			$to_do_messages = (array)$sql_rows;
			
			$teller = 1;
			if (is_array($to_do_messages) AND count($to_do_messages)) {
				foreach($to_do_messages as $object) {
					
					$message = (array)$object;
					
					$user_info = get_userdata($message['wp_user_id']);
					
					if ($message['to_do_message_message'] != '' OR $teller < count($to_do_messages)) {
					
						if ($message['to_do_message_message'] == '') {
							$message['to_do_message_message'] = '-';
						}
					
						$content .= '<label class="text"><b>' . $user_info->data->user_nicename . ' - ' . tfb_return_date($message['to_do_message_added']) . '</b></label><span class="text5">' . nl2br($message['to_do_message_message']) . '</span><br class="clear" />';
					}
					$teller = $teller + 1;
				}
			}
			
			$this->postbox('titleshelp',__('Conversation', 'feedback-conv'), $content);
			
			echo '<div class="submit"><input type="submit" class="button-primary" name="submit" value="Save settings" /></div>';
			
			echo '</form>
						</div>
					</div>
				</div>
			</div>
			';
			
			$this->edit_sidebar();
		}

		function to_do_report_back_page() {
			
			if (!current_user_can('manage_options')) {
				wp_die('You do not have sufficient permissions to access this page.');
			}
			
			$this->admin_header('Report back to us', true, true, 'tfb_report_back', 'tfb_report_back');
			
			// Render the HTML for the Help page or include a file that does
			$content = 'What do you think we can do to let Tiny Feedback Bar grow mature? Please provide us with clear and complete information about the things you want us to change.<br />
			<br />
			Many thanks your input!<br />
			<br />';
			
			$content .= '<form action="" method="post" id="tfb-report-back">';
			
			//echo $content_now;
			
			global $current_user;
			get_currentuserinfo();
			
			$content .= $this->textinput('from_name', 'Your name', 'from_name', $current_user->display_name);
			
			$content .= '<br />Want a copy of this message? Then fill in your e-mail address.<br /><br />';
			
			$content .= $this->textinput('from_email', 'Your e-mail address', 'from_email', $current_user->user_email);
			
			$content .= '<input name="action" type="hidden" value="send" />';
			
			$types[] = array('id' => 'I would like', 'text' => 'I would like ... in the next release');
			$types[] = array('id' => 'Compliment', 'text' => 'My compliments for this plugin');
			$types[] = array('id' => 'New Feature Request', 'text' => 'I have a New Feature Request');
			$types[] = array('id' => 'Bug', 'text' => 'I found a bug');
			$types[] = array('id' => 'General feedback', 'text' => 'I have some general feedback');
			
			$content .= $this->select('feedback_type', 'Type of feedback', 'feedback_type', '', $types);
			
			$content .= '<br /><b>Reporting a bug?</b><br />Found a bug or is Tiny Feedback Bar not properly working in your site? Please provide us with the steps you\'ve taken already. It helps if you can provide screenshots or a copy of error information.<br /><br />';
			
			$content .= $this->textarea('description', 'Description', 'description', '');
			
			$happy[] = array('id' => 'Perfect job! (5 stars)', 'text' => '5 stars - Perfect job!');
			$happy[] = array('id' => 'Nice job (4 stars)', 'text' => '4 stars - Nice job');
			$happy[] = array('id' => 'It\'s allright (3 stars)', 'text' => '3 stars - It\'s allright');
			$happy[] = array('id' => 'Room for improvement (2 stars)', 'text' => '2 stars - Room for improvement');
			$happy[] = array('id' => 'Not too happy (1 star)', 'text' => '1 star - Not too happy');
			
			$content .= '<br /><br />How happy does Tiny Feedback Bar make you?<br />';
			
			$content .= $this->select('happyness', 'Your rate today', 'happyness', '', $happy);
			
			$this->postbox('titleshelp',__('Help us improve Tiny Feedback Bar', 'feedback-report'), $content);
			
			echo '<div class="submit"><input type="submit" class="button-primary" name="submit" value="E-mail the Feedback Now!" /></div>';
			
			echo '    	   </form>
						</div>
					</div>
				</div>
			</div>
			';
			
			$this->report_back_sidebar();
		}

		function to_do_about_page() {
		
			$this->admin_header('About', true, true, 'tfb_about', 'tfb_about');
			
			$content = '<b>Goal</b><br />
				We\'ve grown Tiny Feedback Bar to help communication between web developers and their customers world-wide.<br />
				<br />
				<b>New-born</b><br />
				Until now the marjority of web professionals (webdesigners, &amp; web developers, interface designers, etc) receive several to a whole bunch of e-mail. Daily. Besides the fact that mails range from well-written to brief. No really: VERY brief. One-sentence kind of mails, like: "You done?". Mails typically jump from topic to topic. Making it hard to read back how certain decisions were made.<br />
				<br />
				End result: both parties start gathering a considerable collection of messages without a decent structure.<br />
				<br />
				We encountered that exact same problem a few years ago. We decided to go all in and start developing our own software so this problem would soon be a part of once-upon-a-time. That\'s when we designed the first version of our beloved Tiny Feedback Bar.<br />
				<br />
				<b>Behind The Bar</b><br>
				On our other products we work with a team but Tiny Feedback Bar has been conceived and grown by just one guy: Bas Kierkels. Dutch, mid 30\'s, <del>athletic</del> couch potato and CRAZY about coding. He own\'s a web development studio close to the city of Eindhoven. Soccer-guys now think of PSV, techies thought of Philips. Eindhoven is litterally boiling with new companies, techniques and other kinds of developments. It is an environment that Bas and the other team members LOVE!<br />
				<br />
				<b>First public appearance</b><br />
				May 2013 we decided to let Tiny Feedback Bar make her first public appearance! We developed the first version of the Wordpress plugin that will allow you to install Tiny Feedback Bar in one click and start streamlining your communication within 5 minutes!<br />
				<br />
				<b>Roadmap</b><br />
				What does Tiny Feedback Bar have in store for you? A lot. Want to know exactly what? Read our public <a href="http://www.tinyfeedbackbar.com/roadmap/" target="_blank">Roadmap</a>.';
			
			$this->postbox('titleshelp',__('What\'s the story behind Tiny Feedback Bar?', 'about-help'), $content);
			
			echo '       
						</div>
					</div>
				</div>
			</div>
			';
			
			$this->help_sidebar();
		}

		function to_do_help_page() {
			
			$this->admin_header('Help', true, true, 'tfb_help', 'tfb_help');
			
			$content = 'Tiny Feedback Bar is free. We would like to devote all our time to help grow Tiny Feedback Bar as big as we can. Please help us by checking out our forum and FAQ-sites first before you drop us an e-mail.<br />
			<br />
			<b>Need help NOW?</b><br />
			Click to the forum and/or FAQ and chances are you\'ll find the answer in 5 minutes: MUCH quicker than we can help you.<br />
			<br />
			<b>1. FAQ: Frequently Asked Questions</b><br />
			Needs now explanation: visit this site to find the answer to your question.<br />
			<a href="http://support.tinyfeedbackbar.com/" target="_blank"><b>support.tinyfeedbackbar.com</b></a><br />
			<br />
			<b>2. Forum</b><br />
			Run into a problem? Read the forum. Chances are somebody might have had the same problem before you. You read the answer to your problem way faster than we can help you. Cannot find the solution? Post your problem on the forum. We try to help everyone as soon as possible.<br />
			<a href="http://forum.tinyfeedbackbar.com/" target="_blank"><b>forum.tinyfeedbackbar.com</b></a>
			<br />';
			
			$this->postbox('titleshelp',__('In need of some assitance?', 'feedback-help'), $content);
			
			echo '       
						</div>
					</div>
				</div>
			</div>
			';
			
			$this->help_sidebar();
		}

		function multisite_defaults() {
			$option = get_option('tfb');
			if ( function_exists('is_multisite') && is_multisite() && !is_array($option) ) {
				$options = get_site_option('tfb_ms');
				if ( is_array($options) && isset($options['defaultblog']) && !empty($options['defaultblog']) && $options['defaultblog'] != 0 ) {
					foreach ( get_wpseo_options_arr() as $option ) {
						update_option( $option, get_blog_option( $options['defaultblog'], $option) );
					}
				}
				$option['ms_defaults_set'] = true;
				update_option( 'tfb', $option );
			}
		}
		
		function settings_sidebar() {
		?>
			<div class="postbox-container" style="width:20%;">
				<div class="metabox-holder">	
					<div class="meta-box-sortables">
						<?php
							$content = 'Need help using Tiny Feedback Bar?<br />
							<br />
							Click and use our Help page:<br />
							<br />
							<a href="admin.php?page=tfb-help">Help-section</a>';
							$this->postbox('tips','Need help?', $content);
							
							//$this->news();
						?>
					</div>
					<br/><br/><br/>
				</div>
			</div>
		<?php
		}
		
		function report_back_sidebar() {
		?>
			<div class="postbox-container" style="width:20%;">
				<div class="metabox-holder">	
					<div class="meta-box-sortables">
						<?php
							$content = 'Do you have questions concerning Tiny Feedback Bar?<br />
							<br />
							Visit the Forum or the Support-site:<br />
							<br />
							<a href="http://forum.tinyfeedbackbar.com" target=_blank">forum.tinyfeedbackbar.com</a><br />
							<a href="http://support.tinyfeedbackbar.com" target=_blank">support.tinyfeedbackbar.com</a><br />';
							$this->postbox('tips','Get help here', $content);
						?>
                        
                        <?php
							$content = 'Our developers are working hard on the next version of Tiny Feedback Bar.<br />
							<br />
							Interested what that version will bring you?<br />
							<br />
							Visit our site regularly or subscribe to our free newsletter and we will keep you informed!<br />
							<br />
							<a href="http://www.tinyfeedbackbar.com/roadmap" target=_blank">tinyfeedbackbar.com/roadmap</a>';
							$this->postbox('future','Future Releases', $content);
						?>
					</div>
					<br/><br/><br/>
				</div>
			</div>
		<?php
		}
		
		function help_sidebar() {
		?>
			<div class="postbox-container" style="width:20%;">
				<div class="metabox-holder">	
					<div class="meta-box-sortables">
						<?php
							$content = 'Do you have questions concerning Tiny Feedback Bar?<br />
							<br />
							Visit the Forum or the Support-site:<br />
							<br />
							<a href="http://forum.tinyfeedbackbar.com" target=_blank">forum.tinyfeedbackbar.com</a><br />
							<a href="http://support.tinyfeedbackbar.com" target=_blank">support.tinyfeedbackbar.com</a><br />';
							$this->postbox('tips','Get help here', $content);
						?>
                        
                        <?php
							$content = 'Our developers are working hard on the next version of Tiny Feedback Bar.<br />
							<br />
							Interested what that version will bring you?<br />
							<br />
							Visit our site regularly or subscribe to our free newsletter and we will keep you informed!<br />
							<br />
							<a href="http://www.tinyfeedbackbar.com/roadmap" target=_blank">tinyfeedbackbar.com/roadmap</a>';
							$this->postbox('future','Future Releases', $content);
						?>
					</div>
					<br/><br/><br/>
				</div>
			</div>
		<?php
		}
		
		function edit_sidebar() {
		?>
			<div class="postbox-container" style="width:20%;">
				<div class="metabox-holder">	
					<div class="meta-box-sortables">
						<?php
							$this->postbox('tips',''.__( 'Need help?', 'tfb-help' ).'',
							'<p>'.__( 'Need help using Tiny Feedback Bar? <a href="admin.php?page=tfb-help">Click here for tips and FAQ\'s</a>', 'tfb' ).'</p>');
						?>
						<?php
							$this->postbox('tips',''.__( 'Help improve Tiny Feedback Bar', 'tfb-feedback' ).'',
							'<p>'.__( 'Help US improve Tiny Feedback Bar:<br />
							<br />
							<a href="admin.php?page=tfb-report-back">Click here and tell us how we can improve Tiny Feedback Bar.</a>', 'tfb' ).'</p>');
						?>
					</div>
					<br/><br/><br/>
				</div>
			</div>
		<?php
		}
		
		function admin_header($title, $expl = true, $form = true, $option = 'yoast_wpseo_options', $optionshort = 'wpseo', $contains_files = false) {
			?>
			<div class="wrap">
				<?php 
				if ( (isset($_GET['updated']) && $_GET['updated'] == 'true') || (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') ) {
					$msg = __('Settings updated', 'wordpress-seo' );

					if ( function_exists('w3tc_pgcache_flush') ) {
						w3tc_pgcache_flush();
						$msg .= __(' &amp; W3 Total Cache Page Cache flushed', 'wordpress-seo' );
					} else if (function_exists('wp_cache_clear_cache() ')) {
						wp_cache_clear_cache();
						$msg .= __(' &amp; WP Super Cache flushed', 'wordpress-seo' );
					}

					// flush rewrite rules if XML sitemap settings have been updated.
					if ( isset($_GET['page']) && 'wpseo_xml' == $_GET['page'] )
						flush_rewrite_rules();

					echo '<div id="message" style="width:94%;" class="message updated"><p><strong>'.$msg.'.</strong></p></div>';
				} 
				if ($title == 'Feedback') {
					$width = '98%';
				} else {
					$width = '70%';
				}
				
				?>
				<a href="http://www.tinyfeedbackbar.com/" target="_blank"><div id="tfb-icon" style="background: url(<?php echo TFB_URL; ?>skin/images/tfb-32x32.png) no-repeat;" class="icon32"><br /></div></a>
				<h2 id="tfb-title"><?php _e("Tiny Feedback Bar: ", 'tfb' ); echo $title; ?></h2>
				<div id="tfb_content_top" class="postbox-container" style="width:<?php echo $width; ?>;">
					<div class="metabox-holder">
						<div class="meta-box-sortables">
			<?php
		}
		
		function admin_footer($title, $submit = true) {
			if ($submit) {
			?>
							<div class="submit"><input type="submit" class="button-primary" name="submit" value="<?php _e("Save Settings", 'tfb'); ?>" /></div>
			<?php } ?>
							</form>
						</div>
					</div>
				</div>
				<?php $this->promo_sidebar(); ?>
			</div>				
			<?php
		}
		
	} // end class TFB_Admin
	$tfb_admin = new TFB_Admin();
}
