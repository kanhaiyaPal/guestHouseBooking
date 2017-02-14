<?php
/*Guest House Post Meta Box HTML*/
	// Retrieve current value of rooms based on review ID
	    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    $upload_dir = wp_upload_dir();
    $upload_base_url = $upload_dir['baseurl'];
	
	$guest_house_gallery = get_post_meta( $guest_house_details->ID, 'guest_house_gallery', true );
	$guest_house_gallery_array = explode(',',$guest_house_gallery);
	
    ?>
	
	<table width="100%" id="guest_house_gallery_settings">
		<tr>
			<td>
				<ul>
					<?php if( $guest_house_gallery ): foreach ($guest_house_gallery_array as $key => $id): ?>
						<li class="attachment">
							<div class="attachment-preview">
								<div class="thumbnail">
									<div class="centered">
										<?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?>
										<input type="hidden" name="guest_house_gallery[]" value="<?php echo esc_attr($id); ?>" />
									</div>
								</div>
							</div>
							<a class="dashicons dashicons-trash" title="<?php _e( 'Remove this image', 'tp-hotel-booking' ); ?>"></a>
						</li>
					<?php endforeach; endif; ?>
					<li class="attachment add-new">
						<div class="attachment-preview">
							<div class="thumbnail">
								<div class="dashicons-plus dashicons">
								</div>
							</div>
						</div>
					</li>
				</ul>
			</td>
		</tr>
	</table>
   <script>
		
		jQuery('#guest_house_gallery_settings').on('click', '.attachment.add-new', function (event) {
			event.preventDefault();
			var fileFrame = wp.media.frames.file_frame = wp.media({
				multiple: true
			});
			var self = jQuery(this);
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
				jQuery(this).parent().remove();
			});
	</script>