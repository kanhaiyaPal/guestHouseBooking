<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

class GHOB_post_type_booking_init {	
	public function __construct()
    {
		/*initialize custom post type hook*/
		add_action( 'init', array( $this, 'create_bookings_post') );
		
		/*disable new post adding from backend*/
		add_action('admin_menu',array( $this, 'disable_new_posts'));
		
		/*Custom Meta Box for showing booking details*/
		add_action( 'admin_init', array( $this, 'create_meta_box_guest_house') ); 

	}
	
	function create_bookings_post()
	{
		register_post_type( 'booking',
			array(
				'labels' => array(
					'name' => 'Bookings',
					'singular_name' => 'Booking',
					'edit' => 'Edit',
					'edit_item' => 'Edit Booking',
					'new_item' => 'New Booking',
					'view' => 'View',
					'view_item' => 'View Bookings',
					'search_items' => 'Search Bookings',
					'not_found' => 'No Bookings found',
					'not_found_in_trash' => 'No Bookings found in Trash',
					'parent' => 'Parent Booking'
				),
				'public' => true,
				'menu_position' => 16,
				'supports' => array( 'title'),
				'taxonomies' => array( '' ),
				'menu_icon' => plugins_url( '../assets/images/booking_ico.png', __FILE__ ),
				'has_archive' => true
			)
		);
	}
	
	function disable_new_posts() {
		// Hide sidebar link
		global $submenu;
		unset($submenu['edit.php?post_type=booking'][10]);

		// Hide link on listing page
		if (isset($_GET['post_type']) && $_GET['post_type'] == 'booking') {
			echo '<style type="text/css">
			#favorite-actions, .add-new-h2, .tablenav, a.page-title-action { display:none; }
			</style>';
		}
	}
	
	function create_meta_box_guest_house()
	{
		add_meta_box( 'booking_meta_box',
			'Booking Details',
			array( $this, 'display_booking_details_meta_box'),
			'booking', 'normal', 'high'
		);
	}
	
	function display_booking_details_meta_box($booking_details)
	{
		require_once GHOB_PLUGIN_DIR . '/templates/admin/post_meta_box_booking_details.php';
	}
	
	function get_guest_house_title($post_id)
	{
		return get_the_title($post_id);
	}
	
	function get_room_name($room_id)
	{
		global $wpdb;
		$room_map = $wpdb->prefix.'room_mapping';
		$roomname = $wpdb->get_var("SELECT room_name FROM $room_map WHERE map_id = '$room_id'");
		return $roomname;
	}
	
	function create_new_booking($post_data,$available_slot_array)
	{
		// Create post object
		$booking_post = array(
		  'post_title'    => 'Booking Order #'.md5(uniqid(rand(), true)),
		  'post_content'  => '',
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		  'post_type' => 'booking'
		);
		 
		// Insert the post into the database
		$new_booking_id = wp_insert_post( $booking_post );
		
		$room_slot_count = array($post_data['rv_guest_entity_to_book'],$post_data['rv_guest_room_bed_qty']);
		$combined_array_gid_rmid = $this->get_room_id_guest_house($available_slot_array,$post_data['rv_guest_room_type'],$room_slot_count,$new_booking_id);
		
		if($new_booking_id != 0){
			add_post_meta($new_booking_id, 'guest_house_id', $combined_array_gid_rmid['guest_house']);
			add_post_meta($new_booking_id, 'room_id', $combined_array_gid_rmid['room_id_ar']);
			add_post_meta($new_booking_id, 'bedtype', $post_data['rv_guest_room_type']);
			add_post_meta($new_booking_id, 'paymentmethod', $post_data['rv_payment_method']);
			add_post_meta($new_booking_id, 'receiptno', $post_data['rv_ref_no']);
			add_post_meta($new_booking_id, 'amountpaid', $post_data['rv_paid_amount']);
			add_post_meta($new_booking_id, 'checkindate', $post_data['rv_guest_checkin']);
			add_post_meta($new_booking_id, 'checkoutdate', $post_data['rv_guest_checkout']);
			add_post_meta($new_booking_id, 'guestname', $post_data['rv_guest_name']);
			add_post_meta($new_booking_id, 'guestemail', $post_data['rv_guest_email']);
			add_post_meta($new_booking_id, 'guestphone', $post_data['rv_guest_phone']);
			add_post_meta($new_booking_id, 'guestcompany', $post_data['rv_guest_company']);
			add_post_meta($new_booking_id, 'guestaddress', $post_data['rv_guest_address']);
		}
		
		return $new_booking_id;
	}
	
	function get_room_id_guest_house($slot_id_array,$room_type,$room_bed_qty = array(),$new_booking_id){
		
		$ret_mixed_ar = array();
			
		switch($room_type){
			case 'single':{
				$ret_mixed_ar = $this->book_current_slot($room_bed_qty[1],$slot_id_array,$new_booking_id);
				break;
			}
			case 'double':{
				if($room_bed_qty[0] == 'room'){
					$ret_mixed_ar = $this->book_current_slot($room_bed_qty[1]*2,$slot_id_array,$new_booking_id);
				}elseif($room_bed_qty[0] == 'bed'){
					$ret_mixed_ar = $this->book_current_slot($room_bed_qty[1],$slot_id_array,$new_booking_id);
				}
				break;
			}
			case 'triple':{
				
				if($room_bed_qty[0] == 'room'){
					$ret_mixed_ar = $this->book_current_slot($room_bed_qty[1]*3,$slot_id_array,$new_booking_id);
				}elseif($room_bed_qty[0] == 'bed'){
					$ret_mixed_ar = $this->book_current_slot($room_bed_qty[1],$slot_id_array,$new_booking_id);
				}
				break;
			}
		}
		
		return $ret_mixed_ar;
	}
	
	function book_current_slot($item_qty,$slot_id_array,$new_booking_id){
		$room_id_ar = array();
		$guest_h_id = '';
		
		global $wpdb;
		$booking_table = $wpdb->prefix . 'booking_slots';
		
		for($s_i = 0; $s_i < $item_qty; $s_i++){
			
			$booked_status_ar = array();
			$curr_slot_id = $slot_id_array[$s_i];
			
			$q_r = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $booking_table WHERE slot_id = '$curr_slot_id'" ) );
			if($q_r->booked_status != '0'){
				$booked_status_ar = explode(',',$q_r->booked_status);
			}			
			
			array_push($booked_status_ar,$new_booking_id);
			
			if($q_r->booked_status != '0'){
				$updt_booked_status = implode(',',$booked_status_ar);
			}else{
				$updt_booked_status = $booked_status_ar[0];
			}
			
			$wpdb->update( 
				$booking_table, 
				array(
					'booked_status' => $updt_booked_status
				),
				array( 'slot_id' => $curr_slot_id )
			);

			$guest_h_id = $q_r->guest_house_id;
			array_push($room_id_ar,$q_r->room_no);
		}
		
		return array('guest_house' => $guest_h_id, 'room_id_ar' => implode(',',$room_id_ar));
	}
}
$wpGHOB_custom_booking_post = new GHOB_post_type_booking_init();
	