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
		add_action( 'wp_ajax_ghob_book_slots', array($this, 'ghob_book_slots') );
		
		/*Ajax handler for view occupancy mapping*/
		add_action( 'wp_ajax_get_guest_house_map', array($this, 'ghob_occupancy_details_guest_house') );
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
		if(wp_verify_nonce($_REQUEST['rv_security_key'], 'ghob_book_slots_'.get_current_user_id())){
			
 			if(isset($_SESSION['available_slots'])){
				
				$GHOB_live_book_obj = new GHOIB_live_booking_operations();
				//create new booking post_type
				//update the post id to booking table
				$book_id_post = $GHOB_live_book_obj->create_booking_guest($_POST,$_SESSION['available_slots']);
				if($book_id_post){  
					unset($_SESSION['available_slots']);
					echo 'booking_success';
				}
			}else{
				wp_die('No booking slots found! Please re-search again');
			}
			
		}else{
			wp_die('Transaction Authentication failed');
		}
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	
	function ghob_view_availability() {
			
		if(wp_verify_nonce($_REQUEST['secret'], 'ghob_check_availability_'.get_current_user_id())){
			
			$GHOB_live_booking_obj = new GHOIB_live_booking_operations();
			$slots_array = $GHOB_live_booking_obj->GHOB_check_rooms_availability($_REQUEST);
			$check_pricing_of_room = $GHOB_live_booking_obj->GHOB_check_rooms_pricing($_REQUEST);
			
			if(count($slots_array)>0){
				$_SESSION['available_slots'] = $slots_array;
				echo $check_pricing_of_room;
			}else{
				echo 'not_available';
			}
		}else{
			wp_die('Transaction Authentication failed');
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
}

$wpGHOB_setup_admin_components = new GHOB_admin_components_setup();