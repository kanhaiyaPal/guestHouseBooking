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
		
		/*Ajax handler for reservation fields*/
		add_action( 'wp_ajax_locate_city_location', array($this, 'ghob_locate_city_loaction') );
		add_action( 'wp_ajax_locate_guest_house', array($this, 'ghob_locate_guest_houses') );
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
				if(strlen(get_the_title())>0){get_the_title();}else{echo 'No Title Defined';}
				echo "</option>";
			}
		}
		wp_die();
	}
}

$wpGHOB_setup_admin_components = new GHOB_admin_components_setup();