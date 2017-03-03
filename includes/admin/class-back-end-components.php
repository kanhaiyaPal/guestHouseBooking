<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

class GHOB_admin_components_setup {	
	public function __construct()
    {
		/*Register admin menus*/
		add_action( 'admin_menu', array($this, 'register_admin_menus') );
		
		/*enqueue page specific script*/
		add_action( 'admin_enqueue_scripts', array($this, 'admin_manage_reservation_main_js') );
		
		/*include booking live operations class file*/
		require_once GHOB_PLUGIN_DIR. '/includes/admin/class-live-booking-admin.php';
		
		/*register session hadler*/
		add_action('init', array($this, 'register_session') );
		
		/*Ajax handler for reservation fields*/
		add_action( 'wp_ajax_locate_city_location', array($this, 'ghob_locate_city_loaction') );
		add_action( 'wp_ajax_locate_guest_house', array($this, 'ghob_locate_guest_houses') );
		add_action( 'wp_ajax_ghob_view_availability', array($this, 'ghob_view_availability') );
		add_action( 'wp_ajax_nopriv_ghob_view_availability', array($this, 'ghob_view_availability') );
		add_action( 'wp_ajax_ghob_book_slots', array($this, 'ghob_book_slots') );
		add_action( 'wp_ajax_nopriv_ghob_book_slots', array($this, 'ghob_book_slots') );
		
		/*Ajax handler for view occupancy mapping*/
		add_action( 'wp_ajax_get_guest_house_map', array($this, 'ghob_occupancy_details_guest_house') );
		
		/*Ajax handler for special operations*/
		add_action( 'wp_ajax_get_guesthouse_rooms', array($this, 'ghob_special_op_getrooms') );
		add_action( 'wp_ajax_get_guestbybed_room', array($this, 'ghob_special_op_getguestbyroom') );
		add_action( 'wp_ajax_ghob_shift_guest', array($this, 'ghob_shiftRoomByGuest') );
	}
	
	function register_session(){
		if( !session_id() )
		session_start();
	}
	
	function register_admin_menus()
	{
		add_menu_page( 'Manage Reservations For Guest House', 'Manage Reservations', 'manage_options', 'manage-reservations', array($this,'manage_reservation_html_page'),plugins_url( '../../assets/images/reservation_ico.png', __FILE__ ),10 );
	}
	
	function manage_reservation_html_page()
	{
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		require_once GHOB_PLUGIN_DIR. '/templates/admin/manage-reservations-main.php'; 
	}
	
	function admin_manage_reservation_main_js($hook) 
	{
		if( 'toplevel_page_manage-reservations' == $hook ){
		
			// Register, enqueue scripts and styles here
			wp_enqueue_script( 'manage-reservation-main-page-js',plugins_url( '../../assets/js/admin/manage-reservations-main.js', __FILE__) );
			wp_enqueue_style('manage-reservation-main-page-css', plugins_url( '../../assets/css/admin/manage-reservations-main.css', __FILE__) );
			
			/**for datepicker**/
			wp_enqueue_script('field-date-js','Field_Date.js',array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),time(),true);
			wp_register_style('jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
			wp_enqueue_style( 'jquery-ui-datepicker' );
		}
	}
	
	function populate_guest_house_having_slots()
	{
		$output_guesthouse_html = '';
		
		$args = array(
			'post_type'=> 'guest_house',
			'order'    => 'ASC',
			'post_status' => 'publish'
		);              

		$post_query = new WP_Query( $args );
		
		if($post_query->have_posts() ) {
			while ( $post_query->have_posts() ) {
				$post_query->the_post();
				$p_g_id =  get_the_ID();
				$p_g_title = get_the_title();
				$output_guesthouse_html .= "<option value='$p_g_id'>$p_g_title</option>";
			} 
		} 
		
		return $output_guesthouse_html;
	}
	
