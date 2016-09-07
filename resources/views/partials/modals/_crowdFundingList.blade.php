<div class='modal fade overlay-content admin-crowdfunding-modal' tabindex='-1' role='dialog' id='crowdFundingModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
				<h4 class='modal-title'>Crowdfunding</h4>
			</div>
			<div class='modal-body'>
				@if( isset( $projects ) && sizeof( $projects ) > 0 )
				<ul class="list-group crowdfunding-list margin-top-10">
					@foreach( $projects as $project )
						<li class="list-group-item" data-project-id='{{ $project["id"] }}'>
							<span class='badge remove-project' data-toggle="tooltip" data-placement="top" title="Delete">
								<span class='glyphicon glyphicon glyphicon-trash' aria-hidden='true'></span>
							</span>
							<span class='badge embed-project' data-toggle="tooltip" data-placement="top" title="Embed - Coming Soon!">
								<span class='glyphicon glyphicon glyphicon-indent-left' aria-hidden='true'></span>
							</span>
							<span class='badge edit-project' data-toggle="tooltip" data-placement="top" title="Edit"  />
								<span class='glyphicon glyphicon glyphicon-pencil' aria-hidden='true'></span>
							</span>
							<div class='crowdfunding-list-project-image' data-alias='{!! $project["alias"] !!}' style='background: url({!! $project["icon"] !!}) top left no-repeat; background-size: cover;'>
							</div>
							
							<div class='project-module-name'><a href='/crowdfunding/{!! $project["alias"] !!}'>{!! $project["title"] !!}</a></div>
                    		<div class='project-module-org-name'>
		                        {!! $project["org_name"] !!}
		                    </div>
						</li>
					@endforeach
				</ul>
				@endif
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-primary add-new-project'>Add New</button>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
			</div>
		</div>
	</div>
</div>