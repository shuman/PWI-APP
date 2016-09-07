<div class='modal fade overlay-content' tabindex='-1' role='dialog' id='mediaModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-body'>
				<ul class='nav nav-tabs' role='tablist'>
					<li role='presentation' class=>
						<a href='#photosTab' aria-controls='photosTab' role='tab' data-toggle='tab'>Photos</a>
					</li>
					<li role='presentation'>
					 	<a href='#videosTab' aria-controls='videosTab' role='tab' data-toggle='tab'>Videos</a>
					</li>
				</ul>

				<div class='tab-content'>
					<div role='tabpanel' class='tab-pane' id='photosTab' style='column-count: 0;'>
						<div class='alert alert-success upload-photo-success modal-update-dialog' style='top:0px;left:0px;'>
						    <strong>Success!</strong> <span class='msg'>Your photo has been uploaded.</span>
						</div>
						<div class='alert alert-info upload-photo-info modal-update-dialog' style='top:0px;left:0px;'>
						    Your file is being uploaded...
						</div>
						<div class='alert alert-danger upload-photo-error modal-update-dialog' style='top:0px;left:0px;'>
						    <strong>Error!</strong> <span class='msg'>There was an error uploading your photo.</span>
						</div>
						<p class='heading'>Photos</p>
						<div class='photo_list' style='border-spacing: 10px;'>
							<input type='file' name="logo" class="upload" id="orgPic" />
							<div class='droppable-box box photo-upload '> Drag Image or Click to Upload </div>
							@for($i = 0 ; $i <  sizeof( $photos ) ; $i++ )
								@if( isset( $photos[$i] ) )
									<div class='box' style='background: url( /images/organization/{!! rawurlencode( $photos[$i]->file_path ) !!} ) top left no-repeat; background-size: cover;'>
										<div class='overlay'>
											<button type="button" class="btn btn-danger remove-photo" data-file-id='{{$photos[$i]->file_id}}'>Delete</button>
										</div>
									</div>
								@endif
							@endfor
						</div>
					</div>
					<div role='tabpanel' class='tab-pane' id='videosTab' style='column-count: 0;'>
						<div class='alert alert-success upload-video-success modal-update-dialog' style='top:0px;left:0px;'>
						    <strong>Success!</strong> <span class='msg'>Your video is being saved.</span>
						</div>
						<div class='alert alert-info upload-video-info modal-update-dialog' style='top:0px;left:0px;'>
						    Your video is being saved...
						</div>
						<div class='alert alert-danger upload-video-error modal-update-dialog' style='top:0px;left:0px;'>
						    <strong>Error!</strong> <span class='msg'>There was an error saving your video</span>
						</div>
						<p class='heading'>Videos</p>
						<label for='video-url'>Video Link</label>
						<br />
						<div class='input-group'>
							<input type='text' name='video_url' size='45' class='form-control' />
							<span class='input-group-btn'>
								<button class='btn btn-success save-video' style='height: 45px;'>Save</button> 
							</span>
						</div>
						<div class='video_list' style='display: table; border-spacing: 10px;'>
							@for($i = 0 ; $i <  sizeof( $videos ) ; $i++ )
								<div class='box' style='background: url( {!! $videos[$i]->video_id !!} ) top left no-repeat; background-size: cover;'>
									<div class='overlay'>
										<button type="button" class="btn btn-danger remove-video" data-file-id='{{$videos[$i]->org_video_id}}'>Delete</button>
									</div>
								</div>
							@endfor
						</div>
					</div>
				</div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
			</div>
		 </div>
	</div>
</div>
