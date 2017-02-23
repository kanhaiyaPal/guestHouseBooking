<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$coupon_code = get_post_meta( $coupon_details->ID, 'couponcode', true );
$coupon_percentage = get_post_meta( $coupon_details->ID, 'couponpercentage', true );
$coupon_max_amount = get_post_meta( $coupon_details->ID, 'couponmaxamount', true );
$min_bill = get_post_meta( $coupon_details->ID, 'couponminbilling', true );


?>
<table style="width:100%">
	<tr>
		<td><strong>Coupon Code</strong></td>
		<td><input type="text" name="coupon_code" placeholder="Coupon Code" value="<?php echo $coupon_code; ?>"  /></td>
	</tr>
	<tr>
		<td><strong>Percentage to reduce in total Bill</strong></td>
		<td><input type="text" name="coupon_percentage" placeholder="Coupon Percentage" value="<?php echo $coupon_percentage; ?>"  /></td>
	</tr>
	<tr>
		<td><strong>Amount to reduce in total Bill(Max amount in case percentage is used above)</strong></td>
		<td><input type="text" name="coupon_amount_max" placeholder="Amount to reduce" value="<?php echo $coupon_max_amount; ?>"  /></td>
	</tr>
	<tr>
		<td><strong>Minimum Billing for this Coupon Code</strong></td>
		<td><input type="text" name="coupon_min_bill" placeholder="Coupon Minimum Billing" value="<?php echo $min_bill; ?>"  /></td>
	</tr>
</table>