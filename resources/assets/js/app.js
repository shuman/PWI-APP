window.$ = window.jQuery = require('jquery')
require('bootstrap-sass')
require('jquery-ui')
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

Number.prototype.parseFileSize = function(  ){

	var bytes    = this;
	var kilobyte = 1024;
	var megabyte = kilobyte * 1024;
	var gigabyte = megabyte * 1024;

	if( ( bytes >= 0 ) && ( bytes < kilobyte ) ){
		return bytes + "B";
	}else if( ( bytes >= kilobyte) && ( bytes < megabyte ) ){
		return ( bytes/kilobyte ).toFixed( 2 ) + "KB";
	}else{
		return ( bytes/megabyte ).toFixed( 2 ) + "MB";
	}
};

$(function( ){
    
    var loading = false;
    var finishedMore = false;
    
    /*adjust all the modals for full page height vs. full screen height*/
    
    $(".modal").height( $(window).height( ) );
    
    $(document).on({
	    keyup: function( e ) {
		
			if( e.keyCode == 27 ){
				$(".modal").hide( );
				$(".share").popover('hide');
				$(".signin-action").popover('hide');
				$(".join-action").popover('hide');
			}    
		}
	});
	
	//Fix for double clicking popover after hide
    $('body').on('hidden.bs.popover', function (e) {
	    $(e.target).data("bs.popover").inState = { click: false, hover: false, focus: false }
	});
    
    /**
	*    
	* Actions for pull-down    
	*    
	*/
	
	$(".searchby").on("click", function( ){
		
		if( $(this).hasClass("country") ){
			
			$(".exit-pull-down").trigger("click");
			
			setTimeout( function( ){
				$(".openCountryModal").trigger("click");
			}, 1000 );
			
			
		}else if( $(this).hasClass("cause") ){
			
			$(".exit-pull-down").trigger("click");
			setTimeout( function( ){
				$(".openCauseModal").trigger("click");
			}, 1000 );
			
		}else if( $(this).hasClass('learnmore') ){
			location.href = "http://register.projectworldimpact.com/";
		}
	});
    

    $(".browse-by").find(".browseCause").on("click", function( e ){
    	
    	e.preventDefault( );
    	e.stopPropagation( );

    	$(".openCauseModal").trigger("click");
    });
    
    /*
	*
	* Action for footer PWI to go to marketing site
	*
	*/
	
	$(".pwi-footer-action").on("click", function( ){
		window.open( "http://marketing.projectworldimpact.com");
	});

	/*
	* AJAX action to retrieve States Billing
	*/

	$("select[name=billingCountry], select[name=shippingCountry], select[name=org-country]").on("change", function( ){
		
		var $dd = $(this);

		var dropdown = $dd.attr('name');

		var countryName = $dd.find("option:selected").text( );

		var selectedItem = $(this).find("option:selected").val( );

		var stateList = "";

		if( dropdown == "billingCountry"){
			stateList = "billingState";
		}else if( dropdown == "shippingCountry"){
			stateList = "shippingState";
		}else{
			stateList = "org-state";
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

	$(".modal").on("click", function( e){
		
		if( ! $(this).hasClass('overlay-content') ){
			console.log( 'effed');
			if( ! $(e.target).parents(".overlay-content").size( ) ){
				//$(".modal").hide( );
			}	
		}
	});

	/*
	*   
	* change the cause icon mouseover state   
	*    
	*/
    
    $(".overlay-cause-item").on({
	    mouseenter: function( ){
		    
		    var $icon 		= $(this).find("i");
		    var $text 		= $(this).find(".cause-link");
		    var $viewSub 	= $(this).find(".view-subcauses");
		    
		    var iconClass = $icon.attr("class");
		    
		    $icon
		    .addClass( iconClass.replace("-stroke", "-solid") )
		    .removeClass(iconClass)
		    .css('color', '#f1657f');

		    $text.css('color', '#f1657f');

		    $viewSub.show( );
		    
	    },
	    mouseleave: function( ){
		    
		    var $icon 		= $(this).find("i");
		    var $text 		= $(this).find(".cause-link");
		    var $viewSub 	= $(this).find(".view-subcauses");
		    
		    var iconClass = $icon.attr("class");
		    
		    $icon
		    .addClass( iconClass.replace("-solid", "-stroke") )
		    .removeClass(iconClass)
		    .css('color', '#33aef4');

		    $text.css('color', '#33aef4');

		    $viewSub.hide( );
	    }
    });

    $(".overlay-subcause-list").each(function( ){

    	if( $(this).find(".sub-cause-list a").length < 4 ){

    		var size = $(this).find(".sub-cause-list a").length;

    		$(this).find(".sub-cause-list a").each( function( ){

    			$(this)
    			.parent( )
    			.removeClass("col-lg-3 col-md-3 col-sm-3");

    			if( size == 1 ){
    				$(this)
    				.parent( )
    				.addClass("col-lg-12 col-md-12 col-sm-12");
    			}else if( size == 2 ){
    				$(this)
    				.parent( )
    				.addClass("col-lg-6 col-md-6 col-sm-6");
    			}else if( size == 3 ){
    				$(this)
    				.parent( )
    				.addClass("col-lg-4 col-md-4 col-sm-4");
    			}

    		});
    	}
	});

	/*
	Action for pulling up subcauses
    */

    var $currentSubCause = null;

    $(".view-subcauses").on("click", function( e ){
    	e.preventDefault( );
    	e.stopPropagation( );

    	var causeId = $(this).data('cause-id');

    	var causeName = $(this).data('cause-name');

    	var $subCause = $(".overlay-subcause-list[data-parent-id=" + causeId + "]");

    	$currentSubCause = $subCause;

    	if( $subCause.length > 0 ){

    		$(".overlay-cause-list").fadeOut("slow", function( ){
    			$subCause.find("h1").find("span").html( causeName );
    			$subCause.fadeIn("slow");
    		});
		}
	});

	$(".overlay-subcause-list").find("small").on("click", function( ){

		$currentSubCause.fadeOut("slow", function( ){
			$(".overlay-cause-list").fadeIn("slow");
			$currentSubCause = null;
		});
	});
    
    /*
    * Check to see if an input with name 'initialLoad' is present.
    * This will be used for Product, Organization, Crowdfunding 
    * home pages to load more results when scrolling through
    * the results.
    */
    
    var initialPayLoad  = 0;
    var nextPayLoad 	= 0;
    
    if( $("input[name=initialLoad]").length > 0 ){
        
        //Read inputs that are populated from controller.
        initialPayLoad  = parseInt( $("input[name=initialLoad]").val( ) );
        
        nextPayLoad     = parseInt( $("input[name=nextPayLoad]").val( ) );
        var page            = $("input[name=page]").val( );
        
        //when window scrolls and reaches designated point, call loadMore function
        $(window).on("scroll", function( ){
		
            //Get top of footer container
            var fTop 	= $("footer").position( ).top;

            var scroll 	= $(window).scrollTop( );
            var wHeight = $(window).height( );
            
            if( fTop <= (scroll + wHeight) ){

                if( ! loading && ! finishedMore){
                    loading = true;
                    loadMore( );
                }
            }
        });    
    }
    
    $(".logout").on("click", function( e ){
    	//alert('hello');
	   
	   e.stopPropagation( );
	   e.preventDefault( );
	   
	   location.href = "/auth/logout"; 
	    
    });
    
    $(".dropdown-menu li a").on("click", function( e ){
	   
	   e.stopPropagation( );
	   e.preventDefault( );
	   
	   if( ! $(this).attr("href") == "" ){
		   location.href = $(this).attr("href");    
	   		
	   }else{
		   
		   if( $(this).attr('class') == "openCountryModal" ){
			    //$("#countryModal").find(".modal-content-container").addClass( "countryModalOverride" );
		   }else{
			   //$("#causeModal").find(".exit-cause-modal").css({top: "-20px", right: "0px"});
			   //$("#causeModal").find(".cause-container").addClass("causeModalOverride");
		   }
		}
	});
    
    /*
    * click event for opening the cause modal on homepage
    */
    $(".openCauseModal").on("click", function( e ){
        e.stopPropagation( );
        e.preventDefault( );
        
        $("#causeModal").css("display", "table");
    });
    
    /*
    * click event for opening the country modal on homepage
    */
    $(".openCountryModal").on("click", function( e ){
        e.stopPropagation( );
        e.preventDefault( );
        
        $("#countryModal").css("display", "table");
    });
    
    /*
	* click event for opening the comment modal
	*/
	$(".openCommentModal").on("click", function( e ){
		e.stopPropagation( );
		e.preventDefault( );
		
		$("#postReviewModal").css("display", "table");
	});
	
	/*
	* click event for opening the comment modal
	*/
	$(".openSuggestNP").on("click", function( e ){
		e.stopPropagation( );
		e.preventDefault( );
		
		$("#suggestNonProfitModal").css("display", "table");
	});
	
	$("form[name=suggestNonProfitForm]").on("submit", function( e ){
		
		e.preventDefault( );
		e.stopPropagation( );
		
		$.ajax({
			method: "post",
			url: "/suggest-nonprofit",
			beforeSend: function( ){
				$(".sub-suggestNP").find("input[type=submit]").val( "submitting" ).attr('disabled', true);
				$("form[name=suggestNonProfitForm]").find(".error").hide( );
			},
			data: $("form[name=suggestNonProfitForm").serialize( ),
			dataType: 'json',
			success: function( data ){

				
				$(".sub-suggestNP").find("input[type=submit]").val( "submit" ).attr('disabled', false);

				if( ! data.status ){
					
					for( x in data.errors ){
						$(".error-" + x).html( data.errors[x] ).show( );
					}
					
				}else{

					$("form[name=suggestNonProfitForm").find("input").val( );
					$(".suggestThankYou").show( );
					window.setTimeout(function( ){
						$("#suggestNonProfitModal").hide( );
						$(".suggestThankYou").hide( );
					}, 3000)
				}
			}
		});
	});
	
    /*
    *
    * Events for the home page nav search
    *
    */
    
    $(".search").on("click", function( ){
       
        var searchTerm = $("input[name=search]").val( );
        
        if( searchTerm == "" ){
            
        }else{
            location.href = "/search/" + encodeURIComponent( searchTerm );    
        }
    });
    
       
    /*
    *
    * Event for sharing to social media
    *
    */
    
    $(".share").popover({
	    content: $(".social-media-share").html( ),
	    html: true,
	    placement: "bottom",
	    container: "body",
	    trigger: "click",
	    template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content social-media-share"></div></div>'
	});
	
	$(document).on("click", ".popover-content.social-media-share a", function( e ){
		
		
		e.stopPropagation( );
		e.preventDefault( );
		
		window.open( $(this).attr("href"), '', "height=640,width=480" );
	});
    
    /*
    *
    * Event for bringing up login pop-up
    *
    */
    
    $(".signin-action").popover({
        content: "<div class='login-popover-wrapper'>" + $(".login-wrapper").html( ) + "</div>",
        html: true,
        placement: 'bottom',
        container: 'body',
        trigger: 'click',
        template: '<div class="popover login-popover" ><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });
    
    $(".signin-action").on("click", function( e ){
	    e.preventDefault( );
	    e.stopImmediatePropagation( );
    });
    
    /**
	*    
	* Event for bringing up join popup    
	*    
	*/
	
	$(".join-action").popover({
		content: $(".join-wrapper").html( ),
		html: true,
		placement: 'bottom',
		container: 'body',
		trigger: 'click',
		template: '<div class="popover join-popover" ><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
	});
	
	$(document).on("click", ".join-user", function(  ){
		location.href = 'https://portal.projectworldimpact.com/register/0';
	});
	
	$(document).on("click", ".join-org", function(  ){
		location.href = 'http://join.projectworldimpact.com';
	});

    /*
    *
    * Submit login credentials 
    *
    */
    
    $(document).on("click", ".login-popover-wrapper #actionSignIn", function(  ){
        
        var email = $(".login-popover-wrapper").find("input[name=email]").val( );
        var pword = $(".login-popover-wrapper").find("input[name=password]").val( );
        var uType = $(".login-popover-wrapper").find("input[name=loginAs]:checked").val( );
        
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
						for( x in data.errors ){
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
    
    /*
    *
    * Link to User Portal
    *
    */
    
    $(".toUserPortal").on("click", function( e ){
        
        e.preventDefault( );
        e.stopPropagation( );
        
        $("form[name=passthru]").trigger("submit");
    });
    
    
    /*
	*  
	* Action for Facebook Login Button
	*   
	*/
	
	$(document).on("click", ".login-popover-wrapper .btn-facebook", function(  ){
		location.href = "/auth/social/facebook";
	});
	
	/**
	*
	* Action for twitter login button
	*
	*/
	
	$(document).on("click", ".login-popover-wrapper .btn-twitter", function(  ){
		location.href = "/auth/social/twitter";
	});
	
	/**
	*
	* Action for google login button
	*
	*/
	
	$(document).on("click", ".login-popover-wrapper .btn-google", function(  ){
		location.href = "/auth/social/google";
	});
	
    
    /*
    * Mouseover/leave events for mousing over causes
    * on the cause overlay
    */
    $(".cause-container a").on({
        mouseover: function( ){
            var cause = $(this).data("cause");
            
            $(this).parent( ).find(".center").html( cause );
        },
        mouseleave: function( ){
            $(this).parent( ).find(".center").html( "" );
        }
    });
    
    var iso = "";
    
     /*
    * Mouseover/leave events for mousing over causes
    * on the cause overlay
    */
    $(".country-overlay-item").on({
        mouseover: function( ){
            
            //grab the iso code from the data-iso-codeattribte
            iso = $(this).data('iso-code').toLowerCase( );
            var text = $(this).text( );
            
            //Add text and flag icon to the top of panels
            $(".country-overlay-name").html( text );
            $(".country-space").find(".flag-icon").addClass("flag-icon-" + iso);
            $(".country-space").css("visibility", "visible");
        },
        mouseleave: function( ){
            
            //make the text and flag dissppear
            $(".country-space").css("visibility", "hidden");
            $(".country-overlay-name").html( "" );
            $(".country-space").find(".flag-icon").removeClass("flag-icon-" + iso);
        }
    });
    
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
    
    /* .whatis event
    * Click event for the 'what is pwi' link 
    * on home page - triggers overlay
    */
    
    $(".whatis").on("click", function( e ){
        
        e.preventDefault( );
        e.stopPropagation( );

        if( $(".mobile-pull-down-menu").hasClass('down') ){
			$(".navbar-toggle").trigger("click");
		}

        $("#wrapper").animate({marginTop: "702px"}, "slow"); 
        
        $(".pull-down-about").animate({top: "0px"}, "slow");
        
    });
    
    /**
	*
	* action to close review lightbox
	*
	*/ 
	
	$(".exit").on({
		mouseenter: function( ){
			$(this).css('cursor','pointer');	
		},
		mouseleave: function( ){
			$(this).css('cursor','auto');
		},
		click: function( ){
			
			var el = $(this).data('control');
			
			$(el).hide( );
		}
	});
	
	$(".exit-review").on({
		mouseenter: function( ){
			$(this).css('cursor','pointer');	
		},
		mouseleave: function( ){
			$(this).css('cursor','auto');
		},
		click: function( ){
			$("#postReviewModal").hide( );
		}
	});
    
    
    /* .pull-down-exit event
    * close the overlay from the
    * 'what is pwi' link
    */
    
    $(".exit-pull-down").on("click", function( ){
	    
	    if( parseInt( $(window).width( ) ) <= 885 ){
		    $(".pull-down-about").animate({top: "-598px"}, "slow");
		}else{
			$(".pull-down-about").animate({top: "-662px"}, "slow");	
		}
        
        $("#wrapper").animate({marginTop: "0px"}, "slow");
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
						$(".signin-action").popover('toggle');

					}
					
				}else{
					$follow.html( data.action );
				}
			}
		});
	});
    
    
	/**
	*	
	* mobile nav toggle
	*	
	*/
	
	$(".navbar-toggle").on("click", function( ){
		
		if( $(".mobile-pull-down-menu").hasClass("down") ){
			
			if( $("nav.std-navbar").length > 0 ){
				$(".navbar-toggle")
				.css("background-color", "#f1f1f1")
				.find(".icon-bar")
				.css("background-color", "#888");					
			}else{
				$(".navbar-toggle")
				.css("background-color", "#fff")
				.find(".icon-bar")
				.css("background-color", "#888");		
			}
			
			$("#browseByList").removeClass("in");
			
			$(".mobile-pull-down-menu").find(".mobile-menu-item").slideUp("slow", function( ){
				
				$(".mobile-pull-down-menu").removeClass('down');
			});	
			/*
			$(".mobile-pull-down-menu").animate({top: "-99999999px"}, "slow", function( ){
				
				$(this).removeClass('down');
			});*/			
			
		}else{
			
			//$(".mobile-pull-down-menu").animate({top: "65px"}, "fast", function( ){
			$(".mobile-pull-down-menu").find(".mobile-menu-item").slideDown("fast", function( ){
				
				$(".navbar-toggle")
				.css("background-color", "#33aef4")
				.find(".icon-bar")
				.css("background-color", "#fff");
				
				$(".mobile-pull-down-menu").addClass('down');
			});
		}
	});
	
	
    //Suggested Search
    
    var xhr = null;
	
	var resultsJSON = null;
    
    var page = $("input[name=page]").val( );
	
	$("input[name=search]").on("keyup", function( e ){
        
        var el = $(this);
        
        var value = $(this).val( );
		
		if( value.length > 2 ){

			if( xhr ){
				if( xhr.readyState != 4 ){
					xhr.abort( );
				}
			}
			
			xhr = $.ajax({
                url: "/search/" + value,
                dataType: "json",
                success: function( data ){
                    
                    if( data.length > 0 ){
                        
                        var divList = "<ul style='position:relative;'>";
                        
                        var beginCausePattern   = /^cause/;
                        var beginCountryPattern = /^country/;
                        
                        for( var x in data ){
                            
                            if( beginCausePattern.test(data[x].recordtype) ){
                                var end = findLink( data[x].recordtype );
                                if( end == "causes" ){
	                        		divList += "<li tabindex='" + x + "'><a href='/cause/" + data[x].alias + "'>" + data[x].name.stripSlashes( ) + "</a></li>";        
                                }else{
	                            	divList += "<li tabindex='" + x + "'><a href='/cause/" + data[x].alias + "/" + end + "'>" + data[x].name.stripSlashes( ) + "</a></li>";    
                                }
                            }else if(beginCountryPattern.test( data[x].recordtype ) ){
	                            var end = findLink( data[x].recordtype );
	                            
	                            if( end == "country" ){
		                            divList += "<li tabindex='" + x + "' ><a href='/country/" + data[x].alias + "'>" + data[x].name.stripSlashes( ) + "</a></li>";
	                            }else{
		                        	divList += "<li tabindex='" + x + "' ><a href='/country/" + data[x].alias + "/" + end + "'>" + data[x].name.stripSlashes( ) + "</a></li>";    
	                            }
                                
                            }else if( data[x].recordtype == "organization" ){
                                divList += "<li tabindex='" + x + "'><a href='/organization/" + data[x].alias + "'>" + data[x].name.stripSlashes( ) + "</a></li>";
                            }else{
                                console.log( 'effed' );
                            }
                        }    
                        
                        divList += "</ul>";

                        var popoverTitle = "Suggestions based on your search for '<b >" + value + "</b>'. <span class='glyphicon glyphicon-remove clear-popover pull-right exit' aria-hidden='true'></span>";
                        
                        if( $(".selective-search").hasClass("in") ){
                            console.log( value );
                            $(".selective-search").find(".popover-content").html( divList );
                            $(".selective-search").find(".popover-title b").html( value );
                            
                        }else{
                            
                            el.
                            popover({
                                content: divList, 
                                title: popoverTitle, 
                                placement: 'bottom',
                                container: 'body',
                                html: true,
                                trigger: 'manual',
                                template: 	'<div class="popover selective-search" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}).
                            popover('show');
                        }
                        
                        $(".clear-popover").on("click",function( ){
                            $("input[name=search]").popover('destroy');
                        });
                    }else{
	                    el.popover('destroy');
                    }
                } 
            });
		}else{
            el.popover('destroy');
		}
	});
    
    //Suggested Search End.
    
    /**
	*
	* Actions for posting reviews
	*
	*/
    
    
    var dataRating = 0;
    
    $(".post-comment-button").on("click", function( e ){
	    
	    $(".post-review-errors").html("").hide( );
		var hasErrors = false;
		
	    var id = $(this).data('id');
	    var type = $(this).data('type');
	    var $postParent = $(this).parents(".post-comment" );
	    
	    var comment = $postParent.find("textarea[name=comment]").val( );
	    
	    if( comment == "" ){
		    hasErrors = true;
			$(".post-review-errors").append("Please Write a comment/review.<br />");    
	    }
	    
	    if( dataRating == 0 ){
		    hasErrors = true;
		    $(".post-review-errors").append("Please select a rating.<br />");
	    }
	    
		if( hasErrors ){
		    $(".post-review-errors").show( );
	    }else{

		    $.ajax({
			   	url: "/comment",
			   	method: "POST",
			   	data: {id: id, type: type, rating: dataRating, comment: comment},
			   	beforeSend: function( ){
				   	//$(".post-comment-button").html("posting").attr("disabled", true);
			   	},
			   	dataType: 'json',
			   	success: function( data ){
				   	$(".post-comment-button").html("post comment").attr("disabled", false);
				   	
				   	if( data.status ){
					   	
					   	$postParent.find("textarea[name=comment]").val( "" );
					   	$postParent.find(".rating").find(".star").removeClass('fill');
					   	
					   	if( type == "project" ){
						   	$(".comments").prepend("<div class='comment'><div class='name'>" + data.username + "</div><div class='review'>" + comment + "</div></div>");
					   	}else if( type == "organization"){
						   	
						   	var rating = "<div class='rating'>";
						   	
						   	for( var i = 1 ; i < 6 ; i++ ){
							   	if( i <= dataRating ){
									rating += "<span class='star fill'>";   	
							   	}else{
								   	rating += "<span class='star'>";
							   	}
							   	rating += "<i class=\"icon pwi-icon-star pwi-icon-2em\"></i>";
                                rating += "</span>";
						   	}
						   	
						   	rating += "</div>";
						   	
						   	var newReview = "<div class='review'><div class='review-top'><div class='name'>" + data.username + "</div><div class='rating'>&nbsp;" + rating + "</div><div class='content'>" + comment + "</div></div>";
						   	$(".reviews").prepend( newReview );
						   	
						   	$("#postReviewModal").hide( );
						}
						else if( type == "product" ){
							
						   var rating = "<div class='rating'>";
						   	
						   for( var i = 1 ; i < 6 ; i++ ){
							   if( i <= dataRating ){
									rating += "<span class='star fill'>";   	
							   }else{
								   	rating += "<span class='star'>";
							   }
							   rating += "<i class=\"icon pwi-icon-star pwi-icon-2em\"></i>";
                               rating += "</span>";
						   	}
						   	
						   	rating += "</div>";
						   	
						   	var newReview = "<div class='review'><div class='review-top'><div class='name'>" + data.username + "</div>" + rating + "</div><div class='content'>" + comment + "</div></div>";
						   	
						   	$(".product-reviews").find(".reviews").prepend( newReview );
						}
					}else{
					   	
				   	}
			   	}
		    });
	    }
	});
    
    /**
	*
	* Actions for selecting a rating
	*
	*/    
	
	$(".post-comment-actions").find(".rating span").on("click", function( ){
		
		dataRating = $(this).data('rating');
			
		for( var i = 5 ; i > 0 ; i-- ){
			
			if( dataRating >= i ){
				$(".post-comment-actions").find(".rating span[data-rating=" + i + "]").addClass('fill');
			}else{
				$(".post-comment-actions").find(".rating span[data-rating=" + i + "]").removeClass('fill');
			}
		}	
	});
	    
	if( $(".area-list").length > 0 ){
        
        /* Impact Areas Map */
    
        var bounds      = new google.maps.LatLngBounds( );
        var infowindow  = new google.maps.InfoWindow( );
        var markers     = new Array( );
        var infoWindows = new Array( );
        var lastInfoWindowOpened;

        var countries = new Array( );
        
        if( $(".area-list .row").length > 0 ){

	        $(".area-list > .row").each( function( ){
	
	            var obj = new Object( );
	
	            obj.name = $(this).find(".country-name").text( );
	
	            obj.lat = parseFloat( $(this).find("input[name=lat]").val( ) );
	            obj.lng = parseFloat( $(this).find("input[name=lng]").val( ) );
	
	            obj.alias = $(this).find("input[name=alias]").val( );
	
	            countries.push( obj );
	
	        });
	
	        var mapCanvas = document.getElementById('map');
	
	        var mapOptions = {
	          zoom: 3,
	          center: new google.maps.LatLng( countries[0].lat, countries[0].lng), 
	          mapTypeId: google.maps.MapTypeId.ROADMAP,
	          styles: styles,
	          animatedZoom: true,
	          disableDefaultUI: true
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

				for( var x in countries ){
		
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
		
		        for( var i in markers ){
		
		            google.maps.event.addListener(markers[i], "click", function( ){
		
		                if( typeof lastInfoWindowOpened != "undefined"){
		                    lastInfoWindowOpened.close( );
		                }
		
		                lastInfoWindowOpened = this.infowindow;
		
		                this.infowindow.open(map, this);
		            });
		
		
		            bounds.extend( markers[i].position );
		
		            map.fitBounds( bounds );
		
		            var initialZoom = 1;
		        }
		    }
	     }
	}
    
    $("input[name=sameAsShipping]").on("click", function( ){
        
        if( $(this).is(":checked") ){
            
            $("input[name=billingAddress1]").val( $("input[name=shippingAddress1]").val( ) );
            $("input[name=billingAddress2]").val( $("input[name=shippingAddress2]").val( ) );
            $("input[name=billingCity]").val( $("input[name=shippingCity]").val( ) );
            $("input[name=billingZip]").val( $("input[name=shippingZip]").val( ) );
            
            $("select[name=billingCountry]>option:eq(" + $("select[name=shippingCountry]").find("option:selected").index( ) + ")").prop('selected', 'true');

            $("select[name=billingCountry]").trigger('change');

            setTimeout( function( ){
            	$("select[name=billingState]>option:eq(" + $("select[name=shippingState]").find("option:selected").index( ) + ")").prop('selected', 'true');
            }, 3000 )
            
        }else{
            
            $("input[name=billingAddress1]").val( "" );
            $("input[name=billingAddress2]").val( "" );
            $("input[name=billingCity]").val( "" );
            $("input[name=billingZip]").val( "" );

            $("select[name=billingCountry]>option:eq(0)").prop('selected', true);

            $("select[name=billingState] option").each(function( i ){

            	if( i == 0 ){
            		$(this).text("Select a Country to Retrieve States");
            	}else{
            		$(this).remove( );
            	}

            });
        }

    });

    $("input[name=cc_number]").on("keydown", function( e ){
        	
        if( $(this).val( ).length == 20 ){
        	if( ( e.keyCode != 8  && e.keyCode != 37) ){
        		e.preventDefault( );
        	}
        }
    });

    $("input[name=ccv]").on("keydown", function( e ){
        	
        if( $(this).val( ).length == 4 ){
        	if( ( e.keyCode != 8  && e.keyCode != 37) ){
        		
        		e.preventDefault( );
        	}
        }
    });
    
    function loadMore( ){
        
        var ajaxUrl = "";
        
        switch( page ){
            case "org":
                ajaxUrl = "/organization/more";
                break;
            case "crowdfunding":
                ajaxUrl = "/crowdfunding/more";
                break;
            case "products":
                ajaxUrl = "/products/more";
                break;
        }

        $.ajax({
            method: "POST",
            url: ajaxUrl,
            data: {
              payload: initialPayLoad,
              next: nextPayLoad
            },
            dataType: "json",
            beforeSend: function( ){
                $(".loadingMore").show( );
            },
            success: function( data ){
                
                if( data.count < initialPayLoad ){
                    finishedMore = true;
                }
                
                switch( page ){
                    case "org":
                        buildOrgs( data );
                        break;
                    case "crowdfunding":
                        buildCrowdfunding( data );
                    case "products":
                        buildProducts( data );
                        break;
                }
                
                loading = false;
                $(".loadingMore").hide( );
                nextPayLoad += parseInt( initialPayLoad );
            }
        });
    }
    
    function findLink( recordtype ){
        
        var endCausePattern     = /causes$/;
        var endCountryPattern   = /countries$/;
        var endProjectPattern   = /projects$/;
        var endProductPattern   = /products$/;
        var endOrgPattern       = /organizations$/;
        
       if( endCausePattern.test(recordtype) ){
           return "causes";
       }else if( endCountryPattern.test( recordtype ) ){
           return "country";
       }else if( endProjectPattern.test( recordtype ) ){
           return "projects";
       }else if( endProductPattern.test( recordtype ) ){
           return "products";
       }else if( endOrgPattern.test( recordtype ) ){
           return "organizations";
       } 
    }
    
    function buildOrgs( data ){
        
        var orgs = new Array( );
        var orgPath = data.path;

        delete data.path;

        var html = "";

        for( var x in data ){
	        
	        html += "<div class='org-module margin-top-10'>";
            html += "   <div class='org-module-top'>";
            html += "       <div class='org-module-img-container margin-right-10 margin-bottom-5 padding-0 pull-left'>";
            html += "           <a href='/organization/" + data[x].org_alias + "'><img src='" + data[x].logoImg + "' align='left'/></a>";
            html += "           <br />";
            html += "           <div class='rating below-image'>";
            for( var i = 1 ; i < 6 ; i++ ){
                if( i <= data[x].rating ){
                    html += "       <span class='star fill' >";
                    html += "           <i data-icon='&#xe017;' class='pwi-icon-star pwi-icon-2em'></i>";
                    html += "       </span>";
                }else{
                    html += "       <span class='star' >";
                    html += "           <i data-icon='&#xe017;' class='pwi-icon-star pwi-icon-2em'></i>";
                    html += "       </span>";
                }
            }
            html += "           </div>";
            html += "       </div>";
            html += "       <div class='pull-left'>";
            html += "           <div class='org-module-name pull-left'><a href='/organization/" + data[x].org_alias + "'>" + data[x].org_name.stripSlashes( ) + "</a></div>";
            
            if( data[x].countries.length >  0 ){
	            html += "               <div class='impacts-causes'>";
	            html += "                   <span class='title'>Locations</span><br />";
	            html += "                   <span class='list'>";
	            for( var i = 0 ; i < data[x].countries.length ; i++ ){
	                if( i == 0 ){
	                    html +=                 data[x].countries[i].country_name;
	                }else{
		                if( i < 6 || i > 6){
	                    	html +=                 ", " + data[x].countries[i].country_name;
	                    }else{
		                    html += 				"<a href='' class='readmore'>...See More</a><span class='more'>," + data[x].countries[i].country_name;
	                    }
	                }
	            }
	            if( i >= 6 ){
		            html += "</span><a href='#' class='readless'>Show Less</a>";
	            }
	            html += "                   </span>";
	            html += "               </div>";
	        }
            
            if( data[x].causes.length > 0 ){
            
	            html += "               <div class='impacts-causes'>";
	            html += "                   <span class='title'>Causes</span><br />";
	            html += "                   <span class='list'>";
	            for( var i = 0 ; i < data[x].causes.length ; i++ ){
	                if( i == 0 ){
	                    html +=                 data[x].causes[i].cause_name;
	                }else{
	                    html +=                 ", " + data[x].causes[i].cause_name;
	                }
	            }
	            html += "                   </span>";
	            html += "               </div>";
            }
            html += "           </div>";
            html += "       </div>"

            html += "	<div style='clear:both;'></div>";
            html += "    <div class='org-module-bottom'>";
			html += "        <div class='org-module-desc'>"; 
			
			if( typeof data[x].org_desc != "object" ){
				html += data[x].org_desc;
			} 
			
			html += "		 </div>";
            html += "    </div>"
            html += "</div>";

        }

        $(".orgs-module").append( html );
        
    }

    function buildCrowdfunding( data ){
        
        var projects = new Array( );
        var prjPath  = data.path;

        delete data.path;
        delete data.count;
        
        var html = "";

        for( var x in data ){
            
            html += "<div class='project-module'>";
            html += "   <div class='project-module-top'>"
            html += "       <div class='project-module-img-container margin-right-10 margin-bottom-5 padding-0 pull-left'>";
            html += "           <a href='/crowdfunding/" + data[x].project_alias + "'><img src='" + data[x].icon + "' align='left'/></a>";
            html += "        </div>";
            html += "        <div class='pull-left'>";
            html += "           <div class='project-module-name pull-left'><a href='/crowdfunding/" + data[x].project_alias + "'>" + data[x].title + "</a></div>";
            html += "           <div class='project-module-org-name'>";
            html +=                 data[x].org_name.stripSlashes( );
            html += "           </div>";
            html += "           <div class='impacts-causes'>";                    
            html += "               <span class='title'>Locations</span><br />";
            html += "                <span class='list'>"
            
            for( var i = 0 ; i < data[x].countries.length ; i ++ ){
                
                if( i == 0 ){
                    html += data[x].countries[i].country_name;
                }else{
                    html += ", " + data[x].countries[i].country_name;
                }
            }
            
            html += "                </span>";            
            html += "           </div>";
            html += "           <div class='impacts-causes'>";
            html += "               <span class='title'>Causes</span><br />";
            html += "               <span class='list'>";                
            
            for( var j = 0 ; j < data[x].causes.length ; j++ ){
                
                if( j == 0 ){
                    html += data[x].causes[j].cause_name;
                }else{
                    html += ", " + data[x].causes[j].cause_name;
                }
            }
            
            html += "               </span>";
            html += "           </div>";
            html += "        </div>";
            html += "   </div>";                    
            html += "   <div style='clear:both;'></div>";               
            html += "   <div class='project-module-status'>";                    
            html += "       <div class='status-line'>";
            html += "           <div class='pull-left projectRaisedAmt'>";
            html +=                 data[x].amtRaised;
            html += "           </div>";
            html += "           <div class='pull-right projectGoal'>";                
            html += "               out of <span class='projectGoalAmt'>" + data[x].fundGoal + "</span>";
            html += "           </div>";                
            html += "       </div>";                    
            html += "       <div class='status-line'>";
            html += "           <div class='progress'>";
            html += "               <div class='progress-bar' role='progressbar' aria-valuenow='75' aria-valuemin='0' aria-valuemax='100' style='width: " + data[x].percentage + "%;'></div>";
            html += "           </div>";
            html += "       </div>";
            html += "       <div class='status-line'>";
            html += "           <div class='pull-left projectGoal'>";
            html +=                 data[x].percentage + "% complete";
            html += "           </div>";
            html += "           <div class='pull-right projectGoal'>";
            html += "               <span class='projectGoalAmt'>" + data[x].daysleft + "</span> days left";                    
            html += "           </div>";
            html += "       </div>";
            html += "   </div>";
            html += "</div>";
                
        }
        
        $(".projects").append( html );
        
    }

    function buildProducts( data ){
        
        var products = new Array( );
        var prdPath  = data.path;

        delete data.path;

        var html = "";

        for( var x in data ){
            
            
        }
    }
});


