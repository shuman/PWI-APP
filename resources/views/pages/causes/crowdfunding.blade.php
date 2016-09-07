@extends("header")

@section("content")
<script> setHeight( ); </script>
<input type='hidden' name='page' value='crowdfunding' />

<div class='row'>
    <div class='col-lg-6 col-md-6'>
        <div class='list-header'>CrowdFunding in <b><a href='/cause/{!! $alias !!}'>{!! $cause_name !!}</a></b></div>
        <div class='project-module-list no-outline margin-top-10'>
            <div class='projects'>
                @foreach( $projects as $project )
                <div class="project-module">
                    <div class='project-module-top'>
                        <div class='project-module-img-container margin-right-10 margin-bottom-5 padding-0 pull-left'>
                            <a href='/crowdfunding/{!! $project["project_alias"] !!}'>
                                <img src='{!! $iconPath !!}{!! $project["icon"] !!}' align='left'/>
                            </a>
                        </div>
                        <div class='pull-left'>
                            <div class='project-module-name pull-left'>
                                <a href='/crowdfunding/{!! $project["project_alias"] !!}'>{!! $project["title"] !!}</a>
                            </div>
                            <div class='project-module-org-name'>
                                {!! stripslashes( $project["org_name"] ) !!}
                            </div>
                            <div class='impacts-causes'>
                                <span class='title'>Locations</span><br />
                                <span class='list'>
                                @for( $i = 0 ; $i < sizeof( $project["countries"] ) ; $i++ )
                                    @if( $i == 0 )
                                        {!! $project["countries"][$i]->country_name !!}   
                                    @else
                                        , {!! $project["countries"][$i]->country_name !!}
                                    @endif
                                @endfor
                                
                                </span>
                            </div>

                            <div class='impacts-causes'>
                                <span class='title'>Causes</span><br />
                                <span class='list'>
                                @for( $i = 0 ; $i < sizeof( $project["causes"] ) ; $i++ )
                                    @if( $i == 0 )
                                        {!! $project["causes"][$i]->cause_name !!}   
                                    @else
                                        , {!! $project["causes"][$i]->cause_name !!}
                                    @endif
                                @endfor
                                </span>
                            </div>
                        </div>
                    </div>
                    <div style='clear:both;'></div>
                    <div class='project-module-status'>
                        <div class='status-line'>
                            <div class='pull-left projectRaisedAmt'>
                                {!! $project["amtRaised"] !!}
                            </div>
                            <div class='pull-right projectGoal'>
                                out of <span class='projectGoalAmt'>{!! $project["fundGoal"] !!}</span>
                            </div>
                        </div>
                        <div class='status-line'>
                            <div class='progress'>
                                <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: {!! $project['percentage'] !!}%;">
                                </div>
                            </div>
                        </div>
                        <div class='status-line'>
                            <div class='pull-left projectGoal'>
                                {!! $project["percentage"] !!}% complete
                            </div>
                            <div class='pull-right projectGoal'>
                                <span class='projectGoalAmt'>{!! $project["daysleft"] !!}</span> days left
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class='loadingMore'>Loading More Crowdfunding Projects</div>
        </div>
    </div>
    <div class='col-lg-3 col-md-3'>
        <div class='browse-by'>
            <div class='list-header margin-bottom-15'>Also Browse By</div>
            <div class='margin-top-10 margin-bottom-10'>
                <a href='' class='browseCause'>Causes</a>
            </div>
            <div class='margin-top-10 margin-bottom-10'>
                <a href='' >Countries</a>
            </div>
            <div class='margin-top-10 margin-bottom-10'>
                <a href='' >Crowdfunding</a>
            </div>
            <div class='margin-top-10 margin-bottom-10'>
                <a href=''>Products</a>
            </div>
        </div>
        <hr />
    </div>
</div>

@stop