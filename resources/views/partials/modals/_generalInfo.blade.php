<div class='modal fade overlay-content' tabindex='-1' role='dialog' id='generalInfoModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
				<h4 class='modal-title'>General Information</h4>
			</div>
			<div class='modal-body'>
				<div class='alert alert-success upload-generalInfo-success modal-update-dialog' style='top: 0px; left:0px;'>
				    <strong>Success!</strong> Your information has been updated!
				</div>
				<div class='alert alert-danger upload-generalInfo-error modal-update-dialog' >
				    <strong>Error!</strong> There was a problem updating your information.
				    <div class='error-list'></div>
				</div>
				<div class='input-group'>
					<label for='org-name'>Organization's Name<sup>*</sup></label>
					<br />
					@if( isset( $org ) AND isset( $org->org_name) )
						<input type='text' name='org-name' size='45' class='form-control' value='{!! $org->org_name !!}' />
					@else
						<input type='text' name='org-name' size='45' class='form-control'  />
					@endif
				</div>
				<div class='error error-org-name'></div>
				<br />
				<!--
				<div class='input-group'>
					<label for='gateway'>Brief Description<sup>*</sup></label>
					<br />
					
					<textarea name='brief-description' class='form-control' rows='8'>
						@if( isset( $org ) && isset( $org->org_desc ) )
							{!! trim( $org->org_desc ) !!}
						@endif
					</textarea>
				</div>
				<div class='error error-brief-description'></div>

				<br />
				-->
				<div class='input-group'>
					<label for='mission-statement'>Mission Statement</label>
					<br />
					@if( ! empty( $mission ) )
						<textarea name='mission-statement' class='form-control' rows='8'>{!! trim( $mission)  !!}</textarea>
					@else
						<textarea name='mission-statement' class='form-control' rows='8'></textarea>	
					@endif
				</div>
				<div class='error error-mission-statement'></div>
				<br />
				<div class='input-group'>
					<label for='about-us'>About Us</label>
					<br />
					@if( ! empty( $aboutUs ) )
						<textarea name='about-us' class='form-control' rows='8'>{!! trim( $aboutUs ) !!}</textarea>
					@else
						<textarea name='about-us' class='form-control' rows='8'></textarea>
					@endif
				</div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
				<button type='button' class='btn btn-primary save-general-info'>Save Changes</button> 
			</div>
		</div>
	</div>
</div>