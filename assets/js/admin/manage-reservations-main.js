var GHOB_inst = jQuery.noConflict();

var city = GHOB_inst('select[name="resev_city"]').val();
var location = GHOB_inst('select[name="resev_location"]').val();
var guest_house = GHOB_inst('select[name="resev_guest_house"]').val();
var checkin = GHOB_inst('input[name="check_in_date"]').val();
var checkout = GHOB_inst('input[name="check_out_date"]').val();
var number_of_rooms = GHOB_inst('select[name="number_of_rooms"]').val();
var number_of_beds = GHOB_inst('select[name="number_of_beds"]').val();
var type_of_room = GHOB_inst('select[name="type_of_room"]').val();
var wp_secret_key_reservation = GHOB_inst('input[name="secure_transaction_key"]').val();

function isDate(txtDate)
{
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

function validate_availability_form(){
	
	var error_string = '';
	
	if(city == '0'){error_string +='\nSelect city first';}
	if(location == '0'){error_string +='\nSelect location first';}
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
	if(validate_flag){
		
		var data = {
			'action': 'ghob_view_availability',
			'city': parseInt(city),
			'location': parseInt(location),
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
			alert(response);
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

GHOB_inst( document ).ready(function() {

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