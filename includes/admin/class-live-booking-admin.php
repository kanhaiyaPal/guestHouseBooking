<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}
class GHOIB_live_booking_operations{
	
	private $ghob_wpdb,$booking_table,$room_map_table; 
	
	public function __construct(){
		
		/*include bookings posts class file*/
		require_once GHOB_PLUGIN_DIR. '/includes/class-post-type-booking.php';
		
		global $wpdb;
		$this->ghob_wpdb = &$wpdb;
		$this->booking_table = $this->ghob_wpdb->prefix . "booking_slots";
		$this->room_map_table = $this->ghob_wpdb->prefix . "room_mapping";
		
		
	}
	
	public function GHOB_check_rooms_availability($search_query_array){
		
		$search_sql_query = '';
		$total_empty_slots = 0;
		$available_beds_result = array();
		
		$q_city = $search_query_array['city'];
		$q_location = $search_query_array['location'];
		$q_checkin = $search_query_array['checkin'];
		$q_checkout = $search_query_array['checkout'];
		$q_norooms = $search_query_array['no_rooms'];
		$q_nobeds = $search_query_array['no_beds'];
		$q_type = $search_query_array['type_of_room'];
		$q_guest_house = $search_query_array['guest_house'];
		
		//get all posts having city and location 
		$args = array(
			'post_type'  => 'guest_house',
			'tax_query' => array(				
				array(
					'taxonomy' => 'GHOB_City_Location',
					'field'    => 'term_id',
					'terms'    => array($q_city,$q_location)
					)
				),
			'numberposts' => -1,
			'post_status'   => 'publish',
			'post__in' => array( $q_guest_house )
		);
		
		$city_location_results = new WP_Query( $args );
		
		$posts = $city_location_results->posts;
		
		if(count($posts)>0){
			foreach($posts as $post) {
				$p_sql = "SELECT * FROM $this->booking_table WHERE guest_house_id = '$post->ID' AND is_engaged = '0'";
				if($q_norooms > 0){ $p_sql .= " AND room_no IN ( SELECT map_id FROM $this->room_map_table WHERE room_type = '$q_type' AND guest_house_id = '$post->ID') AND room_no NOT IN (SELECT DISTINCT room_no FROM $this->booking_table WHERE is_engaged!=0)"; }
				
				$p_sql .= " AND room_type = '$q_type'";
				$p_sql .= ";";
				$result_count = $this->ghob_wpdb->get_results($this->ghob_wpdb->prepare($p_sql)); 
				
				if($q_nobeds > 0){  
					if((count($result_count)) >= $q_nobeds){
						$ret_val = $this->push_array_with_result($result_count,$q_checkin,$q_checkout);
						$available_beds_result = $ret_val['slots'];
					}
				}else{
					/*Bed or room check*/
					$comparater = 0;
					switch($q_type){
						case 'single':{ $comparater = count($result_count); break; }
						case 'double':{ $comparater = count($result_count)/2; break; }
						case 'triple':{ $comparater = count($result_count)/3; break; }
					}
					if( $comparater >= $q_norooms){
						$ret_val = $this->push_array_with_result($result_count,$q_checkin,$q_checkout);
						$available_beds_result = $this->filter_out_bad_slots($ret_val['slots'],$ret_val['scrap_room_ids']);
					}
				}
				
			}
		}
		
		if(count($available_beds_result)>0){
			return $available_beds_result;
		}
		else{
			return array();
		}
	}
	
	function filter_out_bad_slots($slot_ids,$room_ids){
		
		$ret_array = array();
		$comb_slot_ids = implode(',',array_unique($slot_ids, SORT_REGULAR));
		if(!empty($room_ids))
		$comb_room_ids = implode(',',array_unique($room_ids, SORT_REGULAR));
		else
		$comb_room_ids =0;
	
		$s_sql = "SELECT * FROM $this->booking_table WHERE slot_id IN ($comb_slot_ids) AND room_no NOT IN ($comb_room_ids)";
		$valid_slots = $this->ghob_wpdb->get_results($this->ghob_wpdb->prepare($s_sql)); 
		foreach($valid_slots as $slot){
			array_push($ret_array,$slot->slot_id);
		}

		return $ret_array;
	}
	
	function push_array_with_result($sql_result_array,$q_checkin,$q_checkout){
		$available_beds_result = array();
		$scrapped_map_ids = array();
		
		foreach($sql_result_array as $result){
			if($result->booked_status == 0){
				array_push($available_beds_result,$result->slot_id);
			}else{
				$booking_ids = explode(',',$result->booked_status);
				$av_flag = false;
				foreach($booking_ids as $bookingid){
					if ( !(FALSE === get_post_status( $bookingid )) ) {
						$check_in_book = strtotime(str_replace("/","-",get_post_meta( $bookingid, 'checkindate', true )));
						$check_out_book = strtotime(str_replace("/","-",get_post_meta( $bookingid, 'checkoutdate', true )));
						
						$req_checkin = strtotime(str_replace("/","-",$q_checkin));
						$req_checkout = strtotime(str_replace("/","-",$q_checkout));
						
						if(!(($req_checkin <= $check_out_book)&&($req_checkout >= $check_in_book))){
							$av_flag = true;
						}else{
							$av_flag = false;
							break;
						}
					}
				}
				if($av_flag)
				array_push($available_beds_result,$result->slot_id);
				else
				array_push($scrapped_map_ids,$result->room_no);
			}
		}
		return array('slots'=>$available_beds_result,'scrap_room_ids'=>$scrapped_map_ids);
	}
	
	public function GHOB_check_rooms_pricing($data_array){
		
		$q_guest_house = $data_array['guest_house'];
		$q_norooms = $data_array['no_rooms'];
		$q_nobeds = $data_array['no_beds'];
		$q_type = $data_array['type_of_room'];
		
		if($q_guest_house > 0){
			switch($q_type){
				case 'single':{ 
					if($q_norooms > 0){
						return get_post_meta( $q_guest_house, 'singlebedprice', true );
					}else{
						return get_post_meta( $q_guest_house, 'singlebedprice', true );
					}
					break; 
				}
				case 'double':{ 
					if($q_norooms > 0){
						return get_post_meta( $q_guest_house, 'doubleroomprice', true );
					}else{
						return get_post_meta( $q_guest_house, 'doublebedprice', true );
					}
					break;
				}
				case 'triple':{ 
					if($q_norooms > 0){
						return get_post_meta( $q_guest_house, 'tripleroomprice', true );
					}else{
						return get_post_meta( $q_guest_house, 'triplebedprice', true );
					}
					break;
				}
			}
		}
		
	}
	
	public function create_booking_guest($form_data,$avail_array)
	{
		$ghob_new_book_post_obj = new GHOB_post_type_booking_init();
		$booking_id = $ghob_new_book_post_obj->create_new_booking($form_data,$avail_array);
		return $booking_id;
	}
}
?>