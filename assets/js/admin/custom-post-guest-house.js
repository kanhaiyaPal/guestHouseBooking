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
	if((present_val!='')&&(!isNaN(present_val))){
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
	if((fl.value!='')&&(!isNaN(fl.value)))
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
			var single_bed_count = GHOB_inst("input[name='singlebed_count']").value;;
			var single_bed_rate = GHOB_inst("input[name='singlebed_rate']").value;
			if(GHOB_inst.trim(single_bed_count).length && GHOB_inst.isNumeric(single_bed_count) && (single_bed_count % 1 != 0)){  
				error_string += 'Please check the input value in Number of Bed field of Single Beds';
			}
			if(GHOB_inst.trim(single_bed_rate).length && GHOB_inst.isNumeric(single_bed_rate) && (single_bed_rate % 1 != 0)){  
				error_string += 'Please check the input value in Price of Bed field of Single Beds';
			}
		}
		if(double_ghob.is(':checked')){
			var GHOB_inst("input[name='doublebed_count']");
			var GHOB_inst("input[name='doublebed_rate']");
			var GHOB_inst("input[name='doubleroom_rate']");
		}
		if(triple_ghob.is(':checked')){
			var GHOB_inst("input[name='triplebed_count']");
			var GHOB_inst("input[name='triplebed_rate']");
			var GHOB_inst("input[name='tripleroom_rate']");
		}
	}else{
		alert('Please select atleast one type of room to generate rooms');
	}
	
	if(error_string.length >0){
		alert(error_string);
		return false;
	}else{
		return true;
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
				var url = attachment.url.replace(hotel_settings.upload_base_url, '');
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
		
		//validate the entries
		GHOIB_room_generator_validate();
	});

	/*--closing document ready--*/
});
