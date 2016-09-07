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
//# sourceMappingURL=fund-mobile.js.map
