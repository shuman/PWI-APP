<div data-role='page' id='demographics'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class="ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-icon-notext
">back</a>
		<h1>
			<div class='img-thumbnail flag-wrapper' style='height: 27px; width: 40px; margin-top: 8px; margin-left: 5px;'>
                <span class='flag-icon flag flag-background flag-icon-{!! strtolower( $country->country_iso_code ) !!}'></span>    
            </div> 
            <span>{!! stripslashes( $country->country_name ) !!}</span>
        </h1>
	</div><!-- /header -->

	<div data-role='main'>
		<div class='stat-header'>Demographics</div>
		
        <div class='country-demographics stat-content'>
            <div class='demographics-data padding-left-5'>
            @foreach( $demographics as $demographic )

                @if( $demographic["type"] == "text" )
                    <b>{!! $demographic["name"] !!}</b>
                    <div class='data-text margin-top-10'>
                    {!! $demographic["data"] !!}
                    </div>
                @else
                    <div class='charts margin-top-10'>
                        <b>{!! $demographic["name"] !!}</b>
                        @foreach( $demographic["data"] as $data )
                        <input type='hidden' name='item_name[]' value='{!! $data["name"] !!}' />
                        <input type='hidden' name='item_percentage[]' value='{!! $data["percentage"] !!}'/>    

                        @if( ! empty( $data["date"] ) )
                            <input type='hidden' name='item_date' value='{!! $data["date"] !!}' />
                        @endif

                        @endforeach
                        <div class='chart-legend'></div>
                         <canvas width="205px" height="205px" style='margin-left: auto; margin-right: auto; display: block;'/>
                    </div>
                @endif
            @endforeach
            </div>
        </div>
    </div>
    <script>
    
    $chartColorArray = ["#eeaef4", "#f1657f", "#f4b533", "#dA4a4a", "#e4272d", "#6e33f4", "#3375f4", "#f433dc", "#33f4a6", "#42da5e", "#ff0000", "#fff000", "#ffff00", "#ee00000", "#eeee00"];

    $(document).on("pageshow", "#demographics", function( ){

        /* Start Country Chart Script */

        $(".charts").each( function( i ){

            console.log('chart');
        
            var chartData = [];
            
            var names = $(this).find("input[name='item_name[]']");
            
            var percentage = $(this).find("input[name='item_percentage[]']");

            var showChart = true;
            
            for( var i = 0 ; i < names.length ; i++ ){
                
                if( isNaN( parseFloat( $(percentage[i]).val( ) ) ) ){
                    showChart = false;
                }
                
                chartData.push({
                    'label': $(names[i]).val( ),
                    'value': parseFloat( $(percentage[i]).val( ) ),
                    'color': $chartColorArray[i],
                    'highlight': $chartColorArray[i]
                });
            }
            
            if( showChart ){
                
                var $ctx = $(this).find("canvas");

                    
                var PieChart = new Chart( $ctx[0].getContext("2d") ).Pie(
                    chartData,{
                     legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"   
                });
                
                var legend = PieChart.generateLegend( );
                $(this).find(".chart-legend").html( legend );
            }else{
                $(this).hide( );
            }
        });
        
        /* End Country Chart Script */

    });
    
    
    </script>
</div>