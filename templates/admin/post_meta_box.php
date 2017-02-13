<?php
/*Guest House Post Meta Box HTML*/
	// Retrieve current value of rooms based on review ID
    $single_bed_room = intval( get_post_meta( $guest_house_details->ID, 'singlebed', true ) );
	if($single_bed_room>0){ $single_bed_cost =  get_post_meta( $guest_house_details->ID, 'singlebedprice', true ); }
	$double_bed_room = intval( get_post_meta( $guest_house_details->ID, 'doublebed', true ) );
	if($double_bed_room>0){ $double_bed_cost =  get_post_meta( $guest_house_details->ID, 'doublebedprice', true ); }
	$triple_bed_room = intval( get_post_meta( $guest_house_details->ID, 'triplebed', true ) );
	if($triple_bed_room>0){ $triple_bed_cost =  get_post_meta( $guest_house_details->ID, 'triplebedprice', true ); }
	
	$guest_house_amenities = get_post_meta( $guest_house_details->ID, 'guest_house_amenities', true );
	$guest_house_amenitites_array = explode(',',$guest_house_amenities);
	
    ?>
	<script>
	function GHOB_handle_metabox_Click(cb){
		if(cb.checked== true){
			document.getElementById(cb.value+"_roomcount").style.display = "block";
			document.getElementById(cb.value+"_roomprice").style.display = "block";
		}else{
			document.getElementById(cb.value+"_roomcount").style.display = "none";
			document.getElementById(cb.value+"_roomprice").style.display = "none";
		}
	}
	</script>
    <table style="width:100%">
		<tr>
			<td><strong>Available Rooms</strong></td>
			<td><input type="checkbox" name="room_cat_single" value="singlebed" <?php if($single_bed_room){ echo "checked"; } ?>  onclick='GHOB_handle_metabox_Click(this);' />Single Bed Rooms</td>
			<td><input id="singlebed_roomcount" style="display:<?php if($single_bed_room){echo "block";}else{echo "none";} ?>" type="text" name="singlebed_count" placeholder="Number of Beds" value="<?php echo ($single_bed_room >0)?$single_bed_room:''; ?>"  /></td>
			<td><input id="singlebed_roomprice" style="display:<?php if($single_bed_room){echo "block";}else{echo "none";} ?>" type="text" name="singlebed_rate" placeholder="Price" value="<?php echo $single_bed_cost; ?>"  /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="checkbox" name="room_cat_double" value="doublebed" <?php if($double_bed_room){ echo "checked"; } ?>  onclick='GHOB_handle_metabox_Click(this);' />Double Bed Rooms</td>
			<td><input id="doublebed_roomcount" style="display:<?php if($double_bed_room){echo "block";}else{echo "none";} ?>" type="text" name="doublebed_count" placeholder="Number of Beds" value="<?php echo ($double_bed_room >0)?$double_bed_room:''; ?>" /></td>
			<td><input id="doublebed_roomprice" style="display:<?php if($double_bed_room){echo "block";}else{echo "none";} ?>" type="text" name="doublebed_rate" placeholder="Price" value="<?php echo $double_bed_cost; ?>" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="checkbox" name="room_cat_triple" value="triplebed" <?php if($triple_bed_room){ echo "checked"; } ?>  onclick='GHOB_handle_metabox_Click(this);' />Triple Bed Rooms</td>
			<td><input id="triplebed_roomcount" style="display:<?php if($triple_bed_room){echo "block";}else{echo "none";} ?>" type="text" name="triplebed_count" placeholder="Number of Beds" value="<?php echo ($triple_bed_room >0)?$triple_bed_room:''; ?>" /></td>
			<td><input id="triplebed_roomprice" style="display:<?php if($triple_bed_room){echo "block";}else{echo "none";} ?>" type="text" name="triplebed_rate" placeholder="Price" value="<?php echo $triple_bed_cost; ?>" /></td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<!--Room Type Input Ends-->
		<tr>
			<td><strong>Amenities:</strong></td>
			<td><input type="checkbox" name="room_amenities[]" value="wifi" <?php if(in_array("wifi",$guest_house_amenitites_array)){echo "checked";} ?> />Wifi</td>
			<td><input type="checkbox" name="room_amenities[]" value="geyser" <?php if(in_array("geyser",$guest_house_amenitites_array)){echo "checked";} ?>  />Geyser</td>
			<td><input type="checkbox" name="room_amenities[]" value="breakfast" <?php if(in_array("breakfast",$guest_house_amenitites_array)){echo "checked";} ?> />Complimetary Breakfast</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="room_amenities[]" value="airc" <?php if(in_array("airc",$guest_house_amenitites_array)){echo "checked";} ?> />AC</td>
			<td><input type="checkbox" name="room_amenities[]" value="tv" <?php if(in_array("tv",$guest_house_amenitites_array)){echo "checked";} ?> />TV</td>
			<td><input type="checkbox" name="room_amenities[]" value="gym" <?php if(in_array("gym",$guest_house_amenitites_array)){echo "checked";} ?> />Gym</td>
			<td><input type="checkbox" name="room_amenities[]" value="minifr" <?php if(in_array("minifr",$guest_house_amenitites_array)){echo "checked";} ?> />Mini Fridge</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="room_amenities[]" value="banquet" <?php if(in_array("banquet",$guest_house_amenitites_array)){echo "checked";} ?> />Banquet Hall</td>
			<td><input type="checkbox" name="room_amenities[]" value="conferenceroom" <?php if(in_array("conferenceroom",$guest_house_amenitites_array)){echo "checked";} ?> />Conference Room</td>
			<td><input type="checkbox" name="room_amenities[]" value="parking" <?php if(in_array("parking",$guest_house_amenitites_array)){echo "checked";} ?> />Parking Facility</td>
			<td><input type="checkbox" name="room_amenities[]" value="bar" <?php if(in_array("bar",$guest_house_amenitites_array)){echo "checked";} ?> />Bar</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="room_amenities[]" value="swimmingpool" <?php if(in_array("swimmingpool",$guest_house_amenitites_array)){echo "checked";} ?> />Swimming Pool</td>
			<td><input type="checkbox" name="room_amenities[]" value="powerbackup" <?php if(in_array("powerbackup",$guest_house_amenitites_array)){echo "checked";} ?> />Power Backup</td>
			<td><input type="checkbox" name="room_amenities[]" value="safe" <?php if(in_array("safe",$guest_house_amenitites_array)){echo "checked";} ?> />In Room Safe</td>
			<td><input type="checkbox" name="room_amenities[]" value="iron" <?php if(in_array("iron",$guest_house_amenitites_array)){echo "checked";} ?> />Iron</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="room_amenities[]" value="cleanwashroom" <?php if(in_array("cleanwashroom",$guest_house_amenitites_array)){echo "checked";} ?> />Clean Washroom</td>
			<td><input type="checkbox" name="room_amenities[]" value="wheelchair" <?php if(in_array("wheelchair",$guest_house_amenitites_array)){echo "checked";} ?> />Wheelchair Accessible</td>
			<td><input type="checkbox" name="room_amenities[]" value="roomheater" <?php if(in_array("roomheater",$guest_house_amenitites_array)){echo "checked";} ?> />Room Heater</td>
			<td><input type="checkbox" name="room_amenities[]" value="laundry" <?php if(in_array("laundry",$guest_house_amenitites_array)){echo "checked";} ?> />Laundry</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="room_amenities[]" value="spawellness" <?php if(in_array("spawellness",$guest_house_amenitites_array)){echo "checked";} ?> />Spa and Wellness Centre</td>
			<td><input type="checkbox" name="room_amenities[]" value="elevator" <?php if(in_array("elevator",$guest_house_amenitites_array)){echo "checked";} ?> />Elevator</td>
			<td><input type="checkbox" name="room_amenities[]" value="petfriendly" <?php if(in_array("petfriendly",$guest_house_amenitites_array)){echo "checked";} ?> />Pet Friendly</td>
			<td><input type="checkbox" name="room_amenities[]" value="hairdryer" <?php if(in_array("hairdryer",$guest_house_amenitites_array)){echo "checked";} ?> />Hair Dryer</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="room_amenities[]" value="cards" <?php if(in_array("cards",$guest_house_amenitites_array)){echo "checked";} ?> />Cards Accepted</td>
			<td><input type="checkbox" name="room_amenities[]" value="smokingarea" <?php if(in_array("smokingarea",$guest_house_amenitites_array)){echo "checked";} ?> />Smoking Area</td>
			<td><input type="checkbox" name="room_amenities[]" value="baconyview" <?php if(in_array("baconyview",$guest_house_amenitites_array)){echo "checked";} ?> />Balcony View</td>
			<td>&nbsp;</td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
    </table>