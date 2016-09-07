@extends('header')
@section('content')

<div data-role='page' id='crowdfunding-page'>
	@include('mobile.headers.generic')
	<div data-role='main' class='ui-content' style='padding:0;' id="crowdfunding-page">
		<input type='hidden' name='initialLoad' value='{!! $initialPull !!}' />
		<input type='hidden' name='nextPayLoad' value='{!! $payload !!}' />
		@if( sizeof( $projects) )
		<div class='projects'>
			<div class='module-header'> 
				<span class='left' >Crowdfunding Projects</span>
			</div>
			<div class='crowdfunding-module-items'>
				@foreach( $projects as $project )
				<div class='crowdfunding-module'>
					<div class='crowdfunding-top'>
						<div class='title'><a href='/crowdfunding/{!! $project["project_alias"] !!}' rel='external'>{!!$project["title"] !!}</a></div>
						<div class='sub-title'>{!! $project["org_name"] !!}</div>
					</div>
					<div class='crowdfunding-data'>
						<div class='crowdfunding-module-img-container'>
							<a href='/crowdfunding/{!! $project["project_alias"] !!}' rel='external'><img src='{!! $project["icon"] !!}' align='left'/></a>

						</div>
						<div class='crowdfunding-impact-countries'>
				            <!-- Organization Impact Countries-->
				            @if( ! empty( $project["countries"] ) )
				            <div class='impacts-causes locations'>
				                <div class='title'>Locations</div>
				                <div class='list'>
				                	@for( $i = 0 ; $i < sizeof( $project["countries"] ) ; $i++ )
				                		@if( $i == 0 )
				                			{!! $project["countries"][$i]->country_name !!}
				                		@else
				                			, {!! $project["countries"][$i]->country_name !!}
				                		@endif
				                	@endfor
				                </div>
				            </div><!-- end .impact-causes -->
				            @else
				            <div class='impacts-causes locations hidden'>
				            	<div class='title'></div>
				            	<div class='list'></div>
				            </div>
				            @endif
				            
				            <!-- Organization Impact Causes -->
				            @if( ! empty( $project["causes"] ) )
				            <div class='impacts-causes causes'>
				                <div class='title'>Causes</div>
								<div class='list'>
									@for( $i = 0 ; $i < sizeof( $project["causes"] ) ; $i++ )
				                		@if( $i == 0 )
				                			{!! $project["causes"][$i]->cause_name !!}
				                		@else
				                			, {!! $project["causes"][$i]->cause_name !!}
				                		@endif
				                	@endfor
								</div>
				            </div><!-- end .impacts-causes -->'
				            @else
				            <div class='impacts-causes causes hidden'>
				            	<div class='title'></div>
				            	<div class='list'></div>
				            </div>
				            @endif
						</div><!-- end .pull-left -->
					</div>
					<div class='crowdfunding-status'>
						<div class='stats'>
							<div class='stat' style='text-align: left;'>
								<span class='number amount-given'>{!! $project["amtRaised"] !!}</span> given
							</div>
							<div class='stat' style='text-align: center;'>
								<span class='number amount-funded'>{!! $project['percentage'] !!}%</span> funded
							</div>
							<div class='stat' style='text-align: right;'>
								<span class='number days-left'>{!! $project["daysleft"] !!} days</span> left
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
			<div class='search-more'><a href=''>more</a></div>
		</div>
		@endif
		@include("mobile.partials._browseby")
		
		<div class='backToTop' > <div>Back to top</div></div>

		<div style='width: 100%; height: 10px;'></div>
	</div>
	@include('mobile.footer')
</div>
<script type='text/javascript'>

	var initialPayLoad  = 0;
    var nextPayLoad 	= 0;

    initialPayLoad  = parseInt( $("input[name=initialLoad]").val( ) );
    nextPayLoad     = parseInt( $("input[name=nextPayLoad]").val( ) );

    var $crowdfundingTemplate = $("<div class='crowdfunding-module'>" + $(".crowdfunding-module:first").html( ) + "</div>");

    $(".search-more").on("click", function( ){

		$.ajax({
            method: "POST",
            url: "/crowdfunding/more",
            data: {
              payload: initialPayLoad,
              next: nextPayLoad
            },
            dataType: "json",
            beforeSend: function( ){
                //$(".loadingMore").show( );
            },
            success: function( data ){

            	var count = data.count;
            	var path  = data.path;

            	delete data.count;
            	delete data.path;

            	for( var x = 0 ; x < count ; x++ ){

					$crowdfundingTemplate.find(".crowdfunding-top .title a").text( data[x].title );
            		$crowdfundingTemplate.find(".crowdfunding-top .title a").attr("href", "/crowdfunding/" + data[x].project_alias );
            		$crowdfundingTemplate.find(".crowdfunding-top .sub-title").text( data[x].org_name );
            		$crowdfundingTemplate.find(".crowdfunding-module-img-container a").attr("href", "/crowdfunding/" + data[x].project_alias);
            		$crowdfundingTemplate.find(".crowdfunding-module-img-container img").attr("src", data[x].icon);

            		if( data[x].countries.length > 0 ){

            			$crowdfundingTemplate.find(".locations").removeClass("hidden");

            			var locationList = "";

            			for(var c in data[x].countries ){
            				if( locationList == "" ){
            					locationList = data[x].countries[c].country_name;
            				}else{
            					locationList += ", " + data[x].countries[c].country_name;
            				}
            			}

            			$crowdfundingTemplate.find(".locations .list").text( locationList );
            		}else{
            			$crowdfundingTemplate.find(".locations").addClass("hidden");
            		}

            		if( data[x].causes.length > 0 ){

            			$crowdfundingTemplate.find(".causes").removeClass("hidden");

            			var causeList = "";

            			for(var c in data[x].causes ){
            				if( causeList == "" ){
            					causeList = data[x].causes[c].cause_name;
            				}else{
            					causeList += ", " + data[x].causes[c].cause_name;
            				}
            			}
            			$crowdfundingTemplate.find(".causes .list").text( causeList );
            		}else{
            			$crowdfundingTemplate.find(".causes").addClass("hidden");
            		}

            		$crowdfundingTemplate.find(".amount-given").text( data[x].amtRaised );
            		$crowdfundingTemplate.find(".amount-funded").text( data[x].percentage + "%" );
            		$crowdfundingTemplate.find(".days-left").text( data[x].daysleft + " days");

            		$crowdfundingTemplate.find(".progress-bar").attr("style", "width: " + data[x].percentage + "%;");

            		$(".crowdfunding-module-items").append( "<div class='crowdfunding-module'> " + $crowdfundingTemplate.html( ) + "</div>" );

            	}

            	if( count < initialPayLoad ){
            		$(".search-more").hide( );
            	}else{
            		nextPayLoad += parseInt( initialPayLoad );	
            	}
            }
        });
	});
</script>
@stop