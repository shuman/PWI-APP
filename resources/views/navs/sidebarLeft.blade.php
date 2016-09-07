<div class="user-sidebar-left">
    
        <!--
        <ul class="nav">
            
            <li class="{{ (Route::is('dashboard')) ? 'active' : '' }}"><a href="{{url('/user/dashboard')}}">Dashboard</a></li>
            <li class="{{ (Route::is('order')) ? 'active' : '' }}"><a href="{{url('/user/order')}}">Order</a></li>
            <li class="{{ (Route::is('settings')) ? 'active' : '' }}"><a href="{{url('/user/settings')}}">Settings</a></li>           
            @if($user->user_type == 'org_users')
            <li><a href="{{url('/passthru')}}?id={{$user->user_id}}&_token={{ csrf_token() }}">Manage Organization</a></li>
            @endif
        </ul>
        -->
        <div class='navigation-box margin-bottom-10'>
            <div class='item line {{ (Route::is('dashboard')) ? 'active-item' : '' }}'>
                <a href="{{url('/user/dashboard')}}">Dashboard</a>
            </div>
            <div class='item line {{ (Route::is('order')) ? 'active-item' : '' }}' >
                <a href="{{url('/user/order')}}">Order</a>
            </div>
            <div class='item line {{ (Route::is('settings')) ? 'active' : '' }}'>
                <a href="{{url('/user/settings')}}">Settings</a>
            </div>
            @if( ! empty( $user->user_org_id ) )
            <div class='item line '>
                <a href="{{url('/organization/dashboard')}}">Manage Organization</a>
            </div>
            <!--<li><a href="{{url('/passthru')}}?id={{$user->user_id}}&_token={{ csrf_token() }}">Manage Organization</a></li>-->
            @endif
        </div><!-- end .navigation-box -->
    
    @if(Route::is("dashboard"))
        <div class="widget following">
            <div class="widget-title">
                <h2>Following <a href="javascript:void(0)" class="config enableRemoveBtn"><i class="fa fa-cog cog"></i></a></h2>
            </div>
            @if(count($follow_org)>0)
            <span class="subtitle">Organization</span>
            <ul>
                @foreach($follow_org as $org_value)
                <li>
                    <a href="/organization/{{$org_value['org_alias']}}" >
                        <img src="{{$org_value['logoImgPath']}}" alt="">{!! stripslashes($org_value['org_name']) !!}
                        <span class="u_action deleteFollow" data-id="{{$org_value['org_id']}}" data-title="org"><i class="icon pwi-icon-close"></i></span>
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
            <div class="followCountryWrap {{ (count($follow_country)>0) ? '' : "hide" }}">
                <span class="subtitle">Countries</span>
                <ul class="follow-country-add">
                    @if(count($follow_country)>0)
                    @foreach($follow_country as $cn_value)
                    <li>
                        <a href="/country/{{$cn_value->country_alias}}" ><span class="flag-icon flag-icon-{{strtolower($cn_value->country_iso_code)}}"></span> {{$cn_value->country_name}}
                            <span class="u_action deleteFollow" data-id="{{$cn_value->country_id}}" data-title="country"><i class="icon pwi-icon-close"></i></span>
                        </a>
                    </li>
                    @endforeach
                    @endif                
                </ul>
            </div>

            <span class="subtitle followCausesWrp {{count($follow_causes)>0?'':'hide'}}">Causes</span>
            <ul class="followedCauses">
                @if(count($follow_causes)>0)
                @foreach($follow_causes as $cs_value)
                <li>
                    <a href="/cause/{{$cs_value['cause_alias']}}"><i class="icon {{$cs_value['icon_class']}}" ></i> 
                        {!! $cs_value['cause_name'] !!}
                        <span class="u_action deleteFollow" data-id="{{$cs_value['cause_id']}}" data-title="cause">
                            <i class="icon pwi-icon-close"></i>
                        </span>
                    </a>
                </li>
                @endforeach
                @endif   
            </ul>
        </div>
        <div class="widget follow-country">
            <div class="widget-title"><h2>Follow a Country</h2></div>
            <div class="subtitle">Start following country you are interested in</div>
            <div class="input-group">
                <select class="form-control search-country" id="search-country" name="follow_country[]" multiple="multiple">
                    @if(count($country)>0)
                    @foreach($country as $country_value)
                    <option value="{{$country_value->country_id}}">{!! $country_value->country_name !!}</option>
                    @endforeach
                    @endif
                </select>
                <div class="input-group-btn">
                    <button class="btn add-country" type="submit">add</button>
                </div>
            </div>
        </div>
        <div class="widget follow-causes">
            <div class="widget-title"><h2>Follow a Cause</h2></div>
            <div class="subtitle">Start following causes you are interested in</div>
            <ul class="causesList">
                @foreach($causes_list as $cause_name)
                <li>
                    <a href="javascript:void(0)" class="followCauses" data-id="{{$cause_name['cause_id']}}"><i class="icon {{$cause_name['icon_class']}}"></i> {!! stripslashes($cause_name['cause_name']) !!}</a>
                </li>
                @endforeach
            </ul>
        </div>
        @if(count($my_reviews) > 0)
            <?php $reviews = 0; ?>
            <div class="widget my-review reviewsHeight">
                <div class="widget-title"><h2>My Reviews <span><a href="javascript:void(0)" class="text-center showAllReviews">View all</a></span></h2></div>
                @foreach($my_reviews as $reviews_value)
                <div class="list-content {{$reviews > 1?'myReviewsHide':''}}">
                    <ul>
                        <li>
                            <div class="list-content">
                                <div class="reviews truncateOff">
                                    <a href="{{url('organization',$reviews_value['org_alias'])}}">  
                                        <img class="propic-sm" src="{{$reviews_value['logoImgPath']}}" alt="{{$reviews_value['org_name']}}">
                                        <h3>{!! stripslashes($reviews_value['org_name'])!!}</h3>
                                    </a>
                                    <div class="truncate">{!! stripslashes($reviews_value['comment_text']) !!}</div>
                                    <div class="rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                        @if($reviews_value['comment_text'] >= $i)
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        @else
                                        <i class="fa fa-star-o" aria-hidden="true"></i>
                                        @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <?php $reviews++; ?>
                @endforeach
            </div>
        @endif
        @if(count($crowfund)>0)
            <?php $crow = 0; ?>
            <div class="widget crowdfunding">
                <div class="widget-title"><h2>Crowdfunding <span><a href="javascript:void(0)" class="text-center showAllCrowd">View all</a></span></h2></div>
                <div class="crowdfund-lists">
                    @foreach($crowfund as $cf_value)
                    <ul class="info-list {{$crow>0?'hideCrowd':''}}">
                        <li>
                            <div class="list-content">
                                <a href="/crowdfunding/{{$cf_value['alias']}}"><img class="propic-sm" src="{{$cf_value['icon']}}" alt="">
                                    <h2>{!! stripslashes($cf_value['project_title']) !!}</h2></a>
                                <p>{!! stripslashes($cf_value['org_name']) !!}</p>
                                <ul>
                                    <li>
                                        <h5>Locations</h5>
                                        <p>{{$cf_value['org_add_line1'].','.$cf_value['org_add_line2']}} {{$cf_value['org_state']}}</p>
                                    </li>
                                    <?php
                                    $causes = json_decode($cf_value['causes'], TRUE);
                                    $i = 0;
                                    ?>
                                    @if(!empty($causes))
                                    <li>
                                        <h5>Causes</h5>
                                        <p>
                                            @foreach($causes as $c_name) 
                                            @if($i==0) 
                                            {{$c_name['cause_name']}} 
                                            @else 
                                            , {{$c_name['cause_name']}} 
                                            @endif
                                            @endforeach
                                        </p>
                                    </li>
                                    @endif
                                    <li class="hide">
                                        <h5>Product</h5>
                                        <p>Sample Product</p>
                                    </li>
                                </ul>
                            </div>

                            <div class="crowdfunding_progress">
                                <div class="row">
                                    <div class="col-md-6 pull-left text-green">${{$cf_value['amt_raises']}}</div>
                                    <div class="col-md-6 pull-right text-right text-grey">out of ${{$cf_value['fund_goal']}}</div>
                                </div>
                                <div class="progress"><span class="bg-success" style="width:{{$cf_value['complete']}}%"></span></div>
                                <div class="row">
                                    <div class="col-md-6 pull-left text-left text-grey">{{$cf_value['complete']}}%<br> Completed</div>
                                    <div class="col-md-6 pull-right text-right text-grey">{{$cf_value['days_left']}} days left</div>
                                </div>
                            </div>
                        </li>
                        <div class="devider"></div>
                    </ul>
                    <?php $crow++; ?>
                    @endforeach
                    <div class="fadeout"></div>
                </div>
            </div>
        @endif
    @elseif(Route::is('settings'))
        <div class="widget">
            <h2>Shipping Preference <a href="javascript:void(0)" class="config"><i class="fa fa-cog pull-right shiping-add-enable"></i></a></h2>
            <span class="text-grey">Account Shipping</span><br>
            <strong class="ship-delete-msg"></strong>
            <div class="shipping_address_wrap">
                @if(count($shiping_data)>0)
                @foreach($shiping_data as $shiping_value)
                @if($shiping_value->user_addr_address_type=='shipping')
                <div class="shipping_address">
                    {{Auth::user()->user_firstname.' '.Auth::user()->user_lastname}}<br>
                    {{$shiping_value->user_addr_line1}},<br>
                    {{$shiping_value->user_addr_city.','.$shiping_value->user_addr_state." ".$shiping_value->user_addr_zip}}
                    <a href="javascript:void(0)" class="ship-add-delete" data-index="{{$shiping_value->user_addr_id}}">Delete</a>
                </div>
                @endif
                @endforeach
                @else
                <!-- No Shipping Address -->
                @endif
            </div>
            <div class="new-shiping-address text-center" style="display: none;">
                <div class="text-left text-blue margin-bottom-10">Add New Address</div>
                <span class="ship-error"></span>
                <input class="form-control ship-address margin-bottom-10" placeholder="Address">
                <input class="form-control ship-city margin-bottom-10" placeholder="City">
                <input class="form-control ship-state margin-bottom-10" placeholder="State">
                <input class="form-control ship-zipcode margin-bottom-10" placeholder="Zip Code">
                <button class="btn btn-blue text-uppercase ship-account-pref" data-title="shipping" >Save Settings</button>
            </div>
        </div>
        <div class="widget">
            <h2>Billing Preference <a href="javascript:void(0)" class="config billing-pref-enable"><i class="fa fa-cog pull-right "></i></a></h2>
            <span class="text-grey">Account Preference</span>
            <br>
            <strong class="bill-delete-msg"></strong>
            <div class="billing_address_wrap">
                @if(count($shiping_data)>0)
                @foreach($shiping_data as $shiping_value)
                @if($shiping_value->user_addr_address_type=='billing')
                <div class="billing_address">
                    {{Auth::user()->user_firstname.' '.Auth::user()->user_lastname}}<br>
                    {{$shiping_value->user_addr_line1}},<br>
                    {{$shiping_value->user_addr_city.','.$shiping_value->user_addr_state." ".$shiping_value->user_addr_zip}}
                    <a href="javascript:void(0)" class="bill-add-delete" data-index="{{$shiping_value->user_addr_id}}">Delete</a>
                </div>
                @endif
                @endforeach
                @else
                <!-- No Billing Information -->
                @endif
            </div>
            <div class="new-billing-pref text-center" style="display: none;">
                <div class="text-left text-blue margin-bottom-10">Add New Address</div>
                <span class="bill-error"></span>
                <input class="form-control margin-bottom-10 billing-address" placeholder="Address">
                <input class="form-control margin-bottom-10 billing-city" placeholder="City">
                <input class="form-control margin-bottom-10 billing-state" placeholder="State">
                <input class="form-control margin-bottom-10 billing-zipcode" placeholder="Zip Code">
                <button class="btn btn-blue text-uppercase bill-account-pref" data-title="billing">Save Settings</button>
            </div>
        </div>
    @else
    @endif
</div>