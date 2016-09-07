
var donateObj = {};

$(function( ){
	

    $(document).on("pagechange", function( e, data){

        var page = $(this)[0].activeElement.id;

        if( page == "donate-page-two" || page == "donate-page-two" || page == "thank-you" ){
            if( typeof donateObj.amount !== "undefined" ){
                $("input[name=donationAmt]").val( donateObj.amount );
            }else{
                $.mobile.pageContainer.pagecontainer("change", "#donate-page-one");
            }        
        }
    });

    var id = "";
	var type = "";
	var user_id = "";
	var paypal_username = "";
    var payment_gateway = "";

	if( $("input[name=id]").length > 0 ){
		id = $("input[name=id]").val( );
	}

	if( $("input[name=pagetype]").length > 0 ){
		type = $("input[name=pagetype]").val( );
	}

	if( $("input[name=user_id]").length > 0 ){
		user_id = $("input[name=user_id]").val( );
	}

	if( $("input[name=paypalUn]").length > 0 ){
		paypal_username = $("input[name=paypalUn]").val( );
	}

    if( $("input[name=payment_gateway]").length > 0 ){
        payment_gateway = $("input[name=payment_gateway]").val( );
    }

    $("#back-to-site").on("click", function( ){
        location.href = "/";
    });

	$(document).on("pagecreate", "#donate-login", function( ){

		var hash = window.location.hash;

		$(".button-login").each( function( ){

			var href = $(this).find("a").attr("href");

			$(this).find("a").attr("href", href + "?mobilepage=donate-login");			

		});
		
		if( document.referrer != "" ){
			var urlSplit = document.referrer.split("#");

			if( urlSplit.length == 1 ){
				$.mobile.pageContainer.pagecontainer("change", "#donate-page-one");
			}
		}
	});

	$(document).on("click", ".continue", function( e ){

		var donation = $("input[name=donationAmt]").val( );
		var $errorBox = $(".donationAmountError");
		var error    = 0;

		if( isNaN( parseFloat( donation) ) ){
			
			$errorBox.html( "Please Enter a Number.").show( );
			error = 1;

			setTimeout( function( ){ $errorBox.html("").fadeOut( ); }, 3000);

			e.preventDefault( );
			e.stopPropagation( );
		}

		if( error == 0 ){
			donateObj.amount = parseFloat(donation).formatMoney(2, ',', '.');

			$(".donationAmount").html( "$" + parseFloat(donation).formatMoney(2, ',', '.') );	
		}
	});

	$("#continue-to-review").on("click", function( ){

        var expYear = $('form[name=donationCheckoutForm]').find("input[name=exp_date_year]").val( );

        if( expYear.length == 4 ){
            expYear = expYear.substring(2,4);

            $('form[name=donationCheckoutForm]').find("input[name=exp_date_year]").val( expYear );
        }
        
        var formData = $('form[name=donationCheckoutForm]').serialize( );

        var $button = $(this);
        
        $.ajax({
            url: "/validateDonation",
            method: "POST",
            data: formData,
            beforeSend: function( ){
                
                //clear any error messages
                $(".error").html( "" ).hide( );
                
                //change button to show processing
                $button.html( "Validating...");
                $button.prop("disabled", true);
            },
            success: function( resp ){

                if( resp.status ){
                    $button.html( "Processing...");
                
                    var $form = $('form[name=donationCheckoutForm]');

                    var expYear = $form.find("input[name=exp_date_year]").val( );

                    if( expYear.length == 4 ){
                        expYear = expYear.substring(2,4);
                    }

                    donateObj.first_name        = $form.find("input[name=first_name]").val( );
                    donateObj.last_name         = $form.find("input[name=last_name]").val( );
                    donateObj.email             = $form.find("input[name=email]").val( );
                    donateObj.cc_number         = $form.find("input[name=cc_number]").val( );
                    donateObj.name_on_card      = $form.find("input[name=name_on_card]").val( );
                    donateObj.exp_date          = $form.find("input[name=exp_date_month]").val( ) + "" + expYear;
                    donateObj.ccv               = $form.find("input[name=ccv]").val( );
                    donateObj.billingAddr1      = $form.find("input[name=billingAddress1]").val( );
                    donateObj.billingAddr2      = $form.find("input[name=billingAddress2]").val( );
                    donateObj.billingCity       = $form.find("input[name=billingCity]").val( );
                    donateObj.billingState      = $form.find("select[name=billingState]").find("option:selected").text( );
                    donateObj.billingStateId    = $form.find("select[name=billingState]").find("option:selected").val( );
                    donateObj.billingZip        = $form.find("input[name=billingZip]").val( );
                    donateObj.billingCountry    = $form.find("select[name=billingCountry]").find("option:selected").text( );
                    donateObj.billingCountryId  = $form.find("select[name=billingCountry]").find("option:selected").val( );

                    var cc_mask = donateObj.cc_number.replace(/\d{12}(\d{4})/, "XXXX XXXX XXXX $1");

                    $reviewPage = $("#donate-page-three");
                    $thankYou   = $("#thank-you");

                    var billingAddress = "<p>Billing Address</p>" + donateObj.billingAddr1 + "<br />";
                    if( donateObj.billingAddr2 != "" ){
                        billingAddress += donateObj.billingAddr2 + "<br />";
                    }

                    billingAddress += donateObj.billingCity + ", " + donateObj.billingState + " " + donateObj.billingZip + " <br />" + donateObj.billingCountry;

                    var creditCardInformation = "Card Number: " + cc_mask + "<br />";
                    creditCardInformation += "Name: " + donateObj.name_on_card + "<br />";
                    creditCardInformation += "Exp. Date:" + $form.find("input[name=exp_date_month]").val( ) + "/" + expYear + "<br />";
                    creditCardInformation += "CVV: " + donateObj.ccv;

                    $reviewPage.find(".billingInformationReview").html( billingAddress );
                    $thankYou.find(".billingInformationReview").html( billingAddress );

                    $reviewPage.find(".creditCardInformation").html( creditCardInformation );
                    $thankYou.find(".creditCardInformation").html( creditCardInformation );

                    $reviewPage.find(".billingTotalReview").text( "$" + donateObj.amount );
                    $thankYou.find(".billingTotalReview").text("$" + donateObj.amount );
                    
                    $button.val( "review order");
                    $button.prop("disabled", false);
                    
                    $.mobile.pageContainer.pagecontainer("change", "#donate-page-three");
                }else{
                    for( var key in resp.errors ){
                        $("." + key + "-error").html( resp.errors[key] ).show( );
                    }

                    $button.html( "review order");
                    $button.prop("disabled", false);
                }
            }
        });
    });

    $("#continue-to-paypal").on("click", function( ){

    	$donationId = "";

        var $button = $(this);
        
        var d = new Date( );
        
        var now = d.getYear( ) + "-" + d.getMonth( ) + "-" + d.getDate( ) + " " + d.getHours( ) + ":" + d.getMinutes( ) + ":" + d.getMilliseconds( );

        var donationData = {
            user_id:                user_id, 
            item_id:                id, 
            item_type:              type, 
            billing_full_name:      donateObj.first_name + " " + donateObj.last_name, 
            first_name:             donateObj.first_name, 
            last_name:              donateObj.last_name, 
            email:                  donateObj.email,
            billing_address_line1:  donateObj.billingAddr1, 
            billing_address_line2:  donateObj.billingAddr2, 
            billing_city:           donateObj.billingCity, 
            billing_state:          donateObj.billingStateId,  
            billing_country:        donateObj.billingCountryId, 
            billing_zip:            donateObj.billingZip, 
            donation_amount:        donateObj.amount,
            cc_number:              donateObj.cc_number,
            ccv:                    donateObj.ccv,
            exp_date:               donateObj.exp_date,
            payment_gateway:        payment_gateway 
        };
        
        $.ajax({
            method: "post",
            url: "/setPendingDonation",
            data: donationData,
            beforeSend: function( ){

                $button.html( "Processing...");
                $button.prop("disabled", true);
            },
            dataType: "json",
            success: function( data ){
                
                dataId = data.id;

                $button.html( "Checkout");
                $button.prop("disabled", false);
                
                if( payment_gateway == 3 ){

                     //find the paypalStdForm
                    var $checkoutForm = $("form[name=paypalStdCheckout]");
                    
                    //Input values into the form
                    $checkoutForm.find("input[name=cmd]").val("_donations");
                    $checkoutForm.find("input[name=return]").val(window.location.protocol + "//" +  window.location.host + "/thankyou");
                    $checkoutForm.find("input[name=notify_url]").val(window.location.protocol + "//" +  window.location.host + "/ipn");

                    $checkoutForm.find("input[name=amount]").val( donateObj.amount );

                    $checkoutForm.find("input[name=custom]").val( dataId );
                    
                    $checkoutForm.find("input[name=business]").val( paypal_username );
                    
                    $checkoutForm.find("input[name=first_name]").val( donateObj.first_name );
                    
                    $checkoutForm.find("input[name=last_name]").val( donateObj.last_name );

                    $checkoutForm.find("input[name=email]").val( donateObj.email );
                    
                    $checkoutForm.find("input[name=address1]").val( donateObj.billingAddr1 );
                    
                    $checkoutForm.find("input[name=address2]").val( donateObj.billingAddr2 );
                    
                    $checkoutForm.find("input[name=city]").val( donateObj.billingCity );
                    
                    $checkoutForm.find("input[name=state]").val( donateObj.billingState );
                    
                    $checkoutForm.find("input[name=zip]").val( donateObj.billingZip );
                    
                    $checkoutForm.find("input[name=country]").val( donateObj.billingCountry );
                    
                    $checkoutForm.trigger("submit");
                }else{

                    $("#thank-you").find(".transaction-id").html( data.txnId );

                     $.mobile.pageContainer.pagecontainer("change", "#thank-you");   
                }
            }
        })
    });

    //Action for Shipping Address Selection
    $("select[name=userAddresses]").on("change", function( ){
        
        var addressSelected = $(this).find(":selected").val( );
        
        if( addressSelected == 0 ){
            $("input[name=billingAddress1]").val( "" );
            $("input[name=billingAddress2]").val( "" );
            $("input[name=billingCity]").val( "" );
            $("select[name=billingState] option").each( function( i ){
                if( i > 0 ){
                    $(this).remove( );
                }
            });
            $("input[name=billingZip]").val( "" );
            $("select[name=billingCountry]>option:eq(0)").prop("selected", true);
        }else{
            
            var addressData = addressSelected.split("|");
            
            $("input[name=billingAddress1]").val( addressData[2] );
            $("input[name=billingAddress2]").val( addressData[3] );
            $("input[name=billingCity]").val( addressData[4] );
            $("input[name=billingZip]").val( addressData[6] );
            
            $("select[name=billingCountry]").find("option[value=" + addressData[7] + "]").prop("selected", true);

            $("select[name=billingCountry]").trigger("change");

            setTimeout( function( ){
                $("select[name=billingState]").find("option[value=" + addressData[5] + "]").prop("selected", true);
            }, 1000);
        }
    });
});
String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

var fundObj = {};
 
