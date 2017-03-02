var GHOB_inst = jQuery.noConflict();

function show_date_field(){
	event.preventDefault();
	GHOB_inst('.ghob-admin-datepicker').show();
	GHOB_inst('.change_checkout_bt').hide();
}

GHOB_inst( document ).ready(function() {
	
	GHOB_inst('.ghob-admin-datepicker').hide();
		
	
	/*--closing document ready--*/
});