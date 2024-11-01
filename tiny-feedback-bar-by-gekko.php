<?php
/*
Plugin Name: Tiny Feedback Bar
Version: 0.3.6
Plugin URI: http://www.tinyfeedbackbar.com/
Description: Just Point & click to add new Tasks, Bugs & New Feature Requests. Easy, in your browser.
Author: Bas Kierkels
Author URI: http://profiles.wordpress.org/baskie

------------------------------------------------------------------------

= Credits: Yoast.com =
This is my first major plugin and - as you know - all beginnings are hard.
To make a smooth start, I looked into the most succesful Wordpress plugins
around to see how these were build. 

The biggest lessons were the one that I learned from Wordpress SEO - in 
my mind - the best SEO plugin around. As you probably know, that is written 
by a fellow Dutchie: Joost de Valk or 'Yoast' as most of you know him.

Tiny Feedback Bar has the same structure as I foun in Wordpress SEO by Yoast 
for which I want to thank Yoast. Because without that example, I would have 
lost ALL my hairs. Those of you who know me know what that means.

I hope you will all enjoy the plugin as much as we already have. Have fun!


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, 
or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

define( 'TFB_URL', plugin_dir_url(__FILE__) );
define( 'TFB_PATH', plugin_dir_path(__FILE__) );
define( 'TFB_BASENAME', plugin_basename( __FILE__ ) );

if (!defined('TFB_PLUGIN_NAME'))
    define('TFB_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('TFB_PLUGIN_DIR'))
    define('TFB_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . TFB_PLUGIN_NAME);

if (!defined('TFB_PLUGIN_URL'))
	define('TFB_PLUGIN_URL', plugin_dir_url(__FILE__) . TFB_PLUGIN_NAME);

if (!defined('TFB_VERSION_KEY'))
    define('TFB_VERSION_KEY', 'TFB_version');

if (!defined('TFB_VERSION_NUM'))
    define('TFB_VERSION_NUM', '0.3.6');

register_activation_hook( __FILE__, array('TFB_Admin', 'tfb_install') );

require_once TFB_PATH.'includes/functions/tfb-functions.php';

if ( is_admin() ) {
	
	if (isset($_GET['action']) AND $_GET['action'] == 'delete' AND $_GET['to_do_id'] != '') {
		tfb_delete_to_do($_GET['to_do_id']);
	} elseif (isset($_POST['action']) AND $_POST['action'] == 'edit' AND $_POST['to_do_id'] != '') {
		require_once(ABSPATH . WPINC . '/pluggable.php');
		tfb_edit_to_do();
	}
	
	require_once TFB_PATH.'admin/inc/table.php';
	require_once TFB_PATH.'admin/tfb_plugin_tools.php';
	require_once TFB_PATH.'includes/configure.php';
	require_once TFB_PATH.'admin/class-config.php';
	require_once TFB_PATH.'includes/functions/ajax.php';
	
	if ( version_compare( $wp_version, '3.2.1', '>') )
		require_once TFB_PATH.'admin/class-pointers.php';
} else {
	
	require_once TFB_PATH.'includes/class-frontend.php';
	require_once TFB_PATH.'includes/functions/ajax.php';
	
}

?>