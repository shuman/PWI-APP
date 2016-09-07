$(function( ){
    
    var lastInfoWindowOpened;
    var orgId = $("input[name=orgId]").val( );
    var userId = "";

    if( $("input[name=userId]").length > 0 ){
    	userId = $("input[name=userId]").val( );
    }
    
    setHeight( );
    
    /* Trigger Country Causes to Toggle */
    
    $(".cause-name").on("click", function( ){
        
        var cause = $(this).data("cause");
        
        $(".cause-name").removeClass("active");
        
        $(this).addClass("active");
        
        $(".cause-description").addClass("hidden");
        
        $("." + cause + "-description").removeClass("hidden");
        
    });
    
	/* End Country Causes to Toggle */
    
    /* Impact Areas Map */
    
    var bounds      = new google.maps.LatLngBounds( );
    var infowindow  = new google.maps.InfoWindow( );
    var markers     = new Array( );
    var infoWindows = new Array( );
    
    var countries = new Array( );
    
	if( $(".area-list .row").length > 0 ){
	    
	    $(".area-list .row").each( function( ){
	        
	        $obj = new Object( );
	        
	        $obj.name = $(this).find(".country-name").text( );
	        
	        $obj.lat = parseFloat( $(this).find("input[name=lat]").val( ) );
	        $obj.lng = parseFloat( $(this).find("input[name=lng]").val( ) );
	        
	        $obj.alias = $(this).find("input[name=alias]").val( );
	        
	        countries.push( $obj );
	        
	    });
	    
	    var mapCanvas = document.getElementById('map');
	    
	
	    var mapOptions = {
	      zoom: 3,
	      center: new google.maps.LatLng( countries[0].lat, countries[0].lng), 
	      mapTypeId: google.maps.MapTypeId.ROADMAP,
	      styles: styles,
	      animatedZoom: true
	    }
	    
	    var map = new google.maps.Map(mapCanvas, mapOptions);
	    
	    map.addListener("mouseover", function( ){
		        
	       map.setOptions({disableDefaultUI: false});
	    });
	
	    map.addListener("mouseout", function( ){
	
	       map.setOptions({disableDefaultUI: true	});
	    });
	    
	    if( countries.length == 1 ){
		    
		    var iWindow = new google.maps.InfoWindow({
		               content: "<b>" + countries[0].name + "</b><br /><a href='/country/" + countries[0].alias + "'>View Country</a>"});
		               
		    var marker = new google.maps.Marker({
		                position: {lat: countries[0].lat, lng: countries[0].lng},
		                map: map,
		                animation: google.maps.Animation.DROP,
		                title: countries[0].name,
		                infowindow: iWindow
		            });
		            
		    google.maps.event.addListener(marker, "click", function( ){
		        this.infowindow.open(map, this);
		    });

		    
	    }else{
		    for( x in countries ){
	        
		        var iWindow =  new google.maps.InfoWindow({
		               content: "<b>" + countries[x].name + "</b><br /><a href='/country/" + countries[x].alias + "'>View Country</a>"
		            });
		        
		        markers.push( new google.maps.Marker({
		                position: {lat: countries[x].lat, lng: countries[x].lng},
		                map: map,
		                animation: google.maps.Animation.DROP,
		                title: countries[x].name,
		                infowindow: iWindow
		            })
		        );
		    }
		    
		    for( i in markers ){
		            
		        google.maps.event.addListener(markers[i], "click", function( ){
		            
		            if( typeof lastInfoWindowOpened != "undefined"){
		                lastInfoWindowOpened.close( );
		            }
		            
		            lastInfoWindowOpened = this.infowindow;
		            
		            this.infowindow.open(map, this);
		        });
		        
		        
		        bounds.extend( markers[i].position );
		        
		        map.fitBounds( bounds );
		        
				initialZoom = 1;
		    }
	    }
	}
    /*
	*    
	* Set up $.colorbox for videos and photos    
	*    
	*/
	
	if( $(".org-photos").length > 0 ){
		$("a.org-photos").colorbox({rel: 'group0', scalePhotos: true, maxWidth: "100%"} );
	}
	
	if( $(".org-videos").length > 0 ){
		$("a.org-videos").colorbox({iframe: true, innerWidth:640, innerHeight:390});
	}
    
    /*
    *
    * Begin Checkout Process for Current Organization
    *
    */
    
    
    var $checkout   = $("#organizationCheckout");
    var $container  = $("#organizationCheckout").find(".checkout");
    var $donation   = $container.find(".donation");

    var org_alias   = "";
    
    //Action for triggering the org donation lightbox
    $(".donate-button").on({
        mouseover: function( ){
            $(this).css('cursor','pointer');
        },
        mouseleave: function( ){
            $(this).css('cursor', 'auto');    
        },
        click: function( ){
            
            org_alias = $(this).data('alias');

            $checkout.css("display","table");
        }
    });
    
    //Close out organization checkout
    $(".exit-organization-checkout").on({
        
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

            $("#organizationCheckout").hide( );

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
                data: {amount: parseFloat(amount.replace(",","")).formatMoney(2, ',', '.'), type: "organization"},
                dataType: "json",
                success: function( resp ){
                    location.href = '/organization/' + org_alias + '/donation';
                }
            });
        }
    });

    $(".upload-icon-cover").click( function( e ){
    	$("#imgCoverPic").trigger("click");
    });

    $(".upload-icon-profile").click( function( e ){
    	$("#imgProfilePic").trigger("click");
    });

    $("#imgCoverPic").on("change", function( e ){

    	var parent = $(this).data("parent");
    	var file_data = this.files[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('type', 'cover');
        form_data.append('id', orgId);
        form_data.append('userId', userId);

        var token = $(this).parent( ).find("input[name=_token]").val( );

        form_data.append('_token', token);

        $.ajax({
        	url: "/organization/uploadimage",
        	method: "POST",
        	data: form_data,
        	contentType: false,
            cache: false,
            processData: false,
            beforeSend: function( ){
            	$(".upload-info").fadeIn( );
            },
            success: function( resp ){
            	if( resp.status ){
            		$(".upload-info").fadeOut( );
            		$(".upload-success").fadeIn( );

            		$("." + parent).css("background", "url(" + resp.url + ") no-repeat center center");
            		$("." + parent).css("background-size", "cover");

            		setTimeout( function( ){
            			$(".upload-success").fadeOut( );
            		}, 3000);

            	}else{
            		$(".upload-info").fadeOut( );
            		$(".upload-error").fadeIn( );

            		setTimeout( function( ){
            			$(".upload-error").fadeOut( );
            		}, 3000);
            	}
            },
            error: function( ){
            	$(".upload-info").fadeOut( );
        		$(".upload-error").fadeIn( );

        		setTimeout( function( ){
        			$(".upload-error").fadeOut( );
        		}, 3000);
            }
		});
	});

	$("#imgProfilePic").on("change", function( e ){
    	
    	var file_data = this.files[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('type', 'logo');
        form_data.append('id', orgId);
        form_data.append('userId', userId);

        var token = $(this).parent( ).find("input[name=_token]").val( );

        form_data.append('_token', token);

        $.ajax({
        	url: "/organization/uploadimage",
        	method: "POST",
        	data: form_data,
        	contentType: false,
            cache: false,
            processData: false,
            beforeSend: function( ){
            	$(".upload-info").fadeIn( );
            },
            success: function( resp ){
            	if( resp.status ){
            		$(".upload-info").fadeOut( );
            		$(".upload-success").fadeIn( );

            		$(".logo").prop("src", resp.url );

            		setTimeout( function( ){
            			$(".upload-success").fadeOut( );
            		}, 3000);

            	}else{
            		$(".upload-info").fadeOut( );
            		$(".upload-error").fadeIn( );

            		setTimeout( function( ){
            			$(".upload-error").fadeOut( );
            		}, 3000);
            	}
            },
            error: function( ){
            	$(".upload-info").fadeOut( );
        		$(".upload-error").fadeIn( );

        		setTimeout( function( ){
        			$(".upload-error").fadeOut( );
        		}, 3000);
            }
		});
    });


});