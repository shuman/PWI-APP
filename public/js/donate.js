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
//# sourceMappingURL=donate.js.map
