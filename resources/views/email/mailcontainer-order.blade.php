<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>{!! $site !!} Order Confirmation E-Mail</title>
</head>
<body>
	<div style="width:700px; margin:0 auto; font-size:15px; font-family:Calibri, Arial, 'Trebuchet MS'; background:#f5f5f5;">
		<div style="width:94%; padding:3%; background:#33aef4; border-bottom:1px solid #dcdcdc; background:url({!! $img !!}) left center no-repeat;">
			<div style="width:60px; height:60px; float:left;"></div>
			<div style="clear:both;"></div>
		</div>
		<div style="width:94%; margin:5px; padding:3%;">
			<p style="margin:5px 0;">
				<b>Hello {!! $record->billing_full_name !!},</b>
			</p>
			<p style="margin:5px 0;">
				Thank you for your order with {!! $site !!}. Your order details are below.
			</p>
			<div style="clear:both;"></div>
			<h2>Billing Address</h2>
			<address>
				{!! $record->billing_address_line1 !!}<br />
				@if( ! empty( $record->billing_address_line2 ) )
					{!! $record->billing_address_line2 !!}<br />
				@endif
				{!! $record->billing_city !!}, {!! $record->billing_state !!} {!! $record->billing_zip !!}<br />
				{!! $record->billing_country !!}
			</address>
			<br />
			<h2>Shipping Address</h2>
			<address>
				{!! $record->shipping_address_line1 !!}<br />
				@if( ! empty( $record->shipping_address_line2 ) )
					{!! $record->shipping_address_line2 !!}<br />
				@endif
				{!! $record->shipping_city !!}, {!! $record->shipping_state !!} {!! $record->shipping_zip !!}<br />
				{!! $record->shipping_country !!}
			</address>
			<br />
			<div style='display: table; width: 100%;'>
			@foreach( $details as $detail )
				<div style='display: table-row;'>
					<div style='display: table-cell; font-size: 14px; width: 80%;'>
						<span style='color: black; font-size: 18px;'>{!! $detail->product_name !!}</span> by <span style='color: #33aef4; font-size: 16px;'>{!! $detail->org_name !!}</span> 
					</div>
					<div style='display: table-cell; width: 20%;'>
						<span style='color: #35db54;'>${!! $detail->product_price !!}.00</span>
						<br />
						<span style='color: black; font-size: 12px;'>Quantity: {!! $detail->quantity !!}</span>
					</div>
				</div>
			@endforeach
			</div>

			<div style="clear:both;"></div>
			<div style="padding:5px; width:250px;">
				<div style="width:100%; margin:7px 0;">
					<p style="width:250px; text-align:left; color:#666666; font-weight:normal; margin:5px 0;">Best,</p>
					<p style="width:250px; text-align:left; color:#666666; font-weight:normal; margin:5px 0 0 0;">The {!! $site !!} Team</p>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
</body>
</html>