$(function( ){

    var page;

    setTimeout(function () {
        page = $(':mobile-pagecontainer').pagecontainer('getActivePage')[0].id;

        if( page == "fund-page-two" || page == "fund-page-three" || page == "thank-you" ){
            if( typeof fundObj.amount !== "undefined" ){
                $("input[name=donationAmt]").val( fundObj.amount );
            }else{
                $.mobile.pageContainer.pagecontainer("change", "#fund-page-one");
            }        
        }
    }, 1000);

    fundObj.id 			= $("div[data-role=main]").find("input[name=id]").val( );
	fundObj.projectName = $("div[data-role=main]").find("input[name=entityName]").val( );

	var userId 			= $("div[data-role=main]").find("input[name=user_id]").val( );
	var paypal_username = $("div[data-role=main]").find("input[name=paypalUn]").val( );
    var payment_gateway = $("div[data-role=main]").find("input[name=payment_gateway]").val( );

    var $incentive 		= $(".fund-incentives-list").find(".incentive");

	//Select Incentive on first page of checkout
    $incentive.on("click", function( ){

    	fundObj.incentive 		= $(this).data('incentive-id');
     	fundObj.incentiveName 	= $(this).find(".title").text( );
		fundObj.type 			= 'incentive';

     	if( $(this).find("input[name=shippingRequired]").val( ) == "Y" ){
			$("form[name=fundCheckoutForm]").find("input[name=showShipping]").val("Y");
			$("form[name=fundCheckoutForm]").find(".shipping-info").slideDown( );
			$("form[name=fundCheckoutForm]").find(".sameAsShipping-wrapper").show( );
			$("form[name=fundCheckoutForm]").find("input[name=sameAsShipping]").prop('checked', false);
		}else{
			$("form[name=fundCheckoutForm]").find("input[name=showShipping]").val("N");
			$("form[name=fundCheckoutForm]").find(".shipping-info").slideUp( );
			$("form[name=fundCheckoutForm]").find(".sameAsShipping-wrapper").hide( );
			$("form[name=fundCheckoutForm]").find("input[name=sameAsShipping]").prop('checked', false);
		}

        if( ! $(this).hasClass('selected') ){
            $(".fund-incentives-list").find(".incentive").each( function( ){

                if( $(this).hasClass('selected') ){

                	var p = $(this).find(".info>.price").html( );

                	$(this).find('.donation').find("input[name=donationAmt]").val( p.replace("$", "").trim( ) );

                    $(this).find('.donation').slideUp( );
                    $(this).removeClass('selected');
                }
            });

            if( $(".details-fund").hasClass('selected') ){

                $(".details-fund").find('.donation').slideUp( );
                $(".details-fund").find('.donation').find('input[type=text]').val("");
                $(".details-fund").removeClass('selected');
            }

            $(this).addClass('selected');
            $(this).find(".donation").slideDown( );    
        }
    });

    //Select the contribute button on the Incentive Page
    $(".details-fund").on("click", function( ){

    	fundObj.incentive 		= "";
    	fundObj.type	  		= 'fund';
    	fundObj.incentiveName 	= "";

    	$("form[name=fundCheckoutForm]").find("input[name=showShipping]").val("N");
		$("form[name=fundCheckoutForm]").find(".shipping-info").slideUp( );

        if( ! $(this).hasClass('selected') ){
            $(".fund-incentives-list").find(".incentive").each( function( ){

                if( $(this).hasClass('selected') ){

                	var p = $(this).find(".info>.price").html( );

                	$(this).find('.donation').find("input[name=donationAmt]").val( p.replace("$", "").trim( ) );

                    $(this).find('.donation').slideUp( );
                    $(this).removeClass('selected');
                }
            });

            $(this).addClass('selected');
            $(this).find(".donation").slideDown( );
        }
    });

    $(document).on("click", "#back-to-site", function( e ){

        var alias = $(this).data('alias');

        location.href = '/crowdfunding/' + alias;

    });

    $(document).on("click", ".continue-button", function( e ){
		
		var donation 	= $(this).parent( ).parent( ).find("input[name=fundAmt]").val( );
		var $errorBox 	= $(".donationAmountError");
		var error    	= 0;
		var $this		= $(this);

		if( isNaN( parseFloat( donation) ) ){
			
			$errorBox.html( "Please Enter a Number.").show( );
			error = 1;

			setTimeout( function( ){ $errorBox.html("").fadeOut( ); }, 3000);

			e.preventDefault( );
			e.stopPropagation( );
		}

		if( error == 0 ){
			fundObj.amount = parseFloat(donation).formatMoney(2, ',', '.');

			$(".donationAmount").html( "$" + parseFloat(donation).formatMoney(2, ',', '.') );	

			var $pageTwo = $("#fund-page-two")
							.find("[data-role=main]");

			$pageTwo.find(".fund-type").html( fundObj.type.capitalizeFirstLetter( ) );
			$pageTwo.find(".donation-amount").html( "$" + fundObj.amount );

			if( fundObj.type == "incentive" ){

				$pageTwo.find(".sameAsShipping").hide( );

				if( $this.parent( ).parent( ).find("input[name=shippingRequired]").val( ) == "Y"){
					$pageTwo.find("input[name=showShipping]").val("Y");
					$pageTwo.find(".shipping-info").removeClass('hidden');

					$pageTwo.find(".sameAsShipping").show( );
				}
				
				$pageTwo.find(".incentive-data").css("display","table");
				$pageTwo.find(".incentive-name").html( fundObj.incentiveName );
				$pageTwo.find(".donation-edit").hide( );
				$pageTwo
				.find(".incentive-desc")
				.html( $("#fund-page-one")
						.find("[data-incentive-id=" + fundObj.incentive + "]")
						.find(".description")
						.html( ) 
				);
			}else{
				$pageTwo.find("input[name=showShipping]").val("N");
				$pageTwo.find(".shipping-info").addClass('hidden');
				$pageTwo.find(".donation-edit").show( );
				$pageTwo.find(".sameAsShipping").hide( );

				$pageTwo.find(".incentive-name").html( "" );
				$pageTwo
				.find(".incentive-desc")
				.html( "" );
			}
		}
	});

    //Action for checking out for Step 2 ( Details Page );
    $("#continue-to-review").on("click", function( ){
        
        var $button = $(this);

        var expYear = $('form[name=fundCheckoutForm]').find("input[name=exp_date_year]").val( );

        if( expYear.length == 4 ){
            expYear = expYear.substring(2,4);

            $('form[name=fundCheckoutForm]').find("input[name=exp_date_year]").val( expYear );
        }

        var formData = $('form[name=fundCheckoutForm]').serialize( );
        
        $.ajax({
            url: "/crowdfunding/validateFunding",
            method: "POST",
            data: formData,
            beforeSend: function( ){
                
                //clear any error messages
                $(".error").html( "" ).hide( );
                
                //change button to show processing
                $button.html( "Validating...");
                $button.prop("disabled", true);
            },
            success: function( ){

                $button.html( "review order" );
                $button.prop("disabled", false);
                
                var $form = $('form[name=fundCheckoutForm]');

                var expYear = $form.find("input[name=exp_date_year]").val( );

                if( expYear.length == 4 ){
                    expYear = expYear.substring(2,4);
                }

                fundObj.first_name 		= $form.find("input[name=first_name]").val( );
                fundObj.last_name  		= $form.find("input[name=last_name]").val( );
                fundObj.email			= $form.find("input[name=email]").val( );
                fundObj.cc_number       = $form.find("input[name=cc_number]").val( );
                fundObj.name_on_card    = $form.find("input[name=name_on_card]").val( );
                fundObj.exp_date        = $form.find("input[name=exp_date_month]").val( ) + "" + expYear;
                fundObj.exp_date_m      = $form.find("input[name=exp_date_month]").val( ); 
                fundObj.exp_date_y      = expYear;
                fundObj.ccv             = $form.find("input[name=ccv]").val( );

                var cc_mask = fundObj.cc_number.replace(/\d{12}(\d{4})/, "XXXX XXXX XXXX $1");

                fundObj.saveShippingAddress = false;

                if( $form.find("input[name=showShipping]").val( ) == "Y" ){
                	fundObj.shippingAddr1 		= $form.find("input[name=shippingAddress1]").val( );
                	fundObj.shippingAddr2 		= $form.find("input[name=shippingAddress2]").val( );
                	fundObj.shippingCity 		= $form.find("input[name=shippingCity]").val( );
                	fundObj.shippingState 		= $form.find("select[name=shippingState]").find("option:selected").text( );
                	fundObj.shippingStateId 	= $form.find("select[name=shippingState]").find("option:selected").val( );
                	fundObj.shippingZip 		= $form.find("input[name=shippingZip]").val( );
                	fundObj.shippingCountry 	= $form.find("select[name=shippingCountry]").find("option:selected").text( );
                	fundObj.shippingCountryId 	= $form.find("select[name=shippingCountry]").find("option:selected").val( );

                	if( $form.find("input[name=saveShippingAddress]").is(":checked") ){
                		fundObj.saveShippingAddress = true;
                	}
                }
				
                fundObj.billingAddr1 			= $form.find("input[name=billingAddress1]").val( );
                fundObj.billingAddr2 			= $form.find("input[name=billingAddress2]").val( );
                fundObj.billingCity 			= $form.find("input[name=billingCity]").val( );
                fundObj.billingState 			= $form.find("select[name=billingState]").find("option:selected").text( );
                fundObj.billingStateId			= $form.find("select[name=billingState]").find("option:selected").val( );
                fundObj.billingZip 				= $form.find("input[name=billingZip]").val( );
                fundObj.billingCountry 			= $form.find("select[name=billingCountry]").find("option:selected").text( );
                fundObj.billingCountryId 		= $form.find("select[name=billingCountry]").find("option:selected").val( );

                fundObj.saveBillingAddress = false;

                if( $form.find("input[name=saveBillingAddress]").is(":checked") ){
            		fundObj.saveBillingAddress = true;
            	}
                
                var $pageThree = $("#fund-page-three");
                var $thankYou  = $("#thank-you");
                    
                if( fundObj.type == "incentive" ){

                	var $chosenIncentive = 
	                $(".fund-incentives-list").find("[data-incentive-id=" + fundObj.incentive + "]");

					$pageThree.find(".order-review-incentive").find(".header").prepend( "Incentive" );
                    $thankYou.find(".order-review-incentive").find(".header").prepend( "Incentive" );
                    
                    $pageThree.find(".incentive-title").html( $chosenIncentive.find(".title").text( ) ).show( ); 
                    $thankYou.find(".incentive-title").html( $chosenIncentive.find(".title").text( ) ).show( ); 
                    
                    $pageThree.find(".incentive-description").html( $chosenIncentive.find(".description").text( ) ).show( );
                    $thankYou.find(".incentive-description").html( $chosenIncentive.find(".description").text( ) ).show( );
                    
                }else{
                    $pageThree.find(".order-review-incentive").find(".header").prepend( "Fund Amount" );
                    $thankYou.find(".order-review-incentive").find(".header").prepend( "Fund Amount" );

                    $pageThree.find(".incentive-title").hide( );
                    $thankYou.find(".incentive-title").hide( );

                    $pageThree.find(".incentive-description").hide( );
                    $thankYou.find(".incentive-description").hide( );	
                }
                
                $pageThree.find(".donation-amount").html( "$" + fundObj.amount );
                $thankYou.find(".donation-amount").html( "$" + fundObj.amount );

                if( $form.find("input[name=showShipping]").val( ) == "Y" ){

                    var shippingAddress = fundObj.shippingAddr1;

                    if( fundObj.shippingAddr2 != "" ){
                        shippingAddress += fundObj.shippingAddr2;
                    }

                    shippingAddress += fundObj.shippingCity + ", " + fundObj.shippingState + " " + fundObj.shippingZip;
                    shippingAddress += "<br />" + fundObj.shippingCountry;

                    $pageThree.find(".shipping-address").html( shippingAddress );
                    $thankYou.find(".shippingInformationReview").html( shippingAddress );

                	$pageThree.find(".order-review-shipping-address").show( );
                    $thankYou.find(".shipping-container").show( );
                    
	            }else{
					$(".order-review-shipping-address").hide( );
                    $thankYou.find(".shipping-container").hide( );
				}

                var creditCardInformation = "Card Number: " + cc_mask + "<br />";
                    creditCardInformation += "Name: " + fundObj.name_on_card + "<br />";
                    creditCardInformation += "Exp. Date:" + $form.find("input[name=exp_date_month]").val( ) + "/" + expYear + "<br />";
                    creditCardInformation += "CVV: " + fundObj.ccv;

				$pageThree.find(".credit-card-information").html( creditCardInformation );
                $thankYou.find(".creditCardInformation").html( creditCardInformation );

                var billingAddress = fundObj.billingAddr1 + "<br />";
                if( fundObj.billingAddr2 != "" ){
                    billingAddress += fundObj.billingAddr2 + "<br />";
                }

                billingAddress += fundObj.billingCity + ", " + fundObj.billingState + " " + fundObj.billingZip + " <br />" + fundObj.billingCountry;
                
                $pageThree.find(".billing-address").html( billingAddress );
                $thankYou.find(".billingInformationReview").html( billingAddress );
                
                $button.val( "review order");
                $button.prop("disabled", false);

                $.mobile.pageContainer.pagecontainer("change", "#fund-page-three");
            },
            statusCode: {
                422: function( jqXHR, textStatus, errorThrown ){
                    
                    $button.html( "review order");
                    $button.prop("disabled", false);
                    
                    var errors = JSON.parse( jqXHR.responseText );
                    
                    for( var key in errors ){
                        $("." + key + "-error").html( errors[key] ).removeClass( 'hidden' ).show( );
                    }

                    $('html,body').scrollTop(0);
                }
            }
        });
	});

	$(".checkout-button").on("click", function( ){
        
        $donationId = "";

        var $button = $(this);
        
        var project_data = {
            "user_id" :                 userId,
            "project_id" :              fundObj.id,
            "incentive_id" :            fundObj.incentive,
            "project_title" :           fundObj.projectName,
            "incentive_title" :         fundObj.incentiveName,
            "donation_amount" :         fundObj.amount,
            "cc_number":                fundObj.cc_number,
            "ccv":                      fundObj.ccv,
            "exp_date":                 fundObj.exp_date,
            "first_name":               fundObj.first_name,
            "last_name":                fundObj.last_name,
            "email":                    fundObj.email, 
            "billing_full_name":        fundObj.first_name + " " + fundObj.last_name,
            "billing_first_name":       fundObj.first_name,
            "billing_last_name" :       fundObj.last_name,
            "billing_email":            fundObj.email,
            "billing_address_line1":    fundObj.billingAddr1,
            "billing_address_line2":    fundObj.billingAddr2,
            "billing_city":             fundObj.billingCity,
            "billing_state":            fundObj.billingStateId,
            "billing_zip":              fundObj.billingZip,
            "billing_country":          fundObj.billingCountryId,
            "shipping_full_name":       fundObj.first_name + " " + fundObj.last_name,
            "shipping_email":           fundObj.email,
            "shipping_address_line1":   fundObj.shippingAddr1,
            "shipping_address_line2":   fundObj.shippingAddr2,
            "shipping_city":            fundObj.shippingCity,
            "shipping_state":           fundObj.shippingStateId,
            "shipping_zip":             fundObj.shippingZip,
            "shipping_country":         fundObj.shippingCountryId,
            "saveShippingAddress": 		fundObj.saveShippingAddress,
            "saveBillingAddress": 		fundObj.saveBillingAddress,
            "hasShippingData":          fundObj.hasShippingData,
            "payment_gateway":          payment_gateway
        };

        $.ajax({
            method: "post",
            url: "/crowdfunding/setPendingDonation",
            data: project_data,
            beforeSend: function( ){
                $button.html( "Processing...");
                $button.prop("disabled", true);
            },
            dataType: "json",
            success: function( data ){

                $button.html( "Place Order");
                $button.prop("disabled", false);
                
                dataId = data.id;
                
                if( payment_gateway == 3 ){

                     //find the paypalStdForm
                    var $checkoutForm = $("form[name=paypalStdCheckout]");
                    
                    //Input values into the form
                    $("input[name=cmd]").val("_donations");
                    $("input[name=return]").val(window.location.protocol + "//" +  window.location.host + "/thankyou");
                    $("input[name=notify_url]").val(window.location.protocol + "//" +  window.location.host + "/crowdfunding/ipn");

                    $("input[name=amount]").val( fundObj.amount );

                    $("input[name=custom]").val( dataId + "|" + fundObj.incentive );
                    
                    $("input[name=business]").val( paypal_username );
                    
                    $("input[name=first_name]").val( fundObj.first_name );
                    
                    $("input[name=last_name]").val( fundObj.last_name );

                    $("input[name=email]").val( fundObj.email );
                    
                    $("input[name=address1]").val( fundObj.billingAddr1 );
                    
                    $("input[name=address2]").val( fundObj.billingAddr2 );
                    
                    $("input[name=city]").val( fundObj.billingCity );
                    
                    $("input[name=state]").val( fundObj.billingState );
                    
                    $("input[name=zip]").val( fundObj.billingZip );
                    
                    $("input[name=country]").val( fundObj.billingCountry );
                    
                    $checkoutForm.trigger("submit");
                }else{
                    $("#thank-you").find(".transaction-id").html( data.txnId );

                     $.mobile.pageContainer.pagecontainer("change", "#thank-you"); 
                }
            }
        });
    });
});
$(function( ){

	/**
	*
	* Checkout process for products
	*
	*/

	setTimeout(function () {
        page = $(':mobile-pagecontainer').pagecontainer('getActivePage')[0].id;

        if( page == "purchase-page-one" || page == "purchase-page-two" || page == "thank-you" ){
            if( typeof data.price === "undefined" ){
                location.href = "/product/" + $("input[name=alias]").val( );
            }        
        }
    }, 1000);
	
	var paypal_username = $("input[name=paypalUn]").val( );
    var user_id         = $("form[name=purchaseCheckoutForm]").find("input[name=user_id]").val( );

    //Get Project Data
    
    var product_name 	= $("input[name=product_name]").val( );
    var org_name 		= $("input[name=org_name]").val( );
    var product_id 		= $(".add-to-cart").data('id');
    var payment_gateway = 2;
    
    var data 			= {user_id: user_id, id: product_id, shipping: parseFloat( $("input[name=shipping]").val( ) ), saveShipping: false, saveBilling: false, payment_gateway: payment_gateway};

    $(document).on("click", "#back-to-site", function( e ){

        var alias = $(this).data('alias');

        location.href = '/';

    });
    
    /**
	*
	* Initial step to open up the lightbox to continue with the product checkout
	*
	*/
    
    $(".add-to-cart").on("click", function( ){
	    
        //$(this).prop('disabled', 'true');
        
		var modifiers = [];
        var errors    = [];
        var product_alias = $(this).data('alias');
        
        $(".error").hide( );

		$(".product-modifiers select").each(function( i ){
	        
	        var name 		= $(this).find("option:first").text( );
			
			var value 		= $(this).find(":selected").val( );
			var valueText 	= $(this).find(":selected").text( );

			var modId 		= $(this).data('mod-id');

			if( value == 0 ){
				errors.push($(this).attr("name") + "|" + "Please select a " + name);
			}else{
				modifiers.push("<div class='modifier'><b>" + name.replace("Select ", "") + "</b>: " + valueText + "</div>");
				$("#purchase-page-one, #purchase-page-two, #thank-you")
				.find(".name[data-id=" + modId + "]")
				.html( name.replace("Select","") );

				$("#purchase-page-one, #purchase-page-two, #thank-you")
				.find(".value[data-id=" + modId + "]")
				.html( valueText );
			}
		});
		
		if( $("select[name=quantity]").find(":selected").val( ) == "0" ){
			errors.push("quantity|Please Input a Quantity");
		}
		
		if( errors.length == 0 ){
			
			data.price 	 = parseFloat( $(".price").data('price') ).formatMoney(2, ',', '.');
			
			data.quantity = parseInt( $("select[name=quantity]").find(":selected").val( ) );

			$("#purchase-page-one, #purchase-page-two, #thank-you")
			.find(".quantity")
			.html( data.quantity );

			$("#purchase-page-one, #purchase-page-two, #thank-you")
			.find(".price")
			.html( "$" + data.price );

			$.mobile.pageContainer.pagecontainer("change", "#purchase-page-one");
			
		}else{
			
			for(var x in errors){
				
				var error = errors[x].split("|");
				
				$("." + error[0] + "-error").html(error[1]).show( );
			}
		}
    });
    
    /*
	*
	* Create event for getting quantity amount for selected modifiers
	*
	*/
	
	var modifierOptions = [];
	var combo = "";
	
	$(".product-modifiers select").on("change", function( i ){
		
		var allSelected = true;
		
		$(".product-modifiers select").each( function( i ){
			
			if( parseInt( $(this).find(":selected").val( ) ) == 0 ){
				allSelected = false;
			}
		});
		
		if( allSelected ){
			
			$(".product-modifiers select").each( function( i ){
				modifierOptions.push( parseInt( $(this).find(":selected").val( ) ) );	
			});
			
			modifierOptions = modifierOptions.reverse( );
				
			combo = modifierOptions.join( );
			
			data.modifierId = combo;
			
			$.ajax({
				method: "get",
				url: "/products/getQuantity",
				data: "options=" + combo + "&id=" + product_id,
				dataType: "json",
				beforeSend: function( ){

					$("select[name=quantity] option").each( function( i ){
						if( i > 0 ){
							$(this).remove( );
						}
					});

					$("select[name=quantity]").find("option:first").html("Getting Quantity ...");
				},
				success: function( resp ){
					$("select[name=quantity]").find("option:first").html("Select Quantity");
					modifierOptions = [];
					
					if( parseInt( resp.count ) > 0 ){
						
						for( var i = 1 ; i <= resp.count ; i++ ){
							$("select[name=quantity]").append("<option value='" + i + "'>" + i + "</option>");
							//$details.find("select[name=product-quantity]").append("<option value='" + i + "'>" + i + "</option>");
						}
						
						//data.modifierId = resp.modifier_id;
						
						$(".price").data("price", resp.price);
						$(".price").html("$" + parseFloat( resp.price ) + ".00");
					}
				}
			});
		}else{
			
			if( $("select[name=quantity]").length > 1 ){
				
				$("select[name=quantity] option").each( function( i ){
					
					if( i > 0 ){
						remove( $(this) );
					}
				});	
			}
		}
	});
    
    /**
	*
	* Step 1 Continue checkout     
	*    
	*/
    
    $("#purchase-page-one").find('.continue-button').on("click", function( ){

    	var expYear = $('form[name=purchaseCheckoutForm]').find("input[name=exp_date_year]").val( );

        if( expYear.length == 4 ){
            expYear = expYear.substring(2,4);

            $('form[name=purchaseCheckoutForm]').find("input[name=exp_date_year]").val( expYear );
        }
	    
	    var formData = $('form[name=purchaseCheckoutForm]').serialize( );

	    console.log( formData );

	    var $button = $(this);
		
		$.ajax({
            url: "/product/validateCheckout",
            method: "POST",
            data: formData,
            beforeSend: function( ){
                
                //clear any error messages
                $(".error").html( "" ).addClass('hidden');
                
                //change button to show processing
				$button.html( "Validating...");
		        $button.prop("disabled", true);
            },
            success: function( resp ){
	            
	            $button.html( "review order");
		        $button.prop("disabled", false);
		        
		        if( resp.status ){

		        	var $form = $('form[name=purchaseCheckoutForm]');

		        	var expYear = $form.find("input[name=exp_date_year]").val( );

	                if( expYear.length == 4 ){
	                    expYear = expYear.substring(2,4);
	                }
			        
			        data.first_name				= $form.find("input[name='first_name']").val( );
			        data.last_name				= $form.find("input[name='last_name']").val( );
			        data.email					= $form.find("input[name='email']").val( );
			        data.shipping_address_line1 = $form.find("input[name='shippingAddress1']").val( );
			        data.shipping_address_line2 = $form.find("input[name='shippingAddress2']").val( );
			        data.shipping_city  		= $form.find("input[name='shippingCity']").val( );
			        data.shipping_state			= $form.find("select[name='shippingState']").find("option:selected").text( );
			        data.shippingstateId		= $form.find("select[name='shippingState']").find("option:selected").val( );
			        data.shipping_zip			= $form.find("input[name='shippingZip']").val( );
			        data.shipping_country		= $form.find("select[name='shippingCountry']").find("option:selected").text( );
			        data.shippingcountryId		= $form.find("select[name='shippingCountry']").find("option:selected").val( );
			        data.billing_address_line1	= $form.find("input[name='billingAddress1']").val( );
			        data.billing_address_line2  = $form.find("input[name='billingAddress2']").val( );
			        data.billing_city			= $form.find("input[name='billingCity']").val( );
			        data.billing_state 			= $form.find("select[name=billingState]").find("option:selected").text( );
			        data.billingstateId			= $form.find("select[name=billingState]").find("option:selected").val( );
			        data.billing_zip			= $form.find("input[name='billingZip']").val( );
			        data.billing_country		= $form.find("select[name=billingCountry]").find("option:selected").text( );
			        data.billingcountryId		= $form.find("select[name=billingCountry]").find("option:selected").val( );
			        data.cc_number				= $form.find("input[name=cc_number]").val( );
                	data.name_on_card  			= $form.find("input[name=name_on_card]").val( );
                	data.exp_date				= $form.find("input[name=exp_date_month]").val( ) + "" + expYear;
                	data.ccv 					= $form.find("input[name=ccv]").val( );

                	var cc_mask = data.cc_number.replace(/\d{12}(\d{4})/, "XXXX XXXX XXXX $1");
				        
			        var shipping;

					var $review 	= $("#purchase-page-two");
					var $thankYou 	= $("#thank-you");

					data.shipping = parseFloat( data.shipping ).formatMoney(2, ',', '.');
					
					if( parseInt( data.shipping) == 0 ){
						shipping = 0.00;
					}else{
						shipping = parseFloat( data.shipping );
					}
					
					var subTotal = parseFloat( data.price ) * parseInt( data.quantity );
					
					var tax = (subTotal/100) * parseFloat( resp.taxCode.tax_percentage );
					
					data.tax = parseFloat(tax).formatMoney(2, ',', '.');
					
					var total = parseFloat(subTotal) + parseFloat( tax ) + parseFloat( data.shipping);

					total = total.formatMoney(2, ',', '.');

					var shippingAddress = data.shipping_address_line1 + "<br />";

					if( data.shipping_address_line2 != "" ){
						shippingAddress += data.shipping_address_line2 + "<br />";
					}

					shippingAddress += data.shipping_city + ", " + data.shipping_state + " " + data.shipping_zip;
					shippingAddress += "<br />" + data.shipping_country;
					
					$review.find(".shipping-address").html( shippingAddress );
					$thankYou.find(".shippingInformationReview").html( shippingAddress );

					var billingAddress = data.billing_address_line1 + "<br />";

					if( data.billing_address_line2 != "" ){
						billingAddress += data.billing_address_line2 + "<br />";
					}

					billingAddress += data.billing_city + ", " + data.billing_state + " " + data.billing_zip;
					billingAddress += "<br />" + data.billing_country;
	
	                $review.find(".billing-address").html( billingAddress );
	                $thankYou.find(".billingInformationReview").html( billingAddress );

	                var creditCardInformation = "Card Number: " + cc_mask + "<br />";
                    creditCardInformation += "Name: " + data.name_on_card + "<br />";
                    creditCardInformation += "Exp. Date:" + $form.find("input[name=exp_date_month]").val( ) + "/" + expYear + "<br />";
                    creditCardInformation += "CVV: " + data.ccv;

					$review.find(".credit-card-information").html( creditCardInformation );
                	$thankYou.find(".creditCardInformation").html( creditCardInformation );
	                
	                $review.find(".item-price").text( "$" + parseFloat(data.price * data.quantity).formatMoney(2, ',', '.') );
	                $thankYou.find(".item-price").text( "$" + parseFloat(data.price * data.quantity).formatMoney(2, ',', '.') );
	                
	                $review.find(".item-tax").text( "$" + parseFloat( tax ).formatMoney(2, ',', '.') );
	                $thankYou.find(".item-tax").text( "$" + parseFloat( tax ).formatMoney(2, ',', '.') );
	                
	                $review.find(".item-shipping").text( "$" + parseFloat(shipping).formatMoney(2, ',', '.') )
	                $thankYou.find(".item-shipping").text( "$" + parseFloat(shipping).formatMoney(2, ',', '.') )
	                
	                $review.find(".item-total").text("$" + parseFloat( total ).formatMoney(2, ',', '.') );
	                $thankYou.find(".item-total").text("$" + parseFloat( total ).formatMoney(2, ',', '.') );
	                
	                $review.find(".product-amount span").html( "$" + parseFloat( data.price ).formatMoney(2, ',', '.') );
	                $thankYou.find(".product-amount span").html( "$" + parseFloat( data.price ).formatMoney(2, ',', '.') );
	                
	                $review.find(".quantity").html( data.quantity );
	                $thankYou.find(".quantity").html( data.quantity );
	                
	                $review.find(".total span").html( parseFloat( subTotal ).formatMoney(2, ',', '.') );
	                $thankYou.find(".total span").html( parseFloat( subTotal ).formatMoney(2, ',', '.') );

	                $review.find(".product-total-amount").html( "$" + total );
	                $thankYou.find(".product-total-amount").html( "$" + total );

	                $.mobile.pageContainer.pagecontainer("change", "#purchase-page-two");
	                
	            }else{
			        
			        var errors = resp.errors;
                    
                    for( var key in errors ){
                        $("." + key + "-error").html( errors[key] ).removeClass( 'hidden' ).show( );
                    }

                    $button.html( "review order");
		        	$button.prop("disabled", false);
				}
            }
        });
	});
    
    /*
    *
    * Actions for step 3 ( Order Review )
    *
    */
    
    $("#purchase-page-two").find(".checkout-button").on("click", function( ){
    
    	var order_id = 0;

    	data.saveBilling 	= false;
    	data.saveShipping 	= false;

    	var $button = $(this);

    	if( $('form[name=purchaseCheckoutForm]').find("input[name=saveShippingAddress]").is(":checked") ){
    		data.saveShipping = true;
    	}

    	if( $('form[name=purchaseCheckoutForm]').find("input[name=saveBillingAddress]").is(":checked") ){
    		data.saveBilling = true;
    	}

    	$.ajax({
	    	method: "post",
	    	url: "/products/setPendingTransaction",
	    	data: data,
	    	dataType: "json",
	    	success: function( resp ){
		    	
		    	//order_id = resp.order_id;
		    	
		    	$("#thank-you").find(".transaction-id").html( resp.txnId );

                $.mobile.pageContainer.pagecontainer("change", "#thank-you"); 
		    }
    	});
    });
});
$(function( ){
	
	var donationObj 	= {};
	var userId 			= $("input[name=user_id]").val( );
	var paypal_username = $("input[name=paypal_un]").val( );
	var payment_gateway = $("input[name=payment_gateway]").val( );

	$("input[name=user_id]").remove( );
	$("input[name=paypal_un]").remove( );
	$("input[name=payment_gateway]").remove( );

	var id 		= $("input[name=donate_id]").val( );
	var type 	= $("input[name=type]").val( );

	if( $("input[name=donationAmt]").length > 0 && $("input[name=donationAmt]").val( ) != "" ){
		donationObj.amount = parseFloat( $("input[name=donationAmt]").val( ) ).formatMoney(2, ',', '.');
	}else{
		donationObj.amount = 0.00
	}

	$(".chg-amount").on("click", function( ){

		var $parent = $(this).parent( ).parent( );

		$parent.find("span").hide( );

		$parent.find("input[type=text]").removeClass('hidden');

		$(this)
		.removeClass('chg-amount')
		.addClass('save-amount')
		.css('width', '45%')
		.html('Save');

		$(this).parent( ).find(".cancel-amount").removeClass('hidden');
	});

	$(document).on("click", ".save-amount", function( ){

		var $this = $(this);

		var donation = $("input[name=donation]").val( );

		var $parent = $(this).parent( ).parent( );

		if( isNaN( parseFloat( donation) ) ){

		}else{
			donationObj.amount = parseFloat( donation ).formatMoney(2, ',','.');

			$parent.find("span").html( "$" + donationObj.amount );

			$parent.find("input[type=text]").val("").addClass('hidden');

			$(this)
			.removeClass('save-amount')
			.addClass('chg-amount')
			.val('Change Amount');
		}
	});

	$(".cancel-amount").on("click", function( ){

		var $parent = $(this).parent( ).parent( );

		//reset donation text box, if it has been filled.
		$("input[name=donation]").val("").addClass('hidden');

		$parent.find(".donate-button")
		.removeClass('save-amount')
		.addClass('chg-amount')
		.css('width', '100%')
		.html('Change Amount');

		$parent.find("span").show( );

		$(this).addClass('hidden');
	});

	$(".continueCheckout").on("click", function( ){
        
        var formData = $('form[name=donationCheckoutForm]').serialize( );
        var type = $("input[name=saveAddress]").data('type');
        
        var $button = $(this);
        
        $.ajax({
            url: "/validateDonation",
            method: "POST",
            data: formData + "&type=" + type + "&payment_gateway=" + payment_gateway,
            beforeSend: function( ){
                
                //clear any error messages
                $(".error").html( "" ).hide( );
                
                //change button to show processing
                $button.html( "Validating...");
                $button.prop("disabled", true);
            },
            success: function( resp ){
                
            	if( resp.status ){

            		var $form = $('form[name=donationCheckoutForm]');

	                donationObj.first_name 		= $form.find("input[name=first_name]").val( );
	                donationObj.last_name 		= $form.find("input[name=last_name]").val( );
	                donationObj.email			= $form.find("input[name=email]").val( );
	                donationObj.cc_number		= $form.find("input[name=cc_number]").val( );
	                donationObj.name_on_card  	= $form.find("input[name=name_on_card]").val( );
	                donationObj.exp_date		= $form.find("select[name=exp_date_month]").find("option:selected").val( ) + "" + $form.find("select[name=exp_date_year]").find("option:selected").val( );
	                donationObj.ccv 			= $form.find("input[name=ccv]").val( );
	                donationObj.billingAddr1  	= $form.find("input[name=billingAddress1]").val( );
	                donationObj.billingAddr2 	= $form.find("input[name=billingAddress2]").val( );
	                donationObj.billingCity   	= $form.find("input[name=billingCity]").val( );
	                donationObj.billingState 	= $form.find("select[name=billingState]").find("option:selected").text( );
                    donationObj.billingStateId 	= $form.find("select[name=billingState]").find("option:selected").val( );
	                donationObj.billingZip 		= $form.find("input[name=billingZip]").val( );
	                donationObj.billingCountry  = $form.find("select[name=billingCountry]").find("option:selected").text( );
                    donationObj.billingCountryId= $form.find("select[name=billingCountry]").find("option:selected").val( );
					donationObj.saveAddress		= $form.find("input[name=saveAddress]").is(":checked");
					donationObj.saveAddressType = type;

					$review 	= $(".donation-review");
	                $donateForm = $(".donation-checkout");
	                
	                $review.find(".donation-amount").html( "$" + donationObj.amount );

	                if( payment_gateway != 3 ){
	                	var cc_mask = donationObj.cc_number.replace(/\d{12}(\d{4})/, "XXXX XXXX XXXX $1");
	                    
		                $review.find(".cc-num").text( cc_mask );

		                $review.find(".cc-name").text( donationObj.name_on_card );

		                $review.find(".cc-expdate").text( $form.find("select[name=exp_date_month]").find("option:selected").val( ) + "/" + $form.find("select[name=exp_date_year]").find("option:selected").val( ) );

		                $review.find(".cc-ccv").text( donationObj.ccv );
	                }

	                $review.find(".billingaddress1").text( donationObj.billingAddr1 );

	                $review.find(".billingaddress2").text( donationObj.billingAddr2 );

	                $review.find(".billingcity").text( donationObj.billingCity );

	                $review.find(".billingstate").text( donationObj.billingState );

	                $review.find(".billingzip").text( donationObj.billingZip );

	                $review.find(".billingcountry").text( donationObj.billingCountry );
	                
	                $button.html( "review order");
	                $button.prop("disabled", false);

	                $donateForm.slideUp("slow", function( ){
	                	$review.slideDown("slow");

	                	$("html, body").animate({scrollTop: 0},800);
	                });
				}else{

					for( var key in resp.errors ){
                        $("." + key + "-error").html( resp.errors[key] ).show( );
                    }

                    $button.html( "review order");
                    $button.prop("disabled", false);
				}
			}
        });
    });

    $(".checkout-button").on("click", function( ){
        
        $donationId = "";

        var $button = $(this);

        var donationData = {
        	user_id: 				userId, 
        	item_id: 				id, 
        	item_type: 				type, 
        	email: 					donationObj.email, 
        	billing_full_name: 		donationObj.first_name + " " + donationObj.last_name, 
        	first_name: 			donationObj.first_name, 
        	last_name: 				donationObj.last_name, 
        	cc_number: 				donationObj.cc_number,
        	ccv: 					donationObj.ccv,
        	exp_date: 				donationObj.exp_date,
        	billing_address_line1: 	donationObj.billingAddr1, 
        	billing_address_line2: 	donationObj.billingAddr2, 
        	billing_city: 			donationObj.billingCity, 
        	billing_state: 			donationObj.billingStateId,  
        	billing_country: 		donationObj.billingCountryId, 
        	billing_zip: 			donationObj.billingZip, 
        	donation_amount: 		donationObj.amount, 
        	saveAddress: 			donationObj.saveAddress, 
        	type: 					donationObj.saveAddressType, 
        	payment_gateway: 		payment_gateway 
        }; 
        
        $.ajax({
            method: "post",
            url: "/setPendingDonation",
            beforeSend: function( ){
            	//change button to show processing
                
                $button.html( "Processing...");
                //$button.prop("disabled", true);

            },
            data: donationData,
            dataType: "json",
            success: function( data ){

            	$button.html( "Checkout");
                //$button.prop("disabled", false);
                
            	dataId = data.id;
                
                if( payment_gateway == 3 ){
                 //find the paypalStdForm
                
	                var $checkoutForm = $("form[name=paypalStdCheckout]");
	                
	                //Input values into the form
	                $("input[name=cmd]").val("_donations");
	                $("input[name=return]").val(window.location.protocol + "//" +  window.location.host + "/thankyou");
	                $("input[name=notify_url]").val(window.location.protocol + "//" +  window.location.host + "/ipn");

	                $("input[name=amount]").val( donationObj.amount );

	                $("input[name=custom]").val( dataId );
	                
	                $("input[name=business]").val( paypal_username );
	                
	                $("input[name=first_name]").val( donationObj.first_name );

	                $("input[name=email]").val( donationObj.email );
	                
	                $("input[name=last_name]").val( donationObj.last_name );
	                
	                $("input[name=address1]").val( donationObj.billingAddr1 );
	                
	                $("input[name=address2]").val( donationObj.billingAddr2 );
	                
	                $("input[name=city]").val( donationObj.billingCity );
	                
	                $("input[name=state]").val( donationObj.billingState );
	                
	                $("input[name=zip]").val( donationObj.billingZip );
	                
	                $("input[name=country]").val( donationObj.billingCountry );
	                
	                $checkoutForm.trigger("submit");
	            }else{

	            	console.dir( data );

	            	if( data.status ){

	            		console.log( "should be true");
	            		console.log( data.status );
	            	
		            	$(".thank-you-message").removeClass('hidden');

		            	$("h1.text-center").remove( );

		            	$(".donation-review-header").remove( );

		            	$("button.edit-button").remove( );

		            	$("button.checkout-button").remove( );

		            	$(".donation-review").find(".order-review").prepend("<div class='donation-reference'><p class='header'>Reference Number</p>" + data.txnId + "</div><hr />");
		            }else{
		            	console.log( "should be false");
		            	console.log( data.status );
		            	$(".donation-review").find(".error").html( data.text ).show( );	
		            }
	            }
            }
        })
    });

	//Action for Shipping Address Selection
    $("select[name=userAddresses]").on("change", function( ){
        
        var addressSelected = $(this).find(":selected").val( );
        
        if( addressSelected == 0 ){
            $("input[name=billingAddress1]").val( "" );
            $("input[name=billingAddress2]").val( "" );
            $("input[name=billingCity]").val( "" );
            $("select[name=billingState] option").each( function( i ){
            	if( i > 0 ){
            		$(this).remove( );
            	}
            });
            $("input[name=billingZip]").val( "" );
            $("select[name=billingCountry]>option:eq(0)").prop("selected", true);
        }else{
            
            var addressData = addressSelected.split("|");
            
            $("input[name=billingAddress1]").val( addressData[2] );
            $("input[name=billingAddress2]").val( addressData[3] );
            $("input[name=billingCity]").val( addressData[4] );
            $("input[name=billingZip]").val( addressData[6] );
            
			$("select[name=billingCountry]").find("option[value=" + addressData[7] + "]").prop("selected", true);

			$("select[name=billingCountry]").trigger("change");

			setTimeout( function( ){
				$("select[name=billingState]").find("option[value=" + addressData[5] + "]").prop("selected", true);
			}, 1000);
		}
    });

    $(".edit-button").on("click", function( e ){

    	$review 	= $(".donation-review");
	    $donateForm = $(".donation-checkout");

	    $review.slideUp("slow", function( ){
        	$donateForm.slideDown("slow");
        });
	});

});
$(function( ){

	//Declare Fund Object
	var fundObj 	 	= {};

	/** Read all hidden fields that came from Controller and delete the fields after read **/
	var userId 			= $("input[name=user_id]").val( );
	$("input[name=user_id]").remove( );

	var paypal_username = $("input[name=paypal_un]").val( );
	$("input[name=paypal_un]").remove( );

	var payment_gateway = $("input[name=payment_gateway]").val( );
	$("input[name=payment_gateway]").remove( );

	var id 				= $("input[name=projectId]").val( );
	$("input[name=projectId]").remove( );

	var $incentive 		= $(".fund-incentives-list").find(".incentive");
	
	var project_title	= $("input[name=title]").val( );
	$("input[name=title]").remove( );

	/** End Read hidden fields **/

	if( $("input[name=donationAmt]").length > 0 && $("input[name=donationAmt]").val( ) != "" ){
		fundObj.amount = parseFloat( $("input[name=donationAmt]").val( ) ).formatMoney(2, ',', '.');
	}else{
		fundObj.amount = 0.00
	}
	
	if( $("input[name=incentiveId]").length > 0 && $("input[name=incentiveId]").val( ) != "" ){
		fundObj.incentive = $("input[name=incentiveId]").val( );
		fundObj.type 	  = "incentive";

		var $fundedItem = $("[data-incentive-id=" + fundObj.incentive + "]");

		fundObj.incentiveName 	= $fundedItem.find(".title").text( );

		if( $fundedItem.find("input[name=shippingRequired]").val( ) == "Y" ){
			$("form[name=fundCheckoutForm]").find("input[name=showShipping]").val("Y");
			$("form[name=fundCheckoutForm]").find(".shipping-info").show( );
			$("form[name=fundCheckoutForm]").find(".shippingHR").show( );
			$("form[name=fundCheckoutForm]").find(".sameAsShipping-wrapper").show( );
		}else{
			$("form[name=fundCheckoutForm]").find(".sameAsShipping-wrapper").hide( );
		}

	}else{

		fundObj.incentive = "";
		fundObj.type 	  = "fund";
		$("form[name=fundCheckoutForm]").find(".sameAsShipping-wrapper").hide( );
	}	

	if( $("select[name=userAddressesBilling]").children("option").length == 1 ){
		$("select[name=userAddressesBilling]").parent( ).hide( );
	}

	if( $("select[name=userAddresses]").children("option").length == 1 ){
		$("select[name=userAddresses]").parent( ).hide( );
	} 

	//Select Incentive on first page of checkout
     $incentive.on("click", function( ){

     	fundObj.incentive 		= $(this).data('incentive-id');
     	fundObj.incentiveName 	= $(this).find(".title").text( );
		fundObj.type 			= 'incentive';

     	if( $(this).find("input[name=shippingRequired]").val( ) == "Y" ){
			$("form[name=fundCheckoutForm]").find("input[name=showShipping]").val("Y");
			$("form[name=fundCheckoutForm]").find(".shipping-info").slideDown( );
			$("form[name=fundCheckoutForm]").find(".sameAsShipping-wrapper").show( );
			$("form[name=fundCheckoutForm]").find("input[name=sameAsShipping]").prop('checked', false);
		}else{
			$("form[name=fundCheckoutForm]").find("input[name=showShipping]").val("N");
			$("form[name=fundCheckoutForm]").find(".shipping-info").slideUp( );
			$("form[name=fundCheckoutForm]").find(".sameAsShipping-wrapper").hide( );
			$("form[name=fundCheckoutForm]").find("input[name=sameAsShipping]").prop('checked', false);
		}

        if( ! $(this).hasClass('selected') ){
            $(".fund-incentives-list").find(".incentive").each( function( ){

                if( $(this).hasClass('selected') ){

                	var p = $(this).find(".info>.price").html( );

                	$(this).find('.donation').find("input[name=donationAmt]").val( p.replace("$", "").trim( ) );

                    $(this).find('.donation').slideUp( );
                    $(this).removeClass('selected');
                }
            });

            if( $(".fund-options").find(".details-fund").hasClass('selected') ){

                $(".details-fund").find('.donation').slideUp( );
                $(".details-fund").find('.donation').find('input[type=text]').val("");
                $(".details-fund").removeClass('selected');
            }

            $(this).addClass('selected');
            $(this).find(".donation").slideDown( );    
        }
    });

    //Select the contribute button on the Incentive Page
    $(".fund-options").find(".details-fund").on("click", function( ){

    	fundObj.incentive 		= "";
    	fundObj.type	  		= 'fund';
    	fundObj.incentiveName 	= "";

    	$("form[name=fundCheckoutForm]").find("input[name=showShipping]").val("N");
		$("form[name=fundCheckoutForm]").find(".shipping-info").slideUp( );
		$("form[name=fundCheckoutForm]").find(".sameAsShipping-wrapper").hide( );

        if( ! $(this).hasClass('selected') ){
            $(".fund-incentives-list").find(".incentive").each( function( ){

                if( $(this).hasClass('selected') ){

                	var p = $(this).find(".info>.price").html( );

                	$(this).find('.donation').find("input[name=donationAmt]").val( p.replace("$", "").trim( ) );

                    $(this).find('.donation').slideUp( );
                    $(this).removeClass('selected');
                }
            });

            $(this).addClass('selected');
            $(this).find(".donation").slideDown( );
        }
    });

    //Action for Shipping Address Selection
    $("select[name=userAddresses]").on("change", function( ){
        
        var addressSelected = $(this).find(":selected").val( );
        
        if( addressSelected == 0 ){
            $("input[name=shippingAddress1]").val( "" );
            $("input[name=shippingAddress2]").val( "" );
            $("input[name=shippingCity]").val( "" );
            $("select[name=shippingState] option").each( function( i ){
            	if( i > 0 ){
            		$(this).remove( );
            	}
            });
            $("input[name=shippingZip]").val( "" );
            $("select[name=shippingCountry]>option:eq(0)").prop("selected", true);
        }else{
            
            var addressData = addressSelected.split("|");

            
        	$("input[name=shippingAddress1]").val( addressData[2] );
         	$("input[name=shippingAddress2]").val( addressData[3] );
            $("input[name=shippingCity]").val( addressData[4] );
            $("input[name=shippingZip]").val( addressData[6] );

            $("select[name=shippingState] option").each( function( i ){
            	if( i > 0 ){
            		$(this).remove( );
            	}
            });
            
			$("select[name=shippingCountry]").find("option[value=" + addressData[7] + "]").prop("selected", true);

			$("select[name=shippingCountry]").trigger("change");

            setTimeout( function( ){
            	$("select[name=shippingState]").find("option[value=" + addressData[5] + "]").prop("selected", true);
			}, 1000);
        }
    });
    
      //Action for Shipping Address Selection
    $("select[name=userAddressesBilling]").on("change", function( ){
        
        var addressSelected = $(this).find(":selected").val( );
        
        if( addressSelected == 0 ){
            $("input[name=billingAddress1]").val( "" );
            $("input[name=billingAddress2]").val( "" );
            $("input[name=billingCity]").val( "" );
            $("select[name=billingState] option").each( function( i ){
            	if( i > 0 ){
            		$(this).remove( );
            	}
            });
            $("input[name=billingZip]").val( "" );
            $("select[name=billingCountry]>option:eq(0)").prop("selected", true);
        }else{
            
            var addressData = addressSelected.split("|");
            
            $("input[name=billingAddress1]").val( addressData[2] );
            $("input[name=billingAddress2]").val( addressData[3] );
            $("input[name=billingCity]").val( addressData[4] );
            $("input[name=billingZip]").val( addressData[6] );
            
			$("select[name=billingCountry]").find("option[value=" + addressData[7] + "]").prop("selected", true);

			$("select[name=billingCountry]").trigger("change");

            setTimeout( function( ){
            	$("select[name=billingState]").find("option[value=" + addressData[5] + "]").prop("selected", true);
			}, 1000);
			
        }
    });

    $(".sameAsShipping").on("change", function( ){

    	if( $(this).is(":checked") ){

    		$("input[name=billingAddress1]").val( $("input[name=shippingAddress1]").val( ) );
            $("input[name=billingAddress2]").val( $("input[name=shippingAddress2]").val( ) );
            $("input[name=billingCity]").val( $("input[name=shippingCity]").val( ) );
            $("input[name=billingZip]").val( $("input[name=shippingZip]").val( ) );

            $("select[name=billingCountry]").find("option[value=" + $("select[name=shippingCountry]").find("option:selected").val( ) + "]").prop("selected", true);

			$("select[name=billingCountry]").trigger("change");            

			$("select[name=billingState]").find("option[value=" + $("select[name=shippingState]").find("option:selected").val( ) + "]").prop("selected", true);
			
		}
	});

    //Action for checking out for Step 2 ( Details Page );
    $(".continueCheckout").on("click", function( ){
        
        var formData = $('form[name=fundCheckoutForm]').serialize( );
        
        var $button = $(this);

        var error = "";

        $(".fund-error").html( "" ).hide( );

        if( fundObj.type == "incentive" ){
        	if( fundObj.incentive != "" ){

        		var amount = $("[data-incentive-id=" + fundObj.incentive + "]").find("input[name=donationAmt]").val( );

        		if( amount == "" ){
        			error = "Please Enter an Amount to Fund.";
        		}else{
        			if( isNaN( parseFloat( amount ) ) ){
        				error = "The donated amount must be a number.";
        			}else{
        				fundObj.amount = parseFloat( amount ).formatMoney(2, ',', '.');
        			}
				}
			}else{
        		error = "Please Select an Incentive.";
        	}
        }else{

        	var amount = $(".details-fund").find("input[name=donationAmt]").val( );

        	if( amount == "" ){
        		error = "Please Enter an Amount to Fund.";
        	}else{
        		if( isNaN( parseFloat( amount ) ) ){
    				error = "The donated amount must be a number.";
    			}else{
    				fundObj.amount = parseFloat( amount ).formatMoney(2, ',', '.');
    			}
        	}
        }	

        if( error == "" ){
        
	        $.ajax({
	            url: "/crowdfunding/validateFunding",
	            method: "POST",
	            data: formData,
	            beforeSend: function( ){
	                
	                //clear any error messages
	                $(".error").html( "" ).hide( );
	                
	                //change button to show processing
	                $button.html( "Validating...");
	                $button.prop("disabled", true);
	            },
	            success: function( ){
	                
	                $button.html( "review order");
	                $button.prop("disabled", false);
	                
	                var $form = $('form[name=fundCheckoutForm]');

	                fundObj.first_name 		= $form.find("input[name=first_name]").val( );
	                fundObj.last_name  		= $form.find("input[name=last_name]").val( );
	                fundObj.email			= $form.find("input[name=email]").val( );
	                fundObj.cc_number		= $form.find("input[name=cc_number]").val( );
	                fundObj.name_on_card  	= $form.find("input[name=name_on_card]").val( );
	                fundObj.exp_date		= $form.find("select[name=exp_date_month]").find("option:selected").val( ) + "" + $form.find("select[name=exp_date_year]").find("option:selected").val( );
	                fundObj.exp_date_m		= $form.find("select[name=exp_date_month]").find("option:selected").val( ); 
	                fundObj.exp_date_y 		= $form.find("select[name=exp_date_year]").find("option:selected").text( );
	                fundObj.ccv 			= $form.find("input[name=ccv]").val( );

	                var cc_mask = fundObj.cc_number.replace(/\d{12}(\d{4})/, "XXXX XXXX XXXX $1");

					fundObj.saveShippingAddress = false;

					if( $form.find("input[name=showShipping]").val( ) == "Y" ){

	                	fundObj.hasShippingData		= 1;

	                	fundObj.shippingAddr1 		= $form.find("input[name=shippingAddress1]").val( );
	                	fundObj.shippingAddr2 		= $form.find("input[name=shippingAddress2]").val( );
	                	fundObj.shippingCity 		= $form.find("input[name=shippingCity]").val( );
	                	fundObj.shippingState 		= $form.find("select[name=shippingState]").find("option:selected").text( );
	                	fundObj.shippingStateId 	= $form.find("select[name=shippingState]").find("option:selected").val( );
	                	fundObj.shippingZip 		= $form.find("input[name=shippingZip]").val( );
	                	fundObj.shippingCountry 	= $form.find("select[name=shippingCountry]").find("option:selected").text( );
	                	fundObj.shippingCountryId 	= $form.find("select[name=shippingCountry]").find("option:selected").val( );

	                	if( $form.find("input[name=saveShippingAddress]").is(":checked") ){
	                		fundObj.saveShippingAddress = true;
	                	}
	                }else{
	                	fundObj.hasShippingData = 0;
	                }

	                fundObj.billingAddr1 			= $form.find("input[name=billingAddress1]").val( );
	                fundObj.billingAddr2 			= $form.find("input[name=billingAddress2]").val( );
	                fundObj.billingCity 			= $form.find("input[name=billingCity]").val( );
	                fundObj.billingState 			= $form.find("select[name=billingState]").find("option:selected").text( );
	                fundObj.billingStateId			= $form.find("select[name=billingState]").find("option:selected").val( );
	                fundObj.billingZip 				= $form.find("input[name=billingZip]").val( );
	                fundObj.billingCountry 			= $form.find("select[name=billingCountry]").find("option:selected").text( );
	                fundObj.billingCountryId 		= $form.find("select[name=billingCountry]").find("option:selected").val( );

	                fundObj.saveBillingAddress = false;

	                if( $form.find("input[name=saveBillingAddress]").is(":checked") ){
                		fundObj.saveBillingAddress = true;
                	}
	                
	                var $review = $(".fund-review");
	                    
	                if( fundObj.type == "incentive" ){

	                	var $chosenIncentive = 
		                $(".fund-incentives-list").find("[data-incentive-id=" + fundObj.incentive + "]");

						$review.find(".order-review-incentive").find(".header").html( "Incentive" );
	                    
	                    $review.find(".incentive-title").html( $chosenIncentive.find(".title").text( ) ); 
	                    
	                    $review.find(".incentive-description").html( $chosenIncentive.find(".description").text( ) );
	                    
	                }else{
	                    $review.find(".order-review-incentive").find(".header").html( "Fund Amount" );
	                }
	                
	                $review.find(".donation-amount").html( "$" + fundObj.amount );

	                if( $form.find("input[name=showShipping]").val( ) == "Y" ){

	                	$(".order-review-shipping-address").show( );
	                    
		                $review.find(".shippingaddress1").text( fundObj.shippingAddr1 );

		                $review.find(".shippingaddress2").text( fundObj.shippingAddr2 );

		                $review.find(".shippingcity").text( fundObj.shippingCity );

		                $review.find(".shippingstate").text( fundObj.shippingState );

		                $review.find(".shippingzip").text( fundObj.shippingZip );

		                $review.find(".shippingcountry").text( fundObj.shippingCountry );
					}else{
						$(".order-review-shipping-address").hide( );
						$(".order-review-shipping-address").prev( ).hide( );
					}

					$review.find(".cc-num").text( cc_mask );

	                $review.find(".cc-name").text( fundObj.name_on_card );

	                $review.find(".cc-expdate").text( fundObj.exp_date );

	                $review.find(".cc-ccv").text( fundObj.ccv );
	                
					$review.find(".billingaddress1").text( fundObj.billingAddr1 );

	                $review.find(".billingaddress2").text( fundObj.billingAddr2 );

	                $review.find(".billingcity").text( fundObj.billingCity );

	                $review.find(".billingstate").text( fundObj.billingState );

	                $review.find(".billingzip").text( fundObj.billingZip );

	                $review.find(".billingcountry").text( fundObj.billingCountry );
	                
	                $button.val( "review order");
	                $button.prop("disabled", false);

	                $(".donation-checkout").slideUp("slow", function( ){

	                	$(".project-fund-title, .project-fund-organization").addClass('text-center');

	                	$(".fund-review").slideDown("slow");
	                });
	            },
	            statusCode: {
	                422: function( jqXHR, textStatus, errorThrown ){
	                    
	                    $button.html( "review order");
	                    $button.prop("disabled", false);
	                    
	                    var errors = JSON.parse( jqXHR.responseText );
	                    
	                    for( var key in errors ){
	                        $("." + key + "-error").html( errors[key] ).removeClass( 'hidden' ).show( );
	                    }

	                    $("html, body").animate({scrollTop: 0},800);
	                }
	            }
	        });
		}else{
			$(".fund-error").html( error ).show( );
			$('html,body').scrollTop(0);
		}
    });

	$(".checkout-button").on("click", function( ){
        
        $donationId = "";
        
        var project_data = {
            "user_id" :                 userId,
            "project_id" :              id,
            "incentive_id" :            fundObj.incentive,
            "project_title" :           project_title,
            "incentive_title" :         fundObj.incentiveName,
            "donation_amount" :         fundObj.amount,
            "cc_number": 				fundObj.cc_number,
        	"ccv": 						fundObj.ccv,
        	"exp_date": 				fundObj.exp_date,
        	"first_name":  				fundObj.first_name,
        	"last_name": 				fundObj.last_name,
        	"email": 					fundObj.email, 
            "billing_full_name":        fundObj.first_name + " " + fundObj.last_name,
            "billing_first_name":       fundObj.first_name,
            "billing_last_name" :       fundObj.last_name,
            "billing_email":            fundObj.email,
            "billing_address_line1":    fundObj.billingAddr1,
            "billing_address_line2":    fundObj.billingAddr2,
            "billing_city":             fundObj.billingCity,
            "billing_state":            fundObj.billingStateId,
            "billing_zip":              fundObj.billingZip,
            "billing_country":          fundObj.billingCountryId,
            "shipping_full_name":       fundObj.first_name + " " + fundObj.last_name,
            "shipping_email":           fundObj.email,
            "shipping_address_line1":   fundObj.shippingAddr1,
            "shipping_address_line2":   fundObj.shippingAddr2,
            "shipping_city":            fundObj.shippingCity,
            "shipping_state":           fundObj.shippingStateId,
            "shipping_zip":             fundObj.shippingZip,
            "shipping_country":         fundObj.shippingCountryId,
            "saveShippingAddress": 		fundObj.saveShippingAddress,
            "saveBillingAddress": 		fundObj.saveBillingAddress,
            "hasShippingData": 			fundObj.hasShippingData,
            "payment_gateway": 			payment_gateway
        };

        $.ajax({
            method: "post",
            url: "/crowdfunding/setPendingDonation",
            data: project_data,
            dataType: "json",
            success: function( data ){
                
                dataId = data.id;
                
                if( payment_gateway == 3 ){
                 //find the paypalStdForm
                
	                var $checkoutForm = $("form[name=paypalStdCheckout]");
	                
	                //Input values into the form
	                $("input[name=cmd]").val("_donations");
	                $("input[name=return]").val(window.location.protocol + "//" +  window.location.host + "/thankyou");
	                $("input[name=notify_url]").val(window.location.protocol + "//" +  window.location.host + "/project/ipn");

	                $("input[name=amount]").val( fundObj.amount );

	                $("input[name=custom]").val( dataId + "|" + fundObj.incentive );
	                
	                $("input[name=business]").val( paypal_username );
	                
	                $("input[name=first_name]").val( fundObj.first_name );
	                
	                $("input[name=last_name]").val( fundObj.last_name );

	                $("input[name=email]").val( fundObj.email );
	                
	                $("input[name=address1]").val( fundObj.billingAddr1 );
	                
	                $("input[name=address2]").val( fundObj.billingAddr2 );
	                
	                $("input[name=city]").val( fundObj.billingCity );
	                
	                $("input[name=state]").val( fundObj.billingState );
	                
	                $("input[name=zip]").val( fundObj.billingZip );
	                
	                $("input[name=country]").val( fundObj.billingCountry );
	                
	                $checkoutForm.trigger("submit");
	            }else{

	            	if( data.status ){

	            		$(".project-fund-title")
	            		.html("<div class='checkout-thank-you-message'>Thank you for your Donation!</div><div style='font-size:12px;'> You will recieve an email shortly.</div>");

	            		$(".project-fund-organization")
	            		.remove( );

	            		$(".fund-review")
	            		.find(".header:first")
	            		.remove( );

	            		$(".order-review-incentive")
	            		.html("<p class='header'>Reference Number</p><div>" + data.txnId + "</div>")

	            		$("button.checkout-button")
	            		.remove( );

	            		$("html, body").animate({ scrollTop: "0px" });
	            	}
	            }
            }
        });
    });

	//Action for Shipping Address Selection
    $("select[name=userAddresses]").on("change", function( ){
        
        var addressSelected = $(this).find(":selected").val( );
        
        if( addressSelected == 0 ){
            $("input[name=billingAddress1]").val( "" );
            $("input[name=billingAddress2]").val( "" );
            $("input[name=billingCity]").val( "" );
            $("select[name=billingState] option").each( function( i ){
            	if( i > 0 ){
            		$(this).remove( );
            	}
            });
            $("input[name=billingZip]").val( "" );
            $("select[name=billingCountry]>option:eq(0)").prop("selected", true);
        }else{
            
            var addressData = addressSelected.split("|");
            
            $("input[name=billingAddress1]").val( addressData[2] );
            $("input[name=billingAddress2]").val( addressData[3] );
            $("input[name=billingCity]").val( addressData[4] );
            $("input[name=billingZip]").val( addressData[6] );
            
			$("select[name=billingCountry]").find("option[value=" + addressData[7] + "]").prop("selected", true);

			$("select[name=billingCountry]").trigger("change");

			setTimeout( function( ){
				$("select[name=billingState]").find("option[value=" + addressData[5] + "]").prop("selected", true);
			}, 1000);
		}
    });

    $(".edit-button").on("click", function( e ){

    	$review 	= $(".fund-review");
	    $donateForm = $(".donation-checkout");

	    $review.slideUp("slow", function( ){
        	$donateForm.slideDown("slow");
        });
	});
});
$(function( ){
	
	var data 	 	 	= {};
	var userId 			= $("input[name=user_id]").val( );
	var paypal_username = $("input[name=paypal_un]").val( );
	var payment_gateway = $("input[name=payment_gateway]").val( );

	var productId 		= $("input[name=productId]").val( );
	var shipping 		= $("input[name=shipping_cost]").val( );

	var modifierIds  	= $("input[name=modifierIds]").val( );

	data.shipping 			= shipping;
	data.id 				= productId;
	data.user_id			= userId;
	data.modifierId     	= modifierIds;
	data.payment_gateway 	= payment_gateway;

	//Action for Shipping Address Selection
    $("select[name=userAddresses]").on("change", function( ){
        
        var addressSelected = $(this).find(":selected").val( );
        
        if( addressSelected == 0 ){
            $("input[name=shippingAddress1]").val( "" );
            $("input[name=shippingAddress2]").val( "" );
            $("input[name=shippingCity]").val( "" );
            $("select[name=shippingState] option").each( function( i ){
            	if( i > 0 ){
            		$(this).remove( );
            	}
            });
            $("input[name=shippingZip]").val( "" );
            $("select[name=shippingCountry]>option:eq(0)").prop("selected", true);
        }else{
            
            var addressData = addressSelected.split("|");

            $("input[name=shippingAddress1]").val( addressData[2] );
         	$("input[name=shippingAddress2]").val( addressData[3] );
            $("input[name=shippingCity]").val( addressData[4] );
            $("input[name=shippingZip]").val( addressData[6] );

            $("select[name=shippingState] option").each( function( i ){
            	if( i > 0 ){
            		$(this).remove( );
            	}
            });
            
			$("select[name=shippingCountry]").find("option[value=" + addressData[7] + "]").prop("selected", true);

			$("select[name=shippingCountry]").trigger("change");

            setTimeout( function( ){
            	$("select[name=shippingState]").find("option[value=" + addressData[5] + "]").prop("selected", true);
			}, 1000);
        }
    });
    
      //Action for Shipping Address Selection
    $("select[name=userAddressesBilling]").on("change", function( ){
        
        var addressSelected = $(this).find(":selected").val( );
        
        if( addressSelected == 0 ){
            $("input[name=billingAddress1]").val( "" );
            $("input[name=billingAddress2]").val( "" );
            $("input[name=billingCity]").val( "" );
            $("select[name=billingState] option").each( function( i ){
            	if( i > 0 ){
            		$(this).remove( );
            	}
            });
            $("input[name=billingZip]").val( "" );
            $("select[name=billingCountry]>option:eq(0)").prop("selected", true);

            setTimeout( function( ){
            	$("select[name=billingState]").find("option[value=" + addressData[5] + "]").prop("selected", true);
			}, 1000);
        }else{
            
            var addressData = addressSelected.split("|");
            
            $("input[name=billingAddress1]").val( addressData[2] );
            $("input[name=billingAddress2]").val( addressData[3] );
            $("input[name=billingCity]").val( addressData[4] );
            $("input[name=billingZip]").val( addressData[6] );
            
			$("select[name=billingCountry]").find("option[value=" + addressData[7] + "]").prop("selected", true);

			$("select[name=billingCountry]").trigger("change");

            $("select[name=billingState]").find("option[value=" + addressData[5] + "]").prop("selected", true);
			
        }
    });

    var modifierOptions = [];
	var combo = "";

	$(".product-modifiers select").on("change", function( i ){
		
		var allSelected = true;
		
		$(".product-modifiers select").each( function( i ){
			
			if( parseInt( $(this).find(":selected").val( ) ) == 0 ){
				allSelected = false;
			}
		});

		if( allSelected ){
			
			$(".product-modifiers select").each( function( i ){
				modifierOptions.push( parseInt( $(this).find(":selected").val( ) ) );	
			});
			
			modifierOptions = modifierOptions.reverse( );
				
			combo = modifierOptions.join( );
			
			data.modifierId = combo;
			
			$.ajax({
				method: "get",
				url: "/products/getQuantity",
				data: "options=" + combo + "&id=" + data.id,
				dataType: "json",
				beforeSend: function( ){

					$("select[name=quantity] option").each( function( i ){
						if( i > 0 ){
							$(this).remove( );
						}

					});

					$("select[name=quantity]").find("option:first").html("Getting Quantity ...");
				},
				success: function( resp ){
					$("select[name=quantity]").find("option:first").html("Select Quantity");
					modifierOptions = [];
					
					if( parseInt( resp.count ) > 0 ){
						
						for( var i = 1 ; i <= resp.count ; i++ ){
							$("select[name=quantity]").append("<option value='" + i + "'>" + i + "</option>");
						}
						
						//data.modifierId = resp.modifier_id;
						
						$(".price").data("price", resp.price );
						$(".price").html("$" + parseFloat( resp.price ).formatMoney(2, ',', '.') );
					}
				}
			});
		}else{
			
			if( $("select[name=quantity]").length > 1 ){
				
				$("select[name=quantity] option").each( function( i ){
					
					if( i > 0 ){
						remove( $(this) );
					}
				});	
			}
		}
	});

	$("select[name=quantity]").on("change", function( ){

		if( $(this).find("option:selected").val( ) == 0 ){
			$(".price").html( "$" + parseFloat( $(".price").data("price") ).formatMoney(2, ',', '.') );
		}else{
			$(".price").html( "$" + parseFloat( ($(this).find("option:selected").val( ) * $(".price").data('price') ) ).formatMoney(2, ',', '.') );	
		}
	});

	$('.continueCheckout').on("click", function( ){
	    
	    var formData = $('form[name=purchaseCheckoutForm]').serialize( );
        
		var $button = $(this);

		var modifiers = [];
        var errors    = [];
        var product_alias = $(this).data('alias');
        
        $(".error").hide( );

		$(".product-modifiers select").each(function( i ){
	        
	        var name 		= $(this).find("option:first").text( );
			
			var value 		= $(this).find(":selected").val( );
			var valueText 	= $(this).find(":selected").text( );
			
			if( value == 0 ){
				errors.push($(this).attr("name") + "|" + "Please select a " + name);
			}else{
				modifiers.push("<div class='modifier'><b>" + name.replace("Select ", "") + "</b>: " + valueText + "</div>");
			}
		});
		
		if( $("select[name=quantity]").find(":selected").val( ) == "0" ){
			errors.push("quantity|Please Input a Quantity");
		}

		if( errors.length == 0 ){
		
			$.ajax({
	            url: "/product/validateCheckout",
	            method: "POST",
	            data: formData,
	            beforeSend: function( ){
	                
	                //clear any error messages
	                $(".error").html( "" ).addClass('hidden');
	                
	                //change button to show processing
					$button.html( "Validating...");
			        $button.prop("disabled", true);
	            },
	            success: function( resp ){
		            
		            $button.html( "review order");
			        $button.prop("disabled", false);
			        
			        if( resp.status ){
				        
				        var $form = $('form[name=purchaseCheckoutForm]');
				        
				        data.first_name					= $form.find("input[name='first_name']").val( );
				        data.last_name					= $form.find("input[name='last_name']").val( );
				        data.email						= $form.find("input[name='email']").val( );
				        data.shipping_address_line1 	= $form.find("input[name='shippingAddress1']").val( );
				        data.shipping_address_line2 	= $form.find("input[name='shippingAddress2']").val( );
				        data.shipping_city 				= $form.find("input[name='shippingCity']").val( );
				        data.shipping_state				= $form.find("select[name='shippingState']").find("option:selected").text( );
				        data.shippingstateId			= $form.find("select[name='shippingState']").find("option:selected").val( );
				        data.shipping_zip				= $form.find("input[name='shippingZip']").val( );
				        data.shipping_country			= $form.find("select[name='shippingCountry']").find("option:selected").text( );
				        data.shippingcountryId			= $form.find("select[name='shippingCountry']").find("option:selected").val( );
				        data.cc_number					= $form.find("input[name=cc_number]").val( );
	                	data.name_on_card  				= $form.find("input[name=name_on_card]").val( );
	                	data.exp_date					= $form.find("select[name=exp_date_month]").find("option:selected").val( ) + "" + $form.find("select[name=exp_date_year]").find("option:selected").val( );
	                	data.ccv 						= $form.find("input[name=ccv]").val( );
				        data.billing_address_line1		= $form.find("input[name='billingAddress1']").val( );
				        data.billing_address_line2    	= $form.find("input[name='billingAddress2']").val( );
				        data.billing_city				= $form.find("input[name='billingCity']").val( );
				        data.billing_state 				= $form.find("select[name=billingState]").find("option:selected").text( );
				        data.billingstateId				= $form.find("select[name=billingState]").find("option:selected").val( );
				        data.billing_zip				= $form.find("input[name='billingZip']").val( );
				        data.billing_country			= $form.find("select[name=billingCountry]").find("option:selected").text( );
				        data.billingcountryId			= $form.find("select[name=billingCountry]").find("option:selected").val( );
				        
						var shipping;

						var $review = $(".purchase-review");

						var cc_mask = data.cc_number.replace(/\d{12}(\d{4})/, "XXXX XXXX XXXX $1");

						data.shipping = parseFloat( data.shipping ).formatMoney(2, ',', '.');
						
						if( parseInt( data.shipping) == 0 ){
							shipping = 0.00;
						}else{
							shipping = parseFloat( data.shipping );
						}

						data.price 		= parseFloat( $(".price").data("price") ).formatMoney(2, ',', '.');
						data.quantity 	= parseInt( $("select[name=quantity]").find(":selected").val( ) );
						
						var subTotal = parseFloat( data.price ) * parseInt( data.quantity );
						
						var tax = (subTotal/100) * parseFloat( resp.taxCode.tax_percentage );
						
						data.tax = parseFloat(tax).formatMoney(2, ',', '.');
						
						var total = parseFloat(subTotal) + parseFloat( tax ) + parseFloat( data.shipping);

						total = total.formatMoney(2, ',', '.');

						$review.find(".shippingaddress1").text( data.shipping_address_line1 );
						
						$review.find(".shippingaddress2").text( data.shipping_address_line2 );
		                
						$review.find(".shippingcity").text( data.shipping_city );
		                
						$review.find(".shippingstate").text( data.shipping_state );
		
		                $review.find(".shippingzip").text( data.shipping_zip );
		
		                $review.find(".shippingcountry").text( data.shipping_country );
		
		                $review.find(".billingaddress1").text( data.billing_address_line1 );
		
		                $review.find(".billingaddress2").text( data.billing_address_line2 );
		
		                $review.find(".billingcity").text( data.billing_city );
		
		                $review.find(".billingstate").text( data.billing_state );
		
		                $review.find(".billingzip").text( data.billing_zip );
		
		                $review.find(".billingcountry").text( data.billing_country );

		                $review.find(".cc-num").text( cc_mask );

	                	$review.find(".cc-name").text( data.name_on_card );

	                	$review.find(".cc-expdate").text( $form.find("select[name=exp_date_month]").find("option:selected").val( ) + "/" + $form.find("select[name=exp_date_year]").find("option:selected").val( ) );

	                	$review.find(".cc-ccv").text( data.ccv );
		                
		                $review.find(".item-price").text( "$" + parseFloat(data.price * data.quantity).formatMoney(2, ',', '.') );
		                
		                $review.find(".item-tax").text( "$" + parseFloat( tax ).formatMoney(2, ',', '.') );
		                
		                $review.find(".item-shipping").text( "$" + parseFloat(shipping).formatMoney(2, ',', '.') )
		                
		                $review.find(".item-total").text("$" + parseFloat( total ).formatMoney(2, ',', '.') );
		                
		                $review.find(".product-amount span").html( "$" + parseFloat( data.price ).formatMoney(2, ',', '.') );
		                
		                $review.find(".quantity span").html( data.quantity );
		                
		                $review.find(".total span").html( "$" + parseFloat( subTotal ).formatMoney(2, ',', '.') );

		                $review.find(".product-total-amount").html( "$" + total );
		                
						$(".product-checkout").slideUp("slow", function( ){
		                	$review.slideDown("slow");
		                });

		                console.log( "after validate " );
		                console.log( data );
		                
			        }else{
				        
				        var errors = resp.errors;
	                    
	                    for( var key in errors ){
	                        $("." + key + "-error").html( errors[key] ).removeClass( 'hidden' ).show( );
	                    }

	                    $button.html( "review order");
			        	$button.prop("disabled", false);
					}
	            }
	        });
		}else{

			$("html, body").animate({ scrollTop: "0px" });

			for(var x in errors){
				
				var error = errors[x].split("|");
				
				$("." + error[0] + "-error").html(error[1]).show( );
			}
		}
	});

	$(".checkout-button").on("click", function( ){
    
    	var order_id = 0;

    	data.saveBilling 	= false;
    	data.saveShipping 	= false;

    	console.log( data );

    	var $button = $(this);

    	if( $('form[name=purchaseCheckoutForm]').find("input[name=saveShippingAddress]").is(":checked") ){
    		data.saveShipping = true;
    	}

    	if( $('form[name=purchaseCheckoutForm]').find("input[name=saveBillingAddress]").is(":checked") ){
    		data.saveBilling = true;
    	}
    	
    	$.ajax({
	    	method: "post",
	    	url: "/products/setPendingTransaction",
	    	beforeSend: function( ){
	                
                //change button to show processing
				$button.html( "Purchasing...");
		        $button.prop("disabled", true);
            },
	    	data: data,
	    	dataType: "json",
	    	success: function( resp ){

	    		$button.html( "Place Order");
		        $button.prop("disabled", false);

				if( resp.status ){

					$(".purchase-review")
					.find(".header:first")
					.html("<div class='checkout-thank-you-message'>Thank you for your Purchase!</div><div> You will recieve an email shortly.</div>");

		    		$(".purchase-review")
		    		.find(".order-review-incentive")
		    		.html("<p class='header'>Reference Number</p><div>" + resp.txnId + "</div>");

		    		$("button.edit-button")
		    		.remove( );

		    		$button
		    		.remove( );

		    		$("html, body").animate({ scrollTop: "0px" });
				}else{
		    		$(".purchase-review")
		    		.find("order-error")
		    		.html( resp.reason );
		    	}
		    }
    	});
    });

    $(".edit-button").on("click", function( e ){

    	var $review = $(".purchase-review");

    	$review.slideUp("slow", function( ){
    		$(".product-checkout").show( );;	
    	});
	});
});

