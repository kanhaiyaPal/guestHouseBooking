<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}
class GHOIB_booking_slot_table{
	
	private $ghob_wpdb,$booking_table,$room_map_table; 
	
	public function __construct(){
		global $wpdb;
		$this->ghob_wpdb = &$wpdb;
		$this->booking_table = $this->ghob_wpdb->prefix . "booking_slots";
		$this->room_map_table = $this->ghob_wpdb->prefix . "room_mapping";
	}
	
	public function create_new_booking_slots_first_time($post_id){
		$single_bedroom_numbers = get_post_meta( $post_id, 'singlebedroomsnumbers', true ); 
		$double_bedroom_numbers = get_post_meta( $post_id, 'doublebedroomsnumbers', true );
		$triple_bedroom_numbers = get_post_meta( $post_id, 'triplebedroomsnumbers', true ); 

		$single_bed_room = intval( get_post_meta( $post_id, 'singlebed', true ) );
		$double_bed_room = intval( get_post_meta( $post_id, 'doublebed', true ) );
		$triple_bed_room = intval( get_post_meta( $post_id, 'triplebed', true ) );
		
		if(!empty($single_bedroom_numbers)){
			$bedroom_numbers_array = explode(',',$single_bedroom_numbers);
			for($s_i=0;	$s_i < $single_bed_room; $s_i++){
				
				//create a unique bed_id
				$temp_bed_id = $post_id.'-single-'.$s_i;
				
				$this->ghob_wpdb->insert( $this->booking_table, array(
					'bed_id' => base64_encode($temp_bed_id), 
					'room_no' => $this->get_room_mapid($post_id,'single',$bedroom_numbers_array[$s_i]),
					'room_type' => 'single',
					'guest_house_id' => $post_id
					),array(
					'%s','%s','%s','%s')
				);
				
			}
		}
		
		if(!empty($double_bedroom_numbers)){
			$double_bedroom_numbers_array = explode(',',$double_bedroom_numbers);
			$intrm_dup_db_ar = array();
			foreach($double_bedroom_numbers_array as $bd_rm){
				array_push($intrm_dup_db_ar,$bd_rm);
				array_push($intrm_dup_db_ar,$bd_rm);
			}
			
			for($d_i=0;	$d_i < ($double_bed_room*2); $d_i++){
				
				//create a unique bed_id
				$temp_bed_id = $post_id.'-double-'.$d_i;
				$d_room_name = $intrm_dup_db_ar[$d_i];
				
				$this->ghob_wpdb->insert( $this->booking_table, array(
					'bed_id' => base64_encode($temp_bed_id), 
					'room_no' => $this->get_room_mapid($post_id,'double',$d_room_name),
					'room_type' => 'double',
					'guest_house_id' => $post_id
					),array(
					'%s','%s','%s','%s')
				);
			}
		}
		
		if(!empty($triple_bedroom_numbers)){
			$triple_bedroom_numbers_array = explode(',',$triple_bedroom_numbers);
			$intrm_dup_tr_ar = array();
			foreach($triple_bedroom_numbers_array as $bd_rm){
				array_push($intrm_dup_tr_ar,$bd_rm);
				array_push($intrm_dup_tr_ar,$bd_rm);
				array_push($intrm_dup_tr_ar,$bd_rm);
			}
			for($t_i=0;	$t_i < ($triple_bed_room*3); $t_i++){
				
				//create a unique bed_id
				$temp_bed_id = $post_id.'-triple-'.$t_i;
				
				$this->ghob_wpdb->insert( $this->booking_table, array(
					'bed_id' => base64_encode($temp_bed_id), 
					'room_no' => $this->get_room_mapid($post_id,'triple',$intrm_dup_tr_ar[$t_i]),
					'room_type' => 'triple',
					'guest_house_id' => $post_id
					),array(
					'%s','%s','%s','%s')
				);
			}
		}
	}
	
