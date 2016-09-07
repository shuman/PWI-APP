<div class="user-sidebar-right">
    @if(Route::is('dashboard') || Route::is('settings'))
        @if($total_donation>0)
            <div class="widget">
                <h2>My Impact</h2>
                <span class="subtitle">See how much you have helped</span>
                <div class="btn btn-green btn-block margin-top-15" style="pointer-events:none">$ {{$total_donation}}</div>
                <button class="btn btn-blue btn-block margin-top-15 see-details">See Details</button>
                <div class="details donation-details">
                    @foreach($donations as $donation)
                    <div class="breakdown">
                        <strong>{{$donation['company_name']}}</strong>
                        <ul>
                            @foreach($donation['impacts'] as $impacts)
                            <li>{{$impacts['title']}} <strong class="pull-right text-blue">${{$impacts['value']}}</strong></li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
    @if(Route::is('dashboard'))
    <!--<div class="widget">
            <div class="fund-img">
                <img class="img-responsive img-radius" src="{{asset('images/shopping-fund.jpg')}}" alt="fund-img" />
                <button class="btn btn-yellow btn-block text-center text-capitalize margin-top-15 see-details">See Details</button>
                <div class="details fund-details">
                    <div class="breakdown">
                        <ul>
                            <li>Product name <strong class="pull-right text-yellow">$100.00</strong></li>
                            <li>Another Source <strong class="pull-right text-yellow">$49.99</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>-->
    <!--div class="widget">
        <div class="learn-account image">
            <img class="img-responsive img-radius" src="{{asset('images/my-account-bg.jpg')}}" class="img" alt="Learn Account">
            <div class="learn-account-icon">
                <i class="icon pwi-icon-cart"></i>
            </div>
            <div class="learn-more">
                <a href="#">Learn More</a>
            </div>
            <div class="my-account">
                <a href="http://fund.projectworldimpact.com">Your Account</a>
            </div>
        </div>
    </div-->
    <!-- My Product -->
    @if(count($latest_product))
    @foreach($latest_product as $latest_product_value)
    <div class="widget">
        <img src="{{$latest_product_value['proImgPath']}}" alt="{{$latest_product_value['org_name']}}" class="img img-responsive">
        <div class="product-info margin-top-10">
            <a href="/product/{{$latest_product_value['product_alias']}}"><img src="{{$latest_product_value['logoImgPath']}}" class="propic-sm" alt="{{$latest_product_value['product_name']}}"> 
             <p class="product-title">{!! stripslashes($latest_product_value['product_name'])!!}</p></a>
            <p class="product-desc"><span class="text-blue">${!! stripslashes($latest_product_value['sales_price']) !!}</span> from {!! stripslashes($latest_product_value['org_name']) !!}</p>
        </div>
        <div class="post-meta padding-top-0">
            <a href="javascript:void(0)" class="sharer text-blue margin-right-40">Share
                <div class="share-pop arrow-box">
                    <ul>
                        <li class="fb">
                            <span class="icon pwi-social-facebook shareLink" data-index="facebook" data-title="{{url('/product/',$latest_product_value['product_alias'])}}"></span>
                        </li>
                        <li class="tw">
                            <span class="icon pwi-social-twitter shareLink" data-index="twitter" data-title="{{url('/product/',$latest_product_value['product_alias'])}}"></span>
                        </li>
                        <li class="in">
                            <span class="icon pwi-social-instagram shareLink" data-inex="instagram" data-title="{{url('/product/',$latest_product_value['product_alias'])}}"></span>
                        </li>
                    </ul>
                </div>
            </a>
            <a href="{{url('/products')}}" class="read_more text-right">See More</a>
        </div>
    </div>
    @endforeach
    @endif
    @elseif(Route::is('order'))
    @if(count($latest_product)>0)
    @foreach($latest_product as $product)
    <div class="widget">
        <img src="{{$product['logoImgPath']}}" alt="{{$product['product_name']}}" class="img-responsive">
        <div class="product-info margin-top-10">
            <img src="{{$product['orgImgPath']}}" class="propic-sm" alt="{{$product['org_name']}}"> 
            <a href="/product/{{$product['product_alias']}}"><p class="product-title">{{$product['product_name']}}</p></a>
            <p class="product-desc"><span class="text-blue">${{$product['product_sales_price']}}</span> from {!! stripslashes($product['org_name']) !!}</p>
        </div>
        <div class="post-meta padding-top-0">
            <a href="#" class="sharer text-blue margin-right-40">Share
                <div class="share-pop arrow-box">
                    <ul>
                        <li class="fb">
                            <span class="icon pwi-social-facebook shareLink" data-index="facebook" data-title="{{url('/product/',$product['product_alias'])}}"></span>
                        </li>
                        <li class="tw">
                            <span class="icon pwi-social-twitter shareLink" data-index="twitter" data-title="{{url('/product/',$product['product_alias'])}}"></span>
                        </li>
                        <li class="in">
                            <span class="icon pwi-social-instagram shareLink" data-inex="instagram" data-title="{{url('/product/',$product['product_alias'])}}"></span>
                        </li>
                    </ul>
                </div>
            </a>
            <a href="{{url('/products')}}" class="read_more text-right">See More</a>
        </div>
    </div>
    @endforeach
    @endif
    @elseif(Route::is('settings'))
    <div class="widget social-media">
        <div class="widget-title"><h2>Social Media <a href="javascript:void(0)" class="config social-media-edit"><i class="fa fa-cog cog"></i></a></h2></div>
        <div class="row">
            <div class="col-md-12">
                <?php

                function searchForMedia($id, $array) {
                    foreach ($array as $key => $val) {
                        if ($val['name'] === $id) {
                            return $val['status'];
                        }
                    }
                    return null;
                }

                $fb_status = searchForMedia("Facebook", $socialmedia);
                $twit_status = searchForMedia("Twitter", $socialmedia);
                $ins_status = searchForMedia("Instagram", $socialmedia);
                ?>
                <ul class="media_list social_sec">
                    <li>
                        <span class="fb icon pwi-social-facebook"></span> Facebook 
                        <span class="pull-right status {{ ($fb_status=='Y') ? 'active' : '' }} fbStatus"><?php echo $fb_status == 'Y' ? 'Enabled' : "Disabled"; ?></span>
                    </li>
                    <li>
                        <span class="tw icon pwi-social-twitter"></span> Twitter 
                        <span class="pull-right status {{ ($twit_status=='Y') ? 'active' : '' }} twitStatus"><?php echo $twit_status == 'Y' ? 'Enabled' : "Disabled"; ?></span>
                    </li>
                    <li>
                        <span class="in icon pwi-social-instagram"></span> Instagram 
                        <span class="pull-right status {{ ($ins_status=='Y') ? 'active' : '' }} insStatus"><?php echo $ins_status == 'Y' ? 'Enabled' : "Disabled"; ?></span>
                    </li>
                </ul>

            </div>
        </div>
    </div>
    <!-- Edit Social Media -->
    <div class="widget new-product-img widget social-edit">
        <div class="widget-title"><h2>Social Media <a href="javascript:void(0)" class="config close-edit"><i class="fa fa-cog cog"></i></a></h2></div>
        <ul>
            <li>
                <div class="social-status {{ ($fb_status=='Y') ? 'active' : '' }}"><span class="fb icon pwi-social-facebook"></span> <strong>Facebook</strong> <span class="pull-right status"><?php echo $fb_status == 'Y' ? 'Enabled' : 'Disabled'; ?></span></div>
                <div class="btn-group btn-group-justified bg-greyBtn">
                    @if(!empty($fb_status))
                    <a href="javascript:void(0)" class="btn <?php echo $fb_status == 'Y' ? 'btn-blue' : 'btn-grey'; ?>  btn-sm social-update" <?php echo $fb_status == 'Y' ? 'style="pointer-events:none"' : ''; ?> data-id="1" data-title="Facebook" data-index="Y">Enable</a>
                    <a href="javascript:void(0)" class="btn <?php echo $fb_status == 'N' ? 'btn-blue' : 'btn-grey'; ?> btn-sm social-update" <?php echo $fb_status == 'N' ? 'style="pointer-events:none"' : ''; ?> data-id="1" data-title="Facebook" data-index="N">Disable</a>
                    @else
                    <a href="javascript:void(0)" class="btn btn-grey btn-sm social-update" data-id="1" data-title="Facebook" data-index="Y">Enable</a>
                    <a href="javascript:void(0)" class="btn btn-blue btn-sm social-update" style="pointer-events:none" data-id="1" data-title="Facebook" data-index="N">Disable</a>
                    @endif
                </div>
            </li>
            <li>
                <div class="social-status {{ ($twit_status=='Y') ? 'active' : '' }}"><span class="tw icon pwi-social-twitter"></span> <strong>Twitter</strong> <span class="pull-right status"><?php echo $twit_status == 'Y' ? 'Enabled' : 'Disabled'; ?></span></div> 
                <div class="btn-group btn-group-justified bg-greyBtn">
                    @if(!empty($twit_status))
                    <a href="javascript:void(0)" class="btn <?php echo $twit_status == 'Y' ? 'btn-blue' : 'btn-grey'; ?>  btn-sm social-update" <?php echo $twit_status == 'Y' ? 'style="pointer-events:none"' : ''; ?> data-id="2" data-title="Twitter" data-index="Y">Enable</a>
                    <a href="javascript:void(0)" class="btn <?php echo $twit_status == 'N' ? 'btn-blue' : 'btn-grey'; ?> btn-sm social-update" <?php echo $twit_status == 'N' ? 'style="pointer-events:none"' : ''; ?> data-id="2" data-title="Twitter" data-index="N">Disable</a>
                    @else 
                    <a href="javascript:void(0)" class="btn btn-grey btn-sm social-update"  data-id="2" data-title="Twitter" data-index="Y">Enable</a>
                    <a href="javascript:void(0)" class="btn btn-blue btn-sm social-update" style="pointer-events:none"  data-id="2" data-title="Twitter" data-index="N">Disable</a>
                    @endif
                </div>
            </li>
            <li>
                <div class="social-status {{ ($ins_status=='Y') ? 'active' : '' }}"><span class="in icon pwi-social-instagram"></span> <strong>Instagram</strong> <span class="pull-right status"><?php echo $ins_status == 'Y' ? 'Enabled' : 'Disabled'; ?></span></div>
                <div class="btn-group btn-group-justified bg-greyBtn">
                    @if(!empty($ins_status))
                    <a href="javascript:void(0)" class="btn <?php echo $ins_status == 'Y' ? 'btn-blue' : 'btn-grey'; ?>  btn-sm social-update" <?php echo $ins_status == 'Y' ? 'style="pointer-events:none"' : ''; ?> data-id="3" data-title="Instagram" data-index="Y">Enable</a>
                    <a href="javascript:void(0)" class="btn <?php echo $ins_status == 'N' ? 'btn-blue' : 'btn-grey'; ?> btn-sm social-update" <?php echo $ins_status == 'N' ? 'style="pointer-events:none"' : ''; ?> data-id="3" data-title="Instagram" data-index="N">Disable</a>
                    @else 
                    <a href="javascript:void(0)" class="btn btn-grey btn-sm social-update" data-id="3" data-title="Instagram" data-index="Y">Enable</a>
                    <a href="javascript:void(0)" class="btn btn-blue btn-sm social-update" style="pointer-events:none" data-id="3" data-title="Instagram" data-index="N">Disable</a>
                    @endif
                </div>
            </li>
        </ul>
    </div>
    <!-- End Here -->

    <div class="new-product-img widget news-letter">
        <div class="widget-title"><h2>Newsletter Updates<a href="javascript:void(0)" class="config edit-newsletter"><i class="fa fa-cog cog"></i></a></h2></div>
        @if(!empty(Auth::user()->news_update_type))
        <button class="btn btn-green btn-block text-capitalize news-update-type">{{Auth::user()->news_update_type}}</button>
        @else
        <button class="btn btn-green btn-block text-capitalize news-update-type">Monthly</button>
        @endif
    </div>
    <!-- New letters Edit -->
    <div class="new-product-img widget news-letter-disable">
        <div class="widget-title">
            <h2>Newsletter Updates<a href="javascript:void(0)" class="config close-edit-newsletter"><i class="fa fa-cog cog"></i></a></h2>
        </div>
        <button class="btn <?php echo Auth::user()->news_update_type == 'daily' ? 'btn-green' : 'btn-grey'; ?> btn-block margin-top-10 news-update-status" data-title="daily" data-index="D">Daily</button>
        <button class="btn <?php echo Auth::user()->news_update_type == 'weekly' ? 'btn-green' : 'btn-grey'; ?> btn-block margin-top-10 news-update-status" data-title="weekly" data-index="W">Weekly</button>
        <button class="btn <?php echo Auth::user()->news_update_type == 'monthly' ? 'btn-green' : 'btn - grey'; ?> btn-block margin-top-10 news-update-status" data-title="monthly" data-index="M">Monthly</button>
        <button class="btn btn-blue center-block text-uppercase margin-top-10 news-leteer-update">Save Settings</button>
        <h5 class="msg"></h5>
    </div>
    @else
    <!-- none -->
    @endif
</div>
