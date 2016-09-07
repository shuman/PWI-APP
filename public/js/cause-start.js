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
//# sourceMappingURL=cause-start.js.map
