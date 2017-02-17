<?php 
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

class GHOIB_plugin_activation{
	
	static function create_GHOB_plugin_database_table()
	{
		global $table_prefix, $wpdb;

		$tblname = 'booking_slots';
		$wp_track_table = $table_prefix . "$tblname ";
		$charset_collate = $wpdb->get_charset_collate();
		
		
		#Check to see if the table exists already, if not, then create it

		if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table) 
		{

			$sql = "CREATE TABLE ". $wp_track_table . " ( ";
			$sql .= "  `slot_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, ";
			$sql .= "  `bed_id` varchar(100) NOT NULL COMMENT 'system assigned bed id', ";
			$sql .= "  `room_no` varchar(100) NOT NULL COMMENT 'room number from the guest house post', ";
			$sql .= "  `room_type` varchar(100) NOT NULL, ";
			$sql .= "  `booked_status` varchar(100) NOT NULL DEFAULT '0' COMMENT 'Booked status(will contain 0 if free or booking id if occupied)', ";
			$sql .= "  `guest_house_id` varchar(100) NOT NULL COMMENT 'Post id of guest house', ";
			$sql .= "  `is_engaged` tinyint(1) NOT NULL DEFAULT '0' ";
			$sql .= ")$charset_collate; ";
			require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
			require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			
			dbDelta($sql);
		}
		
		//file_put_contents(ABSPATH. 'wp-content/plugins/activation_output_buffer.html', ob_get_contents());
	}
}
?>