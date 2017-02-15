<?php 
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

class GHOB_init {
    public function __construct()
    {
		/*add custom post types*/
		require_once GHOB_PLUGIN_DIR . '/includes/class-post-type-guest-house.php'; 
		
		/*set up admin side components*/
		require_once GHOB_PLUGIN_DIR. '/includes/admin/class-back-end-components.php';
    }
	
	
}
 
$wpGHOB = new GHOB_init();