var styles = [
	  {
	      "featureType": "landscape",
	      "elementType": "geometry",
	      "stylers": [
	      { "color": "#33aef4" },
	      { "visibility": "on" }
	    ]
	  }, {
	      "featureType": "poi",
	      "elementType": "geometry.fill",
	      "stylers": [
	      { "visibility": "off" }
	    ]
	  }, {
	      "featureType": "road",
	      "stylers": [
	      { "visibility": "off" }
	    ]
	  }, {
	      "featureType": "administrative",
	      "stylers": [
	      { "visibility": "simplified" },
	      { "color": "#0581c6" }
	    ]
	  }, {
	      "featureType": "administrative.province",
	      "stylers": [
	      { "visibility": "off" }
	    ]
	  }, {
	      "featureType": "administrative.locality",
	      "stylers": [
	      { "visibility": "off" }
	    ]
	  }, {
	      "featureType": "administrative.locality",
	      "stylers": [
	      { "visibility": "off" }
	    ]
	  }, {
	      "featureType": "administrative.neighborhood",
	      "stylers": [
	      { "visibility": "off" }
	    ]
	  }, {
	      "featureType": "administrative.land_parcel",
	      "stylers": [
	      { "visibility": "off" }
	    ]
	  }, {
	      "featureType": "poi",
	      "stylers": [
	      { "visibility": "off" }
	    ]
	  }, {
	      "featureType": "administrative.country",
	      "elementType": "geometry.stroke",
	      "stylers": [
	      { "visibility": "on" },
	      { "color": "#0581c6" }
	    ]
	  }, {
	      "featureType": "water",
	      "elementType": "geometry",
	      "stylers": [
	      { "visibility": "simplified" },
	      { "color": "#33aef4" },
	      { "lightness": 65 }
	    ]
	  }, {
	      "featureType": "water",
	      "elementType": "labels",
	      "stylers": [
	      { "visibility": "off" }
	    ]
	  }, {
	}, {
	    "featureType": "landscape",
	    "elementType": "labels",
	    "stylers": [
	      { "visibility": "off" }
	    ]
	}, {
	    "featureType": "transit",
	    "stylers": [
	      { "visibility": "off" }
	    ]
	}, {
	}
	];
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
$(function( ){
    
    setHeight( );
    
    
    $(".subCauseName").on("click", function( ){
        
        var cause = $(this).data("cause");
        
        $(".subCauseName").removeClass("active");
        
        $(this).addClass("active");
        
        $(".subcause-description").addClass("hidden");
        
        $(".subcause-description-" + cause).removeClass("hidden");
        
    });
    
     /*
    *
    * Begin Checkout Process for Current Cause
    *
    */
    
    var paypal_username = $("input[name=paypalUn]").val( );
    var user_id         = $("form[name=donationCheckoutForm]").find("input[name=user_id]").val( );
    
    var $checkout   = $("#causeCheckout");
    var $container  = $("#causeCheckout").find(".checkout");
    var $donation   = $container.find(".donation");
    var $details    = $container.find(".details");
    var $review     = $container.find(".review");
    
    var steps = [];
    
    var number_of_steps = 4;
    
    var cause_alias;
    /*
    $(".donate-button").on("click", function( ){

        location.href = '/cause/' + $(this).data('alias') + '/donation';

    });
    */
    
    //Action for triggering the cause donation lightbox
    
    $(".donate-button").on({
        mouseover: function( ){
            $(this).css('cursor','pointer');
        },
        mouseleave: function( ){
            $(this).css('cursor', 'auto');    
        },
        click: function( ){
            
            cause_alias = $(this).data('alias');
            
            $checkout.css("display","table");
        }
    });
    
    //Close out cause checkout
    $(".exit-cause-checkout").on({
        
        mouseover: function( ){
            $(this).css('cursor', 'pointer');
        },
        mouseleave: function( ){
            $(this).css('cursor', 'auto');
        },
        click: function( ){
        
            //destroy steps object 
            steps = [];

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
                data: {amount: parseFloat(amount.replace(",","")).formatMoney(2, ',', '.'), type: "cause"},
                dataType: "json",
                success: function( resp ){
                    location.href = '/cause/' + cause_alias + '/donation';
                }
            });
        }
    });
});
$(function( ){
    
    var filters = {};
    
    var filepath = $("input[name=filepath]").val( );
    $("input[name=filepath]").remove( );
    
    $(".filter-items ul li").on("click", function( e ){
        
        var removeItem = false;
        
        if( $(this).hasClass("chosen-filter") ){
            $(this).removeClass('chosen-filter');
            removeItem = true;
        }else{
            $(this).parent( ).find("li").removeClass('chosen-filter');
            $(this).addClass('chosen-filter');    
        }
        
        var overlayHeight = $(".product-loading-overlay").parent( ).height( );
        
        $(".product-loading-overlay").css({
            height: overlayHeight + "px",
            display: "table"
        });
        
        if( parseInt( $(this).data("amount") ) > 0 ){
        
            var type = $(this).data('filter-type');

            switch( type ){

                case "price":

                    if( removeItem ){
                        delete filters.price;
                    }else{
                        var price = $(this).data("price");

                        filters.price = price;    
                    }

                break;
                case "category":

                    if( removeItem ){
                        delete filters.cat_id;
                    }else{
                        var cat_id = $(this).data("cat-id");

                        filters.cat_id = cat_id    
                    }

                break;
                case "rating":

                    if( removeItem ){
                        delete filters.rating;
                    }else{
                        var rating = $(this).data("rating");

                        filters.rating = rating;        
                    }

                break;
            }
            
            $.ajax({
                method: "post",
                url: "/products/filter",
                data: filters,
                dataType: "json",
                success: function( resp ){

                    if( resp.status == 1 ){

                        if( resp.data.length == 0 ){
                        	$(".product-loading-overlay").hide( );
                        }else{

                            var prdList = new Array( );

                            $.each(resp.data, function(count, item){

                                var html = "";

                                html =  "<div class='col-lg-6 col-md-6 margin-top-10'>";
                                html += "   <div class='product-module'>";
                                html += "       <div class='product-module-top'>";
                                html += "           <a href='/product/" + item.product_alias + "'><img src='" + item.image + "' align='left' /></a>";
                                html += "           <div class='product-module-name'><a href='/product/" + item.product_alias + "'>" + item.name + "</a></div>";
                                html += "           <div class='product-module-org-name'>";
                                html += "               <span class='product-price'>" + item.price + "</span> from " + item.org_name.stripSlashes( );  
                                html += "           </div>";
                                html += "           <div class='rating'>";

                                for( var i = 1 ; i < 6 ; i++ ){
                                    if( i <= item.rating ){
                                        html += "       <span class='star fill' >";
                                        html += "          <i data-icon='&#xe017;' class='pwi-icon-star pwi-icon-2em'></i>";
                                        html += "       </span>";
                                    }else{
                                        html += "       <span class='star' >";
                                        html += "          <i data-icon='&#xe017;' class='pwi-icon-star pwi-icon-2em'></i>";
                                        html += "       </span>";
                                    }
                                }
                                html += "           </div>";
                                html += "           <div class='product-module-desc'>";

                                if( item.descExp.length < 50  ){
                                    html +=             item.sdesc;
                                }else{
                                    for( var i = 0 ; i < item.descExp.length ; i++ ){

                                        if( i != 50 ){
                                            html += item.descExp[i] + " ";
                                        }else{
                                            html += "<a href='' class='readmore'>...See More</a>";
                                            html += "<span class='more'>" + item.descExp[i];
                                        }
                                    }
                                    html += "</span><a href='#' class='readless'>Show Less</a>";
                                }

                                html += "           </div>";
                                html += "       </div>";
                                html += "   <div style='clear: both;'></div>";
                                html += "   </div>";
                                html += "</div>";

                                prdList.push( html );
                            });

                            $(".product-module-list")
                            .html( "" )
                            .html( prdList.join('') );

                            $(".product-loading-overlay").hide( );
                        }
                    }
                }
            });
        }
    });
    
    /**
	*
	* Checkout process for products
	*
	*/

	$(".buy-now").on("click", function( ){
		var alias = $(this).data('alias');

		location.href = '/product/' + alias + '/purchase';
	});
	
	
	var paypal_username = $("input[name=paypalUn]").val( );
    var user_id         = $("form[name=productCheckoutForm]").find("input[name=user_id]").val( );

    //Get Project Data
    
    var product_name 	= $("input[name=product_name]").val( );
    var org_name 		= $("input[name=org_name]").val( );
    var $checkout 		= $("#productCheckout");
    var $container 		= $("#productCheckout").find(".checkout");
    var $details    	= $container.find(".details");
    var $review  		= $container.find(".review");
    var product_id 		= $(".add-to-cart").data('id');
    
    var data 			= {user_id: user_id, id: product_id, shipping: parseFloat( $("input[name=shipping]").val( ) )};
    
    var number_of_steps = 3;
    
    /**
	*
	* Initial step to open up the lightbox to continue with the product checkout
	*
	*/
    
    $(".add-to-cart").on("click", function( ){
	    
        //$(this).prop('disabled', 'true');
        
		var modifiers = [];
        var errors    = [];
        var product_alias = $(this).data('alias');
        
        $(".error").hide( );

		$(".product-modifiers select").each(function( i ){
	        
	        var name 		= $(this).find("option:first").text( );
			
			var value 		= $(this).find(":selected").val( );
			var valueText 	= $(this).find(":selected").text( );
			
			if( value == 0 ){
				errors.push($(this).attr("name") + "|" + "Please select a " + name);
			}else{
				modifiers.push("<div class='modifier'><b>" + name.replace("Select ", "") + "</b>: " + valueText + "</div>");
			}
		});
		
		if( $("select[name=quantity]").find(":selected").val( ) == "0" ){
			errors.push("quantity|Please Input a Quantity");
		}
		
		if( errors.length == 0 ){
			
			var price 	 = parseFloat( $(".price").data('price') ).formatMoney(2, ',', '.');
			
			var quantity = parseInt( $("select[name=quantity]").find(":selected").val( ) );
			
			location.href = "/product/" + product_alias + "/purchase?quantity=" + quantity + "&modifiers=" + encodeURIComponent( data.modifierIds );
			
			
		}else{
			
			for(var x in errors){
				
				var error = errors[x].split("|");
				
				$("." + error[0] + "-error").html(error[1]).show( );
			}
		}
    });
    
    /*
	*
	* Create event for getting quantity amount for selected modifiers
	*
	*/
	
	var modifierOptions = [];
	var combo = "";
	
	$(".product-modifiers select").on("change", function( i ){
		
		var allSelected = true;
		
		$(".product-modifiers select").each( function( i ){
			
			if( parseInt( $(this).find(":selected").val( ) ) == 0 ){
				allSelected = false;
			}
		});
		
		if( allSelected ){
			
			$(".product-modifiers select").each( function( i ){
				modifierOptions.push( parseInt( $(this).find(":selected").val( ) ) );	
			});
			
			modifierOptions = modifierOptions.reverse( );
				
			combo = modifierOptions.join( );
			
			data.modifierIds = combo;
			
			$.ajax({
				method: "get",
				url: "/products/getQuantity",
				data: "options=" + combo + "&id=" + product_id,
				dataType: "json",
				beforeSend: function( ){

					$("select[name=quantity] option").each( function( i ){
						console.log( $(this) );
						if( i > 0 ){
							$(this).remove( );
						}

					});

					$("select[name=quantity]").find("option:first").html("Getting Quantity ...");
				},
				success: function( resp ){
					$("select[name=quantity]").find("option:first").html("Select Quantity");
					modifierOptions = [];
					
					if( parseInt( resp.count ) > 0 ){
						
						for( var i = 1 ; i <= resp.count ; i++ ){
							$("select[name=quantity]").append("<option value='" + i + "'>" + i + "</option>");
							$details.find("select[name=product-quantity]").append("<option value='" + i + "'>" + i + "</option>");
						}
						
						data.modifierId = resp.modifier_id;
						
						$(".price").data("price", resp.price);
						$(".price").html("$" + parseFloat( resp.price ) + ".00");
					}
				}
			});
		}else{
			
			if( $("select[name=quantity]").length > 1 ){
				
				$("select[name=quantity] option").each( function( i ){
					
					if( i > 0 ){
						remove( $(this) );
					}
				});	
			}
		}
	});
    
    /*
	*    
	* Create event for updating quantity on checkout
	*   
	*/
    
    $details.find("select[name=product-quantity]").on("change", function( ){
	    
	    var value = parseInt( $(this).find(":selected").val( ) );
	    
	    data.quantity = value;
	    
	    $details.find(".product-amount").find("span").html( "$" + (data.price * data.quantity) + ".00");
	    
    });
    
    
    /**
	*
	* Step 1 Continue checkout     
	*    
	*/
    
    $details.find('.continueCheckout').on("click", function( ){
	    
	    var formData = $('form[name=productCheckoutForm]').serialize( );
        
		var $button = $(this);
		
		$.ajax({
            url: "/product/validateCheckout",
            method: "POST",
            data: formData,
            beforeSend: function( ){
                
                //clear any error messages
                $(".error").html( "" ).addClass('hidden');
                
                //change button to show processing
				$button.html( "Validating...");
		        $button.prop("disabled", true);
            },
            success: function( resp ){
	            
	            $button.html( "review order");
		        $button.prop("disabled", false);
		        
		        if( resp.status ){
			        
			        var $form = $('form[name=productCheckoutForm]');
			        
			        
			        data.first_name			= $form.find("input[name='first_name']").val( );
			        data.last_name			= $form.find("input[name='last_name']").val( );
			        data.email				= $form.find("input[name='email']").val( );
			        data.shippingAddress1 	= $form.find("input[name='shippingAddress1']").val( );
			        data.shippingAddress2 	= $form.find("input[name='shippingAddress2']").val( );
			        data.shippingcity 		= $form.find("input[name='shippingCity']").val( );
			        data.shippingstate		= $form.find("select[name='shippingState']").find("option:selected").text( );
			        data.shippingstateId	= $form.find("select[name='shippingState']").find("option:selected").val( );
			        data.shippingzip		= $form.find("input[name='shippingZip']").val( );
			        data.shippingcountry	= $form.find("select[name='shippingCountry']").find("option:selected").text( );
			        data.shippingcountryId	= $form.find("select[name='shippingCountry']").find("option:selected").val( );
			        data.cc_number			= $form.find("input[name=cc_number]").val( );
	                data.name_on_card  		= $form.find("input[name=name_on_card]").val( );
	                data.exp_date			= $form.find("select[name=exp_date_month]").find("option:selected").val( ) + "" + $form.find("select[name=exp_date_year]").find("option:selected").val( );
	                data.ccv 				= $form.find("input[name=ccv]").val( );
			        data.billingaddress1	= $form.find("input[name='billingAddress1']").val( );
			        data.billingaddress2    = $form.find("input[name='billingAddress2']").val( );
			        data.billingcity		= $form.find("input[name='billingCity']").val( );
			        data.billingstate 		= $form.find("select[name=billingState]").find("option:selected").text( );
			        data.billingstateId		= $form.find("select[name=billingState]").find("option:selected").val( );
			        data.billingzip			= $form.find("input[name='billingZip']").val( );
			        data.billingcountry		= $form.find("select[name=billingCountry]").find("option:selected").text( );
			        data.billingcountryId	= $form.find("select[name=billingCountry]").find("option:selected").val( );
			        
					var shipping;

					var cc_mask = data.cc_number.replace(/\d{12}(\d{4})/, "XXXX XXXX XXXX $1");

					data.shipping = parseFloat( data.shipping ).formatMoney(2, ',', '.');
					
					if( parseInt( data.shipping) == 0 ){
						shipping = 0.00;
					}else{
						shipping = parseFloat( data.shipping );
					}
					
					var subTotal = parseFloat( data.price ) * parseInt( data.quantity );
					
					var tax = (subTotal/100) * parseFloat( resp.taxCode.tax_percentage );
					
					data.tax = parseFloat(tax).formatMoney(2, ',', '.');
					
					var total = parseFloat(subTotal) + parseFloat( tax ) + parseFloat( data.shipping);

					total = total.formatMoney(2, ',', '.');

					$review.find(".shippingaddress1").text( data.shippingAddress1 );
	
	                $review.find(".shippingaddress2").text( data.shippingAddress2 );
	
	                $review.find(".shippingcity").text( data.shippingcity );
	
	                $review.find(".shippingstate").text( data.shippingstate );
	
	                $review.find(".shippingzip").text( data.shippingzip );
	
	                $review.find(".shippingcountry").text( data.shippingcountry );

	                $review.find(".cc-num").text( cc_mask );

	                $review.find(".cc-name").text( data.name_on_card );

	                $review.find(".cc-expdate").text( $form.find("select[name=exp_date_month]").find("option:selected").val( ) + "/" + $form.find("select[name=exp_date_year]").find("option:selected").val( ) );

	                $review.find(".cc-ccv").text( data.ccv );

					$review.find(".billingaddress1").text( data.billingaddress1 );
	
	                $review.find(".billingaddress2").text( data.billingaddress2 );
	
	                $review.find(".billingcity").text( data.billingcity );
	
	                $review.find(".billingstate").text( data.billingstate );
	
	                $review.find(".billingzip").text( data.billingzip );
	
	                $review.find(".billingcountry").text( data.billingcountry );
	                
	                $review.find(".item-price").text( "$" + parseFloat(data.price * data.quantity).formatMoney(2, ',', '.') );
	                
	                $review.find(".item-tax").text( "$" + parseFloat( tax ).formatMoney(2, ',', '.') );
	                
	                $review.find(".item-shipping").text( "$" + parseFloat(shipping).formatMoney(2, ',', '.') )
	                
	                $review.find(".item-total").text("$" + parseFloat( total ).formatMoney(2, ',', '.') );
	                
	                $review.find(".product-amount span").html( "$" + parseFloat( data.price ).formatMoney(2, ',', '.') );
	                
	                $review.find(".quantity span").html( data.quantity );
	                
	                $review.find(".total span").html( parseFloat( subTotal ).formatMoney(2, ',', '.') );

	                $review.find(".product-total-amount").html( "$" + total );
	                
					$review.css("margin-left", "0px");
	                
	                $details.css("margin-left", "-590px");	         
	                
		        }else{
			        
			        var errors = resp.errors;
                    
                    for( var key in errors ){
                        $("." + key + "-error").html( errors[key] ).removeClass( 'hidden' ).show( );
                    }

                    $button.html( "review order");
		        	$button.prop("disabled", false);
				}
            }
        });
	});
    
    $review.find(".edit-button").on("click", function( ){
	    
		$details.css("margin-left", "0px");

        $review.css("margin-left", "9999999px");
	    
    });
    
    /*
    *
    * Actions for step 3 ( Order Review )
    *
    */
    
    $review.find(".checkout-button").on("click", function( ){
    
    	var order_id = 0;
    	
    	$.ajax({
	    	method: "post",
	    	url: "/products/setPendingTransaction",
	    	data: data,
	    	dataType: "json",
	    	success: function( resp ){
		    	
		    	order_id = resp.order_id;
		    	
		    	 //find the paypalStdForm
                var $checkoutForm = $("form[name=paypalStdCheckout]");
                
                //Input values into the form
                $checkoutForm.find("input[name=cmd]").val("_xclick");
                $checkoutForm.find("input[name=return]").val(window.location.protocol + "//" +  window.location.host + "/thankyou");
                $checkoutForm.find("input[name=notify_url]").val(window.location.protocol + "//" +  window.location.host + "/products/ipn");

                $checkoutForm.find("input[name=amount]").val( data.price );
                
                $checkoutForm.find("input[name=quantity]").val( data.quantity ); //new
                
                $checkoutForm.find("input[name=shipping]").val( data.shipping ); //new
                
                $checkoutForm.find("input[name=tax]").val( data.tax ); // new
                
				$checkoutForm.find("input[name=custom]").val( order_id );

				$checkoutForm.find("input[name=email]").val( data.email );
                
                $checkoutForm.find("input[name=business]").val( paypal_username );
                
                $checkoutForm.find("input[name=first_name]").val( data.first_name );
                
                $checkoutForm.find("input[name=last_name]").val( data.last_name );
                
                $checkoutForm.find("input[name=address1]").val( data.billingaddress1 );
                
                $checkoutForm.find("input[name=address2]").val( data.billingaddress2 );
                
                $checkoutForm.find("input[name=city]").val( data.billingcity );
                
                $checkoutForm.find("input[name=state]").val( data.billingstate );
                
                $checkoutForm.find("input[name=zip]").val( data.billingzip );
                
                $checkoutForm.find("input[name=country]").val( data.billingcountry );
                
                $checkoutForm.trigger("submit");
		    	
		    	
	    	}
    	});
    
    });
});
$(function( ){
	
	var paypal_username = $("input[name=paypalUn]").val( );
    var user_id         = $("form[name=donationCheckoutForm]").find("input[name=user_id]").val( );
    
    //Get Project Data
    
    setHeight( );
    
    var project_name 	= $("input[name=project_name]").val( );
    var org_name 		= $("input[name=org_name]").val( );
    var $checkout 		= $("#crowdFundingCheckout");
    var $container 		= $("#crowdFundingCheckout").find(".checkout");
    var $incentive 		= $container.find(".incentives").find(".incentive");
    var $contribute 	= $container.find(".just-contribute");
    
    var alias           = "";
    var incentiveId     = "";
    
    //Fund Project Button Action
    
    $(".fund-project").on("click", function( ){
        
        //$(this).prop('disabled', 'true');
        
        alias = $(this).data('alias');
        
        $checkout.css("display","table");
        
    });

    //Individual Incentive click Action
    $(".incentive-list").find(".incentive").on("click", function( ){

        incentiveId = $(this).data("incentive-id-front");
        
        alias = $(".fund-project").data('alias');
        
        $container
        .find("[data-step=1]")
        .find("[data-incentive-id=" + incentiveId + "]")
        .addClass('selected')
        .find(".donation").slideDown( );
        
        $checkout.css("display","table");
    });
    
    /*
    *
    * Step 1 ( Incentive/Funding ) actions 
    *
    */
    
    //Select Incentive on first page of checkout
     $incentive.on("click", function( ){

        if( ! $(this).hasClass('selected') ){
            $container.find(".incentive").each( function( ){

                if( $(this).hasClass('selected') ){
                    $(this).find('.donation').slideUp( );
                    $(this).removeClass('selected');
                }
            });

            if( $container.find(".just-contribute").hasClass('selected') ){

                $(".just-contribute").find('.donation').slideUp( );
                $(".just-contribute").removeClass('selected');
            }

            $(this).addClass('selected');
            $(this).find(".donation").slideDown( );    
        }
    });
    
    //Select the contribute button on the Incentive Page
    $contribute.on("click", function( ){

        if( ! $(this).hasClass('selected') ){
            $container.find(".incentive").each( function( ){

                if( $(this).hasClass('selected') ){
                    $(this).find('.donation').slideUp( );
                    $(this).removeClass('selected');
                }
            });

            $(this).addClass('selected');
            $(this).find(".donation").slideDown( );
        }
    });
    
    //Select the continue button after selecting an incentive or general funding
    
    $incentive.find(".continue").on("click",function( ){

        incentiveId =  $container.find(".selected").data('incentive-id');

        var amount = $container.find(".incentive[data-incentive-id=" + incentiveId + "]").find("input[name=donationAmt]").val( );
        
        //var needShipping = $container.find(".incentive[data-incentive-id=" + incentiveId + "]").find("input[name=shippingRequired]").val( );
        
        //incentive_name = $container.find(".incentive[data-incentive-id=" + incentiveId + "]").find(".title").text( );
        
		var errors = new Array( );

        if( isNaN( parseFloat( amount ) ) ){
           // alert('error');
        }

        if( errors.length == 0 ){

            $.ajax({
                url: "/crowdfunding/storeFund",
                method: "post",
                data: "amount=" + parseFloat(amount.replace(",","")).formatMoney(2, ',', '.') + "&incentive=" + incentiveId,
                success: function( resp ){

                    location.href = "/crowdfunding/" + alias + "/fund";
                }
            });
        }
    });
    
    $contribute.find(".continue").on("click", function( ){
        var amount = $contribute.find("input[name=donationAmt]").val( );

        var errors = new Array( );

        if( isNaN( parseInt( amount ) ) ){
            //alert('error');
        }

        if( errors.length == 0 ){
            
            $.ajax({
                url: "/crowdfunding/storeFund",
                method: "post",
                data: "amount=" + amount + "&incentive=" + incentiveId,
                success: function( resp ){

                    location.href = "/crowdfunding/" + alias + "/fund";
                }
            });
        }
    });
});
$(document).on("pageload", function( ){

	alert('hi');
	
	var hpHeight = $(".indexWrapper").height( );

	console.log( hpHeight );

});
/*!
 *
 * jQuery TE 1.4.0 , http://jqueryte.com/
 * Copyright (C) 2013, Fatih Koca (fattih@fattih.com), (http://jqueryte.com/about)

 * jQuery TE is provided under the MIT LICENSE.
 *
*/
(function(e){e.fn.jqte=function(t){function l(e,t,n,r,i){var s=f.length+1;return f.push({name:e,cls:s,command:t,key:n,tag:r,emphasis:i})}var n=[{title:"Text Format"},{title:"Font Size"},{title:"Color"},{title:"Bold",hotkey:"B"},{title:"Italic",hotkey:"I"},{title:"Underline",hotkey:"U"},{title:"Ordered List",hotkey:"."},{title:"Unordered List",hotkey:","},{title:"Subscript",hotkey:"down arrow"},{title:"Superscript",hotkey:"up arrow"},{title:"Outdent",hotkey:"left arrow"},{title:"Indent",hotkey:"right arrow"},{title:"Justify Left"},{title:"Justify Center"},{title:"Justify Right"},{title:"Strike Through",hotkey:"K"},{title:"Add Link",hotkey:"L"},{title:"Remove Link"},{title:"Cleaner Style",hotkey:"Delete"},{title:"Horizontal Rule",hotkey:"H"},{title:"Source"}];var r=[["p","Normal"],["h1","Header 1"],["h2","Header 2"],["h3","Header 3"],["h4","Header 4"],["h5","Header 5"],["h6","Header 6"],["pre","Preformatted"]];var i=["10","12","16","18","20","24","28"];var s=["0,0,0","68,68,68","102,102,102","153,153,153","204,204,204","238,238,238","243,243,243","255,255,255",null,"255,0,0","255,153,0","255,255,0","0,255,0","0,255,255","0,0,255","153,0,255","255,0,255",null,"244,204,204","252,229,205","255,242,204","217,234,211","208,224,227","207,226,243","217,210,233","234,209,220","234,153,153","249,203,156","255,229,153","182,215,168","162,196,201","159,197,232","180,167,214","213,166,189","224,102,102","246,178,107","255,217,102","147,196,125","118,165,175","111,168,220","142,124,195","194,123,160","204,0,0","230,145,56","241,194,50","106,168,79","69,129,142","61,133,198","103,78,167","166,77,121","153,0,0","180,95,6","191,144,0","56,118,29","19,79,92","11,83,148","53,28,117","116,27,71","102,0,0","120,63,4","127,96,0","39,78,19","12,52,61","7,55,99","32,18,77","76,17,48"];var o=["Web Address","E-mail Address","Picture URL"];var u=e.extend({status:true,css:"jqte",title:true,titletext:n,button:"OK",format:true,formats:r,fsize:true,fsizes:i,funit:"px",color:true,linktypes:o,b:true,i:true,u:true,ol:true,ul:true,sub:true,sup:true,outdent:true,indent:true,left:true,center:true,right:true,strike:true,link:true,unlink:true,remove:true,rule:true,source:true,placeholder:false,br:true,p:true,change:"",focus:"",blur:""},t);e.fn.jqteVal=function(t){e(this).closest("."+u.css).find("."+u.css+"_editor").html(t)};var a=navigator.userAgent.toLowerCase();if(/msie [1-7]./.test(a))u.title=false;var f=[];l("format","formats","","",false);l("fsize","fSize","","",false);l("color","colors","","",false);l("b","Bold","B",["b","strong"],true);l("i","Italic","I",["i","em"],true);l("u","Underline","U",["u"],true);l("ol","insertorderedlist","¾",["ol"],true);l("ul","insertunorderedlist","¼",["ul"],true);l("sub","subscript","(",["sub"],true);l("sup","superscript","&",["sup"],true);l("outdent","outdent","%",["blockquote"],false);l("indent","indent","'",["blockquote"],true);l("left","justifyLeft","","",false);l("center","justifyCenter","","",false);l("right","justifyRight","","",false);l("strike","strikeThrough","K",["strike"],true);l("link","linkcreator","L",["a"],true);l("unlink","unlink","",["a"],false);l("remove","removeformat",".","",false);l("rule","inserthorizontalrule","H",["hr"],false);l("source","displaysource","","",false);return this.each(function(){function B(){if(window.getSelection)return window.getSelection();else if(document.selection&&document.selection.createRange&&document.selection.type!="None")return document.selection.createRange()}function j(e,t){var n,r=B();if(window.getSelection){if(r.anchorNode&&r.getRangeAt)n=r.getRangeAt(0);if(n){r.removeAllRanges();r.addRange(n)}if(!a.match(/msie/))document.execCommand("StyleWithCSS",false,false);document.execCommand(e,false,t)}else if(document.selection&&document.selection.createRange&&document.selection.type!="None"){n=document.selection.createRange();n.execCommand(e,false,t)}q(false,false)}function F(t,n,r){if(v.not(":focus"))v.focus();if(window.getSelection){var i=B(),s,o,u;if(i.anchorNode&&i.getRangeAt){s=i.getRangeAt(0);o=document.createElement(t);e(o).attr(n,r);u=s.extractContents();o.appendChild(u);s.insertNode(o);i.removeAllRanges();if(n=="style")q(e(o),r);else q(e(o),false)}}else if(document.selection&&document.selection.createRange&&document.selection.type!="None"){var a=document.selection.createRange();var f=a.htmlText;var l="<"+t+" "+n+'="'+r+'">'+f+"</"+t+">";document.selection.createRange().pasteHTML(l)}}function q(e,t){var n=I();n=n?n:e;if(n&&t==false){if(n.parent().is("[style]"))n.attr("style",n.parent().attr("style"));if(n.is("[style]"))n.find("*").attr("style",n.attr("style"))}else if(e&&t&&e.is("[style]")){var r=t.split(";");r=r[0].split(":");if(e.is("[style*="+r[0]+"]"))e.find("*").css(r[0],r[1]);R(e)}}function R(t){if(t){var t=t[0];if(document.body.createTextRange){var n=document.body.createTextRange();n.moveToElementText(t);n.select()}else if(window.getSelection){var r=window.getSelection();var n=document.createRange();if(t!="undefined"&&t!=null){n.selectNodeContents(t);r.removeAllRanges();r.addRange(n);if(e(t).is(":empty")){e(t).append(" ");R(e(t))}}}}}function U(){if(!p.data("sourceOpened")){var t=I();var n="http://";W(true);if(t){var r=t.prop("tagName").toLowerCase();if(r=="a"&&t.is("[href]")){n=t.attr("href");t.attr(S,"")}else F("a",S,"")}else y.val(n).focus();g.click(function(t){if(e(t.target).hasClass(u.css+"_linktypetext")||e(t.target).hasClass(u.css+"_linktypearrow"))X(true)});w.find("a").click(function(){var t=e(this).attr(u.css+"-linktype");w.data("linktype",t);E.find("."+u.css+"_linktypetext").html(w.find("a:eq("+w.data("linktype")+")").text());V(n);X()});V(n);y.focus().val(n).bind("keypress keyup",function(e){if(e.keyCode==13){z(h.find("["+S+"]"));return false}});b.click(function(){z(h.find("["+S+"]"))})}else W(false)}function z(t){y.focus();R(t);t.removeAttr(S);if(w.data("linktype")!="2")j("createlink",y.val());else{j("insertImage",y.val());v.find("img").each(function(){var t=e(this).prev("a");var n=e(this).next("a");if(t.length>0&&t.html()=="")t.remove();else if(n.length>0&&n.html()=="")n.remove()})}W();v.trigger("change")}function W(e){Q("["+S+"]:not([href])");h.find("["+S+"][href]").removeAttr(S);if(e){p.data("linkOpened",true);d.show()}else{p.data("linkOpened",false);d.hide()}X()}function X(e){if(e)w.show();else w.hide()}function V(e){var t=w.data("linktype");if(t=="1"&&(y.val()=="http://"||y.is("[value^=http://]")||!y.is("[value^=mailto]")))y.val("mailto:");else if(t!="1"&&!y.is("[value^=http://]"))y.val("http://");else y.val(e)}function J(t){if(!p.data("sourceOpened")){if(t=="fSize")styleField=P;else if(t=="colors")styleField=H;K(styleField,true);styleField.find("a").unbind("click").click(function(){var n=e(this).attr(u.css+"-styleval");if(t=="fSize"){styleType="font-size";n=n+u.funit}else if(t=="colors"){styleType="color";n="rgb("+n+")"}var r=G(styleType);F("span","style",styleType+":"+n+";"+r);K("",false);e("."+u.css+"_title").remove();v.trigger("change")})}else K(styleField,false);W(false)}function K(e,t){var n="",r=[{d:"fsizeOpened",f:P},{d:"cpallOpened",f:H}];if(e!=""){for(var i=0;i<r.length;i++){if(e==r[i]["f"])n=r[i]}}if(t){p.data(n["d"],true);n["f"].slideDown(100);for(var i=0;i<r.length;i++){if(n["d"]!=r[i]["d"]){p.data(r[i]["d"],false);r[i]["f"].slideUp(100)}}}else{for(var i=0;i<r.length;i++){p.data(r[i]["d"],false);r[i]["f"].slideUp(100)}}}function Q(t){h.find(t).each(function(){e(this).before(e(this).html()).remove()})}function G(e){var t=I();if(t&&t.is("[style]")&&t.css(e)!=""){var n=t.css(e);t.css(e,"");var r=t.attr("style");t.css(e,n);return r}else return""}function Y(){Z(true);D.find("a").click(function(){e("*",this).click(function(e){e.preventDefault();return false});et(e(this).text());var t=e(this).attr(u.css+"-formatval");j("formatBlock","<"+t+">");Z(false)})}function Z(e){var t=e?true:false;t=e&&D.data("status")?true:false;if(t||!e)D.data("status",false).slideUp(200);else D.data("status",true).slideDown(200)}function et(e){var t=D.closest("."+u.css+"_tool").find("."+u.css+"_tool_label").find("."+u.css+"_tool_text");if(e.length>10)e=e.substr(0,7)+"...";t.html(e)}function tt(e){var t,n,r;t=e.replace(/\n/gim,"").replace(/\r/gim,"").replace(/\t/gim,"").replace(/ /gim," ");n=[/\<span(|\s+.*?)><span(|\s+.*?)>(.*?)<\/span><\/span>/gim,/<(\w*[^p])\s*[^\/>]*>\s*<\/\1>/gim,/\<div(|\s+.*?)>(.*?)\<\/div>/gim,/\<strong(|\s+.*?)>(.*?)\<\/strong>/gim,/\<em(|\s+.*?)>(.*?)\<\/em>/gim];r=["<span$2>$3</span>","","<p$1>$2</p>","<b$1>$2</b>","<i$1>$2</i>"];for(A=0;A<5;A++){for(var i=0;i<n.length;i++){t=t.replace(n[i],r[i])}}if(!u.p)t=t.replace(/\<p(|\s+.*?)>(.*?)\<\/p>/ig,"<br/>$2");if(!u.br){n=[/\<br>(.*?)/ig,/\<br\/>(.*?)/ig];r=["<p>$1</p>","<p>$1</p>"];for(var i=0;i<n.length;i++){t=t.replace(n[i],r[i])}}if(!u.p&&!u.br)t=t.replace(/\<p>(.*?)\<\/p>/ig,"<div>$1</div>");return t}function nt(){var e=v.text()==""&&v.html().length<12?"":v.html();l.val(tt(e))}function rt(){v.html(tt(l.val()))}function it(t){var n=false,r=I(),i;if(r){e.each(t,function(t,s){i=r.prop("tagName").toLowerCase();if(i==s)n=true;else{r.parents().each(function(){i=e(this).prop("tagName").toLowerCase();if(i==s)n=true})}});return n}else return false}function st(t){for(var n=0;n<f.length;n++){if(u[f[n].name]&&f[n].emphasis&&f[n].tag!="")it(f[n].tag)?p.find("."+u.css+"_tool_"+f[n].cls).addClass(m):e("."+u.css+"_tool_"+f[n].cls).removeClass(m)}if(u.format&&e.isArray(u.formats)){var r=false;for(var i=0;i<u.formats.length;i++){var s=[];s[0]=u.formats[i][0];if(u.formats[i][0].length>0&&it(s)){et(u.formats[i][1]);r=true;break}}if(!r)et(u.formats[0][1])}K("",false);Z(false)}if(!e(this).data("jqte")||e(this).data("jqte")==null||e(this).data("jqte")=="undefined")e(this).data("jqte",true);else e(this).data("jqte",false);if(!u.status||!e(this).data("jqte")){if(e(this).closest("."+u.css).length>0){var t=e(this).closest("."+u.css).find("."+u.css+"_editor").html();var n="";e(e(this)[0].attributes).each(function(){if(this.nodeName!="style")n=n+" "+this.nodeName+'="'+this.nodeValue+'"'});var r=e(this).is("[data-origin]")&&e(this).attr("data-origin")!=""?e(this).attr("data-origin"):"textarea";var i=">"+t;if(r=="input"||r=="option"){t=t.replace(/"/g,"&#34;").replace(/'/g,"&#39;").replace(/</g,"<").replace(/>/g,">");i='value="'+t+'">'}var o=e(this).clone();e(this).data("jqte",false).closest("."+u.css).before(o).remove();o.replaceWith("<"+r+n+i+"</"+r+">")}return}var l=e(this);var r=e(this).prop("tagName").toLowerCase();e(this).attr("data-origin",r);var c=e(this).is("[value]")||r=="textarea"?e(this).val():e(this).html();c=c.replace(/&#34;/g,'"').replace(/&#39;/g,"'").replace(/</g,"<").replace(/>/g,">").replace(/&/g,"&");e(this).after('<div class="'+u.css+'"></div>');var h=e(this).next("."+u.css);h.html('<div class="'+u.css+"_toolbar"+'" role="toolbar" unselectable></div><div class="'+u.css+'_linkform" style="display:none" role="dialog"></div><div class="'+u.css+"_editor"+'"></div>');var p=h.find("."+u.css+"_toolbar");var d=h.find("."+u.css+"_linkform");var v=h.find("."+u.css+"_editor");var m=u.css+"_tool_depressed";d.append('<div class="'+u.css+'_linktypeselect" unselectable></div><input class="'+u.css+'_linkinput" type="text/css" value=""><div class="'+u.css+'_linkbutton" unselectable>'+u.button+'</div> <div style="height:1px;float:none;clear:both"></div>');var g=d.find("."+u.css+"_linktypeselect");var y=d.find("."+u.css+"_linkinput");var b=d.find("."+u.css+"_linkbutton");g.append('<div class="'+u.css+'_linktypeview" unselectable></div><div class="'+u.css+'_linktypes" role="menu" unselectable></div>');var w=g.find("."+u.css+"_linktypes");var E=g.find("."+u.css+"_linktypeview");var S=u.css+"-setlink";v.after('<div class="'+u.css+"_source "+u.css+'_hiddenField"></div>');var x=h.find("."+u.css+"_source");l.appendTo(x);if(r!="textarea"){var n="";e(l[0].attributes).each(function(){if(this.nodeName!="type"&&this.nodeName!="value")n=n+" "+this.nodeName+'="'+this.nodeValue+'"'});l.replaceWith("<textarea "+n+">"+c+"</textarea>");l=x.find("textarea")}v.attr("contenteditable","true").html(c);for(var T=0;T<f.length;T++){if(u[f[T].name]){var N=f[T].key.length>0?u.titletext[T].hotkey!=null&&u.titletext[T].hotkey!="undefined"&&u.titletext[T].hotkey!=""?" (Ctrl+"+u.titletext[T].hotkey+")":"":"";var C=u.titletext[T].title!=null&&u.titletext[T].title!="undefined"&&u.titletext[T].title!=""?u.titletext[T].title+N:"";p.append('<div class="'+u.css+"_tool "+u.css+"_tool_"+f[T].cls+'" role="button" data-tool="'+T+'" unselectable><a class="'+u.css+'_tool_icon" unselectable></a></div>');p.find("."+u.css+"_tool[data-tool="+T+"]").data({tag:f[T].tag,command:f[T].command,emphasis:f[T].emphasis,title:C});if(f[T].name=="format"&&e.isArray(u.formats)){var k=u.formats[0][1].length>0&&u.formats[0][1]!="undefined"?u.formats[0][1]:"";p.find("."+u.css+"_tool_"+f[T].cls).find("."+u.css+"_tool_icon").replaceWith('<a class="'+u.css+'_tool_label" unselectable><span class="'+u.css+'_tool_text" unselectable>'+k+'</span><span class="'+u.css+'_tool_icon" unselectable></span></a>');p.find("."+u.css+"_tool_"+f[T].cls).append('<div class="'+u.css+'_formats" unselectable></div>');for(var L=0;L<u.formats.length;L++){p.find("."+u.css+"_formats").append("<a "+u.css+'-formatval="'+u.formats[L][0]+'" class="'+u.css+"_format"+" "+u.css+"_format_"+L+'" role="menuitem" unselectable>'+u.formats[L][1]+"</a>")}p.find("."+u.css+"_formats").data("status",false)}else if(f[T].name=="fsize"&&e.isArray(u.fsizes)){p.find("."+u.css+"_tool_"+f[T].cls).append('<div class="'+u.css+'_fontsizes" unselectable></div>');for(var L=0;L<u.fsizes.length;L++){p.find("."+u.css+"_fontsizes").append("<a "+u.css+'-styleval="'+u.fsizes[L]+'" class="'+u.css+"_fontsize"+'" style="font-size:'+u.fsizes[L]+u.funit+'" role="menuitem" unselectable>Abcdefgh...</a>')}}else if(f[T].name=="color"&&e.isArray(s)){p.find("."+u.css+"_tool_"+f[T].cls).append('<div class="'+u.css+'_cpalette" unselectable></div>');for(var A=0;A<s.length;A++){if(s[A]!=null)p.find("."+u.css+"_cpalette").append("<a "+u.css+'-styleval="'+s[A]+'" class="'+u.css+"_color"+'" style="background-color: rgb('+s[A]+')" role="gridcell" unselectable></a>');else p.find("."+u.css+"_cpalette").append('<div class="'+u.css+"_colorSeperator"+'"></div>')}}}}w.data("linktype","0");for(var T=0;T<3;T++){w.append("<a "+u.css+'-linktype="'+T+'" unselectable>'+u.linktypes[T]+"</a>");E.html('<div class="'+u.css+'_linktypearrow" unselectable></div><div class="'+u.css+'_linktypetext">'+w.find("a:eq("+w.data("linktype")+")").text()+"</div>")}var O="";if(/msie/.test(a))O="-ms-";else if(/chrome/.test(a)||/safari/.test(a)||/yandex/.test(a))O="-webkit-";else if(/mozilla/.test(a))O="-moz-";else if(/opera/.test(a))O="-o-";else if(/konqueror/.test(a))O="-khtml-";else O="";if(u.placeholder&&u.placeholder!=""){h.prepend('<div class="'+u.css+'_placeholder" unselectable><div class="'+u.css+'_placeholder_text">'+u.placeholder+"</div></div>");var M=h.find("."+u.css+"_placeholder");M.click(function(){v.focus()})}h.find("[unselectable]").css(O+"user-select","none").addClass("unselectable").attr("unselectable","on").on("selectstart mousedown",false);var _=p.find("."+u.css+"_tool");var D=p.find("."+u.css+"_formats");var P=p.find("."+u.css+"_fontsizes");var H=p.find("."+u.css+"_cpalette");var I=function(){var t,n;if(window.getSelection){n=getSelection();t=n.anchorNode}if(!t&&document.selection&&document.selection.createRange&&document.selection.type!="None"){n=document.selection;var r=n.getRangeAt?n.getRangeAt(0):n.createRange();t=r.commonAncestorContainer?r.commonAncestorContainer:r.parentElement?r.parentElement():r.item(0)}if(t){return t.nodeName=="#text"?e(t.parentNode):e(t)}else return false};_.unbind("click").click(function(t){if(e(this).data("command")=="displaysource"&&!p.data("sourceOpened")){p.find("."+u.css+"_tool").addClass(u.css+"_hiddenField");e(this).removeClass(u.css+"_hiddenField");p.data("sourceOpened",true);l.css("height",v.outerHeight());x.removeClass(u.css+"_hiddenField");v.addClass(u.css+"_hiddenField");l.focus();W(false);K("",false);Z();if(u.placeholder&&u.placeholder!="")M.hide()}else{if(!p.data("sourceOpened")){if(e(this).data("command")=="linkcreator"){if(!p.data("linkOpened"))U();else{W(false);Z(false)}}else if(e(this).data("command")=="formats"){if(e(this).data("command")=="formats"&&!e(t.target).hasClass(u.css+"_format"))Y();K("",false);if(v.not(":focus"))v.focus()}else if(e(this).data("command")=="fSize"||e(this).data("command")=="colors"){if(e(this).data("command")=="fSize"&&!e(t.target).hasClass(u.css+"_fontsize")||e(this).data("command")=="colors"&&!e(t.target).hasClass(u.css+"_color"))J(e(this).data("command"));Z(false);if(v.not(":focus"))v.focus()}else{if(v.not(":focus"))v.focus();j(e(this).data("command"),null);K("",false);Z(false);X();e(this).data("emphasis")==true&&!e(this).hasClass(m)?e(this).addClass(m):e(this).removeClass(m);x.addClass(u.css+"_hiddenField");v.removeClass(u.css+"_hiddenField")}}else{p.data("sourceOpened",false);p.find("."+u.css+"_tool").removeClass(u.css+"_hiddenField");x.addClass(u.css+"_hiddenField");v.removeClass(u.css+"_hiddenField")}if(u.placeholder&&u.placeholder!="")v.html()!=""?M.hide():M.show()}v.trigger("change")}).hover(function(t){if(u.title&&e(this).data("title")!=""&&(e(t.target).hasClass(u.css+"_tool")||e(t.target).hasClass(u.css+"_tool_icon"))){e("."+u.css+"_title").remove();h.append('<div class="'+u.css+'_title"><div class="'+u.css+'_titleArrow"><div class="'+u.css+'_titleArrowIcon"></div></div><div class="'+u.css+'_titleText">'+e(this).data("title")+"</div></div>");var n=e("."+u.css+"_title:first");var r=n.find("."+u.css+"_titleArrowIcon");var i=e(this).position();var s=i.left+e(this).outerWidth()-n.outerWidth()/2-e(this).outerWidth()/2;var o=i.top+e(this).outerHeight()+5;n.delay(400).css({top:o,left:s}).fadeIn(200)}},function(){e("."+u.css+"_title").remove()});var ot=null;v.bind("keypress keyup keydown drop cut copy paste DOMCharacterDataModified DOMSubtreeModified",function(){if(!p.data("sourceOpened"))e(this).trigger("change");X();if(e.isFunction(u.change))u.change();if(u.placeholder&&u.placeholder!="")e(this).text()!=""?M.hide():M.show()}).bind("change",function(){if(!p.data("sourceOpened")){clearTimeout(ot);ot=setTimeout(nt,10)}}).keydown(function(e){if(e.ctrlKey){for(var t=0;t<f.length;t++){if(u[f[t].name]&&e.keyCode==f[t].key.charCodeAt(0)){if(f[t].command!=""&&f[t].command!="linkcreator")j(f[t].command,null);else if(f[t].command=="linkcreator")U();return false}}}}).bind("mouseup keyup",st).focus(function(){if(e.isFunction(u.focus))u.focus();h.addClass(u.css+"_focused");if(/opera/.test(a)){var t=document.createRange();t.selectNodeContents(v[0]);t.collapse(false);var n=window.getSelection();n.removeAllRanges();n.addRange(t)}}).focusout(function(){_.removeClass(m);K("",false);Z(false);X();if(e.isFunction(u.blur))u.blur();h.removeClass(u.css+"_focused");if(e.isArray(u.formats))et(u.formats[0][1])});l.bind("keydown keyup",function(){setTimeout(rt,0);e(this).height(e(this)[0].scrollHeight);if(e(this).val()=="")e(this).height(0)}).focus(function(){h.addClass(u.css+"_focused")}).focusout(function(){h.removeClass(u.css+"_focused")})})}})(jQuery)
<script type='text/javascript'>
    $(function( ){

        /**
        General Information Modal JS Events
        **/

        /** Set up text editor options **/
        var jqteOptions = {
            format: false,
            sub:    false,
            sup:    false,
            ol:     false,
            ul:     false,
            source: false,
            link:   false,
            class: "jqteOverRide", 
        };

        var userId = "";

        /** Check if userId is populated.  If so, populate Above variable **/
        if( $("input[name=userId]").length > 0 ){
            userId = $("input[name=userId]").val( );
        }

        orgId = $("input[name=orgId]").val( );

        /** Set up text editor for Brief Description **/
        $("textarea[name=brief-description]").jqte(jqteOptions);

        /** Set up text editor for Mission Statement **/
        $("textarea[name=mission-statement]").jqte(jqteOptions);

        /** Set up text editor for About Us **/
        $("textarea[name=about-us]").jqte(jqteOptions);    

        /** Event for Saving General Info **/
        $(".save-general-info").on("click", function( ){


            var missionStatement = $("textarea[name=mission-statement]").val( )
            
            var aboutUs             = $("textarea[name=about-us]").val( );

            var briefDescription    = $("textarea[name=brief-description").val( );

            var orgName             = $("input[name=org-name").val( );

            $.ajax({
                url: "/organization/updateGeneralInfo",
                method: "POST",
                data: encodeURI( "missionStatement=" + missionStatement + "&aboutUs=" + aboutUs + "&briefDescription=" + briefDescription + "&orgName=" + orgName + "&orgId=" + orgId),
                dataType: "json",
                beforeSend: function( ){

                },
                success: function( resp ){
                    if( resp.status ){
                        $(".upload-generalInfo-success").show( );

                        $(".org-header").find('.header-content').find('.org-name').html( orgName );
                        $(".about-us").find(".mission-content").html( missionStatement );
                        $(".about-us").find(".aboutUs-content").html( aboutUs );
                    }else{
                        $(".upload-generalInfo-error").find(".error-list").html( resp.error );
                        $(".upload-generalInfo-error").show( );
                    }
                }
            });
        });

        $(".generalInfoModal").on("hide.bs.modal", function( ){
            $(".upload-generalInfo-success").hide( );
            $(".upload-generalInfo-error").find(".error-list").html( "" );
            $(".upload-generalInfo-error").hide( );
        });


        /**
        Cause Modal
        **/

        var cause = {};
        var subcauses = [];
        var countries = [];
        var xhr = null;
        var action = "";
        var causeChanges = false;

        $("input[name=country-text]").on("keyup", function( ){

            var parent = $(this).parent( );

            var inputCoords = $(this).offset( );

            var inputWidth  = $(this).width( ) + 24;

            var inputHeight = $(this).height( ) + 12;

            var tmpCountry = {};

            if( $(this).val( ).length >= 3 ){
                var query = $(this).val( );

                if( xhr ){
                    if( xhr.readyState != 4 ){
                        xhr.abort( );
                    }
                }

                xhr = $.ajax({
                    url: "/findCountry/" + query,
                    dataType: "json",
                    beforeSend: function( ){
                        $("input[name=country-text]").css({
                           background: "url(/images/loading1.gif) top right no-repeat",
                           backgroundSize: "contain" 
                        });
                    },
                    success: function( resp ){

                        $("input[name=country-text]").css("background", "");

                        var countryList = "<div style='top: " + inputHeight + "px; width: " + inputWidth + "px;' class='country-search-container'>";

                        for( x in resp ){
                            countryList += "<div class='country-selection' data-country-id='" + resp[x].country_id + "'>" + resp[x].country_name + "</div>";
                        }
                        countryList += "</div>";

                        parent.append( countryList );
                    }
                });
            }
        });

        $("textarea[name=cause-description-textarea]").jqte(jqteOptions);    


        //event for clicking on a cause (adding)
        $("input[name=cause-option]").on("change", function( ){

            var causeId = $(this).prop("id").split("-");

            causeId = causeId[2];

            if( $(".currentCauseList").find("div[data-org-cause=" + causeId + "]").length > 0 ){
                $(".currentCauseList").find("div[data-org-cause=" + causeId + "]").trigger("click");

            }else{


                cause = {};
                subcauses = [];
                countries = [];
                action = "add";

                clearSubCauses( );

                clearOtherCauses( causeId );

                clearCountries( );

                cause.cause_id = causeId;

                $(".availableSubCauseList").find("label").hide( );

                $(".availableSubCauseList").find("label[data-parent-id=" + causeId + "]").show( );

                $(".cause-action").html( "Add New Cause").addClass("btn-primary").removeClass('btn-success');

                $("textarea[name=cause-description-textarea]").jqteVal("");

                if( $(".subCauseWrapper").hasClass("hidden") ){
                    $(".subCauseWrapper").removeClass( "hidden" );
                }    
            }
        });

        //event for clicking on an already selected Cause ( Updating )
        $(".org-cause").on({
            click: function( ){

                cause = {};
                subcauses = [];
                countries = [];
                action = "update";

                $(".main-cause-heading").html( "Update " + $(this).find(".cause-title").html( ) );

                clearSubCauses( );

                var causeId = $(this).data("org-cause");
                var orgCauseId = $(this).data("org-cause-id");

                cause.cause_id = causeId;
                cause.org_cause_id = orgCauseId;

                clearOtherCauses( causeId );

                clearCountries( );

                $(".availableCauseList label").each( function( ){
                    
                    if( $(this).find("input[type=radio]").prop("id") == "cause-option-" + causeId ){

                        $(this).find("input[type=radio]").prop("checked", true);

                        $(this).find("input[type=radio]").parent( ).addClass('active');

                        $(".availableSubCauseList").find("label[data-parent-id=" + causeId + "]").show( );

                        if( $(".subCauseWrapper").hasClass("hidden") ){
                            $(".subCauseWrapper").removeClass( "hidden" );
                        }

                        $("textarea[name=cause-description-textarea]").jqteVal( $(".currentCauseList").find(".org-cause[data-org-cause=" + causeId + "]").find(".cause-text").html( ) );

                        $(".cause-action").html( "Update Cause Data ").addClass("btn-success").removeClass('btn-primary');
                    }
                });

                $(this).find(".sub-cause-list span").each( function( ){

                    var subCauseId = $(this).data('subcause-id');

                    $(".availableSubCauseList label").find("input[type=checkbox][id=sub-cause-option-" + subCauseId + "]").prop("checked", true);

                    $(".availableSubCauseList label").find("input[type=checkbox][id=sub-cause-option-" + subCauseId + "]").parent( ).addClass('active');
                });

                $(this).find(".cause-country-list span").each( function( ){


                    var id = $(this).data('cause-country');
                    var name = $(this).html( );

                     countryPill = "<div class='country-pill'><button class='btn btn-primary' type='button' data-country-id='" + id + "' data-country-name='" + name + "'>" + name + "<span class='badge'>X</span></div>";

                     countries.push( {id: id, name: name});

                     $(".country-list").append( countryPill ).show( ); 
                });
            },
            mouseover: function( ){
                $(this).find(".cause-remove-button").removeClass('hidden');
            },
            mouseleave: function( ){
                $(this).find(".cause-remove-button").addClass('hidden');
            }
        });

        $(".cause-remove-button").on("click", function( ){

            var _token = $("input[name=_token]").val( );

            $.ajax({
                url: "/organization/removeCause",
                method: "POST",
                data: "causeId=" + $(this).find("button").data("cause-id") + "&orgId=" + orgId + "&_token=" + _token,
                dataType: "json",
                sucess: function( resp ){

                    if( resp.status ){

                        causeChanges = true;
                        $(".org-cause[data-org-cause]").remove( );

                        //clear causes
                        clearOtherCauses( 0 );
                        //clear subcauses
                        clearSubCauses( );
                        //clear countries
                        clearCountries( );
                        //clear textarea
                        $("textarea[name=cause-description-textarea]").jqteVal( "" );
                        //update page causes

                    }
                }
            });
        });

        //Event for clicking a sub-cause
        $("input[name=sub-cause-option]").on("change", function( ){

            if( $(this).is(":checked") ){
                var subCauseId = $(this).prop("id").split("-")[3];

                subcauses.push( subCauseId );
            }else{
                var index = subcauses.splice( subcauses.indexOf( subCauseId ), 1);
            }
        });

        //event for clicking on a country that is in the country list
        $(document).on("click", ".country-selection", function( ){

            tmpCountry = {};

            tmpCountry = {"name": $(this).text( ), "id": $(this).data('country-id')};
            
            $(".country-search-container").remove( );

            $("input[name=country-text]").val( tmpCountry.name );
        });

        //Add the country chosen from above event
        $("#add-country").on("click", function( ){

            var countryPill = "<div class='country-pill'>";

            countries.push( tmpCountry ); 

            countryPill += "<button class='btn btn-primary' type='button' data-country-id='" + tmpCountry.id + "' data-country-name='" + tmpCountry.name + "'>" + tmpCountry.name + "<span class='badge remove-country'>X</span></div>";

            tmpCountry = {};

            $("input[name=country-text]").val( "" );

            $(".country-list").append( countryPill ).show( );    
            
        });

        $(".cause-action").on("click", function( ){

            var _token = $("input[name=_token").val( );

            cause.sub_causes = subcauses;

            cause.countries = countries;

            cause.desc = $("textarea[name=cause-description-textarea]").val( );

            cause.action = action;

            cause.orgId  = orgId;

            cause._token = _token;

            var url = "";

            if( action == "add" ){
                url = "/organization/addCause";
            }else{

                url = "/organization/updateCause";
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: cause,
                dataType: 'json',
                beforeSend: function( ){

                },
                success: function( resp ){

                    if( resp.status ){

                        causeChanges = true;

                        var subCauseList = "";
                        var countryList = "";

                        if( cause.action == "update" ){
                            //update 'selected causes'
                            $(".availableSubCauseList label").each( function( ){

                                if( $(this).find("input[type=checkbox]").is(":checked") ){

                                    var subCauseId = $(this).find("input[type=checkbox]").prop("id").split("-")[3];

                                    if( subCauseList == "" ){
                                        subCauseList = "<span class='org-sub-cause' data-subcause-id='" + subCauseId + "'>" + $(this).find(".cause-name").html( ) + "</span>";
                                    }else{
                                        subCauseList += ", " + "<span class='org-sub-cause' data-subcause-id='" + subCauseId + "'>" + $(this).find(".cause-name").html( ) + "</span>";
                                    }
                                }
                            });

                            $(".org-cause[data-org-cause=" + cause.cause_id + "]")
                            .find(".sub-cause-list")
                            .html( subCauseList );


                            $(".country-list .country-pill").each(function( ){

                                if( countryList == "" ){
                                    countryList = "<span class='org-cause-country' data-cause-country='" + $(this).find("button").data("country-id") + "'>" + $(this).find("button").data("country-name") + "</span>";
                                }else{
                                    countryList += "<span class='org-cause-country' data-cause-country='" + $(this).find("button").data("country-id") + "'>," + $(this).find("button").data("country-name") + "</span>";
                                }
                            });

                            $(".org-cause[data-org-cause=" + cause.cause_id + "]")
                            .find(".cause-country-list")
                            .html( countryList );

                            
                        }else{

                            var $causeEl = $(".availableCauseList label").find( "input[type=radio]:checked").parent( );

                            var causeName = $causeEl.find(".cause-name").text( );
                            var causeIcon = $causeEl
                                            .find(".cause-icon")
                                            .find("i")
                                            .prop("class")
                                            .split(" ")[0];

                            $(".availableSubCauseList label").each( function( ){

                                if( $(this).find("input[type=checkbox]").is(":checked") ){

                                    var subCauseId = $(this).find("input[type=checkbox]").prop("id").split("-")[3];

                                    if( subCauseList == "" ){
                                        subCauseList = "<span class='org-sub-cause' data-subcause-id='" + subCauseId + "'>" + $(this).find(".cause-name").html( ) + "</span>";
                                    }else{
                                        subCauseList += ", " + "<span class='org-sub-cause' data-subcause-id='" + subCauseId + "'>" + $(this).find(".cause-name").html( ) + "</span>";
                                    }
                                }
                            });

                            $(".country-list .country-pill").each(function( ){

                                if( countryList == "" ){
                                    countryList = "<span class='org-cause-country' data-cause-country='" + $(this).find("button").data("country-id") + "'>" + $(this).find("button").data("country-name") + "</span>";
                                }else{
                                    countryList += "<span class='org-cause-country' data-cause-country='" + $(this).find("button").data("country-id") + "'>," + $(this).find("button").data("country-name") + "</span>";
                                }
                            });

                            var newCause = "<div class='col-lg-3 col-md-3 col-sm-3 org-cause' data-org-cause='" + cause.cause_id + "' data-org-cause-id='" + resp.org_cse_id + "'>";

                            newCause += "<div class='row'>";
                            newCause += "   <div class='col-lg-3 col-md-3 col-sm-3'>";
                            newCause += "       <i class='" + causeIcon + " pwi-icon-2em'></i>";
                            newCause += "   </div>";
                            newCause += "   <div class='col-lg-9 col-lg-9 col-sm-9' >";
                            newCause += "       <div class='cause-title' >" + causeName + "</div>";
                            newCause += "       <div class='sub-cause-list'>" + subCauseList + "</div>";
                            newCause += "       <div class='cause-country-list'>" + countryList + "</div>";
                                
                                            
                            newCause += "    </div>"
                            newCause += "</div>";
                            newCause += "<div class='hidden cause-remove-button text-center row'>";
                            newCause += "   <div class='col-lg-12 col-md-12 col-sm-12'>";
                            newCause += "       <button type='button' class='btn btn-danger padding-2 margin-top-2' data-toggle='button' aria-pressed='false' autocomplete='off' data-cause-id='" + cause.cause_id + "'>Remove Cause</button>";
                            newCause += "   </div>";
                            newCause += "</div>";
                            newCause += " <div class='cause-text' id='org-cause-description'>" + cause.desc +"</div>"
                            newCause += "</div>";

                            $(".currentCauseList").append( newCause );
                        }

                        //clear causes
                        clearOtherCauses( 0 );
                        //clear subcauses
                        clearSubCauses( );
                        //clear countries
                        clearCountries( );
                        //clear textarea
                        $("textarea[name=cause-description-textarea]").jqteVal( "" );

                        cause = {};
                        subcauses = [];
                        countries = [];
                    }else{

                    }
                }
            });
        });

        $("#orgCauseModal").on("hidden.bs.modal", function( ){

            if( causeChanges ){
                location.reload( );
            }else{
                //clear causes
                clearOtherCauses( 0 );
                //clear subcauses
                clearSubCauses( );
                //clear countries
                clearCountries( );
                //clear textarea
                $("textarea[name=cause-description-textarea]").jqteVal( "" );

                cause       = {};   
                subcauses   = [];
                countries   = [];
            }
        });

        $(document).on("click", ".remove-country", function(){

            var countryId = $(this).parent( ).data('country-id');
            $(this).parent( ).parent( ).remove( );

            for( x in countries ){
                if( countries[x]["id"] == countryId ){
                    countries.splice(x, 1);
                }
            }

            if( countries.length == 0 ){
                $(".country-list").hide( );
            }
        });

        /** End Cause Modal **/

        /** Begin Contact Info Modal **/

        $(".save-contact-info").on("click", function( ){

            var queryString = $("form[name=contact-form-update]").serialize( );

            $.ajax({
                url: "/organization/updateContactInfo",
                method: "POST",
                data: queryString,
                dataType: "json",
                beforeSend: function( ){

                },
                success: function( resp ){

                    if( resp.status ){

                        var $form = $("form[name=contact-form-update]");

                        var orgWebUrl = $form.find("input[name=org_web_url]").val( );

                        if( orgWebUrl.length > 30 ){

                            $("#orgWebUrl").find("a").html( orgWebUrl.substring( 0, 29 ) + "...");
                        }else{
                            $("#orgWebUrl").find("a").html( orgWebUrl );
                        }

                        if( $("#orgWebUrl").parent( ).hasClass('hidden') && orgWebUrl != "" ){
                            $("#orgWebUrl").parent( ).removeClass('hidden');
                        }else if( ( ! $("#orgWebUrl").parent( ).hasClass('hidden') ) && orgWebUrl == "" )  {
                            $("#orgWebUrl").parent( ).addClass('hidden');
                        }

                        $("#orgPhone").html( $form.find("input[name=org_phone]").val( ) );

                        if( $("#orgPhone").parent( ).hasClass('hidden') && $form.find("input[name=org_phone]").val( ) != "" ){
                            $("#orgPhone").parent( ).removeClass( 'hidden' );
                        }else if( ( ! $("#orgPhone").parent( ).hasClass('hidden') ) && $form.find("input[name=org_phone]").val( ) == "" ){
                            $("#orgPhone").parent( ).addClass("hidden");
                        } 

                        $("#orgEmail").html( $form.find("input[name=org_email]").val( ) );

                        var address = $form.find("input[name=org_address1]").val( ) + "<br >";

                        if( $form.find("input[name=org_address2]").val( ) != "" ){
                            address += $form.find("input[name=org_address2]").val( ) + "<br />";
                        }

                        address += $form.find("input[name=org_city]").val( ) + ", " + resp.state + " " + $form.find("input[name=org_zip]").val( );

                        $("#orgStreetAddress").html( address );

                        $(".upload-contactInfo-success").show( );
                    }else{
                        $(".upload-contactInfo-error").show( );
                    }
                }
            });
        });

        $(".contactInfoModal").on("hide.bs.modal", function( ){
            $(".upload-contactInfo-success").show( );
            $(".upload-contactInfo-error").show( );
        });

        /** End Contact Info **/
        /** Photo/Video Upload **/

        $hasChangedMedia = false;

        $(".droppable-box").on("drag dragstart dragend dragover dragenter dragleave drop", function( e ){
            e.preventDefault( );
            e.stopPropagation( );
        })
        .on('drop', function( e ){
            var droppedFile = e.originalEvent.dataTransfer.files;

            var form_data = new FormData();
            form_data.append('file', droppedFile[0]);
            form_data.append('type', 'photo');
            form_data.append('id', orgId);
            form_data.append('userId', userId);

            var token = $(document).find("input[name=_token]").val( );

            form_data.append('_token', token);
            
            $.ajax({
                url: "/organization/uploadimage",
                method: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function( ){
                    $(".upload-photo-info").fadeIn( );
                    $(".upload-photo-success").hide( );
                    $(".upload-photo-error").hide( );
                },
                success: function( resp ){

                    hasChangedMedia = true;

                    $(".upload-photo-info").fadeOut( );
                    
                    if( resp.status ){
                        
                        $(".upload-photo-success").fadeIn( );

                        var newElement = "<div class='box' style='background: url(" + resp.url + ") top left no-repeat; background-size: cover;'></div>";

                        $(".photo_list").find("div:first").after( newElement );

                        setTimeout( function( ){
                            $(".upload-photo-success").fadeOut( );
                        }, 3000);

                    }else{
                        
                        $(".upload-photo-error").fadeIn( );

                        setTimeout( function( ){
                            $(".upload-photo-error").fadeOut( );
                        }, 3000);
                    }

                }
            });
        }).on('click', function( e ){
            $("#orgPic").trigger("click");
        });

        $("#orgPic").on("change", function( ){

            var file_data = this.files[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('type', 'photo');
            form_data.append('id', orgId);
            form_data.append('userId', userId);
            
            var token = $(document).find("input[name=_token]").val( );

            form_data.append('_token', token);

            $.ajax({
                url: "/organization/uploadimage",
                method: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function( ){
                    $(".upload-photo-info").fadeIn( );
                    $(".upload-photo-success").hide( );
                    $(".upload-photo-error").hide( );
                },
                success: function( resp ){

                    hasChangedMedia = true;

                    $(".upload-photo-info").fadeOut( );

                    if( resp.status ){
                        
                        $(".upload-photo-success").fadeIn( );

                        var newElement = "<div class='box' style='background: url(" + resp.url + ") top left no-repeat; background-size: cover;'></div>";

                        $(".photo_list").find("div:first").after( newElement );

                        setTimeout( function( ){
                            $(".upload-photo-success").fadeOut( );
                        }, 3000);

                    }else{
                        
                        $(".upload-photo-error").fadeIn( );

                        setTimeout( function( ){
                            $(".upload-photo-error").fadeOut( );
                        }, 3000);
                    }
                },
                error: function( ){
                    $(".upload-photo-error").fadeIn( );

                    setTimeout( function( ){
                        $(".upload-photo-error").fadeOut( );
                    }, 3000);
                }
            });
        });

        $(".save-video").on("click", function( ){

            $.ajax({
                url: "/organization/saveVideo",
                method: "POST",
                data: "videoUrl=" + $("input[name=video_url]").val( ) + "&_token=" + $(document).find("input[name=_token]").val( ) + "&orgId=" + orgId,
                dataType: "json",
                beforeSend: function( ){
                    $(".upload-video-info").show( );
                },
                success: function( resp ){
                    
                    $(".upload-video-info").hide( );

                    if( resp.status ){

                        hasChangedMedia = true;

                        $(".video-list div:first").prepend("<div class='box' style='background: url( " + resp.img + " ) top left no-repeat; background-size: cover;'></div>");

                        $(".upload-video-success").find(".msg").html(" Your video has been successfully uploaded.").fadeIn );

                        $("input[name=video_url]").val( "" );

                        setTimeout( function( ){
                            $(".upload-video-success").fadeOut( );
                        }, 3000);

                    }else{

                        $(".upload-video-error").find(".msg").append( "<br />" + resp.msg );
                        $(".upload-photo-error").fadeIn( );

                        setTimeout( function( ){
                            $(".upload-video-error").find(".msg").html( "There was an error saving your video" );
                            $(".upload-photo-error").fadeOut( );
                        }, 5000);
                    }
                }
            });
        });

        $(".box").on({
            mouseenter: function( ){
                $(this).find(".overlay").show( );
            },
            mouseleave: function( ){
                $(this).find(".overlay").hide( );
            }
        });

        $(".remove-photo").on("click", function( ){

            var $parentBox = $(this).parent( ).parent( );

            var fileId = $(this).data('file-id');

            $.ajax({
                url: "/organization/removePhoto",
                method: "POST",
                data: "fileId=" + fileId + "&orgId=" + orgId + "&_token=" + $(document).find("input[name=_token]").val( ),
                dataType: "json",
                success: function( resp ){

                    hasChangedMedia = true;

                    if( resp.status ){
                        $parentBox.remove( );    
                    }else{
                        $(".upload-photo-error").find(".msg").html("There was an error deleting the photo.");
                        $(".upload-photo-error").fadeIn( );

                        setTimeout( function( ){
                            $(".upload-photo-error").find(".msg").html("There was an error uploading your photo.");
                            $(".upload-photo-error").fadeOut( ) ;
                        }, 3000);
                    }
                }
            });
        });

        $("#mediaModal").on("hide.bs.modal", function( ){

            if( hasChangedMedia ){
                location.reload( );    
            }
        });

        $("#mediaModal").on("show.bs.modal", function( e ){

            console.log( e );
            e.preventDefault( );
            e.stopPropagation( );

        });


        function clearSubCauses( ){
            $(".availableSubCauseList label").each( function( ){
                $(this).find("input[type=radio]").prop("checked", false);
                $(this).removeClass("active");
                $(this).hide( );
            });
        }

        function clearOtherCauses( id ){
            $(".availableCauseList label").each( function( ){

                if( id > 0 ){

                    var thisCauseId = $(this).find("input[type=radio]").prop("id").split("-")[2];

                    if( thisCauseId != id ){
                        $(this).find("input[type=radio]").prop("checked", false);
                        $(this).find("input[type=radio]").parent( ).removeClass('active');
                    }
                }else{
                    $(this).find("input[type=radio]").prop("checked", false);
                    $(this).find("input[type=radio]").parent( ).removeClass('active');
                }
            });
        }

        function clearCountries( ){
            $(".country-list").html("").hide( );
        }
    });
</script>
//# sourceMappingURL=all.js.map
