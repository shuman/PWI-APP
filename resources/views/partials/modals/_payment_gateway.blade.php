<div class='modal fade overlay-content' tabindex='-1' role='dialog' id='paymentGatewayModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
				<h4 class='modal-title'>Payment Gateway</h4>
			</div>
			<div class='modal-body'>
				<div class='success-msg'>Your Payment Gateway has been Updated.</div>
				<label for='gateway'>Select a Payment Gateway</label>
				<br />
				<select name='gatewayName' class='form-control' id='gatewayDropDown'>
					<option value='0'>Select a Payment Gateway</option>

					@foreach( $gateways as $gateway )
						@if( isset( $orgGateway ) && $orgGateway->fk_payment_gateway == $gateway->pk )
							<option value='{{$gateway->pk}}' selected>{{ $gateway->payment_gateway_name }}</option>
						@else
							<option value='{{$gateway->pk}}'>{{ $gateway->payment_gateway_name }}</option>
						@endif
					@endforeach
				</select>
				<div class='error error-gateway-choice'></div>
				<div class='gateway-data'>
					@if( isset( $orgGateway ) && ( ! empty( $orgGateway->fk_payment_gateway ) ) )

						@if( $orgGateway->fk_payment_gateway == 1 )

							<div class='input-group'>
								<label for='client-id'>PayPal Client Id</label><br />
								<input type='text' name='pp-clientId' class='form-control' size='45' value='{{ $orgGateway->paypal_client_id }}' />
							</div>
							<div class='error pp-clientId-error'></div>

							<div class='input-group margin-top-10'>
								<label for='secret'>PayPal Secret</label>
								<input type='text' name='pp-secret' class='form-control' size='45' value='{{ $orgGateway->paypal_client_secret }}' />
							</div>
							<div class='error pp-secret-error'></div>

						@elseif( $orgGateway->fk_payment_gateway == 2 )

							<div class='input-group'>
								<label for='gateway-key'>Transnational Gateway Key</label><br />
								<input type='text' name='trans-gatewayKey' class='form-control' size='45' value='{{ $orgGateway->gateway_key }}' />
							</div>
							<div class='error trans-gatewayKey-error'></div>

						@elseif( $orgGateway->fk_payment_gateway == 3 )

							<div class='input-group'>
								<label for='paypal_username'>PayPal Username</label><br />
								<input type='email' name='ppS-username' class='form-control' size='45' value='{{ $orgGateway->paypal_username }}' />
							</div>
							<div class='error ppS-username-error'></div>

						@elseif( $orgGateway->fk_payment_gateway == 4 )

							<div class='input-group'>
								<label for='auth-name'>Authorize.NET Name</label><br />
								<input type='text' name='auth-name' class='form-control' size='45' value='{{ $orgGateway->authorizeNET_name }}'/>
							</div>
							<div class='error auth-name-error'></div>

							<div class='input-group margin-top-10'>
								<label for='auth-key'>Authorize.NET Key</label>
								<input type='text' name='auth-key' class='form-control' size='45' value='{{ $orgGateway->authorizeNET_key }}' />
							</div>
							<div class='error auth-key-error'></div>
						@endif
					@endif
				</div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
				<button type='button' class='btn btn-primary save-gateway'>Save Changes</button> 
			</div>
		</div>
	</div>
</div>