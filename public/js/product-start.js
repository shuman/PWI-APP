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
						
						if( parseInt( resp.price ) > 0 ){
							$(".price").data("price", resp.price);
							$(".price").html("$" + parseFloat( resp.price ) + ".00");	
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
//# sourceMappingURL=product-start.js.map
