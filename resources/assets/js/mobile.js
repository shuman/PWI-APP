window.$ = window.jQuery = require('jquery')
require('jquery-colorbox');

String.prototype.stripSlashes = function(){
    return this.replace(/\\(.)/mg, "$1");
}

Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSeparator = decSeparator == undefined ? "." : decSeparator,
        thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
        sign = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};

$(function( ){

	/*
    * Start events for mobile site.
    */
    $(document).on("submit", "form[name=suggest]", function( e ) {

        e.preventDefault( );
        e.stopPropagation( );

        var $form = $(this);
        
        $.ajax({
            method: "post",
            url: "/suggest-nonprofit",
            beforeSend: function( ){
                $form.find("input[type=submit]").val( "submitting" ).attr('disabled', true);
                $form.find(".error").hide( );
            },
            data: $form.serialize( ),
            dataType: 'json',
            success: function( data ){

                $form.find("input[type=submit]").val( "submit" ).attr('disabled', false);

                if( ! data.status ){
                    
                    for( var x in data.errors ){
                        $(".error-" + x).html( data.errors[x] ).show( );
                    }
                    
                }else{

                    $form.find("input").val( );
                    $form.find("input[type=submit]").val("THANK YOU").addClass("thank-you");
                    window.setTimeout(function( ){
                        $.mobile.back( );
                    }, 3000)
                }
            }
        });
    });

    $(".mobile-menu-button").on("click", function( e ){

        e.preventDefault( );
        e.stopPropagation( );

        if( ! $(this).parent( ).hasClass( 'mobile-menu-container-active' ) ){
            $(this).parent( ).addClass('mobile-menu-container-active');

            $(".mobile-menu").animate({top: "45px"}, "fast");
        }else{
            $(this).parent( ).removeClass('mobile-menu-container-active');

            $(".mobile-menu").animate({top: "-1000px"}, "fast");
        }
    });
    
    $(".join").on("click", function( e ){

        e.stopPropagation( );
        e.preventDefault( );

        if( $("#joinSubMenu").css("display") == "none"){
            $(this).parent( ).parent( ).addClass("active-menu-item");
            $("#joinSubMenu").slideDown( );
        }else{
            $(this).parent( ).parent( ).removeClass("active-menu-item");
            $("#joinSubMenu").slideUp( );
        }
    });

    $(".account").on("click", function( e ){

        e.stopPropagation( );
        e.preventDefault( );

        if( $("#accountSubMenu").css("display") == "none"){
            $(this).parent( ).parent( ).addClass("active-menu-item-red");
            $("#accountSubMenu").slideDown( );
        }else{
            $(this).parent( ).parent( ).removeClass("active-menu-item-red");
            $("#accountSubMenu").slideUp( );
        }
    });

    $(".browseby").on("click", function( e ){

        e.stopPropagation( );
        e.preventDefault( );

        if( $("#browseSubMenu").is(":hidden") ){
            $(this).parent( ).parent( ).addClass("active-menu-item");
            $("#browseSubMenu").slideDown( );
        }else{
            $(this).parent( ).parent( ).removeClass("active-menu-item");
            $("#browseSubMenu").slideUp( );
        }
    });

    $("input[name=mobile-menu-search]").on("click", function( ){

        if( ! $(this).parent( ).hasClass('search-active') ){
            $(this).parent( ).addClass('search-active');
        }else{
            $(this).parent( ).removeClass('search-active');
        }
    });

    $(".exit-search").on("click", function( ){

        $(this).parent( ).removeClass('search-active');
    });

    var photoList = $(".photo-list").width( );
    var windowWidth = $(window).width( );

    
    if( photoList > windowWidth ){

        var margin = photoList - windowWidth;

        $(".photo-list-container").css("margin-left", "-" + margin + "px");

        $(".photo-list").css("margin-left", ( margin + 10 ) + "px" );

        $(".photo-list, .photo-list-container").width( photoList + margin );
    }

    var videoList = $(".video-list").width( );

    if( videoList > windowWidth ){

        var margin = videoList - windowWidth;

        $(".video-list-container").css("margin-left", "-" + margin + "px");

        $(".video-list").css("margin-left", ( margin + 10 ) + "px" );

        $(".video-list, .video-list-container").width( videoList + margin );
    }else{
        $(".video-list, .video-list-container").width( videoList + margin );
    }

    /* .readmore event
    * Click event for the 'Read More' link on descriptions for
    * crowdfunding, products and organiztions
    */
    $(document).on("click", ".readmore", function( e ){
        
        e.preventDefault( );
        e.stopPropagation( );
        
        $(this)
        .parent( )
        .find(".more")
        .show( );
        $(this).hide( );
        
        $(this)
        .parent( )
        .find('.readless')
        .show( );
        
    });
    
    /* .readless event
    * Click event for the 'Read Less' link on descriptions for
    * crowdfunding, products and organiztions
    */
    $(document).on("click", ".readless", function( e ){
        e.preventDefault( );
        e.stopPropagation( );
        
        $(this)
        .parent( )
        .find('.more')
        .hide( );
        
        $(this).hide( );
        
        $(this)
         .parent( )
        .find('.readmore')
        .show( );
        
    });

    $(".backToTop > a").on('click', function ( e ) {
        
        e.preventDefault( );
        e.stopPropagation( );
         $("html, body").animate({ "scrollTop" : 0 }, 500, function( ){
            console.log( 'animate callback' );
         });
    });

    var pagetype = "";

    if( $("input[name=pagetype]").length > 0 ){
        pagetype = $("input[name=pagetype]").val( );

        $(".donateToName").html( $("input[name=entityName]").val( ) );
    }

    /*
    *
    * Submit login credentials 
    *
    */
    
    $(document).on("click", ".login-popover-wrapper #actionSignIn", function(  ){
        
        var email = $(".login-popover-wrapper").find("input[name=email]").val( );
        var pword = $(".login-popover-wrapper").find("input[name=password]").val( );
        //var uType = $(".login-popover-wrapper").find("input[name=loginAs]:checked").val( );
        var uType = "user";
        
        $.ajax({
            method: "post",
            url: "/auth/login",
            beforeSend: function( ){
                
                //clear all errors 
                $(".login-popover-wrapper").find(".error").hide( );
            },
            data: {email: email, password: pword, _token: $("input[name=_token]").val( ), loginAs: uType},
            dataType: "json",
            success: function( data ){

                if( ! data.status ){
                    
                    if( typeof data.message !== "undefined" ){
                        $(".generic-error").html( data.message ).show( );
                    }else{
                        for( var x in data.errors ){
                            if( x == "email" ){
                                $(".email-error").html( data.errors[x] ).show( );
                            }
                            
                            if( x == "password" || x == "generic"){
                                $(".generic-error").html( data.errors[x] ).show( );
                            }
                        }   
                    }
                }else{
                    location.href = data.intended;
                }
            }
        });
    });

    $(".home-link").on("click", function( ){
        location.href = '/';
    });

    var touchScroll = function( event ) {
        event.preventDefault();
    };

    $(".share a").on("click", function( e ){

        e.preventDefault( );
        e.stopPropagation( );

        $(".overlay").fadeIn("slow", function( ){
            $(".share-link-container").fadeIn("slow");
        });

        $( document ).bind( 'touchmove', touchScroll );

    });

    $(".share-link-container").find(".cancel").on("click", function( ){

        $(".share-link-container").fadeOut("slow", function( ){
            $(".overlay").fadeOut("fast");
        });

        $( document ).unbind( 'touchmove');
    });

    $(".fb-share").on("click", function( ){
        FB.ui({
            method: "share",
            href: location.href
        }, function( ){});
    });

    $(".tw-share").on("click", function( ){
        window.open("https://twitter.com/intent/tweet?text=" + encodeURI( "Check out" ) + "&url=" + location.href + "&via=prjWorldImpact");
    });

    $(".p-share").on("click", function( ){
         var pinUrl = "http://pinterest.com/pin/create/link?url=" + location.href + "&media=&description=";

         window.open( pinUrl );
    });

    $(".overlay-link").on("tap", function( ){
        $(".mobile-menu-container").removeClass('mobile-menu-container-active');

        $(".mobile-menu").animate({top: "-1000px"}, "fast");

    });

    $("input[name=sameAsShipping]").on("change", function( ){

        if( $(this).is(":checked") ){

            $("input[name=billingAddress1]").val( $("input[name=shippingAddress1]").val( ) );
            $("input[name=billingAddress2]").val( $("input[name=shippingAddress2]").val( ) );
            $("input[name=billingCity]").val( $("input[name=shippingCity]").val( ) );
            $("input[name=billingZip]").val( $("input[name=shippingZip]").val( ) );

            $("select[name=billingCountry]").find("option[value=" + $("select[name=shippingCountry]").find("option:selected").val( ) + "]").prop("selected", true);

            $("select[name=billingCountry]").trigger("change");            

            setTimeout( function( ){
                $("select[name=billingState]").find("option[value=" + $("select[name=shippingState]").find("option:selected").val( ) + "]").prop("selected", true);

                $("select[name=billingState]").trigger("change"); 
            }, 2000);
        }
    });

    $("select").on("change", function( ){

        var value = $(this).find('option:selected').text( );

        $(this).parent( ).find("span").text( value );
    });

    /*
    * AJAX action to retrieve States Billing
    */

    $("select[name=billingCountry], select[name=shippingCountry]").on("change", function( ){

        var $dd = $(this);

        var dropdown = $dd.attr('name');

        var countryName = $dd.find("option:selected").text( );

        var selectedItem = $(this).find("option:selected").val( );

        var stateList = "";

        if( dropdown == "billingCountry"){
            stateList = "billingState";
        }else{
            stateList = "shippingState";
        }

        $.ajax({
            method: "get",
            url: "/getStates",
            beforeSend: function( ){

                $("select[name=" + stateList + "]").empty( );

                $("select[name=" + stateList + "]").append("<option value='0'></option>");
                
                $("select[name=" + stateList + "]").find("option:first").text( "" );
                $("select[name=" + stateList + "]").find("option:first").text( "Retrieving States for " + countryName);
            },
            data: "id=" + selectedItem,
            dataType: 'json',
            success: function( resp ){
                
                if( resp.length > 0 ){
                    $("select[name=" + stateList + "]").find("option:first").text( "Select a State");

                    for( var x in resp ){
                        $("select[name=" + stateList + "]").append("<option value='" + resp[x].state_id + "'>" + resp[x].state_name + "</option>");
                    }
                }else{
                    $("select[name=" + stateList + "]").find("option:first").text( "Select a Country to Retrieve States");
                }
            }
        });
    });

    $(document).on("pagechange", function( ){
        $(".mobile-menu").animate({top: "-1000px"}, "fast");
    });

    /* .follow click event
    *   have a user follow an org, cause, country, etc.
    */
    
    $(".follow").on("click", function( ){
        
        var $follow = $(this);
        
        var data = {
            follow_item: $(this).data("type"),
            follow_item_id: $(this).data("id")  
        };
        
        $.ajax({
            method: "post",
            url: "/follow",
            data: data,
            dataType: "json",
            success: function( data ){
                
                if( ! data.status ){
                    
                    if( data.action == "signin" ){
                        $//(".signin-action").popover('toggle');

                    }
                    
                }else{
                    $follow.find("a").html( data.action );
                }
            }
        });
    });
    
});

