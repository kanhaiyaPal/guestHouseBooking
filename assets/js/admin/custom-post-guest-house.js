var GHOB_inst = jQuery.noConflict();

/*
 * Function to handle display of fields while entering guest house room details
 */
function GHOB_handle_metabox_Click(cb){
	if(cb.checked== true){
		document.getElementById(cb.value+"_roomcount").style.display = "block";
		document.getElementById(cb.value+"_roomprice").style.display = "block";
		document.getElementById(cb.value+"_bedprice").style.display = "block";
	}else{
		document.getElementById(cb.value+"_roomcount").style.display = "none";
		document.getElementById(cb.value+"_roomprice").style.display = "none";
		document.getElementById(cb.value+"_bedprice").style.display = "none";
		document.getElementById(cb.value+"_total").innerHTML = '';
	}
}

/*
 * Calcualte Total Number of beds
 */ 
function GHOB_calculate_total(bedtype){
	var present_val = document.getElementById(bedtype+"_roomcount").value;
	var label_id = document.getElementById(bedtype+"_total");
	if((present_val!='')&&(GHOB_inst.isNumeric(present_val))){
		switch(bedtype){
			case 'singlebed':{ label_id.innerHTML = 'Total Beds:'+parseInt(present_val)*1; break; }
			case 'doublebed':{ label_id.innerHTML = 'Total Beds:'+parseInt(present_val)*2; break; }
			case 'triplebed':{ label_id.innerHTML = 'Total Beds:'+parseInt(present_val)*3; break; }
		}
	}else{
		if(present_val!=''){
			alert('Please enter a numeric value in Number of room field');
			label_id.innerHTML = '';
		}
	}
}

/*
 * Copying value of single bed price field to single room price as they are same
 */ 
function GHOB_copy_singlebed_price(fl)
{
	if((fl.value!='')&&(GHOB_inst.isNumeric(fl.value)))
	document.getElementById("singlebed_roomprice").value = fl.value;
	else{
		if(fl.value!='')
		alert('Please enter a numeric value in Price of room field');
		else
		document.getElementById("singlebed_roomprice").value = '';	
	}
}

function GHOIB_room_generator_validate(){
	var single_ghob = GHOB_inst("input[name='room_cat_single']");
	var double_ghob = GHOB_inst("input[name='room_cat_double']");
	var triple_ghob = GHOB_inst("input[name='room_cat_triple']");
	
	var error_string = '';
	if((single_ghob.is(':checked'))||(double_ghob.is(':checked'))||(triple_ghob.is(':checked')))
	{
		if(single_ghob.is(':checked')){
			var single_bed_count = GHOB_inst("input[name='singlebed_count']").val();
			var single_bed_rate = GHOB_inst("input[name='singlebed_rate']").val();
			if(!(GHOB_inst.trim(single_bed_count).length && GHOB_inst.isNumeric(single_bed_count) && (single_bed_count % 1 == 0))){  
				error_string += '\n\u21D2Please check the input value in Number of Bed field of Single Beds';
			}
			if(!(GHOB_inst.trim(single_bed_rate).length && GHOB_inst.isNumeric(single_bed_rate) && (single_bed_rate % 1 == 0))){  
				error_string += '\n\u21D2Please check the input value in Price of Bed field of Single Beds';
			}
		}
		if(double_ghob.is(':checked')){
				
			var double_bed_count = GHOB_inst("input[name='doublebed_count']").val();
			var double_bed_rate = GHOB_inst("input[name='doublebed_rate']").val();
			var double_bed_room = GHOB_inst("input[name='doubleroom_rate']").val();
			
			if(!(GHOB_inst.trim(double_bed_count).length && GHOB_inst.isNumeric(double_bed_count) && (double_bed_count % 1 == 0))){  
				error_string += '\n\u21D2Please check the input value in Number of Bed field of Double Beds';
			}
			if(!(GHOB_inst.trim(double_bed_rate).length && GHOB_inst.isNumeric(double_bed_rate) && (double_bed_rate % 1 == 0))){  
				error_string += '\n\u21D2Please check the input value in Price of Bed field of Double Beds';
			}
			if(!(GHOB_inst.trim(double_bed_room).length && GHOB_inst.isNumeric(double_bed_room) && (double_bed_room % 1 == 0))){  
				error_string += '\n\u21D2Please check the input value in Price of Room field of Double Beds';
			}
		}
		if(triple_ghob.is(':checked')){
			
			var triple_bed_count = GHOB_inst("input[name='triplebed_count']").val();
			var triple_bed_rate = GHOB_inst("input[name='triplebed_rate']").val();
			var triple_bed_room = GHOB_inst("input[name='tripleroom_rate']").val();
			
			if(!(GHOB_inst.trim(triple_bed_count).length && GHOB_inst.isNumeric(triple_bed_count) && (triple_bed_count % 1 == 0))){  
				error_string += '\n\u21D2Please check the input value in Number of Bed field of Triple Beds';
			}
			if(!(GHOB_inst.trim(triple_bed_rate).length && GHOB_inst.isNumeric(triple_bed_rate) && (triple_bed_rate % 1 == 0))){  
				error_string += '\n\u21D2Please check the input value in Price of Bed field of Triple Beds';
			}
			if(!(GHOB_inst.trim(triple_bed_room).length && GHOB_inst.isNumeric(triple_bed_room) && (triple_bed_room % 1 == 0))){  
				error_string += '\n\u21D2Please check the input value in Price of Room field of Triple Beds';
			}
		}
	}else{
		alert('Please select atleast one type of room to generate rooms');
		return false;
	}
	
	if(error_string.length >0){
		alert(error_string);
		return false;
	}else{
		return true;
	}
}

