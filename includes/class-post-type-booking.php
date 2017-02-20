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
				'menu_icon' => plugins_url( '../assets/images/guest_ico.png', __FILE__ ),
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
			#favorite-actions, .add-new-h2, .tablenav { display:none; }
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
		$roomname = $wpdb->get_var("SELECT room_name FROM $room_map WHERE room_id = '$room_id'");
		return $roomname;
	}
	
	function create_new_booking($post_data)
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
		
		if($new_booking_id != 0){
			add_post_meta($new_booking_id, 'guest_house_id', $post_data['guest_house_id']);
			add_post_meta($new_booking_id, 'room_id', $post_data['room_id']);
			add_post_meta($new_booking_id, 'bedtype', $post_data['bedtype']);
			add_post_meta($new_booking_id, 'paymentmethod', $post_data['paymentmethod']);
			add_post_meta($new_booking_id, 'receiptno', $post_data['receiptno']);
			add_post_meta($new_booking_id, 'amountpaid', $post_data['amountpaid']);
			add_post_meta($new_booking_id, 'checkindate', $post_data['checkindate']);
			add_post_meta($new_booking_id, 'checkoutdate', $post_data['checkoutdate']);
			add_post_meta($new_booking_id, 'guestname', $post_data['guestname']);
			add_post_meta($new_booking_id, 'guestemail', $post_data['guestemail']);
			add_post_meta($new_booking_id, 'guestphone', $post_data['guestphone']);
			add_post_meta($new_booking_id, 'guestcompany', $post_data['guestcompany']);
			add_post_meta($new_booking_id, 'guestaddress', $post_data['guestaddress']);
		}
	}

}
$wpGHOB_custom_booking_post = new GHOB_post_type_booking_init();
	