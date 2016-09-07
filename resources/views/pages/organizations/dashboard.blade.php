@extends("header")

@section("content")

<input type='hidden' name='orgId' value='{{$org->org_id}}' />
@if( ! is_null( $orgGateway ) )
	<input type='hidden' name='gateway' value='{{$orgGateway->fk_payment_gateway}}' />
@else
	<input type='hidden' name='gateway' value='' />
@endif
<input type='hidden' name='orgAlias' value='{{$org->org_alias}}' />

<div class='non-profit-box'>
	@if( isset( $logo->file_path) )
		<img src='/images/organization/{{ $logo->file_path }}' align='left'/>
	@else
		<img src='/images/orgPlaceHolder.jpg' align='left'/>
	@endif
	<div class='name'>{{ $org->org_name }}</div>
	<div class='box-actions'>
		<div class='non-profit-box-action copy-link'>Copy Profile Link</div>
		<div class='non-profit-box-action view-profile'>View/Edit Profile</div>
		<p style='top: -99999px; position:absolute;'>
			<textarea id="orgCopyUrl">{!! url( "/organization/" . $org->org_alias ) !!}</textarea>
		</p>
	</div>
</div>
<div class='org-dashboard margin-top-10 margin-bottom-65'>
	<div class='leftSide'>
		<div class='navigation-box'>
			<div class='item line active-item'>Dashboard</div>
			<div class='item line' data-toggle='modal' data-target='#paymentGatewayModal'>Payment Gateway</div>
			<div class='item line' data-toggle='modal' data-target='#subscriptionModal'>Subscription</div>
			<!--<div class='item line'>Reports</div>
			<div class='item'>Find a Vender</div>-->
		</div><!-- end .navigation-box -->
		<!--
		<div class='newsletter-list margin-top-10'>
			<div class='heading'>
				Newsletter List
				<a href='#' class='gear'>
					<i class='fa fa-cog cog'></i>
				</a>
			</div>
			<div class='view-list margin-top-10'>View List</div>
		</div><!-- end .newsletter-list -->

		<div class='profile-score margin-top-10'>
			<div class='heading'>Profile Score</div>
			<div class='admin-subtitle'>Profile points increases the likely hood of your organization being found.</div>
			<div class='status-bar margin-top-10'>
				<div class='status' style='width: {{$percentage}}%;'></div>
			</div>
			<div class='points-earned'>
				<div class='admin-grey-header'>{{$profileScore}} Points Earned</div>
				Add below and increase your score so more people will get 
				to know you
			</div>
		</div><!-- end .profile-score -->

		<div class='game-card margin-top-10'>
			<div class='heading'>Score Card</div>

			@foreach( $gradeList as $gradeItem )
				<div class='row margin-top-5'>
					<div class='col-lg-6 col-md-6 col-sm-6 padding-top-5'>{{ $gradeItem["description"] }}</div>
					@if( $gradeItem["points"] > 1 )
						<div class='col-lg-4 col-md-4 col-sm-4 points'>{{ $gradeItem["points"]}} points</div>
					@else
						<div class='col-lg-4 col-md-4 col-sm-4 points'>{{ $gradeItem["points"]}} point</div>
					@endif
					<div class='col-lg-2 col-md-2 col-sm-2'>
						@if( $gradeItem["has"] )
							<div class='game-check waiting pull-right'>
								<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
							</div>
						@else
							<div class='game-check accomplished pull-right game-item-'>
								<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</div>
						@endif
					</div>
				</div>

			@endforeach
		</div><!-- end .game-card -->

		<div class='dashboard-reporting donations-reporting margin-top-10'>
			<div class='heading'>Donations</div>
			<div class='subtitle'>How much people have contributed</div>

			<div class='progress margin-top-5' style='width: 100%;'>{{ money_format('%(n', ( $donations + $projectDonations ) ) }} </div>

			<div class='row margin-top-5'>
				<div class='col-lg-6 col-md-6 col-sm-6 key'>Crowdfunding</div>
				<div class='col-lg-6 col-md-6 col-sm-6 value text-right'>{{ money_format('%(n', $projectDonations ) }}</div>
			</div>

			<div class='row margin-top-5'>
				<div class='col-lg-6 col-md-6 col-sm-6 key'>Donations</div>
				<div class='col-lg-6 col-md-6 col-sm-6 value text-right'>{{ money_format('%(n', $donations ) }}</div>
			</div>
		</div><!-- end .donations-reporting -->

		<div class='dashboard-reporting product-reporting margin-top-10'>
			<div class='heading'>Products</div>
			<div class='subtitle'>How much people have contributed</div>

			<div class='progress margin-top-5' style='width: 100%;'>{{ money_format('%(n', ( $productTotalAmount ) ) }} </div>

			<div class='row margin-top-5'>
				<div class='col-lg-8 col-md-8 col-sm-8 key'>Amount of Products Sold</div>
				<div class='col-lg-4 col-md-4 col-sm-4 value text-right'>{{ $numberSold }}</div>
			</div>

			<div class='row margin-top-5'>
				<div class='col-lg-8 col-md-8 col-sm-8 key'>Average Price of Item</div>
				<div class='col-lg-4 col-md-4 col-sm-4 value text-right'>{{ money_format('%(n', $avgPrice ) }}</div>
			</div>

			<div class='row margin-top-5'>
				<div class='col-lg-8 col-md-8 col-sm-8 key'>Most Popular Item</div>
				<div class='col-lg-4 col-md-4 col-sm-4 value text-right'>{{ $mostPopular }}</div>
			</div>
		</div><!-- end .product-reporting -->
	</div>



	<div class='dashboard-main'>
		<div class='quick-view'>
			<p class='heading'>
				Quick View
				<!--<span style='font-size: 12px; color: #e5e5e5'>last 7 days</span>-->
			</p>
			<div class='row'>
				@if( $quickView["pwi"] != 0 )
					<div class='col-lg-2 col-md-2 col-sm-2 stat-number'>
						<div>{{ $quickView["pwi"] }}</div>
						<div style='font-size: 12px; color: #e5e5e5'>PWI Views</div>
					</div>
				@endif

				@if( $quickView["location"] != 0 )
					<div class='col-lg-2 col-md-2 col-sm-2 stat-number'>
						<div>{{ $quickView["location"] }}</div>
						<div style='font-size: 12px; color: #e5e5e5'>Location Views</div>
					</div>
				@endif

				@if( $quickView["causes"] != 0 )
					<div class='col-lg-2 col-md-2 col-sm-2 stat-number'>
						<div>{{ $quickView["causes"] }}</div>
						<div style='font-size: 12px; color: #e5e5e5'>Cause Views</div>
					</div>
				@endif

				@if( $quickView["profileViews"] != 0 )
					<div class='col-lg-2 col-md-2 col-sm-2 stat-number'>
						<div>{{ $quickView["profileViews"] != 0 }}</div>
						<div style='font-size: 12px; color: #e5e5e5'>Profile Views</div>
					</div>
				@endif

				@if( $quickView["followers"] != 0 )
					<div class='col-lg-2 col-md-2 col-sm-2 stat-number'>
						<div>{{ $quickView["followers"] }}</div>
						<div style='font-size: 12px; color: #e5e5e5'>Followers</div>
					</div>
				@endif
				<!-- space for fund numbers
				<div class='col-lg-2 col-md-2 col-sm-2'></div>
				-->
			</div>
		</div><!-- end .quick-view -->

		<div class='pwi-services margin-top-10'>
			<div class='row'>
				<div class='col-lg-4 col-md-4 col-sm-4'>
					<img src='/images/NP_Backend_Convene_Button.png' class='img-responsive' />
				</div>
				<div class='col-lg-4 col-md-4 col-sm-4'>
					<img src='/images/NP_Backend_Fund_Button.png' class='img-responsive' />
				</div>
				<div class='col-lg-4 col-md-4 col-sm-4'>
					<img src='/images/NP_Backend_Storyteller_Button.png' class='img-responsive' />
				</div>
			</div>
			<div class='row margin-top-10'>
				<div class='col-lg-4 col-md-4 col-sm-4'>
					<img src='/images/NP_Backend_SocialMedia_Button.png' class='img-responsive' />
				</div>
				<div class='col-lg-4 col-md-4 col-sm-4'>
					<img src='/images/NP_Backend_Grants_Button.png' class='img-responsive' />
				</div>
				<div class='col-lg-4 col-md-4 col-sm-4'>
					<img src='/images/NP_Backend_GroupGive_Button.png' class='img-responsive' />
				</div>
			</div>
		</div><!-- .pwi-services -->

		<div class='dashboard-content margin-top-10' style='width: 100%; position: relative;'>
			@if( sizeof( $projects) > 0 )
				<div class='dashboard-project admin-dashboard-content project-module-list'>
					<div class='heading'>Crowdfunding Projects</div>
					<div class='projects'>
				        @foreach( $projects as $project )
				        <div class="project-module">
				            <div class='project-module-top'>
				                <div class='project-module-img-container margin-right-10 margin-bottom-5 padding-0 pull-left'>
				                    <a href='/crowdfunding/{!! $project["alias"] !!}'><img src='{!! $project["icon"] !!}' align='left'/></a>
				                </div>
				                <div class='pull-left'>
				                    <div class='project-module-name pull-left'><a href='/crowdfunding/{!! $project["alias"] !!}'>{!! $project["title"] !!}</a></div>
				                    <div class='project-module-org-name'>
				                        {!! $project["org_name"] !!}
				                    </div><!-- end .project-module-org-name -->
				                    
				                    <!-- Project Impact Countries -->
				                    <div class='impacts-causes'>
				                        <span class='title'>Locations</span><br />
				                        <span class='list'>{!! $project["countries"] !!}</span>
				                    </div><!-- end .impacts-causes Impact Countries -->
				                    
				                    <!-- Project Impact Causes -->
				                    <div class='impacts-causes'>
				                        <span class='title'>Causes</span><br />
				                        <span class='list'>{!! $project["causes"] !!}</span>
				                    </div><!-- end .impact-causes Impact Causes -->
				                </div><!-- end .pull-left -->
				            </div><!-- end .project-module-top -->
				            <div style='clear:both;'></div>
				            <div class='project-module-status'>
				                <!-- Amount Raised & Project Goal -->
				                <div class='status-line'>
				                    <div class='pull-left projectRaisedAmt'>
				                        {!! $project["amtRaised"] !!}
				                    </div>
				                    <div class='pull-right projectGoal'>
				                        out of <span class='projectGoalAmt'>{!! $project["fundGoal"] !!}</span>
				                    </div>
				                </div><!-- end .status-line ( Amount Raised & Project Goal ) -->
				                
				                <!-- Progress Bar -->
				                <div class='status-line'>
				                    <div class='progress' style='width: 100%;'>
				                        <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: {!! $project['percentage'] !!}%;">
				                        </div>
				                    </div>
				                </div><!-- end .status-line ( Progres Bar ) -->
				                
				                <!-- Project Percentage & Project Goal Amount -->
				                <div class='status-line'>
				                    <div class='pull-left projectGoal'>
				                        {!! $project["percentage"] !!}% complete
				                    </div>
				                    <div class='pull-right projectGoal'>
				                        <span class='projectGoalAmt'>{!! $project["daysleft"] !!}</span> days left
				                    </div>
				                </div><!-- end .status-line ( Project Percentage & Project Goal Amount ) -->
				            </div><!-- .project-module-status -->
				        </div><!-- .project-module -->
				        @endforeach
				    </div><!-- end .projects -->
				</div>
			@endif

			@if( sizeof( $causeNews ) > 0 )
				@foreach( $causeNews as $k => $v )
					@if( isset( $causeNews[$k] ) )
						<div class='admin-dashboard-news news-{{ $k }} margin-top-10'>
							<div class='heading'>{{$k}} News</div>
							@for( $i = 0 ; $i < 3 ; $i++ )
								<div class='article margin-top-5'>
									<img src='{{ $v[$i]["image"]}}' align='left' />
									<div class='title'>{{ $v[$i]["title"]}}</div>
									<div class='article-data'>
										<span class='source'>{{ $v[$i]["source"] }}</span>
										<span class='time-posted'>{{ $v[$i]["date"]}}</span>
									</div>
									<div class='content'>{{ $v[$i]["text"]}}</div>
								</div>
							@endfor
							<div class='heading margin-top-10'>{{$k}} Twitter</div>
							@foreach( $causeTwitter[$k] as $hashtag )
								@for( $i = 0; $i < 3 ; $i++ )
									<div class='article margin-top-5'>
										<div class='content'>{!! $hashtag[$i]["tweet"] !!}</div>
										<div class='time-posted'>{{ $hashtag[$i]["date"] }}</div>
									</div>	
								@endfor
							@endforeach
						</div>
					@endif
				@endforeach
			@endif

			<div class='pwi-bottom-content margin-top-10'>
				<div class='text-center'>
					<span class='project'>project</span>
					<span class='world'>world</span>
					<span class='impact'>impact</span>
				</div>
				<div class='content'></div>
			</div>			

			<div class='rightSide'>
				<!--
				<div class='admin-featured-profile'>
					<div class='heading'>Featured Profile</div>
					<div style='font-size:12px; color: #e5e5e5;'>Take a look at a profile gaining alot of attention</div>

				</div>
				-->
				@if( sizeof( $countryNews ) )
					@foreach( $countryNews as $k => $v )
						<div class='admin-dashboard-news-country margin-top-10'>
							<div class='heading'>{{ $k }} News</div>
							@for( $i = 0 ; $i < 2 ; $i++ )
								<div class='article margin-top-5'>
									<div class='title'>{{ $v[$i]["title"] }}</div>
									<div>
										<span class='source'>{{ $v[$i]["source"] }}</span>
										<span class='time-posted'>{{ $v[$i]["date"] }}</span>
									</div>
									<div class='content'>{{ $v[$i]["text"] }}</div>
								</div>
							@endfor
						</div>
					@endforeach
				@endif
			</div>
		</div>
	</div>