	function generate_room_map_first_time($post_id){
		
		$single_bedroom_numbers = get_post_meta( $post_id, 'singlebedroomsnumbers', true ); 
		$double_bedroom_numbers = get_post_meta( $post_id, 'doublebedroomsnumbers', true );
		$triple_bedroom_numbers = get_post_meta( $post_id, 'triplebedroomsnumbers', true ); 
		
		if (strlen($triple_bedroom_numbers) > 0){
			$triple_bedroom_numbers_array = explode(',',$triple_bedroom_numbers);
			for($t_m=0; $t_m < count($triple_bedroom_numbers_array); $t_m++){
				
				$temp_room_id = $post_id.'-triplebedroom-'.$t_m;
				
				$this->ghob_wpdb->insert( $this->room_map_table, array(
					'room_id' => base64_encode($temp_room_id), 
					'room_name' => $triple_bedroom_numbers_array[$t_m],
					'room_type' => 'triple',
					'guest_house_id' => $post_id
					),array(
					'%s','%s','%s','%s')
				);
			}
		}
		
		if (strlen($double_bedroom_numbers) > 0){
			$double_bedroom_numbers_array = explode(',',$double_bedroom_numbers);
			for($d_m=0; $d_m < count($double_bedroom_numbers_array); $d_m++){
				
				$temp_room_id = $post_id.'-doublebedroom-'.$d_m;
				
				$this->ghob_wpdb->insert( $this->room_map_table, array(
					'room_id' => base64_encode($temp_room_id), 
					'room_name' => $double_bedroom_numbers_array[$d_m],
					'room_type' => 'double',
					'guest_house_id' => $post_id
					),array(
					'%s','%s','%s','%s')
				);
			}
		}
		
		if (strlen($single_bedroom_numbers) > 0){
			$single_bedroom_numbers_array = explode(',',$single_bedroom_numbers);
			for($s_m=0; $s_m < count($single_bedroom_numbers_array); $s_m++){
				
				$temp_room_id = $post_id.'-singlebedroom-'.$s_m;
				
				$this->ghob_wpdb->insert( $this->room_map_table, array(
					'room_id' => base64_encode($temp_room_id), 
					'room_name' => $single_bedroom_numbers_array[$s_m],
					'room_type' => 'single',
					'guest_house_id' => $post_id
					),array(
					'%s','%s','%s','%s')
				);
			}
		}
	}
	
	function get_room_mapid($post_id,$roomtype,$roomname){
		
		$mapid = $this->ghob_wpdb->get_var("SELECT map_id FROM $this->room_map_table WHERE guest_house_id	= '$post_id' AND room_type = '$roomtype' AND room_name = '$roomname'");
		return $mapid;
	}
	
