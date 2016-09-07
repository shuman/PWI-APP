<div class='modal fade overlay-content' tabindex='-1' role='dialog' id='contactInfoModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
				<h4 class='modal-title'>Contact Information</h4>
			</div>
			<div class='modal-body'>
				<div class='alert alert-success upload-contactInfo-success modal-update-dialog' style='top: 0px; left: 0px;'>
				    <strong>Success!</strong> Your information has been updated!
				</div>
				<div class='alert alert-danger upload-contactInfo-error modal-update-dialog' style='top: 0px; left: 0px;'>
				    <strong>Error!</strong> There was a problem updating your information.
				</div>
				<form name='contact-form-update'>
					{!! Form::token( ) !!}
					{!! Form::hidden('orgId', $org->org_id) !!}
					<div class='input-group' style='width: 100%;'>
						<label for='org-web-url'>Website Url<sup>*</sup></label>
						<br />
						@if( isset( $org ) AND isset( $org->org_weburl) )
							<input type='text' name='org_web_url' size='45' class='form-control' value='{!! $org->org_weburl !!}' />
						@else
							<input type='text' name='org_web_url' size='45' class='form-control'  />
						@endif
					</div>
					<div class='error error-org_web_url'></div>

					<div class='input-group margin-top-10' style='width: 100%;'>
						<label for='org-phone'>Phone Number<sup>*</sup></label>
						<br />
						@if( isset( $org ) AND isset( $org->org_mobile_number ) )
							<input type='text' name='org_phone' size='45' class='form-control' value='{!! $org->org_mobile_number !!}' />
						@else
							<input type='text' name='org_phone' size='45' class='form-control'  />
						@endif
					</div>
					<div class='error error-org_phone'></div>

					<div class='input-group margin-top-10' style='width: 100%;'>
						<label for='org-email'>Email <sup>*</sup></label>
						<br />
						@if( isset( $org ) AND isset( $org->org_email ) )
							<input type='text' name='org_email' size='45' class='form-control' value='{!! $org->org_email !!}' />
						@else
							<input type='text' name='org_email' size='45' class='form-control'  />
						@endif
					</div>
					<div class='error error-org_email'></div>

					<div class='input-group margin-top-10' style='width: 100%;'>
						<label for='org-name'>Address <sup>*</sup></label>
						<br />
						<div class='row'>
							<div class='col-lg-6 col-md-6 col-sm-6 padding-right-0'>
								<input type='text' name='org_address1' placeholder='Address Line 1' class='form-control' value='{!! $org->org_addressline1 !!}' />
							</div>
							<div class='col-lg-6 col-md-6 col-sm-6 padding-left-0'>
								<input type='text' name='org_address2' placeholder='Address Line 2' class='form-control' value='{!! $org->org_addressline2 !!}' />
							</div>
						</div>
						<div class='row'>
							<div class='col-lg-3 col-mg-3 col-sm-3 padding-right-0'>
								<input type='text' name='org_city' placeholder='City' class='form-control' value='{!! $org->org_city !!}' />
							</div>
							<div class='col-lg-3 col-mg-3 col-sm-3 padding-0'>
								<select name='org_state' class='form-control' style='height: 45px; border-radius: 0px; -webkit-appearance: none;'>
									<option value='0'>State</option>
									@if( isset( $ciStates ) && ! empty( $ciStates ) ) )
										@foreach( $ciStates as $state )
											@if( $org->org_state == $state->state_id )
												<option value='{{ $state->state_id }}' selected>{{ $state->state_name }}</option>
											@else
												<option value='{{ $state->state_id }}'>{{ $state->state_name }}</option>
											@endif 
										@endforeach
									@endif
								</select>
							</div>
							<div class='col-lg-3 col-mg-3 col-sm-3 padding-0'>
								<input type='text' name='org_zip' placeholder='Zip' class='form-control' value='{!! $org->org_zip !!}' />
							</div>
							<div class='col-lg-3 col-mg-3 col-sm-3 padding-left-0'>
								<select name='org_country' class='form-control' style='height: 45px; border-radius: 0px; -webkit-appearance: none;'>
									@foreach( $countryDropDownList as $k => $v )
										@if( $k == $org->org_country )
											<option value='{{ $k }}' selected>{{$v}}</option>
										@else
											<option value='{{ $k }}'>{{$v}}</option>
										@endif
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
				<button type='button' class='btn btn-primary save-contact-info'>Save Changes</button> 
			</div>
		</div>
	</div>
</div>