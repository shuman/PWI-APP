@if( count( $projects ) > 0 )
<div class='project-module-list padding-left-5 margin-bottom-10 col-lg-12'>
    @if( isset( $isAdmin ) )
        @if( $isAdmin )
            <div class='edit-cog' style='color: #e5e5e5;' data-toggle='modal' data-target='#crowdFundingModal'>
                <i class='fa fa-cog cog'></i>
            </div>
            @include("partials.modals._crowdFundingList")
            @include("partials.modals._addCrowdFundingProject")
        @endif
        
    @endif
    <div class='project-module-list-header '>
        @if( isset( $country->country_name) )
            Crowdfunding in {!! $country->country_name !!}
        @else
            Crowdfunding  
        @endif
        
        @if( ! isset( $org ) )
        <span class='margin-left-5 margin-right-5'></span>
        {!! HTML::link( $prjViewAll, 'View All') !!}
        @endif
    </div> 
    <div class='projects'>
        @foreach( $projects as $project )
        <div class="project-module">
            <div class='project-module-top'>
                <div class='project-module-img-container margin-right-10 margin-bottom-5 padding-0 pull-left'>
                    <a href='/crowdfunding/{!! $project["alias"] !!}'><img src='{!! $project["icon"] !!}' align='left'/></a>
                </div>
                <div class='pull-left'>
                    <div class='project-module-name pull-left'><a href='/crowdfunding/{!! $project["alias"] !!}'>{!! $project["title"] !!}</a></div>
                    <div class='project-module-org-name'>
                        {!! $project["org_name"] !!}
                    </div><!-- end .project-module-org-name -->
                    
                    <!-- Project Impact Countries -->
                    @if( ! empty( $project["countries"] ) )
                    <div class='impacts-causes'>
                        <span class='title'>Locations</span><br />
                        <span class='list'>{!! $project["countries"] !!}</span>
                    </div><!-- end .impacts-causes Impact Countries -->
                    @endif
                    
                    <!-- Project Impact Causes -->
                    @if( ! empty( $project["causes"] ) )
                    <div class='impacts-causes'>
                        <span class='title'>Causes</span><br />
                        <span class='list'>{!! $project["causes"] !!}</span>
                    </div><!-- end .impact-causes Impact Causes -->
                    @endif
                </div><!-- end .pull-left -->
            </div><!-- end .project-module-top -->
            <div style='clear:both;'></div>
            <div class='project-module-status'>
                <!-- Amount Raised & Project Goal -->
                <div class='status-line'>
                    <div class='pull-left projectRaisedAmt'>
                        {!! $project["amtRaised"] !!}
                    </div>
                    <div class='pull-right projectGoal'>
                        out of <span class='projectGoalAmt'>{!! $project["fundGoal"] !!}</span>
                    </div>
                </div><!-- end .status-line ( Amount Raised & Project Goal ) -->
                
                <!-- Progress Bar -->
                <div class='status-line'>
                    <div class='progress'>
                        <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: {!! $project['percentage'] !!}%;">
                        </div>
                    </div>
                </div><!-- end .status-line ( Progres Bar ) -->
                
                <!-- Project Percentage & Project Goal Amount -->
                <div class='status-line'>
                    <div class='pull-left projectGoal'>
                        {!! $project["percentage"] !!}% complete
                    </div>
                    <div class='pull-right projectGoal'>
                        <span class='projectGoalAmt'>{!! $project["daysleft"] !!}</span> days left
                    </div>
                </div><!-- end .status-line ( Project Percentage & Project Goal Amount ) -->
            </div><!-- .project-module-status -->
        </div><!-- .project-module -->
            @if( isset( $isAdmin ) )
                @if( $isAdmin )
                    @include( 'partials.modals._editCrowdFundingProject', ["project" => $project] )
                @endif
            @endif
        @endforeach

    </div><!-- end .projects -->
</div><!-- end .project-module-list -->
@else
    @if( isset( $isAdmin ) )
        @if( $isAdmin )
            <div class='btn btn-primary margin-bottom-10' style='width: 100%;' data-toggle='modal' data-target='#addCrowdFundingProjectModal'>
            Add Crowdfunding Project
            </div>  
            @include("partials.modals._addCrowdFundingProject")
        @endif
    @endif
@endif