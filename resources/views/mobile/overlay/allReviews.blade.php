<div data-role='page' id='allReviews'>
	<div data-role='header' class='overlay-header'>
		<a data-rel='back' class='cancel-review'>Back</a>
		<h1>All reviews</h1>
	</div><!-- /header -->

	<div data-role='main'>
		@for( $i = 0 ; $i < sizeof( $reviews ) ; $i++ )
			<div class='review'>
				<div class='top'>
					<span class='username'>{!! $reviews[$i]->comment_username !!}</span>
					<span class='rating'>
					@for($r = 1; $r < 6; $r++ )
	                    @if( $r <= $reviews[$i]->comment_rating )
	                        <span class="star fill" >
	                            <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
	                        </span>          
	                    @else
	                        <span class="star" >
	                            <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em"></i>
	                        </span>
	                    @endif
	                @endfor
					</span>
				</div>
				<div class='desc'>
					<?php $tmp = explode(" ", $reviews[$i]->comment_text ); ?>

					@if( sizeof( $tmp ) > 100 )
						@for( $j = 0 ; $j < sizeof( $tmp ) ; $j++ )
	                        @if( $j < 100 || $j > 100 )
	                            {!! $tmp[$j] !!}
	                        @else( $j == 100 )
	                            <a href='' class='readmore'>...See More</a>
	                            <span class='more'>{!! $tmp[$j] !!}
	                        @endif
	                    @endfor
	                    </span><a href='#' class='readless'>Show Less</a>
					@else
						{!! $reviews[$i]->comment_text !!}
					@endif
				</div>
			</div>
		@endfor
	</div>
</div>