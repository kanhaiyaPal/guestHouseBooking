<?php 
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$city_list = $this->populate_guest_house_city();
?>
<ul id="ghob_tabs">
    <li class="active">Live Booking of Room</li>
    <li>View Bookings</li>
    <li>View Occupancy Status</li>
    <li>Coupon management</li>
    <li>Tab 5</li>
</ul>
<ul id="ghob_tab">
    <!--Live Booking of Rooms-->
    <li class="active">
    	<div class="main_live_container_div">
        	<div class="form_container form-style-3" >
            	<fieldset><legend>Book a Room</legend>
                	<label for="resev_city"><span>City</span><span class="required">*</span>
                    	<select name="resev_city" onchange="ghob_search_location(this.value)" class="select-field">
                        	<option value="0" selected="selected">--Select City--</option>
							<?php echo $city_list; ?>
                        </select>
                    </label>
                    <label for="resev_location"><span>Location</span><span class="required">*</span>
                    	<select name="resev_location" class="select-field" onchange="ghob_search_guest_house(this.value)" >
                        	<option value="0" selected="selected">--Select Location--</option>
                        </select>
                    </label>
                    <label for="resev_guest_house"><span>Guest House</span><span class="required">*</span>
                    	<select name="resev_guest_house" class="select-field" >
                        	<option value="0" selected="selected">--Select Guest House--</option>
                        </select>
                    </label>
                    <label for="check_in_date"><span>Check-In Date</span><span class="required">*</span>
                    	<input type="text" name="check_in_date" value="" class="ghob-admin-datepicker" />
                    </label>
                    <label for="check_out_date"><span>Check-Out Date</span><span class="required">*</span>
                    	<input type="text" name="check_out_date" value="" class="ghob-admin-datepicker"  />
                    </label>
                    <label for="number_of_beds"><span>Number of Beds</span><span class="required">&nbsp;</span>
                    	<select name="number_of_beds" class="select-field" >
                        	<option value="0" selected="selected">--Select Number of Beds--</option>
                            <option value="1" >1</option>
                            <option value="2" >2</option>
                            <option value="3" >3</option>
                            <option value="4" >4</option>
                            <option value="5" >5</option>
                            <option value="6" >6</option>
                            <option value="7" >7</option>
                            <option value="8" >8</option>
                            <option value="9" >9</option>
                            <option value="10" >10</option>
                        </select>
                    </label>
					-OR-
                    <label for="number_of_rooms"><span>Number of Rooms</span><span class="required">&nbsp;</span>
                    	<select name="number_of_rooms" class="select-field" >
                        	<option value="0" selected="selected">--Select Number of Rooms--</option>
                            <option value="1" >1</option>
                            <option value="2" >2</option>
                            <option value="3" >3</option>
                            <option value="4" >4</option>
                            <option value="5" >5</option>
                            <option value="6" >6</option>
                            <option value="7" >7</option>
                            <option value="8" >8</option>
                            <option value="9" >9</option>
                            <option value="10" >10</option>
                        </select>
                    </label>
					<label for="type_of_room"><span>Type of Room</span><span class="required">*</span>
                    	<select name="type_of_room" class="select-field" >
                        	<option value="0" selected="selected">--Select Type of Room--</option>
                            <option value="single" >Single</option>
                            <option value="double" >Double</option>
                            <option value="triple" >Triple</option>
                        </select>
                    </label>
					<input type="hidden" name="secure_transaction_key" value="<?php echo wp_create_nonce('ghob_check_availability_'.get_current_user_id()); ?>" />
                    <label><span>&nbsp;</span><input type="button" onclick="check_master_availability()" value="Check Availability" /></label>
                </fieldset>
            </div>
			
			<div class="form_container form-style-3" id="display_not_available_message" >
            	<fieldset><legend>No Rooms/Beds Found !!</legend>
                	<label for="guest_name"><span>Sorry No Rooms/Beds available for search criteria ! kindly change the search criteria to book room</span>
					</label>
				</fieldset>
			</div>
        </div>
		<div class="form_container form-style-3" id="display_booking_form" >
            	<fieldset><legend>Fill Booking Details</legend>
                	<label for="guest_name"><span>Guest Name</span><span class="required">*</span>
						<input type="text" name="guest_name" value="" required/>
                    </label>
					<label for="guest_email"><span>Guest Email</span><span class="required">*</span>
						<input type="email" name="guest_email" value="" required/>
                    </label>
					<label for="guest_phone"><span>Guest Phone</span><span class="required">*</span>
						<input type="tel" name="guest_phone" value="" pattern="\d{10}" required/>
                    </label>
					<label for="guest_phone"><span>Guest Company</span><span class="required">&nbsp;</span>
						<input type="text" name="guest_company" value="" />
                    </label>
					<label for="guest_address"><span>Guest Address</span><span class="required">&nbsp;</span>
						<textarea id="guest_address" class="textarea-field"></textarea></label>
                    </label>
					<label for="guest_amount_payable"><span>Amount Payable </span><span class="required">&nbsp;</span>
						<input type="text" id="display_amount_admin" value="" disabled />
                    </label>
                    <label for="guest_amount_paid"><span>Amount Paid</span><span class="required">*</span>
						<input type="tel" name="guest_amount_paid" value="" pattern="\d" required/>
                    </label>
                    <label for="guest_amount_refernce"><span>Receipt/ Reference No.</span><span class="required">&nbsp;</span>
						<input type="tel" name="guest_amount_refernce" value=""  />
                    </label>
					<label for="guest_payment_method"><span>Payment Method</span><span class="required">*</span>
						<select name="guest_payment_method" class="select-field" required>
                        	<option value="0" selected="selected">--Select Payment Method--</option>
                            <option value="Cash" >Cash</option>
                            <option value="Cheque" >Cheque</option>
                            <option value="Credit/Debit Card" >Credit Card/Debit Card</option>
                            
                        </select>
                    </label>
					<input type="hidden" name="secure_booking_key" value="<?php echo wp_create_nonce('ghob_book_slots_'.get_current_user_id()); ?>" />
                    <label><span>&nbsp;</span><input type="button" onclick="ghob_book_room()" value="Book Room" /></label>
                </fieldset>
            </div>
    </li>
    
    <li>
        <h2>This is the second tab</h2>
    </li>
    
    <li>
        <h2>Tab number three wee hee</h2>
    </li>
    
    <li>
        <h2>Fourth tab not bad</h2>
    </li>
    
    <li>
        <h2>Tab number five here we go!</h2>
    </li>
</ul>
<div class="ghob_overlay_ajax" style="display: none">
    <div class="ghob_overlay_ajax_center">
        <img alt="Loading..." src="<?php echo plugins_url( '../../assets/images/ajax-loader.gif', __FILE__); ?>" />
    </div>
</div>