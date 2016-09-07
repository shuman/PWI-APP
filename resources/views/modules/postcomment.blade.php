@if( Route::is('crowdfundView') || Route::is('orgView') || Route::is('productView') )
        @if( ! is_null( $user ) )
<div class='post-comment'>
	<div class='error post-review-errors'></div>
    <textarea name='comment' placeholder="Write a comment..."></textarea>
    <br />
    <div class='post-comment-actions'>
		<div class='pull-left'>
            <div class='rating below-image'>
                @for($i = 1; $i < 6; $i++ )
                <span class="star" data-rating='{!! $i !!}'>
                    <i data-icon="&#xe017;" class="pwi-icon-star pwi-icon-2em" ></i>
                </span>
                @endfor
        	</div>
        </div>
		<div class='pull-right'>    
			@if( isset( $project ) )
				<button class='post-comment-button' data-id='{!! $project->project_id !!}' data-type='project'>post comment</button>
			@elseif( isset( $org ) )
				<button class='post-comment-button' data-id='{!! $org->org_id !!}' data-type='organization'>post comment</button>
			@elseif( isset( $product) )
				<button class='post-comment-button' data-id='{!! $product->product_id !!}' data-type='product'>post review</button>
			@endif
		</div>
	</div>
</div>
@else
	You must be logged in to post a comment.
@endif
@endif