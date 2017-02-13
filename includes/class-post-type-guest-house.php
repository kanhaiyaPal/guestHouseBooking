<?php
class GHOB_post_type_guest_house_init {	
	public function __construct()
    {
		/*initialize custom post type hook*/
		add_action( 'init', array( $this, 'create_guest_house_post') );
		
		/*Custom Meta Box for entering details of Guest House*/
		add_action( 'admin_init', array( $this, 'create_meta_box_guest_house') ); 
		
		/*Save custom meta box data*/
		add_action( 'save_post', array( $this, 'add_guest_house_details_fields'), 10, 2 ); 
		
		/*add hook to template for Guest House Display*/
		add_filter( 'template_include', array( $this, 'include_template_function'), 1 ); 
		
		/*register custom taxonomy for guest-house post*/
		add_action( 'init',  array( $this, 'create_guesthouse_hierarchical_taxonomy'), 0 ); 
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
	}
	
	function display_guest_house_details_meta_box($guest_house_details)
	{
		require_once GHOB_PLUGIN_DIR . '/templates/admin/post_meta_box.php';
	}
	
	function add_guest_house_details_fields($guest_house_details_id, $guest_house_details)
	{
		if($guest_house_details->post_type == 'guest_house'){
			
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
			}else{
				update_post_meta( $guest_house_details_id, 'doublebed', '' );
				update_post_meta( $guest_house_details_id, 'doublebedprice', '' );
			}
			if ( isset( $_POST['room_cat_triple'] ) && $_POST['room_cat_triple'] != '' ) {
				update_post_meta( $guest_house_details_id, 'triplebed', $_POST['triplebed_count'] );
				update_post_meta( $guest_house_details_id, 'triplebedprice', $_POST['triplebed_rate'] );
			}else{
				update_post_meta( $guest_house_details_id, 'triplebed', '' );
				update_post_meta( $guest_house_details_id, 'triplebedprice', '' );
			}
			
			if( isset($_POST['room_amenities']) && is_array($_POST['room_amenities']) ) {
				$amenities_list = implode(',', $_POST['room_amenities']);
				update_post_meta($guest_house_details_id, 'guest_house_amenities', $amenities_list);
			}else{
				update_post_meta($guest_house_details_id, 'guest_house_amenities', '');
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
		$labels = array(
			'name' => _x( 'Guest House City', 'taxonomy general name' ),
			'singular_name' => _x( 'Guest House City', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Guest House City' ),
			'all_items' => __( 'All Guest House City' ),
			'parent_item' => __( 'Parent Guest House City' ),
			'parent_item_colon' => __( 'Parent Guest House City:' ),
			'edit_item' => __( 'Edit Guest House City' ), 
			'update_item' => __( 'Update Guest House City' ),
			'add_new_item' => __( 'Add New Guest House City' ),
			'new_item_name' => __( 'New Guest House City Name' ),
			'menu_name' => __( 'Guest House City' ),
		); 	

		register_taxonomy('Guest_House_City','guest_house', array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'guest_house_city' ),
		));

		/*Register Guest House Location Taxonomy*/
		$labels = array(
			'name' => _x( 'Guest House Location', 'taxonomy general name' ),
			'singular_name' => _x( 'Guest House Location', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Guest Location City' ),
			'all_items' => __( 'All Guest House Location' ),
			'parent_item' => __( 'Parent Guest House Location' ),
			'parent_item_colon' => __( 'Parent Guest House Location:' ),
			'edit_item' => __( 'Edit Guest House Location' ), 
			'update_item' => __( 'Update Guest House Location' ),
			'add_new_item' => __( 'Add New Guest House Location' ),
			'new_item_name' => __( 'New Guest House Location Name' ),
			'menu_name' => __( 'Guest House Location' ),
		); 	

		register_taxonomy('Guest_House_Location','guest_house', array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'guest_house_location' ),
		));
	}
}

$wpGHOB_custom_post_type = new GHOB_post_type_guest_house_init();