	function populate_guest_house_city()
	{
		$output_options = '';
		$args = [
			'taxonomy'     => 'GHOB_City_Location',
			'parent'        => 0,
			'number'        => 10,
			'hide_empty'    => false           
		];
		$city_terms = get_terms( $args );
		foreach($city_terms as $city)
		{
			$output_options.='<option value="'.$city->term_id.'">'.$city->name.'</option>'; 
		}
		
		if(empty($output_options)){
			$output_options.='<option value="-1">No Cities Found</option>'; 
		}
		
		return $output_options;
	}
	
	function ghob_locate_city_loaction() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		$termID = $_POST['term_city_id'];
		$taxonomyName = "GHOB_City_Location";
		$termchildren = get_term_children( $termID, $taxonomyName );
	
		foreach ($termchildren as $child) {
			$term = get_term_by( 'id', $child, $taxonomyName );
			echo '<option value="'.$term->term_id.'">'. $term->name . '</option>';
		}

		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	function ghob_book_slots(){
		
		$GHOB_live_book_obj = new GHOIB_live_booking_operations();
		if(wp_verify_nonce($_REQUEST['rv_security_key'], 'ghob_book_slots_'.get_current_user_id())){
			
 			if(isset($_SESSION['available_slots'])){
				
				$book_id_post = $GHOB_live_book_obj->create_booking_guest($_POST,$_SESSION['available_slots']);
				if($book_id_post){  
					unset($_SESSION['available_slots']);
					echo 'booking_success';
				}
			}else{
				wp_die('No booking slots found! Please re-search again');
			}
			
		}else{
			if(wp_verify_nonce($_REQUEST['front_booking_nonce'], 'front_end_final_booking_')){
				if(isset($_COOKIE['slot_array'])){
					
					$send_ar_array = array(
						'rv_guest_entity_to_book' => 'bed',
						'rv_guest_room_bed_qty' => $_COOKIE['g_room_qty'],
						'rv_guest_room_type' => $_COOKIE['g_room_type'],
						'rv_payment_method' => 'Pay on Counter',
						'rv_ref_no' => 'Booked Via Online Method',
						'rv_paid_amount' => 0,
						'rv_guest_checkin' => $_COOKIE['g_checkin'],
						'rv_guest_checkout' => $_COOKIE['g_checkout'],
						'rv_guest_name' => $_POST['front_book_guestname'],
						'rv_guest_email' => $_POST['front_book_guestemail'],
						'rv_guest_phone' => $_POST['front_book_guestmobile'],
						'rv_guest_company' => $_POST['front_book_guestcompany'],
						'rv_guest_address' => $_POST['front_book_guestaddress'],
					);
					
					$ck_slot_ar = stripslashes($_COOKIE['slot_array']);
					$ck_slot_ar = json_decode($ck_slot_ar, true);

					$book_id_post = $GHOB_live_book_obj->create_booking_guest($send_ar_array,$ck_slot_ar);
					if($book_id_post){  
						if(isset($_COOKIE['slot_array'])){ unset($_COOKIE['slot_array']); }
						if(isset($_COOKIE['room_price'])){ unset($_COOKIE['room_price']); }
						if(isset($_COOKIE['g_checkin'])){ unset($_COOKIE['g_checkin']); }
						if(isset($_COOKIE['g_checkout'])){ unset($_COOKIE['g_checkout']); }
						if(isset($_COOKIE['g_room_qty'])){ unset($_COOKIE['g_room_qty']); }
						if(isset($_COOKIE['g_room_type'])){ unset($_COOKIE['g_room_type']); }
						echo 'booking_success.'.$book_id_post;
						wp_die();
					}else{
						wp_die('booking_unsuccess');
					}
				}else{
					wp_die('No booking slots found! Please re-search again');
				}
			}
			wp_die('Transaction Authentication failed');
		}
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	function ghob_view_availability() {
		
		$GHOB_live_booking_obj = new GHOIB_live_booking_operations();
			
		if(wp_verify_nonce($_REQUEST['secret'], 'ghob_check_availability_'.get_current_user_id())){
						
			$slots_array = $GHOB_live_booking_obj->GHOB_check_rooms_availability($_REQUEST);
			$check_pricing_of_room = $GHOB_live_booking_obj->GHOB_check_rooms_pricing($_REQUEST);
			
			if(count($slots_array)>0){
				unset($_SESSION['available_slots']);
				$_SESSION['available_slots'] = $slots_array;
				echo $check_pricing_of_room;
			}else{
				echo 'not_available';
			}
		}else{
			//check if it is a frontend request
			if(wp_verify_nonce($_REQUEST['front_user_nonce'], 'front_end_availability_query_')){
				//prepare array
				$request_array = array(
					'guest_house'=> $_POST['front_user_guesthouse'],
					'checkin'=> $_POST['front_user_checkin'],
					'checkout'=> $_POST['front_user_checkout'],
					'no_beds'=> $_POST['front_user_quantity'],
					'type_of_room'=> $_POST['front_user_roomtype'],
					'no_rooms' => 0,
					'city' => 0,
					'location' => 0
				);
				
				$slots_array = $GHOB_live_booking_obj->GHOB_check_rooms_availability($request_array);
				$check_pricing_of_room = $GHOB_live_booking_obj->GHOB_check_rooms_pricing($request_array);
				
				if(count($slots_array)>0){
					//set cookie data
					if(isset($_COOKIE['slot_array'])){ unset($_COOKIE['slot_array']); }
					if(isset($_COOKIE['room_price'])){ unset($_COOKIE['room_price']); }
					if(isset($_COOKIE['g_checkin'])){ unset($_COOKIE['g_checkin']); }
					if(isset($_COOKIE['g_checkout'])){ unset($_COOKIE['g_checkout']); }
					if(isset($_COOKIE['g_room_qty'])){ unset($_COOKIE['g_room_qty']); }
					if(isset($_COOKIE['g_room_type'])){ unset($_COOKIE['g_room_type']); }
					
					setcookie("slot_array", json_encode($slots_array), time() + (86400 * 30), "/");
					setcookie("room_price", $check_pricing_of_room, time() + (86400 * 30), "/");
					setcookie("g_checkin", $_POST['front_user_checkin'], time() + (86400 * 30), "/");
					setcookie("g_checkout", $_POST['front_user_checkout'], time() + (86400 * 30), "/");
					setcookie("g_room_qty", $_POST['front_user_quantity'], time() + (86400 * 30), "/");
					setcookie("g_room_type", $_POST['front_user_roomtype'], time() + (86400 * 30), "/");
					
					echo 'available';
				}else{
					echo 'not_available';
				}
				
			}else{
				wp_die('Transaction Authentication failed');
			}
		}
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	function ghob_locate_guest_houses(){
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		$locationID = $_POST['term_location_id'];
		$args = array(
		'post_type' => 'guest_house',
		'tax_query' => array(
			array(
			'taxonomy' => 'GHOB_City_Location',
			'field' => 'id',
			'terms' => $locationID
			 )
		  )
		);
		$query = new WP_Query( $args ); 
		
		if ( $query->have_posts() ) {
			while($query->have_posts()){
				$query->the_post();
				echo "<option value='". get_the_ID()."'>";
				if(strlen(get_the_title())>0){echo get_the_title();}else{echo 'No Title Defined';}
				echo "</option>";
			}
		}
		wp_die();
	}
	
	function ghob_occupancy_details_guest_house()
	{
		global $wpdb;
		$mapping_table = $wpdb->prefix. 'room_mapping';
		$booking_table = $wpdb->prefix. 'booking_slots';
		
		$room_mapping_output = '';
		
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		$guesthouseID = $_POST['guest_house_id'];
		if($guesthouseID > 0){
			//get all rooms
			$ghob_get_rooms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $mapping_table WHERE guest_house_id ='$guesthouseID';"));
			if(count($ghob_get_rooms) > 0){
				foreach( $ghob_get_rooms as $room) {
					$room_mapping_output .= '<table class="map_table"><tr><td class="table_header_map" colspan="2"><h3>'.$room->room_name.' - '.$room->room_type.' Bed Room </h3></td></tr>';
					
					$ghob_get_slots = $wpdb->get_results($wpdb->prepare("SELECT * FROM $booking_table WHERE room_no ='$room->map_id';"));
					if(count($ghob_get_slots)>0){
						$bed_counter = 1;
						foreach($ghob_get_slots as $slot){
							$room_mapping_output .= '<tr>';
							$room_mapping_output .= '<td class="bed_c">Bed - '.$bed_counter.'</td>';
							if($slot->booked_status != 0 ){
								if(strpos($slot->booked_status, ',') !== false){
									$booking_id_ar = explode(',',$slot->booked_status);
									$room_mapping_output .= '<td class="engaged_caution" height="160">'; 
									$booking_id_count = 0;
									foreach($booking_id_ar as $booking_id){
										if($this->is_displayable_to_map($booking_id))
										{
											$guest_info_a = $this->get_customer_details_booking_id($booking_id);
											
											$room_mapping_output .= 'Guest name:'.$guest_info_a['guest_name'].'<br/><hr/>';
											$room_mapping_output .= 'Guest Mobile:'.$guest_info_a['guest_mobile'].'<br/><hr/>';
											$room_mapping_output .= 'Check In Date:'.$guest_info_a['checkin'].'<br/><hr/>';
											$room_mapping_output .= 'Check Out Date:'.$guest_info_a['checkout'].'<br/><hr/>';
											$room_mapping_output .= '<hr/><hr/>';
										}
										else{ $booking_id_count++; continue; } 
									}
									if(count($booking_id_ar) == $booking_id_count){
										$room_mapping_output .= ' Vacant ';
									}
									$room_mapping_output .= '</td>';
								}else{
									if($this->is_displayable_to_map($slot->booked_status))
									{
										$guest_info = $this->get_customer_details_booking_id($slot->booked_status);
										$room_mapping_output .= '<td class="engaged" height="160">'; 
										$room_mapping_output .= 'Guest name:'.$guest_info['guest_name'].'<br/><hr/>';
										$room_mapping_output .= 'Guest Mobile:'.$guest_info['guest_mobile'].'<br/><hr/>';
										$room_mapping_output .= 'Check In Date:'.$guest_info['checkin'].'<br/><hr/>';
										$room_mapping_output .= 'Check Out Date:'.$guest_info['checkout'].'<br/><hr/>';
										$room_mapping_output .= '</td>';
									}
									else{ $room_mapping_output .= '<td class="vacant"> Vacant </td>'; }
								}
							}else{
								$room_mapping_output .= '<td height="160"> Vacant </td>';
							}
							$room_mapping_output .= '</tr>';
							$bed_counter++;
						}
					}
					$room_mapping_output .= '</table>';
				}
			}else{
				wp_die('System failed to draw the room map, please generate room by visiting guest house page');
			}
			echo $room_mapping_output;
		}
		
		wp_die();
	}
	
	function is_displayable_to_map($booking_id){
		// Convert to timestamp
		$start_ts = strtotime(str_replace('/','-',get_post_meta($booking_id,'checkindate',true)));
		$end_ts = strtotime(str_replace('/','-',get_post_meta($booking_id,'checkoutdate',true)));
		$user_ts = date('d-m-Y');

		// Check that user date is between start & end
		return (!($user_ts >= $start_ts) && ($user_ts <= $end_ts));
	}
	
	function get_customer_details_booking_id($booking_id){
		
		return array('guest_name' => get_post_meta($booking_id,'guestname',true),'guest_mobile' => get_post_meta($booking_id,'guestphone',true),'checkin'=> get_post_meta($booking_id,'checkindate',true),'checkout'=> get_post_meta($booking_id,'checkoutdate',true));
	}
	
	function ghob_special_op_getrooms(){
		
		global $wpdb;
		$mapping_table = $wpdb->prefix. 'room_mapping';
		$booking_table = $wpdb->prefix. 'booking_slots';
		$output_options_room = '';
		
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		$gID = $_POST['guest_house_id'];
		
		$ghob_get_rooms = $wpdb->get_results($wpdb->prepare("SELECT * FROM $mapping_table WHERE guest_house_id ='$gID';"));
		
		if(count($ghob_get_rooms) > 0){
			foreach( $ghob_get_rooms as $room) {
				$output_options_room .= '<option value="'.$room->map_id.'">'. $room->room_name .'</option>';
			}
		}
		echo $output_options_room;
		wp_die();
	}
	
	function ghob_special_op_getguestbyroom(){
		global $wpdb;
		$mapping_table = $wpdb->prefix. 'room_mapping';
		$booking_table = $wpdb->prefix. 'booking_slots';
		$output_options_guests = '';
		$guest_list_counter = 0;
		
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		$mapID = $_POST['ghid_map_id'];
		
		$ghob_get_bookings = $wpdb->get_results($wpdb->prepare("SELECT * FROM $booking_table WHERE room_no ='$mapID';"));
		
		if(count($ghob_get_bookings) > 0){
			foreach( $ghob_get_bookings as $booking) {
				if($booking->booked_status != 0){
					if(strpos($booking->booked_status, ',') != false){
						//multiple bookings
						$bookings_array = explode(',',$booking->booked_status);
						foreach($bookings_array as $book){
							if($this->is_displayable_to_map($book)){
								$guest_name = get_post_meta($book,'guestname',true);
								$output_options_guests .= '<option value="'.$booking->slot_id.'_g_'.$book.'">'. $guest_name .'</option>';
								$guest_list_counter++;
							}
						}
					}else{
						//single booking
						if($this->is_displayable_to_map($booking->booked_status)){
							$guest_name = get_post_meta($booking->booked_status,'guestname',true);
							$output_options_guests .= '<option value="'.$booking->slot_id.'_g_'.$booking->booked_status.'">'. $guest_name .'</option>';
							$guest_list_counter++;
						}
					}
				}
			}
		}else{
			$output_options_guests .= 'No Booking Slots Found! kindly generate rooms first';
		}
		
		if($guest_list_counter > 0){
			echo $output_options_guests;
			wp_die();
		}else{
			$output_options_guests .= 'No Bookings found';
		}
		echo $output_options_guests;
		wp_die();
	}
	
	function ghob_shiftRoomByGuest(){
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		$room_shifted_to = $_POST['room_to_shift'];
		
		$mixed_val_slotid_bookingid = $_POST['guest_slotid_bookingid'];
		$mixed_val_slotid_bookingid = explode('_g_',$mixed_val_slotid_bookingid);
		$guest_slot_id = $mixed_val_slotid_bookingid[0];
		$guest_booking_id = $mixed_val_slotid_bookingid[1];
		$guest_checkout = get_post_meta($guest_booking_id ,'checkoutdate',true);
		$guest_checkin = get_post_meta($guest_booking_id ,'checkindate',true);
		
		$guesth_id = $_POST['guest_house_id'];
		$shifting_room = $_POST['room_to_shift'];
		
		$is_slot_available = $this->check_slots_available_by_room($guesth_id,$guest_checkin,$guest_checkout,$shifting_room);
		
		if(!$is_slot_available){ echo 'room_not_empty'; }else{
			
			$movement_st = $this->move_guest_to_newlocation($is_slot_available,$guest_booking_id,$guest_slot_id);
			if($movement_st){ echo 'movement_done_successfully'; }else{ echo 'error_movement';}
		}
		wp_die();
	}
	
	function check_slots_available_by_room($guest_house_id,$g_checkin,$g_checkout,$room_no_to_shift){

		$GHOB_live_booking_obj = new GHOIB_live_booking_operations();
		$is_available = $GHOB_live_booking_obj->available_room_for_shifting($guest_house_id,$g_checkin,$g_checkout,$room_no_to_shift);
		if($is_available){
			return $is_available;
		}else{
			return false;
		}
	}
	
	function move_guest_to_newlocation($slot_new_id,$guest_booking_id,$slot_old_id){
		$GHOB_live_booking_obj = new GHOIB_live_booking_operations();
		$move_status = $GHOB_live_booking_obj->move_guest_to_new_room($slot_new_id,$guest_booking_id,$slot_old_id);
		return $move_status;
	}
}

$wpGHOB_setup_admin_components = new GHOB_admin_components_setup();