	public function modify_booking_slots($post_id){
		
		$single_bedroom_numbers = get_post_meta( $post_id, 'singlebedroomsnumbers', true ); 
		$double_bedroom_numbers = get_post_meta( $post_id, 'doublebedroomsnumbers', true );
		$triple_bedroom_numbers = get_post_meta( $post_id, 'triplebedroomsnumbers', true ); 
		
		/*Detect if any room are deleted or added*/
		$single_bed_room_count = intval( get_post_meta( $post_id, 'singlebed', true ) );
		$double_bed_room_count = intval( get_post_meta( $post_id, 'doublebed', true ) );
		$triple_bed_room_count = intval( get_post_meta( $post_id, 'triplebed', true ) );
		
		
		/**Addition/Deletion of new/old single rooms**/
		if($single_bed_room_count > 0){
			$prev_single_bed_count = $this->count_previous_beds($post_id,'single');
			if($prev_single_bed_count){
				if($single_bed_room_count != $prev_single_bed_count){
					if($single_bed_room_count > $prev_single_bed_count){
						
						$single_bedroom_numbers_array = explode(',',$single_bedroom_numbers);
						$single_bedroom_numbers_array = array_reverse($single_bedroom_numbers_array);
						$additional_single_room_count = $single_bed_room_count - $prev_single_bed_count;
						
						for($a_n=0; $a_n < $additional_single_room_count; $a_n++){
							$this->add_room_number($post_id,'single',$single_bedroom_numbers_array[$a_n],$single_bed_room_count);
							$single_bed_room_count--;
						}
					}
					if($single_bed_room_count < $prev_single_bed_count){
						$deleted_single_room_count = $prev_single_bed_count - $single_bed_room_count;
						$single_bedroom_numbers_array = explode(',',$single_bedroom_numbers);
						$single_bedroom_numbers_array = array_reverse($single_bedroom_numbers_array);
						
						for($d_n=0; $d_n < $deleted_single_room_count; $d_n++){
							$prev_single_bed_count=$prev_single_bed_count-1;
							$this->delete_room_number($post_id,'single',$single_bedroom_numbers_array[$d_n],$prev_single_bed_count);
							
						}
					}
				}
			}
		}
		
		/**Addition/Deletion of new/old double rooms**/
		if($double_bed_room_count > 0){
			$prev_double_bed_room_count = $this->count_previous_beds($post_id,'double');
			if($prev_double_bed_room_count){
				if($double_bed_room_count != $prev_double_bed_room_count){
					if($double_bed_room_count > $prev_double_bed_room_count){
						
						$double_bedroom_numbers_array = explode(',',$double_bedroom_numbers);
						$double_bedroom_numbers_array = array_reverse($double_bedroom_numbers_array);
						$additional_double_room_count = $double_bed_room_count - $prev_double_bed_room_count;
						
						for($a_n=0; $a_n < $additional_double_room_count; $a_n++){
							$this->add_room_number($post_id,'double',$double_bedroom_numbers_array[$a_n],$double_bed_room_count);
							$double_bed_room_count--;
						}
					}
					if($double_bed_room_count < $prev_double_bed_room_count){
						$deleted_double_room_count = $prev_double_bed_room_count - $double_bed_room_count;
						$double_bedroom_numbers_array = explode(',',$double_bedroom_numbers);
						$double_bedroom_numbers_array = array_reverse($double_bedroom_numbers_array);
						
						for($d_n=0; $d_n < $deleted_double_room_count; $d_n++){
							$prev_double_bed_room_count--;
							$this->delete_room_number($post_id,'double',$double_bedroom_numbers_array[$d_n],$prev_double_bed_room_count);
							
						}
					}
				}
			}
		}
		
		/**Addition/Deletion of new/old triple rooms**/
		if($triple_bed_room_count > 0){
			$prev_triple_bed_room_count = $this->count_previous_beds($post_id,'triple');
			if($prev_triple_bed_room_count){
				if($triple_bed_room_count != $prev_triple_bed_room_count){
					if($triple_bed_room_count > $prev_triple_bed_room_count){
						
						$triple_bedroom_numbers_array = explode(',',$triple_bedroom_numbers);
						$triple_bedroom_numbers_array = array_reverse($triple_bedroom_numbers_array);
						$additional_triple_room_count = $triple_bed_room_count - $prev_triple_bed_room_count;
						
						for($a_n=0; $a_n < $additional_triple_room_count; $a_n++){
							$this->add_room_number($post_id,'triple',$triple_bedroom_numbers_array[$a_n],$triple_bed_room_count);
							$triple_bed_room_count--;
						}
					}
					if($triple_bed_room_count < $prev_triple_bed_room_count){
						$deleted_triple_room_count = $prev_triple_bed_room_count - $triple_bed_room_count;
						$triple_bedroom_numbers_array = explode(',',$triple_bedroom_numbers);
						$triple_bedroom_numbers_array = array_reverse($triple_bedroom_numbers_array);
						
						for($d_n=0; $d_n < $deleted_triple_room_count; $d_n++){
							$prev_triple_bed_room_count--;
							$this->delete_room_number($post_id,'triple',$triple_bedroom_numbers_array[$d_n],$prev_triple_bed_room_count);
							
						}
					}
				}
			}
		}
		
		/*update details of each room type*/
		$this->update_room_map_table($post_id);
	}
	
	function update_room_map_table($post_id){
		
		$single_bedroom_numbers = get_post_meta( $post_id, 'singlebedroomsnumbers', true ); 
		$double_bedroom_numbers = get_post_meta( $post_id, 'doublebedroomsnumbers', true );
		$triple_bedroom_numbers = get_post_meta( $post_id, 'triplebedroomsnumbers', true ); 
		
		if(!empty($triple_bedroom_numbers)){
			$triple_bedroom_numbers_array = explode(',',$triple_bedroom_numbers);
			$iter_tr = 0;
			foreach($triple_bedroom_numbers_array as $triple_bedroom){
				$generate_room_id = base64_encode($post_id.'-triplebedroom-'.$iter_tr);
				$this->ghob_wpdb->update( 
					$this->room_map_table, 
					array( 
						'room_name' => $triple_bedroom
					), 
					array( 'room_id' => $generate_room_id )
				);
				$iter_tr++;
				$generate_room_id = 0;
			}
		}
		
		if(!empty($double_bedroom_numbers)){
			$double_bedroom_numbers_array = explode(',',$double_bedroom_numbers);
			$iter_db = 0;
			foreach($double_bedroom_numbers_array as $double_bedroom){
				$generate_db_room_id = base64_encode($post_id.'-doublebedroom-'.$iter_db);
				$this->ghob_wpdb->update( 
					$this->room_map_table, 
					array( 
						'room_name' => $double_bedroom
					), 
					array( 'room_id' => $generate_db_room_id )
				);
				$iter_db++;
				$generate_db_room_id = 0;
			}
		}
		
		if(!empty($single_bedroom_numbers)){
			$single_bedroom_numbers_array = explode(',',$single_bedroom_numbers);
			$iter_sn = 0;
			foreach($single_bedroom_numbers_array as $single_bedroom){
				$generate_sn_room_id = base64_encode($post_id.'-singlebedroom-'.$iter_sn);
				$this->ghob_wpdb->update( 
					$this->room_map_table, 
					array( 
						'room_name' => $single_bedroom
					), 
					array( 'room_id' => $generate_sn_room_id )
				);
				$iter_sn++;
				$generate_sn_room_id = 0;
			}
		}
		
	}
	
