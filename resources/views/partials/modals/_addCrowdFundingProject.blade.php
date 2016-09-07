<div class='modal fade overlay-content admin-crowdfunding-modal new-crowdfunding-modal' tabindex='-1' role='dialog' id='addCrowdFundingProjectModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-body'>
				<ul class='nav nav-tabs' role='tablist'>
					<li role='presentation' class='active'>
						<a href='#addCFP_DescriptionTab' aria-controls='#addCFP_DescriptionTab' role='tab' data-toggle='tab'>Description</a>
					</li>
					<li role='presentation'>
					 	<a href='#addCFP_IncentivesTab' aria-controls='#addCFP_IncentivesTab' role='tab' data-toggle='tab'>Incentives</a>
					</li>
					<!--
					<li role='presentation'>
					 	<a href='#addCFP_UpdatesTab' aria-controls='#addCFP_UpdatesTab' role='tab' data-toggle='tab'>Updates</a>
					</li>
					-->
				</ul>

				<div class='tab-content'>
					<div role='tabpanel' class='tab-pane active' id='addCFP_DescriptionTab' style='column-count: 0;'>
						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='project_name'>Project Title<sup>*</sup></label>
							<input type='text' name='project_name' size='45' class='form-control' />
						</div>
						<div class='error name-error'></div>
						

						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='incentive-description'>Project Story</label>
							<textarea name='project_story' class='form-control' ></textarea>
						</div>
						
						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='video-url'>Video Link</label>
							<input type='text' name='project_video_url' size='45' placeholder="Your Project's Video" class='form-control' />
						</div>

						<div class='row margin-top-10'>
							<div class='col-lg-6 col-md-6 col-sm-6'>
								<div class="form-group">
									<label for='project_goal'>Project Goal<sup>*</sup></label>
								    <div class="input-group">
								    	<div class="input-group-addon">$</div>
								      	<input type="text" class="form-control" name='project_goal'>
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
											<input type='text' name='project_end_date_month' placeholder='MM' class='form-control' />
										</div>
										<div class='col-lg-4 col-md-4 col-sm-4 padding-0'>
											<input type='text' name='project_end_date_day' 	 placeholder='DD' class='form-control' />
										</div>
										<div class='col-lg-4 col-md-4 col-sm-4 padding-0 padding-right-15'>
											<input type='text' name='project_end_date_year'  placeholder='YEAR' 	class='form-control' />
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
								<div class='project_thumbnail_holder'></div>
								<button class='btn btn-success project-thumbnail-upload' data-parent-modal='#addCrowdFundingProjectModal' type='button'>Choose Image</button>
							</div>
							<div class='col-lg-8 col-md-8 col-sm-8'>
								<label for='header_photo'>Header Photo <small>590 x 185 pixels</small></label><br />
								<input type='file' name='header_photo' class='upload'/>
								<div class='project_header_info'>
									<div class='file_name'></div>
									<div class='file_size'></div>
								</div>
								<div class='project_header_holder'></div>
								<button class='btn btn-success project-header-upload' data-parent-modal='#addCrowdFundingProjectModal' type='button'>Choose Image</button>
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
									<div class='row'>
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
					<div role='tabpanel' class='tab-pane' id='addCFP_IncentivesTab' style='column-count: 0;'>
						<p class='heading'>
							Incentives<br/>
							<small>Add incentives to boost donations</small>
						</p>
						<div class='incentive-list margin-top-10'></div>
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
					</div>
					<!--
					<div role='tabpanel' class='tab-pane' id='addCFP_UpdatesTab' style='column-count: 0;'>
						<p class='heading'>
							Updates<br/>
							<small>Add Updates to inform users of the Projects Progress</small>
						</p>
						<div class='project-updates-list'></div>

						<div class='input-group margin-top-10' style='width: 100%;'>
							<label for='incentive-update'>Update</label>
							<textarea name='incentive-update' class='form-control' ></textarea>
							<div class='incentive-update_error'></div>
						</div>
					</div>
					-->
				</div>
			</div>
			<div class='modal-footer row'>
				<button type='button' class='btn btn-primary save-new-project'>Save New Project</button> 
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
			</div>
		 </div>
	</div>
</div>
