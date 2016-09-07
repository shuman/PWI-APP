
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