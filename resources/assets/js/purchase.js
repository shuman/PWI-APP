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
						
						if( parseInt( resp.price ) > 0 ){
							$(".price").data("price", resp.price );
							$(".price").html("$" + parseFloat( resp.price ).formatMoney(2, ',', '.') );	
						}
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
