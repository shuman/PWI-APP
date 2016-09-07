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