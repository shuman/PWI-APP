<?php
	$facebook  = "";
	$twitter   = "";
	$instagram = "";
	$pinterest = "";

	

	foreach( $socialmedia as $item){

		switch( strtolower( $item->social_media_name ) ){
			case "facebook":
				$facebook = $item->org_sm_pageid;
			break;
			case "twitter":
				$twitter = $item->org_sm_pageid;
			break;
			case "instagram":
				$instagram = $item->org_sm_pageid;
			break;
			case "pinterest":
				$pinterest = $item->org_sm_pageid;
			break;
		}
	}
?>
<div class='modal fade overlay-content' tabindex='-1' role='dialog' id='socialMediaModal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
					<span aria-hidden='true'>&times;</span>
				</button>
				<h4 class='modal-title'>Social Media Links</h4>
			</div>
			<div class='modal-body'>
				
				<div>
					<label for='twitter-handle'>Twitter</label>
					<br />
					<small>Enter your organization's twitter handle. Go to your profile then
					copy what comes after the text below</small>
					<div class='input-group margin-top-10' style='width: 75%;'>
						<span class='input-group-addon' id='twitter-handle-addon'>www.twitter.com/</span>
						<input type='text' class='form-control' name='twitter-handle' id='twitter-handle' aria-describedby='twitter-handle-addon' value='{{$twitter}}'/>
					</div>
				</div>

				<div class='margin-top-10'>
					<label for='facebook-handle'>Facebook</label>
					<br />
					<small>Enter your organization's facebook handle. Go to your profile then
					copy what comes after the text below.</small>
					<div class='input-group margin-top-10' style='width: 75%;'>
						<span class='input-group-addon' id='facebook-handle-addon'>www.facebook.com/</span>
						<input type='text' class='form-control' name='facebook-handle' id='facebook-handle' aria-describedby='facebook-handle-addon' value='{{$facebook}}'/>
					</div>
				</div>

				<div class='margin-top-10'>
					<label for='instagram-handle'>Instagram</label>
					<br />
					<small>Enter your organization's instagram handle. Go to your profile then
					copy what comes after the text below.</small>
					<div class='input-group margin-top-10' style='width: 75%;'>
						<span class='input-group-addon' id='instagram-handle-addon'>www.instagram.com/</span>
						<input type='text' class='form-control' name='instagram-handle' id='instagram-handle' aria-describedby='instagram-handle-addon' value='{{$instagram}}'/>
					</div>
				</div>

				<div class='margin-top-10'>
					<label for='pinterest-handle'>Pinterest</label>
					<br />
					<small >Enter your organization's pinterest handle. Go to your profile then
					copy what comes after the text below.</small>
					<div class='input-group margin-top-10' style='width: 75%;'>
						<span class='input-group-addon' id='pinterest-handle-addon'>www.pinterest.com/</span>
						<input type='text' class='form-control' name='pinterest-handle' id='pinterest-handle' aria-describedby='pinterest-handle-addon' value='{{$pinterest}}'/>
					</div>
				</div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
				<button type='button' class='btn btn-primary save-social-media'>Save Changes</button> 
			</div>
		</div>
	</div>
</div>