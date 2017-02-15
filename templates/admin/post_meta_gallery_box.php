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