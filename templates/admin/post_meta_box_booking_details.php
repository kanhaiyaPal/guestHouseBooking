<?php 
if ( !defined( 'ABSPATH' ) ) {
	exit();
} 

$guest_house_id = get_post_meta( $booking_details->ID, 'guest_house_id', true );
$room_id = get_post_meta( $booking_details->ID, 'room_id', true );
$bedtype = get_post_meta( $booking_details->ID, 'bedtype', true );
$payment_method = get_post_meta( $booking_details->ID, 'paymentmethod', true );
$receipt_no = get_post_meta( $booking_details->ID, 'receiptno', true );
$amount = get_post_meta( $booking_details->ID, 'amountpaid', true );
$checkin = get_post_meta( $booking_details->ID, 'checkindate', true );
$checkout = get_post_meta( $booking_details->ID, 'checkoutdate', true );

$username = get_post_meta( $booking_details->ID, 'guestname', true );
$useremail = get_post_meta( $booking_details->ID, 'guestemail', true );
$userphone = get_post_meta( $booking_details->ID, 'guestphone', true );
$usercompany = get_post_meta( $booking_details->ID, 'guestcompany', true );
$useraddress = get_post_meta( $booking_details->ID, 'guestaddress', true );

if(strpos($room_id, ',') !== false) {
	$room_id_array = explode(',',$room_id);
}else{
	$room_id_array = array($room_id);
}
?>
<table style="width:100%">
	<tr>
		<td><strong>Booking ID</strong></td>
		<td><?=$booking_details->ID?></td>
	</tr>
	<tr>
		<td><strong>Guest House</strong></td>
		<td><?php echo $this->get_guest_house_title($guest_house_id); ?></td>
	</tr>
	<tr>
		<td><strong>Room Number</strong></td>
		<td><?php foreach($room_id_array as $s_room_id){ echo $this->get_room_name($s_room_id).'|'; } ?></td>
	</tr>
	<tr>
		<td><strong>Bed/Room Type</strong></td>
		<td><?=$bedtype?></td>
	</tr>
	<tr>
		<td><h3>Payment Details</h3></td>
		<td><hr/></td>
	</tr>
	<tr>
		<td><strong>Payment Method</strong></td>
		<td><?=$payment_method?></td>
	</tr>
	<tr>
		<td><strong>Receipt/ Reference No.</strong></td>
		<td><?=$receipt_no?></td>
	</tr>
	<tr>
		<td><strong>Amount Paid</strong></td>
		<td><?=$amount?></td>
	</tr>
	<tr>
		<td><h3>Stay Details</h3></td>
		<td><hr/></td>
	</tr>
	<tr>
		<td><strong>Check-in Date</strong></td>
		<td><?=$checkin?></td>
	</tr>
	<tr>
		<td><strong>Check-out Date</strong></td>
		<td><?=$checkout?></td>
	</tr>
	<tr>
		<td><h3>User Details</h3></td>
		<td><hr/></td>
	</tr>
	<tr>
		<td><strong>Guest Name</strong></td>
		<td><?=$username?></td>
	</tr>
	<tr>
		<td><strong>Email-id</strong></td>
		<td><?=$useremail?></td>
	</tr>
	<tr>
		<td><strong>Phone No.</strong></td>
		<td><?=$userphone?></td>
	</tr>
	<tr>
		<td><strong>Company</strong></td>
		<td><?=$usercompany?></td>
	</tr>
	<tr>
		<td><strong>Address</strong></td>
		<td><?=$useraddress?></td>
	</tr>
</table> 