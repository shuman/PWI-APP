@if( sizeof( $projects) )
<div class='projects'>
	<div class='module-header'> 
		<span class='left' >Crowdfunding</span>
		<!--<span class='right'><a>See More</a></span>-->
	</div>
	<div class='crowdfunding-module-items'>
		@foreach( $projects as $project )
		<div class='crowdfunding-module'>
			<div class='crowdfunding-top'>
				<div class='title'>{!!$project["title"] !!}</div>
				<div class='sub-title'>{!! $project["org_name"] !!}</div>
			</div>
			<div class='crowdfunding-data'>
				<div class='crowdfunding-module-img-container'>
					<a href='/crowdfunding/{!! $project["alias"] !!}' rel='external'><img src='{!! $project["icon"] !!}' align='left'/></a>

				</div>
				<div class='crowdfunding-impact-countries'>
		            <!-- Organization Impact Countries-->
		            @if( ! empty( $project["countries"] ) )
		            <div class='impacts-causes'>
		                <div class='title'>Locations</div>
		                <div class='list'>{!! $project["countries"] !!}</div>
		            </div><!-- end .impact-causes -->
		            @endif
		            
		            <!-- Organization Impact Causes -->
		            @if( ! empty( $project["causes"] ) )
		            <div class='impacts-causes'>
		                <div class='title'>Causes</div>

		                <div class='list'>{!! $project["causes"] !!}</div>
		            </div><!-- end .impacts-causes -->
		            @endif
				</div><!-- end .pull-left -->
			</div>
			<div class='crowdfunding-status'>
				<div class='stats'>
					<div class='stat' style='text-align: left;'>
						<span class='number'>{!! $project["amtRaised"] !!}</span> given
					</div>
					<div class='stat' style='text-align: center;'>
						<span class='number'>{!! $project['percentage'] !!}%</span> funded
					</div>
					<div class='stat' style='text-align: right;'>
						<span class='number'>{!! $project["daysleft"] !!} days</span> left
					</div>
				</div>
				<div class='progress'>
					<div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: {!! $project['percentage'] !!}%;">
                    </div>
				</div>
			</div>
		</div>
		@endforeach
	</div>
</div>
@endif