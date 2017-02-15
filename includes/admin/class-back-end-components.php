<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

class GHOB_admin_components_setup {	
	public function __construct()
    {
		/*Register admin menus*/
		add_action( 'admin_menu', array($this, 'register_admin_menus') );
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
		echo 'Manage Reservations';
	}
}

$wpGHOB_setup_admin_components = new GHOB_admin_components_setup();