<div class='incentives' data-step='1'>
    <p class='margin-top-15'>Pick Incentive</p>
    <div class='just-contribute'>
        No thanks, just want to contribute
        <div class='donation' data-incentive-id='donate'>
            <p>Donation Amount</p>
            <div class='denomination pull-left'>
                <input type='text' name='donationAmt' />
            </div>
            <div class='action pull-right'>
                <button class='continue'>continue</button>
            </div>
        </div>
    </div>
    <div class='incentives-list'>
    @foreach( $incentives as $incentive )
        <div class='incentive margin-top-10' data-incentive-id='{!! $incentive->project_incentive_id !!}'>
            @if( $incentive->project_available_incentive_count == $incentive->project_incentive_purchasedcount )
            <div class='zero-left-overlay'></div>
            @endif
            <div class='title'>{!! $incentive->project_incentive_title !!}</div>    
            <div class='info'>
                <span class='price pull-left'>{!! money_format('%(#10n', $incentive->project_incentive_amount ) !!}</span>
                @if( $incentive->project_available_incentive_count == $incentive->project_incentive_purchasedcount )
                <span class='left no-more pull-right'>0 Left</span>
                @else
                <span class='left pull-right'>{!! ( $incentive->project_available_incentive_count - $incentive->project_incentive_purchasedcount ) !!} Left</span>
                @endif
            </div>
            <div class='description'>{!! $incentive->project_incentive_description !!}</div>
            <div class='donation'>
                <p>Donation Amount</p>
                <div class='denomination pull-left'>
                    <input type='text' name='donationAmt' value='{!! $incentive->project_incentive_amount !!}' />
                    <input type='hidden' name='shippingRequired' value='{!! $incentive->project_donor_shipping_address !!}' />
                </div>
                <div class='action pull-right'>
                    <button class='continue'>continue</button>
                </div>
            </div>
        </div>
    @endforeach
    </div>
</div>