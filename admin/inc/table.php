<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once( ABSPATH . 'wp-content/plugins/tiny-feedback-bar-by-gekko/includes/functions/date.php' );
require_once( ABSPATH . 'wp-content/plugins/tiny-feedback-bar-by-gekko/includes/functions/to_do.php' );

class TFB_Feedback_Table extends WP_List_Table {
	
    function __construct(){
        global $status, $page, $wpdb, $table_data;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'movie',     //singular name of the listed records
            'plural'    => 'movies',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }
    
    function column_default($item, $column_name){
        switch($column_name){
            case 'to_do_update':
			case 'user_name':
			case 'to_do_origin_wp_user_id':
			case 'to_do_diff':
			case 'to_do_started':
            case 'to_do_descr':
                return $item[$column_name];
			case 'to_do_status':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_to_do_name($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&to_do_id=%s">Edit</a>',$_REQUEST['page'],'edit',$item['to_do_id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&to_do_id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['to_do_id']),
        );
        
        //Return the title contents
        return sprintf('%1$s%3$s',
            /*$1%s*/ $item['to_do_name'],
            /*$2%s*/ $item['to_do_id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['to_do_id']                //The value of the checkbox should be the record's id
        );
    }
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'to_do_name'     => 'Name',
            'to_do_started'  => 'Started',
			'user_name'     => 'By',
            'to_do_status'    => 'Status',		
            'to_do_diff'  => 'Latest update'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'to_do_name'     => array('to_do_name',false),     //true means it's already sorted
			'user_name'     => array('user_name',false),
			'to_do_status'    => array('to_do_status',false),
            'to_do_diff'  => array('to_do_diff',true),
            'to_do_started'  => array('to_do_started',true),
			'to_do_added'  => array('to_do_added',true)
        );
        return $sortable_columns;
    }
	
    function get_bulk_actions() {
        $actions = array(
            //'delete'    => 'Delete'
        );
        return $actions;
    }
	
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
		if( 'delete'===$this->current_action() ) {
			die('Function cannot be used just yet.');
			foreach($_GET['to_do_id'] as $to_dd) {
                tfb_delete_to_do($to_dd);
            }
        }
    }
	
	function count_diff($stamp) {
		
		$diff = time() - $stamp;
		
		if ($diff < 60) { //less than 1 day ago
			$sec = $diff;
			$output = '' . $sec . ' seconds ago';
		} elseif ($diff < 3600) { //less than 1 day ago
			$min = round(($diff / 60), 0);
			if ($min == 1) {
				$output = '1 minute ago';
			} else {
				$output = '' . $min . ' minutes ago';
			}
		} elseif ($diff < 86400) { //less than 1 day ago
			$hour = round(($diff / 3600), 0);
			$output = $hour . ' hour';
		} elseif ($diff > 86400) { //longer than 1 day ago
			$output = round(($diff / 86400), 0);
			if ($output == 1) {
				$output .= '1 day';
			} else {
				$output .= ' days';
			}
		}
		
		return $output;
	}
    
    function prepare_items() {
        global $wpdb, $table_data; //This is used only if making any database queries

        $per_page = 50;
		$data = array();
        
		$columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
		$this->process_bulk_action();
        
		if (isset($_GET['filter_status']) AND $_GET['filter_status'] == 'all') {
			$query_to_do = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do 
								ORDER BY to_do_update DESC";
		} elseif (isset($_GET['filter_status']) AND $_GET['filter_status'] != '') {
			$query_to_do = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do 
								WHERE to_do_status = '" . $_GET['filter_status'] . "'
								ORDER BY to_do_update DESC";
		} else {
			$query_to_do = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do 
								WHERE to_do_status != '300'
								AND to_do_status != '400' 
								AND to_do_status != '500'
								ORDER BY to_do_update DESC";
		}
		$sql_rows = $wpdb->get_results($query_to_do);
		$rijen = $wpdb->num_rows;
		
		if ($rijen > 0) {
			foreach($sql_rows as $row_to_do) {
				
				$row_to_do = (array)$row_to_do;
				
				$user = get_userdata($row_to_do['to_do_origin_wp_user_id']);
				$row['user_name'] = '';
				if (isset($user->data->user_nicename)) {
					$row['user_name'] = $user->data->user_nicename;
				} else {
					$row['user_name'] = '-';
				}
				
				$row['to_do_id'] = $row_to_do['to_do_id'];
				$row['to_do_type'] = $row_to_do['to_do_type'];
				$row['to_do_name'] = $row_to_do['to_do_name'];
				$row['to_do_added'] = $row_to_do['to_do_added'];
				$started = $this->count_diff($row_to_do['to_do_added']);
				if (isset($user->data->user_nicename)) {
					$started .= ' by ' . $user->data->user_nicename;
				}
				$row['to_do_started'] = $this->count_diff($row_to_do['to_do_added']);
				
				if (strlen($row_to_do['to_do_descr']) > 80) {
					$row['to_do_descr'] = substr($row_to_do['to_do_descr'], 0, 80) . '..';
				} elseif ($row_to_do['to_do_descr'] == '') {
					$row['to_do_descr'] = '-';
				} else {
					$row['to_do_descr'] = $row_to_do['to_do_descr'];
				}
				$row['to_do_status'] = tfb_return_to_do_status($row_to_do['to_do_status']);
				if ($row_to_do['to_do_update'] == 0) {
					$output = $this->count_diff($row_to_do['to_do_added']);
				} else {
					$output = $this->count_diff($row_to_do['to_do_update']);
				}
				
				$query_to_do_message = "SELECT * FROM " . $wpdb->prefix . "tfb_to_do_messages 
									WHERE to_do_id = '" . $row_to_do['to_do_id'] . "' 
									ORDER BY to_do_message_added DESC 
									LIMIT 1";
				$sql_message = $wpdb->get_results($query_to_do_message);
				$to_do_message = (array)$sql_message;
				
				if (count($to_do_message) == 1) {
					
					$to_do_message = (array)$sql_message[0];
					
					$user = get_userdata($to_do_message['wp_user_id']);
					if (isset($user->data->user_nicename)) {
						$output .= ' by ' . $user->data->user_nicename;
					}
					if (strlen(strip_tags($to_do_message['to_do_message_message'])) > 50) {
						$message = substr(strip_tags($to_do_message['to_do_message_message']), 0, 48) . '..';
					} else {
						$message = strip_tags($to_do_message['to_do_message_message']);
					}
					$output .= '<br /><i>' . $message . '</i>';
				}
				
				if ($row_to_do['to_do_update'] == 0) {
					$row['to_do_update'] = $row_to_do['to_do_added'];
				} else {
					$row['to_do_update'] = $row_to_do['to_do_update'];
				}
				$row['to_do_diff'] = $output;
				
				$data[] = $row;
			}
		}
		 
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'to_do_update'; //If no sort, default to title
			if ($orderby == 'to_do_diff') {
				$orderby = 'to_do_update';
			} elseif ($orderby == 'to_do_started') {
				$orderby = 'to_do_added';
			}
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'DESC'; //If no order, default to asc
			$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
			return ($order==='desc') ? $result : -$result; //Send final sort direction to usort
        }
		if (isset($_GET['orderby'])) {
        	usort($data, 'usort_reorder');
		}
		
        $current_page = $this->get_pagenum();
        
		$total_items = count($data);
        
		if (is_array($data)) {
			$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		}
        
		$this->items = $data;
        
		$this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}

?>