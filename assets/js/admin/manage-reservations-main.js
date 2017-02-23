var GHOB_inst = jQuery.noConflict();

function isDate(txtDate){
  var currVal = txtDate;
  if(currVal == '')
    return false;
  
  //Declare Regex  
  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; 
  var dtArray = currVal.match(rxDatePattern); // is format OK?

  if (dtArray == null)
     return false;
 
  //Checks for mm/dd/yyyy format.
  dtDay = dtArray[1];
  dtMonth= dtArray[3];
  dtYear = dtArray[5];

  if (dtMonth < 1 || dtMonth > 12)
      return false;
  else if (dtDay < 1 || dtDay> 31)
      return false;
  else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
      return false;
  else if (dtMonth == 2)
  {
     var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
     if (dtDay> 29 || (dtDay ==29 && !isleap))
          return false;
  }
  return true;
}

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

function isValidPhone(phone_no){
	var regex_ph = /^\d{10}$/;
	return regex_ph.test(phone_no);
} 

function validate_booking_form_filling(){
	
	var guest_name = GHOB_inst('input[name="guest_name"]').val()
	var guest_email = GHOB_inst('input[name="guest_email"]').val();
	var guest_phone = GHOB_inst('input[name="guest_phone"]').val();
	var guest_company = GHOB_inst('input[name="guest_company"]').val();
	var guest_address = GHOB_inst('input[name="guest_address"]').val();
	var guest_amount_paid= GHOB_inst('input[name="guest_amount_paid"]').val();
	var guest_amount_ref = GHOB_inst('input[name="guest_amount_refernce"]').val();
	var guest_payment_method = GHOB_inst('select[name="guest_payment_method"]').val();
	
	var error_string = '';
	
	if(guest_name == ''){error_string +='\nGuest Name field cannot be blank';}
	if(guest_email == ''){error_string +='\nGuest Email field cannot be blank';}else{ if(!isEmail(guest_email)){ error_string +='\nGuest Email is not valid'; } }
	if(guest_phone == ''){error_string +='\nGuest Phone field cannot be blank';}else{ if(!isValidPhone(guest_phone)){ error_string +='\nGuest Phone is not valid'; } }
	if(guest_amount_paid == ''){error_string +='\nAmount Paid field cannot be blank';}else{ if(isNaN(guest_amount_paid)){ error_string +='\nPaid amount value is not valid'; } }
	if(guest_payment_method == '0'){error_string +='\nPlease select appropriate payment method';}
	
	if(error_string.length > 0){
		alert(error_string);
		return false;
	}
	
	return true;
}

function validate_availability_form(){
	
	var city = GHOB_inst('select[name="resev_city"]').val();
	var g_location = GHOB_inst('select[name="resev_location"]').val();
	var guest_house = GHOB_inst('select[name="resev_guest_house"]').val();
	var checkin = GHOB_inst('input[name="check_in_date"]').val();
	var checkout = GHOB_inst('input[name="check_out_date"]').val();
	var entity_select = GHOB_inst('select[name="selector_of_field"]').val();
	var number_of_rooms = GHOB_inst('select[name="number_of_rooms"]').val();
	var number_of_beds = GHOB_inst('select[name="number_of_beds"]').val();
	var type_of_room = GHOB_inst('select[name="type_of_room"]').val();
	var wp_secret_key_reservation = GHOB_inst('input[name="secure_transaction_key"]').val();
	
	var error_string = '';
	
	if(city == '0'){error_string +='\nSelect city first';}
	if(g_location == '0'){error_string +='\nSelect location first';}
	if(guest_house == '0'){error_string +='\nSelect Guest House first';}
	if(!isDate(checkin)){error_string +='\nCheck-In date is not valid';}
	if(!isDate(checkout)){error_string +='\nCheck-Out date is not valid';}
	
	var sel_checkin = GHOB_inst('input[name="check_in_date"]').datepicker('getDate');
	var sel_checkout = GHOB_inst('input[name="check_out_date"]').datepicker('getDate');
	var now = new Date();
	now.setHours(0,0,0,0);
	if (((isDate(checkin)) && (sel_checkin < now))||((isDate(checkout))&& (sel_checkout < now))) {
	  error_string +='\nBooking is not allowed on past dates';
	}
	
	if(sel_checkout<sel_checkin){
		error_string +='\nCheckout should be made later than Checkin date';
	}
	
	if(entity_select == '0'){ error_string +='\nSelect type of booking you want to do, either rooms or beds'; }
	
	if((number_of_rooms == '0')&&(number_of_beds == '0')){ error_string +='\nSelect either number of rooms or numbers of beds to book';}
	if(type_of_room == '0'){error_string +='\nSelect Type of Room first';}
	
	if(error_string.length>0){
		alert(error_string);
		return false;
	}
	
	return true;
}