function adjustFooter( ){
	if( $(".home-page-bg").length > 0 ){

    	var homeBGHeight = $(".home-page-bg").height( );

    	$("footer.mobile-footer").css("top", (parseInt( $(".home-page-bg").height( ) ) + parseInt( $("nav").height( ) ) ) + "px");
    }
}

$(document).on("pagecreate", "#main", function( ){
        
        var pageHeight = "";

        if( $(".home-page-content").length > 0 ){
            pageHeight = $(this).height( ) + $("[data-role=header]").height( ) + 81;
        }else{
            pageHeight = $(this).height( ) + $("[data-role=header]").height( );    
        }
        
        $(".indexWrapper").css("height", pageHeight);
        
});

$(document).on('pagebeforehide', '#suggest', function( ){
    $(this).find(".error").css('display', 'none').val( "" );
    $(this).find("input[type=text]").val( "" );    
});
/*
$("#suggest").pagecontainer({
    beforechange: function( event, ui){
        alert('hi');
        $(this).find(".error").css('display', 'none').val( "" );
        $(this).find("input[type=text]").val( "" );    
    },
    beforetransition: function( event, ui ) { alert('before trans');}
});
*/

$(".openCountryModal").on("click", function( ){
    $.mobile.pageContainer.pagecontainer("change", "#continents");
});

