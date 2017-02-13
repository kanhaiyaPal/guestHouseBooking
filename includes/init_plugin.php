<?php 

class GHOB_init {
    public function __construct()
    {
		require_once GHOB_PLUGIN_DIR . '/includes/class-post-type-guest-house.php'; 
    }
	
	
}
 
$wpGHOB = new GHOB_init();
