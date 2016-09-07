<div class='modal fade overlay-content admin-crowdfunding-modal edit-crowdfunding-modal' tabindex='-1' role='dialog' id='editCrowdFundingProjectModal_{{ $project["id"] }}'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-body'>
				<ul class='nav nav-tabs' role='tablist'>
					<li role='presentation' class='active'>
						<a href='#editCFP_DescriptionTab_{{$project["id"]}}' aria-controls='#editCFP_DescriptionTab_{{$project["id"]}}' role='tab' data-toggle='tab'>Description</a>
					</li>
					<li role='presentation'>
					 	<a href='#editCFP_IncentivesTab_{{$project["id"]}}' aria-controls='#editCFP_IncentivesTab_{{$project["id"]}}' role='tab' data-toggle='tab'>Incentives</a>
					</li>
					<li role='presentation'>
					 	<a href='#editCFP_UpdatesTab_{{$project["id"]}}' aria-controls='#editCFP_UpdatesTab_{{$project["id"]}}' role='tab' data-toggle='tab'>Updates</a>
					</li>
				</ul>

				<div class='tab-content'>
					<div role='tabpanel' class='tab-pane active' id='editCFP_DescriptionTab_{{$project["id"]}}' style='column-count: 0;'>
						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='project_name'>Project Title<sup>*</sup></label>
							<input type='text' name='project_name' size='45' class='form-control' value='{{ $project["title"] }}' />
						</div>
						<div class='error name-error'></div>
						
						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='incentive-description'>Project Story</label>
							<textarea name='project_story' class='form-control' >{!! $project["desc"] !!}</textarea>
						</div>
						
						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='video-url'>Video Link</label>
							<input type='text' name='project_video_url' size='45' placeholder="Your Project's Video" class='form-control' value='{!! $project["origVideoUrl"] !!}'/>
						</div>
						<a href='/' class='viewVideo' data-status='off'>View Video</a>
						<div class='videoContainer hidden'>
							<div class="embed-responsive embed-responsive-16by9 margin-top-10">
							  <iframe class="embed-responsive-item" src='{!! $project["videoUrl"] !!}'></iframe>
							</div>
						</div>
						
						<div class='row margin-top-10'>
							<div class='col-lg-6 col-md-6 col-sm-6'>
								<div class="form-group">
									<label for='project_goal'>Project Goal<sup>*</sup></label>
								    <div class="input-group">
								    	<div class="input-group-addon">$</div>
								      	<input type="text" class="form-control" name='project_goal' value='{!! $project["goalInt"] !!}'>
								      	<div class="input-group-addon">.00</div>
								    </div>
								</div>
								<div class='error goal-error'></div>
							</div>
							<div class='col-lg-6 col-md-6 col-sm-6'>
								<div class='input-group'>
									<label for='project_goal'>End Date<sup>*</sup></label>
									<div class='row'>
										<div class='col-lg-4 col-md-4 col-sm-4 padding-0 padding-left-15'>
											<input type='text' name='project_end_date_month' placeholder='MM' class='form-control' value='{{ $project["endMonth"] }}'/>
										</div>
										<div class='col-lg-4 col-md-4 col-sm-4 padding-0'>
											<input type='text' name='project_end_date_day' 	 placeholder='DD' class='form-control' value='{{ $project["endDay"] }}' />
										</div>
										<div class='col-lg-4 col-md-4 col-sm-4 padding-0 padding-right-15'>
											<input type='text' name='project_end_date_year'  placeholder='YEAR' 	class='form-control' value='{{ $project["endYear"] }}' />
										</div>
									</div>
									<div class='error end_date-error margin-top-15'></div>
								</div>
							</div>
						</div>

						<div class='row margin-top-10'>
							<div class='col-lg-4 col-md-4 col-sm-4 text-center'>
								<label for='thumbnail_photo'>Thumbnail Photo</label>
								<input type='file' name='thumbnail_photo' class='upload'/>
								<div class='project_thumbnail_info'>
									<div class='file_name'></div>
									<div class='file_size'></div>
								</div>
								@if( ! empty( $project["icon"] ) )
									<div class='project_thumbnail_holder' style='background: url( {!! $project["icon"] !!}) top left no-repeat; background-size: cover; display: block;'></div>
								@endif
								<button class='btn btn-success project-thumbnail-upload' data-parent-modal='#editCrowdFundingProjectModal_{{ $project["id"] }}' type='button'>Choose Image</button>
							</div>
							<div class='col-lg-8 col-md-8 col-sm-8'>
								<label for='header_photo'>Header Photo <small>590 x 185 pixels</small></label><br />
								<input type='file' name='header_photo' class='upload'/>
								<div class='project_header_info'>
									<div class='file_name'></div>
									<div class='file_size'></div>
								</div>
								@if( ! empty( $project["header"] ) )
									<div class='project_header_holder' style='background: url( {!! $project["header"] !!}) top left no-repeat; background-size: cover; display: block;'></div>
								@endif
								<button class='btn btn-success project-header-upload' type='button' data-parent-modal='#editCrowdFundingProjectModal_{{ $project["id"] }}'>Choose Image</button>
							</div>
						</div>

						<div class='project-cause-list margin-top-10'>
							<p class='heading'>
								Select Cause(s)<br />
								<small>Don't see the cause? Only the causes that you have entered will show as options.</small>
							</p>
							<div class='currentCauseList row margin-0'>
								@foreach( $causes as $cause )
								<div class='col-lg-4 col-md-4 col-sm-4 project-org-cause' data-org-cause='{{ $cause["id"] }}' data-org-cause-id='{{ $cause["orgCauseId"] }}' >
									@if( Helper::checkKeyValueExists( $project["projectCauses"]->toArray( ), "cause_id", $cause["id"] ) )
									<div class='row chosen_project_cause'>
									@else
									<div class='row'>
									@endif
										<div class='col-lg-3 col-md-3 col-sm-3'>
											<i class='{{ $iconMap[$cause["id"]]}} pwi-icon-2em'></i>
										</div>
										<div class='col-lg-9 col-lg-9 col-sm-9 padding-left-0' >
											<div class='cause-title padding-top-5'>{{ $cause["name"] }}</div>
										</div>
									</div>
								</div>
								@endforeach
							</div>
						</div>
					</div>
					<div role='tabpanel' class='tab-pane' id='editCFP_IncentivesTab_{{$project["id"]}}' style='column-count: 0;'>
						<p class='heading'>
							Incentives<br/>
							<small>Add incentives to boost donations</small>
						</p>
						<div class='incentive-list margin-top-10'>
							<ul class='list-group'>
							@foreach( $project["incentives"] as $incentive )
								<li class='list-group-item' data-incentive-id='incentive_{{ $incentive->project_incentive_id}}' data-project-id='{{ $project["id"] }}'>
									<div class='hidden incentive-desc'>{!! $incentive->project_incentive_description !!}}</div>
									{!! Form::hidden('incentiveName', $incentive->project_incentive_title) !!}
									{!! Form::hidden('incentiveAmt',  $incentive->project_incentive_amount) !!}
									{!! Form::hidden('incentiveCnt',  $incentive->project_available_incentive_count) !!}
									{!! Form::hidden('incentiveShip', $incentive->project_donor_shipping_address) !!}
									{!! Form::hidden('incentiveId',   $incentive->project_incentive_id) !!}
									
                					<span class='badge remove-incentive'>
                						<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
                					</span>
                					<span class='badge edit-incentive'>
                						<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>
                					</span>
                					<span class='incentive-list-name'>{{$incentive->project_incentive_title}}</span>
                				</li>
							@endforeach
							</ul>
						</div>
						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='incentive-name'>Incentive Name</label>
							<input type='text' name='incentive-name' class='form-control' />
						</div>
						<div class='error incentive-name_error'></div>
						<div class='row margin-top-10'>
							<div class='col-lg-6 col-md-6 col-sm-6'>
								<div class="form-group">
									<label for='incentive-donation-amount'>Incentive Amount</label>
								    <div class="input-group">
								    	<div class="input-group-addon">$</div>
								      	<input type='text' name='incentive-donation-amount' class='form-control' />
								      	<div class="input-group-addon">.00</div>
								    </div>
								    <div class='error incentive-donation-amount_error'></div>
								</div>
							</div>
							<div class='col-lg-6 col-md-6 col-sm-6'>
								<div class='input-group'>
									<label for='incentive-number-available'>Number Available</label>
									<input type='text' name='incentive-number-available' class='form-control' />
								</div>
								<div class='error incentive-number-available_error'></div>
							</div>
						</div>

						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='incentive-description'>Description</label>
							<textarea name='incentive-description' class='form-control' ></textarea>
							<div class='incentive-description_error'></div>
						</div>

						<div class='checkbox margin-top-10' style='width: 100%;'>
							<label for='incentive-has-shipping'>
								<input type='checkbox' name='incentive-has-shipping' value='1' /> Does this Incentive Require Shipping?	
							</label>
						</div>

						<input type='button' class='btn btn-success add-incentive' value='Add Incentive' />
						<input type='button' class='btn btn-danger clear-incentive margin-top-10' value='Clear Incentive' />
					</div>
					
					<div role='tabpanel' class='tab-pane' id='editCFP_UpdatesTab_{{$project["id"]}}' style='column-count: 0;'>
						<p class='heading'>
							Updates<br/>
							<small>Add Updates to inform users of the Projects Progress</small>
						</p>
						<div class='project-updates-list'>
							<ul class='list-group'>
							@foreach( $project["updates"] as $update )
								<li class='list-group-item' data-update-id='update_{{ $update->project_update_id}}' data-project-id='{{ $project["id"] }}'>
									<div class='hidden update-desc'>{!! $update->description !!}}</div>
									{!! Form::hidden('updateTitle', $update->title) !!}
									{!! Form::hidden('updateId',    $update->project_update_id) !!}
									
                					<span class='badge remove-update'>
                						<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
                					</span>
                					<span class='badge edit-update'>
                						<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>
                					</span>
                					@if( is_null( $update->updated_at ) )
                						<small>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($update->updated_at))->diffForHumans() }}</small>
                					@else
                						<small>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($update->created_at))->diffForHumans() }}</small>
                					@endif
                					&nbsp; 
                					<span class='update-list-title'>{{$update->title}}</span>
                				</li>
							@endforeach
							</ul>
						</div>

						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='update_title'>Title</label>
							<input type='text' name='update-title' class='form-control' />
							<div class='update-title_error'></div>
						</div>

						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='update_desc'>Update</label>
							<textarea name='update-desc' class='form-control' ></textarea>
							<div class='update-desc_error'></div>
						</div>

						<input type='button' class='btn btn-success add-update margin-top-10' value='Add Update' />
						<input type='button' class='btn btn-danger clear-update margin-top-10' value='Clear Update' />
					</div>
				</div>
			</div>
			<div class='modal-footer row'>
				<button type='button' class='btn btn-primary update-project' data-project-id='{{ $project["id"] }}'>Update Project</button> 
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
			</div>
		 </div>
	</div>
</div>
