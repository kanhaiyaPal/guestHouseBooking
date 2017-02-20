<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

class GHOB_post_type_guest_house_init {	
	public function __construct()
    {
		/*initialize custom post type hook*/
		add_action( 'init', array( $this, 'create_guest_house_post') );
		
		/*enqueue js for guest house booking details and gallery uploads*/
		add_action( 'admin_enqueue_scripts', array($this, 'admin_custom_post_js') );
		
		/*Custom Meta Box for entering details and gallery of Guest House*/
		add_action( 'admin_init', array( $this, 'create_meta_box_guest_house') ); 
		
		/*Save custom meta box data*/
		add_action( 'save_post', array( $this, 'add_guest_house_details_fields'), 10, 2 ); 
		
		/*add hook to template for Guest House Display*/
		add_filter( 'template_include', array( $this, 'include_template_function'), 1 ); 
		
		/*register custom taxonomy for guest-house post*/
		add_action( 'init',  array( $this, 'create_guesthouse_hierarchical_taxonomy'), 0 ); 
		
		/*For Booking slots and room mapping class*/
		require_once GHOB_PLUGIN_DIR . '/includes/class-booking-slot-table.php';
		
	}
	
	function create_guest_house_post()
	{
		register_post_type( 'guest_house',
			array(
				'labels' => array(
					'name' => 'Guest Houses',
					'singular_name' => 'Guest House',
					'add_new' => 'Add New',
					'add_new_item' => 'Add New Guest House',
					'edit' => 'Edit',
					'edit_item' => 'Edit Guest House',
					'new_item' => 'New Guest House',
					'view' => 'View',
					'view_item' => 'View Guest Houses',
					'search_items' => 'Search Guest Houses',
					'not_found' => 'No Guest Houses found',
					'not_found_in_trash' => 'No Guest Houses found in Trash',
					'parent' => 'Parent Guest House'
				),
	 
				'public' => true,
				'menu_position' => 15,
				'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),
				'taxonomies' => array( '' ),
				'menu_icon' => plugins_url( '../assets/images/guest_ico.png', __FILE__ ),
				'has_archive' => true
			)
		);
	}
	
	function create_meta_box_guest_house()
	{
		add_meta_box( 'guest_house_meta_box',
			'Guest House Details',
			array( $this, 'display_guest_house_details_meta_box'),
			'guest_house', 'normal', 'high'
		);
		
		/*Add Guest House Gallery Box*/
		add_meta_box( 'guest_house_gallery_meta_box',
			'Guest House Gallery',
			array( $this, 'display_guest_house_gallery_meta_box'),
			'guest_house', 'normal', 'high'
		);
	}
	
	function display_guest_house_details_meta_box($guest_house_details)
	{
		require_once GHOB_PLUGIN_DIR . '/templates/admin/post_meta_box.php';
	}
	
	function display_guest_house_gallery_meta_box($guest_house_details)
	{
		require_once GHOB_PLUGIN_DIR . '/templates/admin/post_meta_gallery_box.php';
	}
	
