function setHeight( ){
	var footerHeight = parseInt( $("footer").height( ) );
    var navHeight = parseInt( $("nav").height( ) );
    var docHeight = parseInt( $(document).height( ) );
    var screenHeight = 0;
    
    if( footerHeight > navHeight ){
	    screenHeight = ( ( footerHeight + navHeight ) + docHeight );
	    
    }else{
	    screenHeight = ( ( navHeight + footerHeight ) + docHeight );
    }
    
    $("#wrapper > .container").css("min-height", docHeight + "px");
    
    $("#wrapper").css("min-height", screenHeight + "px");

    
    $(window).on("resize", function( ){
        
        $(".modal").height( $(document).height( ) );
        
        var footerHeight = parseInt( $("footer").height( ) );
	    var navHeight = parseInt( $("nav").height( ) );
	    var docHeight = parseInt( $(document).height( ) );
	    var screenHeight = 0;
	    
	    if( footerHeight > navHeight ){ 
		    screenHeight = ( ( footerHeight + navHeight ) + docHeight );
		    
	    }else{
		    screenHeight = ( ( navHeight + footerHeight ) + docHeight );
	    }
        
		$("#wrapper > .container").css("min-height", parseInt( docHeight) + "px");
    });
}