function check_master_availability(){
	//validate the form first
	var validate_flag = validate_availability_form();
	
	var city = GHOB_inst('select[name="resev_city"]').val();
	var g_location = GHOB_inst('select[name="resev_location"]').val();
	var guest_house = GHOB_inst('select[name="resev_guest_house"]').val();
	var checkin = GHOB_inst('input[name="check_in_date"]').val();
	var checkout = GHOB_inst('input[name="check_out_date"]').val();
	var number_of_rooms = GHOB_inst('select[name="number_of_rooms"]').val();
	var number_of_beds = GHOB_inst('select[name="number_of_beds"]').val();
	var type_of_room = GHOB_inst('select[name="type_of_room"]').val();
	var wp_secret_key_reservation = GHOB_inst('input[name="secure_transaction_key"]').val();
	
	if(validate_flag){
		
		var data = {
			'action': 'ghob_view_availability',
			'city': parseInt(city),
			'location': parseInt(g_location),
			'guest_house':parseInt(guest_house),
			'checkin': checkin,
			'checkout': checkout,
			'no_rooms': number_of_rooms,
			'no_beds': number_of_beds,
			'type_of_room': type_of_room,
			'secret': wp_secret_key_reservation
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		GHOB_inst.post(ajaxurl, data, function(response) {
			console.log(response);
			if(response == 'not_available'){  
				GHOB_inst('#display_booking_form').hide();
				GHOB_inst('#display_not_available_message').show("slow");
			}else{
				GHOB_inst('input#display_amount_admin').val(response+' per bed/room');
				GHOB_inst('#display_booking_form').show( "slow" );
				GHOB_inst('#display_not_available_message').hide();
			}
		});
	}
}

function ghob_search_location(selected_city_id){
	
	if(parseInt(selected_city_id)==0){
		GHOB_inst('select[name="resev_location"]').html('<option value="0" selected="selected">--Select Location--</option>');
		return false;	
	}
	
	var data = {
		'action': 'locate_city_location',
		'term_city_id': parseInt(selected_city_id)
	};
	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	GHOB_inst.post(ajaxurl, data, function(response) {
		GHOB_inst('select[name="resev_location"]').append(response);
	});

}

function ghob_book_room(){
	
	var availability_validator = validate_availability_form();
	
	var guest_name = GHOB_inst('input[name="guest_name"]').val()
	var guest_email = GHOB_inst('input[name="guest_email"]').val();
	var guest_phone = GHOB_inst('input[name="guest_phone"]').val();
	var guest_company = GHOB_inst('input[name="guest_company"]').val();
	var guest_address = GHOB_inst('#guest_address').val();
	var guest_amount_paid= GHOB_inst('input[name="guest_amount_paid"]').val();
	var guest_amount_ref = GHOB_inst('input[name="guest_amount_refernce"]').val();
	var guest_payment_method = GHOB_inst('select[name="guest_payment_method"]').val();
	var guest_security_key = GHOB_inst('input[name="secure_booking_key"]').val();
	var checkin = GHOB_inst('input[name="check_in_date"]').val();
	var checkout = GHOB_inst('input[name="check_out_date"]').val();
	var type_of_room = GHOB_inst('select[name="type_of_room"]').val();
	var number_of_rooms = GHOB_inst('select[name="number_of_rooms"]').val();
	var number_of_beds = GHOB_inst('select[name="number_of_beds"]').val();
	
	var room_bed_qty = 0;
	var entity = '';
	if(number_of_rooms > 0){  room_bed_qty = number_of_rooms; entity='room'; }else{ room_bed_qty = number_of_beds; entity='bed'; }
	
	if(availability_validator){
		var var_booking = validate_booking_form_filling();
		if(var_booking){
			var data = {
				'action': 'ghob_book_slots',
				'rv_guest_name': guest_name,
				'rv_guest_email':guest_email,
				'rv_guest_phone': guest_phone,
				'rv_guest_company': guest_company,
				'rv_guest_address': guest_address,
				'rv_paid_amount': guest_amount_paid,
				'rv_ref_no': guest_amount_ref,
				'rv_payment_method': guest_payment_method,
				'rv_guest_checkin': checkin,
				'rv_guest_checkout': checkout,
				'rv_guest_room_type': type_of_room,
				'rv_guest_room_bed_qty': room_bed_qty,
				'rv_guest_entity_to_book': entity,
				'rv_security_key': guest_security_key
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			GHOB_inst.post(ajaxurl, data, function(response) {
				console.log(response);
				if(response == 'booking_success'){  
					/*clear form fields and reset fields of availability*/
					ghob_clear_form_availability_booking();
					GHOB_inst('#display_booking_form').hide();
					GHOB_inst('#display_room_booked').show("slow");
				}else{
					GHOB_inst('input#display_amount_admin').val(response+' per bed/room');
					GHOB_inst('#display_booking_form').show( "slow" );
					GHOB_inst('#display_not_available_message').hide();
				}
			});
		}
	}
}

function ghob_field_selector(selct_option){
	if(selct_option == '1'){ GHOB_inst('#field_number_of_beds').show();  GHOB_inst('#field_number_of_rooms').hide(); }
	if(selct_option == '2'){ GHOB_inst('#field_number_of_rooms').show(); GHOB_inst('#field_number_of_beds').hide(); }
}

function ghob_clear_form_availability_booking(){
	//reset all fields
	
	GHOB_inst('select[name="resev_city"]').val('0');
	GHOB_inst('select[name="resev_location"]').html('<option value="0" selected="selected">--Select Location--</option>');
	GHOB_inst('select[name="resev_location"]').val('0');
	GHOB_inst('select[name="resev_guest_house"]').val('0');
	GHOB_inst('input[name="check_in_date"]').val('');
	GHOB_inst('input[name="check_out_date"]').val('');
	GHOB_inst('select[name="selector_of_field"]').val('0')
	GHOB_inst('#field_number_of_beds').hide();
	GHOB_inst('#field_number_of_rooms').hide();
	GHOB_inst('select[name="number_of_rooms"]').val('0');
	GHOB_inst('select[name="number_of_beds"]').val('0');
	GHOB_inst('select[name="type_of_room"]').val('0');
	GHOB_inst('input[name="secure_transaction_key"]').val('');
	GHOB_inst('input[name="guest_name"]').val('');
	GHOB_inst('input[name="guest_email"]').val('');
	GHOB_inst('input[name="guest_phone"]').val('');
	GHOB_inst('input[name="guest_company"]').val('');
	GHOB_inst('#guest_address').val('');
	GHOB_inst('input[name="guest_amount_paid"]').val('');
	GHOB_inst('input[name="guest_amount_refernce"]').val('');
	GHOB_inst('select[name="guest_payment_method"]').val('0');
	GHOB_inst('input[name="secure_booking_key"]').val('');
	GHOB_inst('input[name="check_in_date"]').val('');
	GHOB_inst('input[name="check_out_date"]').val('');
	GHOB_inst('select[name="type_of_room"]').val('0');
	GHOB_inst('select[name="number_of_rooms"]').val('0');
	GHOB_inst('select[name="number_of_beds"]').val('0');
	
}

function ghob_search_guest_house(location_id){
	if(parseInt(location_id)==0){
		GHOB_inst('select[name="resev_guest_house"]').html('<option value="0" selected="selected">--Select Guest House--</option>');
		return false;	
	}
	var data = {
		'action': 'locate_guest_house',
		'term_location_id': parseInt(location_id)
	};
	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	GHOB_inst.post(ajaxurl, data, function(response) {
		GHOB_inst('select[name="resev_guest_house"]').append(response);
	});
}


/***Functions to perform view occupany map***/
function show_guest_house_map(){
	
	var selected_guest_house = GHOB_inst('select[name="selected_guest_house_view"]').val();
	if(selected_guest_house != 0){
		var data = {
			'action': 'get_guest_house_map',
			'guest_house_id': parseInt(selected_guest_house)
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		GHOB_inst.post(ajaxurl, data, function(response) {
			GHOB_inst('div.display_mapping_wrapper').html(response);
		});
	}else{
		alert('Select a Guest House to continue');
	}
}

GHOB_inst( document ).ready(function() {
	
	GHOB_inst('#display_booking_form').hide();
	GHOB_inst('#display_not_available_message').hide();
	GHOB_inst('#display_room_booked').hide();
	GHOB_inst('#display_room_booked_error').hide();
	GHOB_inst('#field_number_of_beds').hide();
	GHOB_inst('#field_number_of_rooms').hide();
	
    GHOB_inst("ul#ghob_tabs li").click(function(e){
        if (!GHOB_inst(this).hasClass("active")) {
            var tabNum = GHOB_inst(this).index();
            var nthChild = tabNum+1;
            GHOB_inst("ul#ghob_tabs li.active").removeClass("active");
            GHOB_inst(this).addClass("active");
            GHOB_inst("ul#ghob_tab li.active").removeClass("active");
            GHOB_inst("ul#ghob_tab li:nth-child("+nthChild+")").addClass("active");
        }
    });
	
	var dateToday = new Date(); 
	GHOB_inst('.ghob-admin-datepicker').datepicker({dateFormat: 'dd/mm/yy',numberOfMonths: 3,minDate: dateToday});	
	
}).ajaxStart(function(){
    GHOB_inst(".ghob_overlay_ajax").show();
})
.ajaxStop(function(){
    GHOB_inst(".ghob_overlay_ajax").hide();
});