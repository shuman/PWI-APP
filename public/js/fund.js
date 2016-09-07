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
//# sourceMappingURL=fund.js.map