/*
 * functions to generate html fields for single room numbers field
 */
function GHOIB_generate_rooms_html($roomtype)
{
	var room = '';
	var room_text = '';
	
	switch($roomtype){
		case 'single':{	room='single'; room_text='Single'; break; }
		case 'double':{ room='double'; room_text='Double'; break; }
		case 'triple':{ room='triple'; room_text='Triple'; break; }
	}

	if(GHOB_inst('td#ghob_input_'+room+'_room_numbers').children().length != 0){
		//check if room is added or deleted
		var room_number_entry_table = GHOB_inst('td#ghob_input_'+room+'_room_numbers table tr:nth-child(2) td');
		var old_rooms_count = GHOB_inst('td#ghob_input_'+room+'_room_numbers table tr:nth-child(2) td span').children('input').length;
		var new_rooms_count = parseInt(GHOB_inst("input[name='"+room+"bed_count']").val());
		var addition_field_html = '';
		
		if(old_rooms_count != new_rooms_count){
			if(old_rooms_count > new_rooms_count){
				//remove some room WITH CAUTION
				/*VIEW DEVELOPMENT NOTES: NOTE NO. 1*/
				/*this is done for temporary basis*/
				var difference_room_count_up = old_rooms_count - new_rooms_count;
				for(k = 0; k < difference_room_count_up*2; k++) { 
					GHOB_inst('td#ghob_input_'+room+'_room_numbers table tr:nth-child(2) td span:last-child').remove();
				}
			}
			if(old_rooms_count < new_rooms_count){
				//add new rooms
				var difference_room_count = new_rooms_count - old_rooms_count;
				for(j = 0; j < difference_room_count; j++) { 
					addition_field_html += '<span><input type="text" name="'+room+'_bedrooms_numbers[]" value="" required/></span><span>&nbsp;</span>';
				}
				room_number_entry_table.append(addition_field_html);
			}
		}
		//if(addition no issues just append a new input span to existing table)
		//if (deletion of field)
		//     |-> Check whether any room number is booked or not
		//                         |-> If booked don't allow to delete the room
		//	   |-> If no room is booked for section remove entries from last 
	}else{
		//cell is empty
		var field_html = '';
		field_html += '<table><tr><td><strong>'+room_text+' Bed Rooms</strong><br/><hr/></td></tr>';
		field_html += '<tr><td>';
		for(i = 0; i < parseInt(GHOB_inst("input[name='"+room+"bed_count']").val()); i++) { 
			field_html += '<span><input type="text" name="'+room+'_bedrooms_numbers[]" value="" required/></span><span>&nbsp;</span>';
		}
		field_html += '</td></tr>';
		field_html += '</table>';
		GHOB_inst('td#ghob_input_'+room+'_room_numbers').html(field_html);
	}
}


GHOB_inst( document ).ready(function() {
	/*
	 * function to handle gallery upload for guest house 
	 */
	GHOB_inst('#guest_house_gallery_settings').on('click', '.attachment.add-new', function (event) {
		event.preventDefault();
		var fileFrame = wp.media.frames.file_frame = wp.media({
			multiple: true
		});
		var self = GHOB_inst(this);
		fileFrame.on('select', function () {
			var attachments = fileFrame.state().get('selection').toJSON();
			var html = '';

			for (var i = 0; i < attachments.length; i++) {
				var attachment = attachments[i];
				//var url = attachment.url.replace(hotel_settings.upload_base_url, '');
				html += '<li class="attachment">';
				html += '<div class="attachment-preview">';
				html += '<div class="thumbnail">';
				html += '<div class="centered">'
				html += '<img src="' + attachment.url + '"/>';
				html += '<input type="hidden" name="guest_house_gallery[]" value="' + attachment.id + '" />'
				html += '</div>';
				html += '</div>';
				html += '</div>';
				html += '<a class="dashicons dashicons-trash" title="Remove this image"></a>';
			html += '</li>';
			}
			self.before(html);
		});
		fileFrame.open();
	}).on('click', '.attachment .dashicons-trash', function (event) {
		event.preventDefault();
		GHOB_inst(this).parent().remove();
	});

	/*
	 * Function to generate Room number inputs
	 *
	 */
	GHOB_inst('#custom_guest_house_generate_rooms').click(function(event){
		event.preventDefault();
		
		var single_ghob = GHOB_inst("input[name='room_cat_single']");
		var double_ghob = GHOB_inst("input[name='room_cat_double']");
		var triple_ghob = GHOB_inst("input[name='room_cat_triple']");
		
		
		//validate the entries
		var val_result = GHOIB_room_generator_validate();
		if(val_result){
			if(single_ghob.is(':checked')){	GHOIB_generate_rooms_html('single');	}
			if(double_ghob.is(':checked')){	GHOIB_generate_rooms_html('double');	}
			if(triple_ghob.is(':checked')){	GHOIB_generate_rooms_html('triple');	}
		}
	});

	/*--closing document ready--*/
});
