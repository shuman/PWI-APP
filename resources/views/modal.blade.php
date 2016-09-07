<div class='modal white' id='causeModal'>
    <div class='cause-container'>
        <div class='exit-cause-modal exit pull-right' data-control="#causeModal">
            <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
        </div>
        <div class='select'>Select Cause</div>
        <div class='overlay-causes overlay-content'>
            <div class='overlay-cause-list'>
                @for( $i = 0 ; $i < sizeof( $causes ) ; $i++ )
                @if( $i == 0 )
                <div class='row'>
                    @elseif( ( $i%4 ) == 0)
                </div>
                <div class='row'>
                    @endif 
                    <div class='col-lg-3 col-md-3 col-sm-3 overlay-cause-item' >
                        <div class='row'>
                            <div class='col-lg-4 col-md-4 col-sm-4'>
                                <a href='/cause/{!! $causes[$i]->cause_alias !!}' class='modal-cause-icon pull-left'>

                                    <i class='{!! $iconmap[$causes[$i]->cause_id] !!}'></i>
                                </a>
                            </div>
                            <div class='col-lg-8 col-md-8 col-sm-8 modal-cause-name'>
                                <div>
                                    <a href='/cause/{!! $causes[$i]->cause_alias !!}' class='cause-link'>{!! $causes[$i]->cause_name !!}</a>
                                    <br/>
                                    <a href='#subcause' class='view-subcauses' data-cause-id='{!! $causes[$i]->cause_id !!}' data-cause-name='{!! $causes[$i]->cause_name !!}'>View Subcauses</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor   
                    @if( ($i%4) != 0 )
                </div>
                @endif
            </div>
            <?php
            $lastId = "";
            $j = 0;
            ?>

            @foreach( $subcauses as $subcause )
            @if( $subcause->cause_parent_id != $lastId )
            @if( ! empty( $lastId ) )
            <!-- check to see if row needs to be closed -->
            @if( ($j%4) != 0 || $j == 4)
        </div>
        <!-- end row -->
        @endif
        <?php $j = 0; ?>
        <!-- end subcause -->
    </div>
    @endif
    <div class='overlay-subcause-list' data-parent-id='{!! $subcause->cause_parent_id !!}' >
        <h1><i class='{!! $iconmap[$subcause->cause_parent_id] !!}'></i><span></span><br /><small>Back to Causes</small></h1>
        @else
        @if( ($j%4) == 0 && $j != 0)
        <!-- end row -->
    </div>
    <?php $j = 0; ?>
    @endif
    @endif

    @if( $j == 0 )
    <!-- create row -->
    <div class='row'>
        @endif

        <!-- display content - individual subcause -->
        <div class='col-lg-3 col-md-3 col-sm-3 margin-top-10 margin-bottom-10 sub-cause-list' >
            <a href='/cause/{!! $subcause->cause_alias !!}' class='cause-link'>{!! $subcause->cause_name !!}</a>
        </div>

        <!-- set lastId to the current parent_id && increment j -->
        <?php
        $lastId = $subcause->cause_parent_id;
        $j++;
        ?>
        @endforeach
        @if( ($j%4) != 0 || $j == 4)
    </div>
    <!-- end row -->
    @endif
</div><!-- end last subcause -->
</div>
</div>
</div>

<div class='modal white' id='countryModal'>
    <div class='modal-content-container'>
        <div class='exit-country-modal exit' data-control="#countryModal"  style='position: absolute; right: 10px; top: 10px;'>
            <span class='glyphicon glyphicon-remove'  aria-hidden='true'></span>
        </div>
        <h2 style='font-size: 36px; font-weight: 600;'>Select a Country</h2>
        <div class='country-space'>
            <span class='flag-wrapper' style='height: 33px; width: 50px; margin-left: -100px;'>
                <span class='flag-icon flag flag-background'></span>
            </span> 
            <div class='country-overlay-name' style='position: fixed; margin-left: 10px; display: inline;'></div>
        </div>

        @if( Agent::isMobile( ) )
        <div class='panel-group' id='mobile-country-list' role='tablist' aria-mulitselectable='true'>
            @for( $i = 0 ; $i < sizeof( $zones ) ; $i++ )
            <div class='panel panel-default'>
                <div class='panel-heading' role='tab' id='heading-{!! $i !!}'>
                    <h4 class='panel-title'>
                        <a role="button" data-toggle="collapse" data-parent="#mobile-country-list" href="#country-pane-{!! $i !!}" aria-expanded="true" aria-controls="country-pane-{!! $i !!}">{!! $zones[$i]->zone_name !!}</a>
                    </h4>
                </div>
                <div id='country-pane-{!! $i !!}' class='panel-collapse collapse' role='tabpanel' aria-labelledby='heading-{!! $i !!}'>
                    <div class='panel-body'>
                        {!! $countries[$zones[$i]->zone_id] !!}
                    </div>
                </div>
            </div>
            @endfor
        </div>
        @else
        <!-- Country Tabs -->
        <div class='continentContainer overlay-content'>

            <ul class='nav nav-tabs' role='tablist'>

                @for( $i = 0 ; $i < sizeof( $zones ) ; $i++ )
                @if( $i == 0 )
                <li role='presentation' class='active'><a href='#country-pane-{!! $i !!}' aria-controls='country-pane-{!! $i !!}' role='tab' data-toggle='tab'>{!! $zones[$i]->zone_name !!}</a></li>         
                @else
                <li role='presentation'><a href='#country-pane-{!! $i !!}' aria-controls='country-pane-{!! $i !!}' role='tab' data-toggle='tab'>{!! $zones[$i]->zone_name !!}</a></li>                 
                @endif
                @endfor
            </ul>
        </div>

        <!-- Country Panes -->
        <div class='tab-content country-tab'>
            @for( $i = 0 ; $i < sizeof( $zones ) ; $i++ )
            @if( $i == 0 )
            <div role='tabpanel' class='tab-pane active modal-tab-pane' id='country-pane-{!! $i !!}'>
                {!! $countries[$zones[$i]->zone_id] !!}
            </div>
            @else
            <div role='tabpanel' class='tab-pane modal-tab-pane' id='country-pane-{!! $i !!}'>
                {!! $countries[$zones[$i]->zone_id] !!}
            </div>             
            @endif
            @endfor
        </div>
        @endif
    </div>
</div>

<div class='modal white' id='postReviewModal'>
    <div class='review-container overlay-content'>
        <div class='exit-review exit pull-right' data-control="#postReviewModal">
            <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
        </div>
        <h2>Leave Comment</h2>
        @include("modules.postcomment")
    </div>
</div>

<div class='modal white' id='suggestNonProfitModal'>
    <div class='suggestNP-container'>
        @include("modules.suggestNonProfit")
    </div>
</div>