</div>
@include("partials.modals._payment_gateway")
@include("partials.modals._subscriptions")
<script type='text/javascript'>

	var orgId = $("input[name=orgId]").val( );

	var leftSide = $(".leftSide").height( );
	var main = $(".dashboard-main").height( );

	if( leftSide > main ){
		$(".dashboard-main").css("min-height", (parseInt( leftSide) + 100 ) + "px");
	}

	var copyTextareaBtn = document.querySelector(".copy-link");

	copyTextareaBtn.addEventListener('click', function(event) {
		
	  	var copyTextarea = document.querySelector('#orgCopyUrl');
	  	copyTextarea.select();

	  	var successful = document.execCommand('copy');
	    
		if( successful ){
			copyTextareaBtn.innerHTML = "URL Copied to Clipboard";

			setTimeout(function( ){
				copyTextareaBtn.innerHTML = "Copy Profile Link";				
			}, 3000)
	    }
	});
	
	/*
	if( $("input[name=gateway]").val( ) != "" ){

		gateway = $("input[name=gateway]").val( );

		$(".gateway-data").html( displayDropDownCredentials( $("input[name=gateway]").val( ) ) );
	}*/

	$(document).on("change", "#gatewayDropDown", function( ){

		gateway = $(this).find("option:selected").val( );
		
		$(".gateway-data").html( displayDropDownCredentials( $(this).find("option:selected").val( ) ) );
	});

	function displayDropDownCredentials( val ){

		var output = "<hr />";

		switch( val ){
			case "1":

				output += "<div class='input-group'>";
				output += "	<label for='client-id'>PayPal Client Id</label><br />";
				output += "	<input type='text' name='pp-clientId' class='form-control' size='45'/>";
				output += "</div>";
				output += " <div class='error pp-clientId-error'></div>";

				output += "<div class='input-group margin-top-10'>";
				output += " <label for='secret'>PayPal Secret</label>";
				output += " <input type='text' name='pp-secret' class='form-control' size='45' />";
				output += "</div>";
				output += " <div class='error pp-secret-error'></div>";

			break;
			case "2":
				output += "<div class='input-group'>";
				output += "	<label for='gateway-key'>Transnational Gateway Key</label><br />";
				output += "	<input type='text' name='trans-gatewayKey' class='form-control' size='45'/>";
				output += "</div>";
				output += "	<div class='error trans-gatewayKey-error'></div>";
			break;
			case "3":
				output += "<div class='input-group'>";
				output += "	<label for='paypal_username'>PayPal Username</label><br />";
				output += "	<input type='email' name='ppS-username' class='form-control' size='45'/>";
				output += "</div>";
				output += " <div class='error ppS-username-error'></div>";
			break;
			case "4":
				output += "<div class='input-group'>";
				output += "	<label for='auth-name'>Authorize.NET Name</label><br />";
				output += "	<input type='text' name='auth-name' class='form-control' size='45'/>";
				output += "</div>";
				output += "	<div class='error auth-name-error'></div>";

				output += "<div class='input-group margin-top-10'>";
				output += " <label for='auth-key'>Authorize.NET Key</label>";
				output += " <input type='text' name='auth-key' class='form-control' size='45' />";
				output += "</div>";
				output += " <div class='error auth-key-error'></div>";

			break;
		}
		
		return output;
			
	}

	$(".save-gateway").on("click", function( ){

		var error = 0;
		var queryString = "";
		var noGateway = false;

		if( $("#paymentGatewayModal").find('select[name=gatewayName]').find("option:selected").val( ) == 0 ){
			noGateway = true;
			$(".error-gateway-choice").html( "Please Selected a Gateway" ).show( );
		}

		if( ! noGateway ){
			switch( gateway ){
				case "1":
					queryString = "clientId=" + $("input[name=pp-clientId]").val( ) + "&secret=" + $("input[name=pp-secret]").val( ) + "&gateway=1&orgId=" + orgId;
				break;
				case "2":
					queryString = "gatewayKey=" + $("input[name=trans-gatewayKey]").val( ) + "&gateway=2&orgId=" + orgId;
				break;
				case "3":
					queryString = "payPalUsername=" + $("input[name=ppS-username]").val( ) + "&gateway=3&orgId=" + orgId;
				break;
				case "4":
					queryString = "name=" + $("input[name=auth-name]").val( ) + "&key=" + $("input[name=auth-key]").val( ) + "&gateway=4&orgId=" + orgId;
				break;
			}

			$.ajax({ 
				method: "POST",
				url: "/organization/setGateway",
				data: queryString,
				beforeSend: function( ){
					$("#paymentGatewayModal").find(".error").hide( );
				},	
				dataType: "json",
				success: function( resp ){

					if( resp.status ){
						if( gateway == 1 ){
							$("input[name=pp-clientId]").val( "" );
							$("input[name=pp-secret]").val( "" );
						}else if( gateway == 2 ){
							$("input[name=trans-gatewayKey]").val( "" );
						}else if( gateway == 3 ){
							$("input[name=ppS-username]").val( "" );
						}else if( gateway == 4 ){
							$("input[name=auth-name]").val( "" );
							$("input[name=auth-key]").val( "" );
						}

						$(".success-msg").show( );

						setTimeout(function( ){
							$(".success-msg").hide( );
						}, 3000);
					}else{
						for( var key in resp.errors ){
							if( gateway == 1 ){
								if( key == "clientId" ){
									$(".pp-clientId-error").html( resp.errors[key] ).show( );
								}else if( key == "secret"){
									$(".pp-secret-error").html( resp.errors[key] ).show( );
								}
							}else if( gateway == 2 ){
								$(".trans-gatewayKey-error").html( resp.errors[key] ).show( );
							}else if( gateway == 3 ){
								$(".ppS-username-error").html( resp.errors[key] ).show( );
							}else if( gateway == 4 ){
								if( key == "clientId" ){
									$(".auth-name-error").html( resp.errors[key] ).show( );
								}else if( key == "secret"){
									$(".auth-key-error").html( resp.errors[key] ).show( );
								}
							}
						}
					}
				}
			});
		}
	});

	$(".save-subscription").on("click", function( ){

		var $container = $("#subscriptionModal");

		var form_data = new FormData();

		form_data.append("cc_number", 		$container.find("input[name=cc_number]").val( ) );
		form_data.append("cc_ccv", 			$container.find("input[name=cc_ccv]").val( ) );
		form_data.append("cc_exp_month", 	$container.find("input[name=cc_exp_month]").val( ) );
		form_data.append("cc_exp_year",		$container.find("input[name=cc_exp_year]").val( ) );
		form_data.append("subscription_id",	$container.find("input[name=subscriptionId]").val( ) );
		form_data.append("orgId", 			orgId);
		form_data.append("_token",			$(document).find("input[name=_token]").val( ) );

		$.ajax({
			url: "/organization/saveSubscriptionData",
			method: "POST",
			data: form_data,
			dataType: "json",
			beforeSend: function( ){

			},
			success: function( resp ){

			}
		});
	});

	$(".view-profile").on("click", function( ){
		location.href = "/organization/" + $(document).find("input[name=orgAlias]").val( );
	});

	$(".copy-link").on("click", function( ){

		var aux = document.createElement("input");

		//aux.setAttribute("value", location.protocol + "//" + location.host + );

	});
</script>
@stop