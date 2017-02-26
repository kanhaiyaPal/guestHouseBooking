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
			<select name="selected_guest_house_sp_mov" >
			<option value="0">--Select Guest house--</option>
			<?php echo $guest_house_list_sp; ?>
			</select> 
			<table id="form_guestchange">
				<tr><td>Move From </td></tr>
				<tr>
					<td>Select Room:
					<select name="selected_guest_house_sp" >
					<option value="0">--Select Guest house--</option>
					<?php echo $guest_house_list_sp; ?>
					</select>
					</td>
					<td>Select Guest:
					<select name="selected_guest_house_sp" >
					<option value="0">--Select Guest house--</option>
					<?php echo $guest_house_list_sp; ?>
					</select>
					
					</td>
				</tr>
				<tr><td>Move To </td></tr>
				<tr>
					<td>Select Room:
					<select name="selected_guest_house_sp" >
					<option value="0">--Select Guest house--</option>
					<?php echo $guest_house_list_sp; ?>
					</select>
					</td>
					<td>Select Bed:
					<select name="selected_guest_house_sp" >
					<option value="0">--Select Guest house--</option>
					<?php echo $guest_house_list_sp; ?>
					</select>
					
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td><h4>Early Checkout</h4></td>
	</tr>
	<tr>
		<td>Guest House:
			<select name="selected_guest_house_sp_mov" >
			<option value="0">--Select Guest house--</option>
			<?php echo $guest_house_list_sp; ?>
			</select> 
			Select Room:
			<select name="selected_guest_house_sp_mov" >
			<option value="0">--Select Guest house--</option>
			<?php echo $guest_house_list_sp; ?>
			</select> 
			Select Guest:
			<select name="selected_guest_house_sp_mov" >
			<option value="0">--Select Guest house--</option>
			<?php echo $guest_house_list_sp; ?>
			</select>
			Choose Date:
			<input type="date" name="early_checkout_date" />
		</td>
	</tr>
</table>