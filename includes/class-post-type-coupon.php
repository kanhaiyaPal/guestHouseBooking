<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

class GHOB_post_type_coupon_init {	
	public function __construct()
    {
		/*initialize custom post type hook*/
		add_action( 'init', array( $this, 'create_coupon_post') );
		
		/*Custom Meta Box for showing booking details*/
		add_action( 'admin_init', array( $this, 'create_meta_box_coupons') ); 
		
		/*Save custom meta box data*/
		add_action( 'save_post', array( $this, 'add_coupon_details_fields'), 10, 2 ); 

	}
	
	function create_coupon_post(){
		register_post_type( 'coupon',
			array(
				'labels' => array(
					'name' => 'Coupons',
					'singular_name' => 'Coupon',
					'edit' => 'Edit',
					'edit_item' => 'Edit Coupons',
					'new_item' => 'New Coupon',
					'view' => 'View',
					'view_item' => 'View Coupons',
					'search_items' => 'Search Coupons',
					'not_found' => 'No Coupons found',
					'not_found_in_trash' => 'No Coupons found in Trash',
					'parent' => 'Parent Coupon'
				),
				'public' => true,
				'menu_position' => 16,
				'supports' => array( 'title'),
				'taxonomies' => array( '' ),
				'menu_icon' => plugins_url( '../assets/images/coupons_ico.png', __FILE__ ),
				'has_archive' => true
			)
		);
	}
	
	function create_meta_box_coupons(){
		add_meta_box( 'coupons_meta_box',
			'Coupons Details',
			array( $this, 'display_coupon_details_meta_box'),
			'coupon', 'normal', 'high'
		);
	}
	
	function display_coupon_details_meta_box($coupon_details){
		
		require_once GHOB_PLUGIN_DIR . '/templates/admin/post_meta_box_coupons_details.php';
	}
	
	function add_coupon_details_fields($coupon_details_id, $coupon_details){
		
		if($coupon_details->post_type == 'coupon'){
			
			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}
			
			if ( isset( $_POST['coupon_code'] ) && $_POST['coupon_code'] != '' ) {
				update_post_meta( $coupon_details_id, 'couponcode', $_POST['singlebed_count'] );
			}else{
				update_post_meta( $coupon_details_id, 'couponcode', '' );
			}
			
			if ( isset( $_POST['coupon_percentage'] ) && $_POST['coupon_percentage'] != '' ) {
				update_post_meta( $coupon_details_id, 'couponpercentage', $_POST['singlebed_count'] );
			}else{
				update_post_meta( $coupon_details_id, 'couponpercentage', '' );
			}
			
			if ( isset( $_POST['coupon_amount_max'] ) && $_POST['coupon_amount_max'] != '' ) {
				update_post_meta( $coupon_details_id, 'couponmaxamount', $_POST['singlebed_count'] );
			}else{
				update_post_meta( $coupon_details_id, 'couponmaxamount', '' );
			}
			
			if ( isset( $_POST['coupon_min_bill'] ) && $_POST['coupon_min_bill'] != '' ) {
				update_post_meta( $coupon_details_id, 'couponminbilling', $_POST['singlebed_count'] );
			}else{
				update_post_meta( $coupon_details_id, 'couponminbilling', '' );
			}
		}
	}
}

$wpGHOB_coupon_instance = new GHOB_post_type_coupon_init();
?>