<div data-role='page' id='review'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class='cancel-review'>Cancel</a>
		<h1>Your Review</h1>
		@if( ! is_null( $user ) )
		<a href='#' class='post-review'>Post</a>
		@endif
	</div><!-- /header -->

	<div data-role='main'>
		@if( ! is_null( $user ) )
		<div class='rating-container'>
			<span class='rating'>
			@for($r = 1; $r < 6; $r++ )
                <span class="star" style='padding: 10px;' data-rating='{{ $r }}'>
                    <i data-icon="&#xe017;" class="pwi-icon-star" style='font-size: 4em;'></i>
                </span>
            @endfor
			</span>
		</div>
		<div class='write-review'>
			<textarea placeholder='Write a Review...'></textarea>
		</div>
		@else
		<div class='rating-container' style='text-align: center;'>
			 You must be logged into write a review.
		</div>
		@endif
	</div>

	<script type='text/javascript'>

		var type 		= $(document).find("input[name=pagetype]").val( );
		var id 			= $(document).find("input[name=id]").val( );
		var dataRating 	= 0;

		$(".rating .star").on("click", function( ){

			dataRating = $(this).data('rating');

	        $("#review").find(".rating .star").removeClass('fill');
	            
	        for( var i = 5 ; i > 0 ; i-- ){
	            if( dataRating >= i ){
	                $(".rating span[data-rating=" + i + "]").addClass('fill');
	            }else{
	                $(".rating span[data-rating=" + i + "]").removeClass('fill');
	            }
	        }
	    });

		$(".post-review").on("click", function( ){

			var review = $(".write-review").find("textarea").val( );
			var errors = 0;

			if( dataRating == 0 ){
				alert(' no rating ');
				errors = 1;
			}

			if( review == "" ){
				alert( ' no review ' );
				errors = 1;
			}

			if( errors == 1 ){
			    alert('errors');
		    }else{

			    $.ajax({
				   	url: "/comment",
				   	method: "POST",
				   	data: {id: id, type: type, rating: dataRating, comment: comment},
				   	beforeSend: function( ){
					   	$.mobile.loading("show", {
					   		text: "Posting Review...",
					   		textVisible: true,
					   		
					   	});
				   	},
				   	dataType: 'json',
				   	success: function( data ){
					   	
					   	if( data.status ){
						   	$.mobile.loading("show",{
						   		text: "Review Posted",
						   		textonly: true
						   	});

						   	setTimeout(function(){
						   		$.mobile.loading("hide");


						   	}, 1000)
						   	
						}else{
						   	
					   	}
				   	}
			    });
		    }
		});

	</script>
</div>