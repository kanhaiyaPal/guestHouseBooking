<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$guest_house_list = $this->populate_guest_house_having_slots();
?>
Guest House 
<select name="selected_guest_house_view" >
 <option value="0">--Select Guest house--</option>
 <?php echo $guest_house_list; ?>
</select>
<input type="button" value="Go" onclick="show_guest_house_map()"/>
<div class="display_mapping_wrapper">
	
</div>