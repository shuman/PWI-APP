$(function( ){
    
    $chartColorArray = ["#eeaef4", "#f1657f", "#f4b533", "#dA4a4a", "#e4272d", "#6e33f4", "#3375f4", "#f433dc", "#33f4a6", "#42da5e", "#ff0000", "#fff000", "#ffff00", "#ee00000", "#eeee00"];
    
    setHeight( );
    
    /* Trigger Country Causes to Toggle */
    
    $(".countryCauseName").on("click", function( ){
        
        var cause = $(this).data("cause");
        
        $(".countryCauseName").removeClass("active");
        
        $(this).addClass("active");
        
        $(".country-cause-description").addClass("hidden");
        
        $("." + cause + "-description").removeClass("hidden");
        
    });

    var chartSize = parseInt( $(".charts").width( ) * .95 );

    $(".charts").find("canvas").css({
        width: chartSize + "px",
        height: chartSize + "px"
    });

    $(window).on("resize", function( ){

        var chartSize = parseInt( $(".charts").width( ) * .95 );

        $(".charts").find("canvas").css({
            width: chartSize + "px",
            height: chartSize + "px"
        });
    });
    
    /* End Country Causes to Toggle */
    
    /* Start Country Map Script */

    if( $("input[name=latitude]").length > 0 ){
    
        var bounds = new google.maps.LatLngBounds( );
        var map;
        
        var initialZoom = 0;
        var zoomFactor = 3;
        
        var lat = $("input[name=latitude]").val( );
        var lng = $("input[name=longitude]").val( );
        
        var mapCanvas = document.getElementById('map');
        
        var mapOptions = {
            zoom: 3,
            center: new google.maps.LatLng( lat, lng),
            myTypeid: google.maps.MapTypeId.ROADMAP,
            styles: styles,
            animatedZoom: true,
            disableDefaultUI: true
        };
        
        map = new google.maps.Map(mapCanvas, mapOptions);

        var myLatLng = {lat: parseFloat( lat ), lng: parseFloat( lng )};

        var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map,
                        animation: google.maps.Animation.DROP,
                    });
    }
    
    /* End Country Map Script */
    
    /* Start Country Chart Script */
    
    $(".charts").each( function( i ){
        
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
    
    
    /*
    *
    * Begin Checkout Process for Current Country
    *
    */
    
	var $checkout    = $("#countryCheckout");
    var $container   = $checkout.find(".checkout");
    var $donation    = $container.find(".donation");

    var country_alis = "";
    
    //Action for triggering the country donation lightbox
    $(".donate-button").on({
        mouseover: function( ){
            $(this).css('cursor','pointer');
        },
        mouseleave: function( ){
            $(this).css('cursor', 'auto');    
        },
        click: function( ){

            country_alias = $(this).data('alias');
            
            $checkout.css("display","table");
        }
    });
    
    //Close out country checkout
    $(".exit-country-checkout").on({
        
        mouseover: function( ){
            $(this).css('cursor', 'pointer');
        },
        mouseleave: function( ){
            $(this).css('cursor', 'auto');
        },
        click: function( ){
        
           //clear any selected incentive
            $donation.find("input").val( "" );
            
            //clear all form fields on the checkout page
            $("form[name=donationCheckoutForm]")[0].reset( );

            $checkout.hide( );

             //$('.fund-project').prop('disabled', 'false');
        }
    });
    
    /*
    *
    * Step 1 ( Donation Amount / Login ) actions 
    *
    */
    
    $donation.find(".continue").on("click", function( ){
        
        var amount = $donation.find("input[name=donationAmt]").val( );
        
        var hasErrors = false;
        
        if( isNaN( parseInt( amount ) ) || isNaN( parseFloat( amount  ) ) ){
           hasErrors = true;
        }

        if( ! hasErrors ){
            
            $.ajax({
                url: "/storeDonation",
                method: "POST",
                data: {amount: parseFloat(amount.replace(",","")).formatMoney(2, ',', '.'), type: "country"},
                dataType: "json",
                success: function( resp ){
                    location.href = '/country/' + country_alias + '/donation';
                }
            });
        }
    });
});