	function add_guest_house_details_fields($guest_house_details_id, $guest_house_details)
	{
		if($guest_house_details->post_type == 'guest_house'){
			
			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}
			
			if ( isset( $_POST['room_cat_single'] ) && $_POST['room_cat_single'] != '' ) {
				update_post_meta( $guest_house_details_id, 'singlebed', $_POST['singlebed_count'] );
				update_post_meta( $guest_house_details_id, 'singlebedprice', $_POST['singlebed_rate'] );
			}else{
				update_post_meta( $guest_house_details_id, 'singlebed', '' );
				update_post_meta( $guest_house_details_id, 'singlebedprice', '' );
			}
			if ( isset( $_POST['room_cat_double'] ) && $_POST['room_cat_double'] != '' ) {
				update_post_meta( $guest_house_details_id, 'doublebed', $_POST['doublebed_count'] );
				update_post_meta( $guest_house_details_id, 'doublebedprice', $_POST['doublebed_rate'] );
				update_post_meta( $guest_house_details_id, 'doubleroomprice', $_POST['doubleroom_rate'] );
			}else{
				update_post_meta( $guest_house_details_id, 'doublebed', '' );
				update_post_meta( $guest_house_details_id, 'doublebedprice', '' );
				update_post_meta( $guest_house_details_id, 'doubleroomprice', '' );
			}
			if ( isset( $_POST['room_cat_triple'] ) && $_POST['room_cat_triple'] != '' ) {
				update_post_meta( $guest_house_details_id, 'triplebed', $_POST['triplebed_count'] );
				update_post_meta( $guest_house_details_id, 'triplebedprice', $_POST['triplebed_rate'] );
				update_post_meta( $guest_house_details_id, 'tripleroomprice', $_POST['tripleroom_rate'] );
			}else{
				update_post_meta( $guest_house_details_id, 'triplebed', '' );
				update_post_meta( $guest_house_details_id, 'triplebedprice', '' );
				update_post_meta( $guest_house_details_id, 'tripleroomprice','');
			}
			
			/*Saving room amenities settings*/
			if( isset($_POST['room_amenities']) && is_array($_POST['room_amenities']) ) {
				$amenities_list = implode(',', $_POST['room_amenities']);
				update_post_meta($guest_house_details_id, 'guest_house_amenities', $amenities_list);
			}else{
				update_post_meta($guest_house_details_id, 'guest_house_amenities', '');
			}
			
			/*Saving gallery images*/
			if( isset($_POST['guest_house_gallery']) && is_array($_POST['guest_house_gallery']) ) {
				$amenities_list = implode(',', $_POST['guest_house_gallery']);
				update_post_meta($guest_house_details_id, 'guest_house_gallery', $amenities_list);
			}else{
				update_post_meta($guest_house_details_id, 'guest_house_gallery', '');
			}
			
			/*Saving room numbers*/
			//single
			if( isset($_POST['single_bedrooms_numbers']) && is_array($_POST['single_bedrooms_numbers']) ) {
				$single_room_list = implode(',', $_POST['single_bedrooms_numbers']);
				update_post_meta($guest_house_details_id, 'singlebedroomsnumbers', $single_room_list);
			}else{
				update_post_meta($guest_house_details_id, 'singlebedroomsnumbers', '');
			}
			//double
			if( isset($_POST['double_bedrooms_numbers']) && is_array($_POST['double_bedrooms_numbers']) ) {
				$double_room_list = implode(',', $_POST['double_bedrooms_numbers']);
				update_post_meta($guest_house_details_id, 'doublebedroomsnumbers', $double_room_list);
			}else{
				update_post_meta($guest_house_details_id, 'doublebedroomsnumbers', '');
			}
			//triple
			if( isset($_POST['triple_bedrooms_numbers']) && is_array($_POST['triple_bedrooms_numbers']) ) {
				$triple_room_list = implode(',', $_POST['triple_bedrooms_numbers']);
				update_post_meta($guest_house_details_id, 'triplebedroomsnumbers', $triple_room_list);
			}else{
				update_post_meta($guest_house_details_id, 'triplebedroomsnumbers', '');
			}
			
			/*update booking slots and room mapping*/
			$booking_slots_mapping_obj = new GHOIB_booking_slot_table();
			$data_already_exist = $booking_slots_mapping_obj->detect_existing_slots($guest_house_details_id);
			if($data_already_exist){
				$booking_slots_mapping_obj->generate_room_map_first_time($guest_house_details_id);
				$booking_slots_mapping_obj->create_new_booking_slots_first_time($guest_house_details_id);
			}else{

				$booking_slots_mapping_obj->modify_booking_slots($guest_house_details_id);
			}
		}
	}
	
	function include_template_function( $template_path ) {
		if ( get_post_type() == 'guest_house' ) {
			if ( is_single() ) {
				// checks if the file exists in the theme first,
				// otherwise serve the file from the plugin
				if ( $theme_file = locate_template( array ( 'single-guest_house.php' ) ) ) {
					$template_path = $theme_file;
				} else {
					$template_path = GHOB_PLUGIN_DIR . '/templates/front/single-guest_house.php';
				}
			}
		}
		return $template_path;
	}
	
	function create_guesthouse_hierarchical_taxonomy() {

		/*Register Guest House City Taxonomy*/
		$labels_city = array(
			'name' => _x( 'Manage City and Locations', 'taxonomy general name' ),
			'singular_name' => _x( 'Guest House City/Location', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Guest House City/Location' ),
			'all_items' => __( 'All Guest House City/Location' ),
			'parent_item' => __( 'Parent Guest House City/Location' ),
			'parent_item_colon' => __( 'Parent Guest House City/Location:' ),
			'edit_item' => __( 'Edit Guest House City/Location' ), 
			'update_item' => __( 'Update Guest House City/Location' ),
			'add_new_item' => __( 'Add New Guest House City/Location' ),
			'new_item_name' => __( 'New Guest House City Name/Location' ),
			'menu_name' => __( 'Guest House City/Location' ),
		); 	

		register_taxonomy('GHOB_City_Location','guest_house', array(
			'hierarchical' => true,
			'labels' => $labels_city,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'ghob_city_location' ),
		));

	}
	
	function admin_custom_post_js($hook) {

		$cpt = 'guest_house';

		if( in_array($hook, array('post.php', 'post-new.php') ) ){
			$screen = get_current_screen();

			if( is_object( $screen ) && $cpt == $screen->post_type ){

				// Register, enqueue scripts and styles here
				wp_enqueue_script( 'custom-post-guest-house-js',plugins_url( '../assets/js/admin/custom-post-guest-house.js', __FILE__) );
			}
		}
		
	}
	
	function GHOB_room_number_html_generator($roomtype,$guest_house_id){
		
		$bedroom_numbers = '';
		$room_html = '';
		$room_type_name = '';
		$room_field_name = '';
		
		switch($roomtype){
			case 'single':{ 
				$bedroom_numbers = get_post_meta( $guest_house_id, 'singlebedroomsnumbers', true ); 
				$room_type_name = 'Single Bed Rooms'; 
				$room_field_name = 'single';
				break;
			} 	 
			case 'double':{	
				$bedroom_numbers = get_post_meta( $guest_house_id, 'doublebedroomsnumbers', true ); 
				$room_type_name = 'Double Bed Rooms';
				$room_field_name = 'double';
				break;
			} 
			case 'triple':{ 
				$bedroom_numbers = get_post_meta( $guest_house_id, 'triplebedroomsnumbers', true ); 
				$room_type_name = 'Triple Bed Rooms';
				$room_field_name = 'triple';
				break;
			} 
		}
		if(!empty($bedroom_numbers)){
			$bedroom_numbers_array = explode(',',$bedroom_numbers);
			$room_html .= '<table width="100%"><tr><td><strong>'.$room_type_name.'</strong><br/><hr/></td></tr>';
			$room_html .=  '<tr><td>';
			foreach($bedroom_numbers_array as $sn_room_no){
				
				$room_html .=  '<span><input type="text" name="'.$room_field_name.'_bedrooms_numbers[]" value="'.$sn_room_no.'" /></span><span>&nbsp;</span>';
				
			}
			$room_html .=  '</td></tr>';
			$room_html .= '</table>';
		}
		return $room_html;
	}
}

$wpGHOB_custom_post_type = new GHOB_post_type_guest_house_init();