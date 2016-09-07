<div class='modal fade overlay-content' tabindex='-1' role='dialog' id='orgCauseModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
				<h4 class='modal-title'>Update/Add Causes</h4>
			</div>
			<div class='modal-body'>
				<div class='alert alert-success upload-cause-success modal-update-dialog' >
				    <strong>Success!</strong> Your information has been updated.!
				</div>
				<div class='alert alert-danger upload-cause-error modal-update-dialog' >
				    <strong>Error!</strong> There was a problem uploading your file:
				    <p class='error-list'></p>
				</div>

				<p class='currentCauses'>
					Selected Causes
				</p>
				
				<div class='currentCauseList row'>
					@foreach( $causes as $cause )
					<div class='col-lg-4 col-md-4 col-sm-4 org-cause' data-org-cause='{{ $cause["id"] }}' data-org-cause-id='{{ $cause["orgCauseId"] }}'>
						<div class='row'>
							<div class='col-lg-3 col-md-3 col-sm-3'>
								<i class='{{ $iconMap[$cause["id"]]}} pwi-icon-2em'></i>
							</div>
							<div class='col-lg-9 col-lg-9 col-sm-9' >
								<div class='cause-title' >{{ $cause["name"] }}</div>
								<div class='sub-cause-list'>
									@if( sizeof( $orgSubCauses) > 0 )
										@foreach( $orgSubCauses as $orgSubCause )
											@if( $cause["id"] == $orgSubCause->cause_id )
												<span class='org-sub-cause' data-subcause-id='{{ $orgSubCause->org_sc_item_id }}'>{{ $orgSubCause->cause_name }}</span>
											@endif
										@endforeach
									@endif
								</div>
								<div class='cause-country-list'>
									<?php $firstCountry = 1; ?>
									@for( $i = 0 ; $i < sizeof( $countries ) ; $i++ )
										@if( $countries[$i]->cause_id == $cause["id"] )
											@if( $firstCountry == 1 )
											<?php $firstCountry = 0; ?>
											@else
											,
											@endif
											<span class='org-cause-country' data-cause-country='{{ $countries[$i]->org_sc_item_id}}'>{{ $countries[$i]->country_name }}</span>
										@endif
									@endfor
								</div>
							</div>
						</div>
						<div class='row hidden cause-remove-button text-center'>
							<div class='col-lg-12 col-md-12 col-sm-12'>
							 	<button type='button' class='btn btn-danger padding-2 margin-top-2' data-toggle='button' aria-pressed='false' autocomplete='off' data-cause-id='{{ $cause["id"] }}'>Remove Cause</button>
							</div>
						</div>
						<div class='cause-text' id='org-cause-description'>{!! $cause["desc"] !!}</div>
					</div>
					@endforeach
				</div>
				<hr />
				<p class='heading main-cause-heading'>
					Add New Cause
				</p>

				<div class='availableCauseList row btn-group' data-toggle='buttons'>
					@foreach( $causeList as $causeListItem )
						<label class='btn btn-default margin-10' style='border-radius: 4px;'>
							<input type="radio" name="cause-option" id="cause-option-{{ $causeListItem['cause_id'] }}" autocomplete="off"> 
							<div class='cause-icon' style='font-size:1.25em;'>
								<i class='{{$iconMap[$causeListItem["cause_id"]]}} pull-left' style='font-weight: bold;'></i>
							</div>
							<div class='cause-name' style='padding-left: 1.5em;'>
								{{ $causeListItem["cause_name"] }}
							</div>
						</label>
					@endforeach
				</div>
				<hr />
				<div class='subCauseWrapper hidden'>
					<p class='heading'>
						Select the sub cause
					</p>
					<div class='availableSubCauseList row btn-group' data-toggle='buttons'>
						@foreach( $subCauseList as $subCauseListItem )
							<label class='btn btn-default margin-10 sub-cause-item' style='border-radius: 4px;' data-parent-id='{{$subCauseListItem["cause_parent_id"]}}'>
								<input type="checkbox" name="sub-cause-option" id="sub-cause-option-{{ $subCauseListItem['cause_id'] }}" autocomplete="off"> 
								<div class='cause-icon' style='font-size:1.25em;'>
									<i class='{{$iconMap[$subCauseListItem["cause_parent_id"]]}} pull-left' style='font-weight: bold;'></i>
								</div>
								<div class='cause-name' style='padding-left: 1.5em;'>
									{{$subCauseListItem["cause_name"]}}
								</div>
							</label>
						@endforeach
					</div>
					<hr />	
				</div>

				<p class='heading'>Select the Impact Countries</p>
				<div class='input-group' id='add-country-input-group'>
					<input type='text' size='45' name='country-text' class='form-control' />
					<span class='input-group-addon btn btn-success' id='add-country'>Add</span>
				</div>

				<div class='country-list'></div>
				<hr />
				
				<p class='selectedCauseDescription'>Cause Description</p>
				<textarea name='cause-description-textarea' class='form-control' ></textarea>
				<hr />
				<div class='btn btn-primary add-new-cause cause-action' style='width: 100%;'>Add New Cause</div>

			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
			</div>
		</div>
	</div>
</div>