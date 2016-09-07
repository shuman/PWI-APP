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
//# sourceMappingURL=purchase-mobile.js.map
