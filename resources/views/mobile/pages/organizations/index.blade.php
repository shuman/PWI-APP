@extends('header')
@section('content')

<div data-role='page' id='org-page'>
	@include('mobile.headers.generic')
	<div data-role='main' class='ui-content' style='padding:0;' id="organization-page">
		<input type='hidden' name='initialLoad' value='{!! $initialPull !!}' />
		<input type='hidden' name='nextPayLoad' value='{!! $payload !!}' />
		@if( sizeof( $orgs ) > 0 )
		<div class='module-header'> 
			<span class='left' >Organizations</span>
			<span class='right'><a>See More</a></span>
		</div>
		<div class='org-module-items'>
		@foreach( $orgs as $org )
			<div class='org-module'>
				<div class='org-module-img-container margin-right-15 margin-bottom-5 padding-0 '>
		            <a href='/organization/{!! $org["org_alias"] !!}' rel='external'><img src='{!! $org["logoImg"] !!}' align='left'/></a>
		        </div><!-- end .org-module-img-container -->
		        <div class='org-module-data'>
		            <div class='org-module-name'><a href='/organization/{!! $org["org_alias"] !!}' rel='external'>{!! stripslashes( $org["org_name"] ) !!}</a></div>
		            
		            <!-- Organization Impact Countries-->
		            @if( ! empty( $org["countries"] ) )
		            <div class='org-impacts-causes locations'>
		                <span class='title'>Locations</span>&nbsp;
		                <span class='list'>
		                @for( $i = 0 ; $i < sizeof( $org["countries"] ) ; $i++ )
		                	@if( $i == 0 )
		                		{!! $org["countries"][$i]->country_name !!}
		                	@else
		                		, {!! $org["countries"][$i]->country_name !!}
		                	@endif
		                @endfor
		                </span>
		            </div><!-- end .impact-causes -->
		            @else
			            <div class='impacts-causes locations hidden'>
			            	<div class='title'></div>
			            	<div class='list'></div>
			            </div>
		            @endif
		            
		            <!-- Organization Impact Causes -->
		            @if( sizeof( $org["causes"] ) > 0 )
		            <div class='org-impacts-causes causes'>
		                <span class='title'>Causes</span>&nbsp;
		                <span class='list'>
		                	@for( $i = 0 ; $i < sizeof( $org["causes"] ) ; $i++ )
			                	@if( $i == 0 )
			                		{!! $org["causes"][$i]->cause_name !!}
			                	@else
			                		, {!! $org["causes"][$i]->cause_name !!}
			                	@endif
			                @endfor
		                </span>
		            </div><!-- end .impacts-causes -->
		            @else
			            <div class='impacts-causes causes hidden'>
			            	<div class='title'></div>
			            	<div class='list'></div>
			            </div>
		            @endif

		        </div><!-- end .pull-left -->
		    </div>
		@endforeach
		</div>
		<div class='search-more'><a href='#'>more</a></div>
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

    var $orgTemplate = $("<div class='org-module'>" + $(".org-module:first").html( ) + "</div>");

    console.log( $orgTemplate );

    $(".search-more").on("click", function( ){

		$.ajax({
            method: "POST",
            url: "/organization/more",
            data: {
              payload: initialPayLoad,
              next: nextPayLoad
            },
            dataType: "json",
            beforeSend: function( ){
                //$(".loadingMore").show( );
            },
            success: function( data ){

            	//var count = data.count;
            	var path  = data.path;

            	//delete data.count;
            	delete data.path;

            	var count =  Object.keys( data ).length;

            	for( var x = 0 ; x < count ; x++ ){
            		$orgTemplate.find(".org-module-img-container a").attr("href", "/organization/" + data[x].org_alias );
            		$orgTemplate.find(".org-module-img-container img").attr("src", data[x].logoImg );

            		$orgTemplate.find(".org-module-name a").attr("href", "/organization/"  + data[x].org_alias);
            		$orgTemplate.find(".org-module-name a").text( data[x].org_name.stripSlashes( ) );

            		if( data[x].countries.length > 0 ){

            			$orgTemplate.find(".locations").removeClass("hidden");

            			var locationList = "";

            			for(var c in data[x].countries ){
            				if( locationList == "" ){
            					locationList = data[x].countries[c].country_name;
            				}else{
            					locationList += ", " + data[x].countries[c].country_name;
            				}
            			}

            			$orgTemplate.find(".locations .list").text( locationList );

            		}else{
            			$orgTemplate.find(".locations").addClass("hidden");
            		}

            		if( data[x].causes.length > 0 ){

            			$orgTemplate.find(".causes").removeClass("hidden");

            			var causeList = "";

            			for(var c in data[x].causes ){
            				if( causeList == "" ){
            					causeList = data[x].causes[c].cause_name;
            				}else{
            					causeList += ", " + data[x].causes[c].cause_name;
            				}
            			}

            			$orgTemplate.find(".causes .list").text( causeList );
            		}else{
            			$orgTemplate.find(".causes").addClass("hidden");
            		}

            		$(".org-module-items").append( "<div class='org-module'> " + $orgTemplate.html( ) + "</div>" );
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