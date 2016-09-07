<div class='modal fade overlay-content' tabindex='-1' role='dialog' id='subscriptionModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
				<h4 class='modal-title'>Subscription Plan</h4>
			</div>
			<div class='modal-body'>
				@if( $hasSubscription )
					{!! Form::hidden('subscriptionid', $subscription[0]->subscription_id) !!}
					<p class='heading'>Current Plan</p>
					<div class='row'>
						<div class='col-lg-6 col-md-6 col-sm-6'>Annual Revenue</div>
						<div class='col-lg-4 col-md-4 col-sm-4'>Annual Subscription</div>
						<div class='col-lg-2 col-md-2 col-sm-2'></div>
					</div>
					<div class='row padding-5 margin-2' style='border: 1px solid #e5e5e5; border-radius: 4px;'>
						<div class='col-lg-6 col-md-6 col-sm-6'>{{$subscription[0]->subscription_text}}</div>
						<div class='col-lg-4 col-md-4 col-sm-4'>${{$subscription[0]->subscription_fee}}.00</div>
						<div class='col-lg-2 col-md-2 col-sm-2'>${{$subscription[0]->subscription_feeperday}}/day</div>
					</div>
				@else
					{!! Form::hidden('subscriptionId', '') !!}
					<h1> You currently do not have a subscription. <br />
					<small> Email: <a href='mailto: info@projectworldimpact.com'>info@projectworldimpact.com</a> for more information</small></h1>
				@endif
				<!--
				<div class='input-group margin-top-10' style='width: 100%;'>
					<label for='credit-card-info'>Card Number</label><br />
					<input type='text' name='cc_number' placeholder='Card Number' class='form-control' />
					<div class='error cc_number-error'></div>
				</div>

				<div class='input-group margin-top-10'>
					<label for='credit-card-info'>CCV</label><br />
					<input type='text' name='cc_ccv' placeholder='CCV' class='form-control' />
					<div class='error cc_ccv-error'></div>
				</div>

				<div class='margin-top-10 row'>
					<div class='col-lg-3 col-md-3 col-sm-3'>
						<div class='input-group'>
							<label for='credit-card-info'>Card Expiration</label><br />
							<input type='text' name='cc_exp_month' placeholder='MM' class='form-control' />
						</div>
					</div>
					<div class='col-lg-3 col-md-3 col-sm-3'>
						<div class='input-group'>
							<label for='credit-card-info'>Card Expiration</label><br />
							<input type='text' name='cc_exp_year' placeholder='YYYY' class='form-control' />
						</div>
					</div>
					
					<div class='error cc_date-error'></div>
				</div>
				-->
			</div>
			<!--
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
				<button type='button' class='btn btn-primary save-subscription'>Save Information</button> 
			</div>
			-->
		</div>
	</div>
</div>