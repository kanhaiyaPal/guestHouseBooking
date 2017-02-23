<?php 
if ( !defined( 'ABSPATH' ) ) {
	exit();
}
?>
<ul id="ghob_tabs">
    <li class="active">Live Booking of Room</li>
    <li>View Occupancy Status</li>
</ul>
<ul id="ghob_tab">
    <!--Live Booking of Rooms-->
    <li class="active">
    	<?php 
			/*include view occupany template*/
			require_once GHOB_PLUGIN_DIR. '/templates/admin/page-live-booking.php'; 
		?>
    </li>
    
    <li>
        <?php 
			/*include view occupany template*/
			require_once GHOB_PLUGIN_DIR. '/templates/admin/page-view-occupany.php'; 
		?>
    </li>
</ul>
<div class="ghob_overlay_ajax" style="display: none">
    <div class="ghob_overlay_ajax_center">
        <img alt="Loading..." src="<?php echo plugins_url( '../../assets/images/ajax-loader.gif', __FILE__); ?>" />
    </div>
</div>