	function delete_room_number($post_id,$roomtype,$roomnumber,$roomindex){
		
		$room_id = base64_encode($post_id.'-'.$roomtype.'bedroom-'.$roomindex);
		
		$mapid = $this->ghob_wpdb->get_var("SELECT map_id FROM $this->room_map_table WHERE guest_house_id = '$post_id' AND room_type = '$roomtype' AND room_name = '$roomnumber' AND room_id='$room_id'");
		
		if($mapid>0){
			$this->ghob_wpdb->delete( $this->booking_table, array( 'room_no' => $mapid ) );
			$this->ghob_wpdb->delete( $this->room_map_table, array( 'map_id' => $mapid ) );
		}
	}
	
	function add_room_number($post_id,$roomtype,$roomnumber,$roomindex){
		
		$temp_room_id = '';
		
		switch($roomtype){
			case 'single': { $temp_room_id = $post_id.'-singlebedroom-'.$roomindex; break;}
			case 'double': { $temp_room_id = $post_id.'-doublebedroom-'.$roomindex; break;}
			case 'triple': { $temp_room_id = $post_id.'-triplebedroom-'.$roomindex; break;}
		}
			
		$this->ghob_wpdb->insert( $this->room_map_table, array(
			'room_id' => base64_encode($temp_room_id), 
			'room_name' => $roomnumber,
			'room_type' => $roomtype,
			'guest_house_id' => $post_id
			),array(
			'%s','%s','%s','%s')
		);
		
		switch($roomtype){
			case 'single':{
				$this->add_booking_slot($post_id,$roomtype,$roomnumber,$roomindex);
				break;
			}
			case 'double':{
				$this->add_booking_slot($post_id,$roomtype,$roomnumber,$roomindex);
				break;
			}
			case 'triple':{
				$this->add_booking_slot($post_id,$roomtype,$roomnumber,$roomindex);
				break;
			}
		}
	}
	
	function add_booking_slot($post_id,$roomtype,$roomnumber,$roomindex){
		
		$loop_counter = 0;

		switch($roomtype){
			case 'single': { $loop_counter = 1; break;}
			case 'double': { $loop_counter = 2; break;}
			case 'triple': { $loop_counter = 3; break;}
		}
		
		for($g_i = ($roomindex-1)*$loop_counter; $g_i < $roomindex*$loop_counter; $g_i++){
			
			//create a unique bed_id
			$temp_bed_id = $post_id.'-'.$roomtype.'-'.$g_i;
			
			$this->ghob_wpdb->insert( $this->booking_table, array(
				'bed_id' => base64_encode($temp_bed_id), 
				'room_no' => $this->get_room_mapid($post_id,$roomtype,$roomnumber),
				'room_type' => $roomtype,
				'guest_house_id' => $post_id
				),array(
				'%s','%s','%s','%s')
			);
		}
	}
	
	public function detect_existing_slots($post_id){
		
		$rowcount = $this->ghob_wpdb->get_var("SELECT COUNT(*) FROM $this->booking_table WHERE guest_house_id	= '$post_id'");
		if($rowcount >0){
			return false;  //Slots already created for this guest house. Proceed with Caution
		}
		return true;
	}
	
	function count_previous_beds($post_id,$room_type){
		
		$room_type_val = '';
		
		switch($room_type){
			case 'single':{ $room_type_val='single'; break; }
			case 'double':{ $room_type_val='double'; break; }
			case 'triple':{ $room_type_val='triple'; break; }
		}
		
		$rowcount = $this->ghob_wpdb->get_var("SELECT COUNT(*) FROM $this->booking_table WHERE guest_house_id	= '$post_id' AND room_type='$room_type_val'");
		if($rowcount >0){
			return $rowcount;  //Slots already created for this guest house. Proceed with Caution
		}
		return false;
	}
}
?>