<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$guest_house_list_sp = $this->populate_guest_house_having_slots();
?>
<table>
	<tr>
		<td><h4>Change Room for a Guest</h4></td>
	</tr>
	<tr>
		<td>Guest House:
			<select name="selected_guest_house_sp_mov" onchange="sp_get_rooms_list(this.value)">
			<option value="0">--Select Guest house--</option>
			<?php echo $guest_house_list_sp; ?>
			</select> 
			<table id="form_guestchange">
				<tr><td>Move From </td></tr>
				<tr>
					<td>Select Room:
					<select name="select_room_sp" onchange="sp_get_guests_list(this.value)">
					<option value="0">--Select Room--</option>
					</select>
					</td>
					<td>Select Guest Name:
					<select name="select_room_guest_sp" >
					<option value="0">--Select Guest--</option>
					</select>
					
					</td>
				</tr>
				<tr><td>Move To </td></tr>
				<tr>
					<td>Select Room:
					<select name="select_room_shifted_sp" >
					<option value="0">--Select Room--</option>
					</select>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
					<input type="button" value="Move Guest" onclick="shift_current_guest_to_room()" /> 
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>