<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}
class GHOIB_live_booking_operations{
	
	private $ghob_wpdb,$booking_table,$room_map_table; 
	
	public function __construct(){
		global $wpdb;
		$this->ghob_wpdb = &$wpdb;
		$this->booking_table = $this->ghob_wpdb->prefix . "booking_slots";
		$this->room_map_table = $this->ghob_wpdb->prefix . "room_mapping";
	}
	
	public GHOB_check_rooms_availability($search_query_array){
		
		$search_sql_query = '';
		$total_empty_slots = 0;
		
		$q_city = $search_query_array['city'];
		$q_location = $search_query_array['location'];
		$q_checkin = $search_query_array['checkin'];
		$q_checkout = $search_query_array['checkout'];
		$q_norooms = $search_query_array['number_of_rooms'];
		$q_nobeds = $search_query_array['number_of_beds'];
		$q_type = $search_query_array['type_of_room'];
		$q_guest_house = $search_query_array['guest_house'];
		
		//get all posts having city and location 
		$args = array(
			'post_type'  => 'guest_house',
			'tax_query' => array(				
				array(
					'taxonomy' => 'GHOB_City_Location'
					'field'    => 'term_id',
					'terms'    => array($q_city,$q_location)
					)
				),
			'numberposts' => -1,
			'post_type'   => 'publish',
			'post__in' => array( $q_guest_house )
		);
		
		$city_location_results = new WP_Query( $args );
		$posts = $city_location_results->posts;
		if(count($posts)>0){
			foreach($posts as $post) {
				$p_sql = "SELECT COUNT(*) FROM $this->booking_table WHERE guest_house_id = $post->ID AND is_engaged = 0";
				if($q_norooms > 0){ $p_sql .= " AND slot_id IN ( SELECT slot_id FROM $this->booking_table WHERE room_no IN(SELECT map_id WHERE room_type = $q_type AND guest_house_id = $post->ID))"; }
				$p_sql .= " AND room_type = $q_type AND booked_status = 0 ";
				if($q_nobeds > 0){ $p_sql .= " WHERE COUNT(*)>= $q_nobeds"; }
				$p_sql .= ";";
				$result_count = $this->ghob_wpdb->get_var($this->ghob_wpdb->prepare($p_sql)); 
				if($result_count>0){
					$total_empty_slots +=(int)$result_count;
				}
			}
		}
		
	}
}
?>