<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

/**
 * Description of UserController
 *
 * @author PWI
 */
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\NewsRepository as NewsRepository;
use Illuminate\Http\Request;
use App\ProductDetail;
use App\ProductMaster;
use App\UserAddress;
use App\Http\Helper;
use App\Donations;
use App\Projects;
use App\Follow;
use App\OrderMessage;
use App\Organizations;
use App\Rating;
use App\Causes;
use App\Country;
use App\User;
use Auth;
use Input;
use DB;
use App\Files;
use App\SocialMedia;
use Illuminate\Support\Facades\Response;
use App\Products;
use App\UserSocialMedia;
use App\Media;
use Folklore\Image\Facades\Image;
use Config;
use Carbon\Carbon;
use App\Videos;
use File;

class UserController extends Controller {

    private $newsRepo = null;
    private $userRepo = null;
    private $helper = null;

    public function __construct(UserRepository $userRepo, Request $request, Helper $helper, NewsRepository $newsRepo) {
        $this->user = $request->instance()->query('user');
        $this->helper = $helper;
        $this->userRepo = $userRepo;
        $this->request = $request;
        $this->newsRepo = $newsRepo;
        $this->middleware('auth', ['except' => ['testpad']]);
    }

    public function testpad(){
        //test
    }

    public function dashboard(Request $request) {
        //get loged in user id
        $user_id = Auth::user()->user_id;

        // get logied in user crowdfunding details
        $project = Projects::where('pwi_projects.project_featured', '=', 'Y')
                ->join('pwi_files as ICON', 'ICON.file_id', '=', 'pwi_projects.project_icon')
                ->join('pwi_organization as ORG', 'ORG.org_id', '=', 'pwi_projects.org_id')
                ->join('pwi_follow as follow', 'follow.follow_type_id', '=', 'ORG.org_id')
                ->where('follow.follow_type', '=', 'org')
                ->where('follow.follow_user_id', '=', $user_id)
                ->where('follow.follow_status', '=', 'active')
                ->get(['pwi_projects.project_title', 'pwi_projects.project_alias', 'pwi_projects.project_amout_raised as amtRaised',
            'pwi_projects.project_fund_goal as fundGoal', 'pwi_projects.project_end_date', 'ICON.file_orig_name as imageName',
            'ICON.file_path AS icon', 'ORG.org_name', 'ORG.org_alias', 'ORG.org_id', 'ORG.org_addressline1', 'ORG.org_addressline2', 'ORG.org_state']);
        $user_cf = array();
        if (count($project) > 0) {
            foreach ($project as $p_value) {

                $causes = Causes::where('pwi_org_cause.org_id', $p_value->org_id)
                                ->join('pwi_org_cause', 'pwi_org_cause.cause_id', '=', 'pwi_causes.cause_id')->get(['pwi_causes.cause_name']);

                $daysLeft = Carbon::createFromTimeStamp(strtotime($p_value->project_end_date))->diffInDays();
                $projectPercentageComplete = 0;
                if ((int) $p_value->amtRaised > 0) {
                    $projectPercentageComplete = (((int) $p_value->amtRaised / (int) str_replace(",", "", $p_value->fundGoal)) * 100);
                }
                $project_title = $p_value->project_title;
                $project_alias = $p_value->project_alias;
                $org_name = $p_value->org_name;
                $org_alias = $p_value->org_alias;
                $amount_raised = $p_value->amtRaised;
                $fund_goal = $p_value->fundGoal;
                $org_add_line1 = $p_value->org_addressline1;
                $org_add_line2 = $p_value->org_addressline2;
                $org_state = $p_value->org_state;

                $tmp = $p_value->toArray();
                if (file_exists(public_path() . Config::get("globals.prjImgPath") . $tmp["icon"])) {
                    $tmp["icon"] = Config::get("globals.prjImgPath") . $tmp["icon"];
                } else {
                    $tmp["icon"] = "/images/cfPlaceholder.png";
                }
                $tmp['title'] = $project_title;
                $tmp['alias'] = $project_alias;
                $tmp['org_name'] = $org_name;
                $tmp['org_alias'] = $org_alias;
                $tmp['org_add_line1'] = $org_add_line1;
                $tmp['org_add_line2'] = $org_add_line2;
                $tmp['org_state'] = $org_state;
                $tmp['causes'] = json_encode($causes);
                $tmp['amt_raises'] = $amount_raised;
                $tmp['fund_goal'] = $fund_goal;
                $tmp['complete'] = $projectPercentageComplete;
                $tmp['days_left'] = $daysLeft;
                $user_cf[] = $tmp;
            }
        }
        /*
         * user reviews
         */
        $my_reviews = Rating::where('comment_user_id', $user_id)
                ->join('pwi_organization as ORG', 'ORG.org_id', '=', 'pwi_comments.comment_item_id')
                ->join("pwi_files AS LOGO", "LOGO.file_id", "=", "ORG.org_logo")
                ->orderby('comment_id', 'desc')
                ->get(['ORG.org_name', 'ORG.org_alias', 'pwi_comments.comment_text', 'pwi_comments.comment_rating', 'LOGO.file_orig_name as logoImg', 'LOGO.file_path as logoImgPath']);

        $reviews_list = array();
        if (count($my_reviews) > 0) {
            foreach ($my_reviews as $reviews) {
                $orgname = $reviews->org_name;
                $org_comment_text = $reviews->comment_text;
                $org_comment_rating = $reviews->comment_rating;
                $org_logo = $reviews->logoImg;
                $org_alias = $reviews->org_alias;

                $tmp = $reviews->toArray();
                if (file_exists(public_path() . Config::get("globals.orgImgPath") . $tmp["logoImgPath"])) {
                    $tmp["logoImgPath"] = Config::get("globals.orgImgPath") . $tmp["logoImgPath"];
                } else {
                    $tmp["logoImgPath"] = "/images/orgPlaceHolder.jpg";
                }
                $tmp['org_name'] = $orgname;
                $tmp['comment_text'] = $org_comment_text;
                $tmp['comment_rating'] = $org_comment_rating;
                $tmp['org_img'] = $org_logo;
                $tmp['org_alias'] = $org_alias;
                $reviews_list[] = $tmp;
            }
        }

        /*
         * Folling organization data
         */
        $follow_org = Follow::orderby('pwi_follow.follow_id', 'desc')
                ->join('pwi_organization as ORG', 'ORG.org_id', '=', 'pwi_follow.follow_type_id')
                ->join("pwi_files AS LOGO", "LOGO.file_id", "=", "ORG.org_logo")
                ->where('pwi_follow.follow_user_id', $user_id)
                ->where('pwi_follow.follow_status', '=', 'active')
                ->distinct()
                ->get(['ORG.org_name', 'ORG.org_alias', 'ORG.org_desc', 'ORG.org_id', 'LOGO.file_orig_name', 'LOGO.file_path as logoImgPath']);

        $follow_org_list = array();
        if (count($follow_org) > 0) {
            foreach ($follow_org as $org_value) {
                $follow_orgtmp = $org_value->toArray();
                if (file_exists(public_path() . Config::get('globals.orgImgPath') . $follow_orgtmp['logoImgPath'])) {
                    $follow_orgtmp['logoImgPath'] = Config::get("globals.orgImgPath") . $follow_orgtmp["logoImgPath"];
                } else {
                    $follow_orgtmp['logoImgPath'] = "/images/orgPlaceHolder.jpg";
                }

                $follow_orgtmp['org_name'] = $org_value->org_name;
                $follow_orgtmp['org_id'] = $org_value->org_id;
                $follow_orgtmp['org_alias'] = $org_value->org_alias;
                $follow_orgtmp['org_img'] = $org_value->file_orig_name;
                $follow_org_list[] = $follow_orgtmp;
            }
        }

        /*
         * user following country name
         */
        $follow_country = Follow::orderby('pwi_follow.follow_id', 'desc')
                        ->join('pwi_country as CN', 'CN.country_id', '=', 'pwi_follow.follow_type_id')
                        ->where('pwi_follow.follow_user_id', $user_id)
                        ->where('pwi_follow.follow_status', '=', 'active')
                        ->where('pwi_follow.follow_type', 'country')
                        ->distinct()->get(['CN.country_iso_code', 'CN.country_id', 'CN.country_name', 'CN.country_alias']);
        /*
         * user following causes name
         */
        $follow_causes = Follow::orderby('pwi_follow.follow_id', 'desc')
                        ->join('pwi_causes as CS', 'CS.cause_id', '=', 'pwi_follow.follow_type_id')
                        ->join("pwi_files AS LOGO", "LOGO.file_id", "=", "CS.cause_icon_img")
                        ->where('pwi_follow.follow_user_id', $user_id)
                        ->where('pwi_follow.follow_type', 'cause')
                        ->where('pwi_follow.follow_status', '=', 'active')
                        ->distinct()->get(['CS.cause_name', 'CS.cause_alias', 'CS.cause_id', 'LOGO.file_orig_name', 'LOGO.file_path as logoImgPath']);

        $follow_causes_list = array();
        if (count($follow_causes) > 0) {
            foreach ($follow_causes as $causes_value) {
                $tmp = $causes_value->toArray();
                $tmp['cause_name'] = $causes_value->cause_name;
                $tmp['cause_id'] = $causes_value->cause_id;
                $tmp['cause_alias'] = $causes_value->cause_alias;
                $tmp['icon_class'] = $this->helper->getCauseIconClass($causes_value->cause_id);
                $follow_causes_list[] = $tmp;
            }
        }


        $total_donation = Donations::where('user_id', $user_id)
                ->where('donation_status', 1)
                ->sum('donation_amount');
        $donations = $this->userRepo->getDonationInfo($user_id);

        $user_icon = Files::where('file_id', Auth::user()->user_photo_id)->first();


        /*
         * Get Organization Latest Video
         * 
         */
        $latest_video = Organizations::orderby('VIDEO.createdatetime', 'desc')
                ->join('pwi_org_videos as VIDEO', 'VIDEO.org_id', '=', 'pwi_organization.org_id')
                ->join('pwi_follow as PF', 'PF.follow_type_id', '=', 'pwi_organization.org_id')
                ->join('pwi_files as FILE', 'FILE.file_id', '=', 'pwi_organization.org_logo')
                ->where('PF.follow_user_id', '=', $user_id)
                ->where('PF.follow_type', 'org')
                ->where('PF.follow_status', '=', 'active')
                // ->orderby('PF.follow_id', '=', 'desc')
                ->take(1)
                ->get(['VIDEO.org_video_id', 'VIDEO.video_url', 'VIDEO.video_id', 'pwi_organization.org_name', 'pwi_organization.org_alias',
            'pwi_organization.org_desc', 'FILE.file_path']);

        $latest_video_list = array();
        foreach ($latest_video as $video) {
            $latest_video_tmp = array();
            $latest_video_tmp['org_name'] = $video->org_name;
            $latest_video_tmp['org_alias'] = $video->org_alias;
            $latest_video_tmp['description'] = $video->org_desc;
            if (file_exists(public_path() . Config::get('globals.orgImgPath') . $video->file_path)) {
                $latest_video_tmp['logoImgPath'] = Config::get("globals.orgImgPath") . $video->file_path;
            } else {
                $latest_video_tmp['logoImgPath'] = "/images/orgPlaceHolder.jpg";
            }
            $latest_video_tmp['org_video_id'] = $video->org_video_id;
            $latest_video_tmp['video_url'] = $video->video_url;
            $latest_video_tmp['video_id'] = $video->video_id;
            $latest_video_list[] = $latest_video_tmp;
        }

        /*
         * Get Organization Latest Photos
         * 
         */
        $latest_photos = Organizations::orderby('PHOTO.createdatetime', 'desc')
                ->join('pwi_org_photos as PHOTO', 'PHOTO.org_id', '=', 'pwi_organization.org_id')
                ->join('pwi_follow as PF', 'PF.follow_type_id', '=', 'pwi_organization.org_id')
                ->join('pwi_files as FILE', 'FILE.file_id', '=', 'pwi_organization.org_logo')
                ->where('PF.follow_user_id', '=', $user_id)
                ->where('PF.follow_type', 'org')
                ->where('PF.follow_status', '=', 'active')
                ->take(1)
                ->get(['PHOTO.org_photo_id', 'FILE.file_path', 'pwi_organization.org_name',
            'pwi_organization.org_id', 'pwi_organization.org_alias', 'pwi_organization.org_desc']);

        $l_p = 0;
        $photo_list = array();
        if (count($latest_photos) > 0) {
            foreach ($latest_photos as $f_org) {
                if ($l_p == 0) {
                    $photo_tmp = array();
                    $photo_tmp['org_name'] = $f_org->org_name;
                    $photo_tmp['org_name'] = $f_org->org_name;
                    $photo_tmp['org_alias'] = $f_org->org_alias;
                    $photo_tmp['org_desc'] = $f_org->org_desc;
                    if (file_exists(public_path() . Config::get('globals.orgImgPath') . $f_org->file_path)) {
                        $photo_tmp['logoImgPath'] = Config::get("globals.orgImgPath") . $f_org->file_path;
                    } else {
                        $photo_tmp['logoImgPath'] = "/images/orgPlaceHolder.jpg";
                    }

                    $latest_photo_data = Files::orderby('PHOTO.org_photo_id', 'desc')->join('pwi_org_photos as PHOTO', 'PHOTO.file_id', '=', 'pwi_files.file_id')
                            ->where('PHOTO.org_id', $f_org->org_id)
                            ->get(['pwi_files.file_path']);
                    $list = array();
                    if (count($latest_photo_data) > 0) {
                        $tmp = array();
                        foreach ($latest_photo_data as $photo) {
                            if (file_exists(public_path() . Config::get('globals.orgImgPath') . $photo->file_path)) {
                                $tmp['orgImg'] = Config::get("globals.orgImgPath") . $photo->file_path;
                            } else {
                                $tmp['orgImg'] = "/images/orgPlaceHolder.jpg";
                            }
                            $list[] = $tmp;
                        }
                    }
                    $photo_tmp['photo'] = json_encode($list);
                    $photo_list[] = $photo_tmp;
                    $l_p++;
                }
            }
        }

        /*
         * Organization Data
         * select PRO.org_id as Project_ID, PRO.project_title as Title from pwi_organization as ORG 
          join pwi_projects as PRO on PRO.org_id= ORG.org_id
          join pwi_files as ICON on ICON.file_id=ORG.org_logo
          join pwi_follow as FOLLOW on FOLLOW.follow_type_id = ORG.org_id
          where FOLLOW.follow_user_id =277
         */
        $org_data = Organizations::where('pwi_organization.org_featured', '=', 'Y')
                ->where('pwi_organization.org_status', '=', 'active')
                ->join('pwi_follow as p_fol', 'p_fol.follow_type_id', '=', 'pwi_organization.org_id')
                ->join('pwi_projects as PRO', 'PRO.org_id', '=', 'pwi_organization.org_id')
                ->join('pwi_files as ICON', 'ICON.file_id', '=', 'pwi_organization.org_logo')
                ->where('p_fol.follow_user_id', '=', $user_id)
                ->where('p_fol.follow_status', '=', 'active')
                ->get([
            'pwi_organization.org_name', 'pwi_organization.org_id', 'pwi_organization.org_alias', 'pwi_organization.org_desc',
            'pwi_organization.org_addressline1', 'pwi_organization.org_addressline2', 'pwi_organization.org_city', 'pwi_organization.org_state',
            'PRO.project_title', 'PRO.project_alias', 'PRO.project_icon', 'PRO.project_video_url', 'PRO.project_story', 'ICON.file_path'
        ]);

        $user_news_feed = array();
        if (!empty($org_data) && count($org_data) > 0) {
            foreach ($org_data as $org_feed_value) {
                $causes = Causes::where('pwi_org_cause.org_id', $org_feed_value->org_id)
                                ->join('pwi_org_cause', 'pwi_org_cause.cause_id', '=', 'pwi_causes.cause_id')->get(['pwi_causes.cause_name']);
                $feed_tmp = array();
                if (file_exists(public_path() . Config::get('globals.orgImgPath') . $org_feed_value->file_path)) {
                    $feed_tmp['logoImgPath'] = Config::get("globals.orgImgPath") . $org_feed_value->file_path;
                } else {
                    $feed_tmp['logoImgPath'] = "/images/orgPlaceHolder.jpg";
                }
                $feed_tmp['org_name'] = $org_feed_value->org_name;
                $feed_tmp['org_alias'] = $org_feed_value->org_alias;
                $feed_tmp['description'] = $org_feed_value->org_desc;
                $feed_tmp['address'] = $org_feed_value->org_addressline1 . ',' . $org_feed_value->org_addressline2;
                $feed_tmp['org_city'] = $org_feed_value->org_city;
                $feed_tmp['org_state'] = $org_feed_value->org_state;
                $feed_tmp['project_title'] = $org_feed_value->project_title;
                $feed_tmp['project_alias'] = $org_feed_value->project_alias;

                $project_icon = Files::where('file_id', $org_feed_value->project_icon)->first(['file_path', 'file_orig_name']);
                if (file_exists(public_path() . Config::get('globals.prjImgPath') . $project_icon->file_path)) {
                    $feed_tmp['projectImgPath'] = Config::get("globals.prjImgPath") . $project_icon->file_path;
                } else {
                    $feed_tmp['projectImgPath'] = "/images/cfPlaceholder.png";
                }
                $feed_tmp['project_video_url'] = $org_feed_value->project_video_url;
                $feed_tmp['causes'] = json_encode($causes);
                $feed_tmp['project_story'] = $org_feed_value->project_story;

                $user_news_feed[] = $feed_tmp;
            }
        }

        /*
         * Get Latest Organization List
         */
        /*
         * select ORG.org_name,PC.cause_name,PC.cause_id from pwi_follow 
          join pwi_org_cause as POC on POC.cause_id=pwi_follow.follow_type_id
          join pwi_organization as ORG on ORG.org_id =POC.org_id
          join pwi_files as LOGO on LOGO.file_id = ORG.org_logo
          join pwi_causes as PC on PC.cause_id = POC.cause_id
          where pwi_follow.follow_user_id =277
         */
        $latest_organizations = Follow::where('pwi_follow.follow_user_id', '=', $user_id)
                ->where('pwi_follow.follow_status', '=', 'active')
                ->join('pwi_org_cause as POC', 'POC.cause_id', '=', 'pwi_follow.follow_type_id')
                ->join('pwi_organization as ORG', 'ORG.org_id', '=', 'POC.org_id')
                ->join('pwi_files as LOGO', 'LOGO.file_id', '=', 'ORG.org_logo')
                ->join('pwi_causes as PC', 'PC.cause_id', '=', 'POC.cause_id')
                ->orderBy("RandomSort")
                ->distinct()
                ->take(3)
                ->get(['ORG.org_name', 'PC.cause_name', 'PC.cause_id', 'ORG.org_alias', 'LOGO.file_path as logoImgPath', 'LOGO.file_orig_name as fileName', 'PC.cause_alias']);
        $latest_org_list = array();
        if (count($latest_organizations) > 0) {
            foreach ($latest_organizations as $l_org) {
                $latest_org_tmp = array();
                $latest_org_tmp['org_name'] = $l_org->org_name;
                $latest_org_tmp['org_alias'] = $l_org->org_alias;
                $latest_org_tmp['cause_name'] = $l_org->cause_name;
                $latest_org_tmp['cause_alias'] = $l_org->cause_alias;
                $latest_org_tmp['file_name'] = $l_org->fileName;
                $latest_org_tmp['icon_class'] = $this->helper->getCauseIconClass($l_org->cause_id);
                if (file_exists(public_path() . Config::get('globals.orgImgPath') . $l_org->logoImgPath)) {
                    $latest_org_tmp['logoImgPath'] = Config::get("globals.orgImgPath") . $l_org->logoImgPath;
                } else {
                    $latest_org_tmp['logoImgPath'] = "/images/orgPlaceHolder.jpg";
                }
                $latest_org_list[] = $latest_org_tmp;
            }
        }
        /*
          $new_organization = Organizations::where("pwi_organization.org_featured", "=", "Y")
          ->where("pwi_organization.org_status", "=", "active")
          ->where("pwi_organization.org_logo", ">", "0")
          ->leftJoin("pwi_files AS LOGO", "LOGO.file_id", "=", "pwi_organization.org_logo")
          ->leftJoin("pwi_projects as pwp", "pwp.org_id", "=", "pwi_organization.org_id")
          ->groupBy("pwi_organization.org_id")
          ->orderBy("pwi_organization.org_id", "asc")
          ->take(1)
          ->get();
         */

        /* Get country list */
        $country = DB::select('SELECT `pwi_country`.`country_id`, `pwi_country`.`country_name` FROM `pwi_country` WHERE `pwi_country`.`country_id` NOT IN (SELECT `follow_type_id` FROM `pwi_follow`'
                        . ' WHERE `pwi_follow`.`follow_type_id`=`pwi_country`.`country_id` AND `pwi_follow`.`follow_user_id`="' . $user_id . '" AND `pwi_follow`.`follow_status`="active" AND `pwi_follow`.`follow_type`="country")');

        /* Get Causes List */
        $cause_list = DB::select("SELECT PC.cause_name AS name,PC.cause_alias AS cause_alias,PC.cause_id AS cause_id FROM pwi_causes AS PC WHERE PC.cause_parent_id=0 AND PC.cause_id NOT IN (SELECT pwi_follow.follow_type_id FROM pwi_follow WHERE pwi_follow.follow_type_id=PC.cause_id AND pwi_follow.follow_user_id='" . $user_id . "' AND pwi_follow.follow_type='cause' AND pwi_follow.follow_status='active') AND PC.cause_icon_img > 0 ");

        $causesList = array();
        if (count($cause_list) > 0) {
            foreach ($cause_list as $causesname) {
                $tmpArr = array();
                $tmpArr['cause_id'] = $causesname->cause_id;
                $tmpArr['cause_name'] = $causesname->name;
                $tmpArr['cause_alias'] = $causesname->cause_alias;
                $tmpArr['icon_class'] = $this->helper->getCauseIconClass($causesname->cause_id);
                $causesList[] = $tmpArr;
            }
        }

        // End Geting Causes
        $latest_product = Products::where('product_status', 'active')
                        ->join("pwi_files AS FILE", "FILE.file_id", "=", "pwi_products.product_image_id")
                        ->join("pwi_organization as ORG", 'ORG.org_id', '=', 'pwi_products.org_id')
                        ->join("pwi_follow", 'pwi_follow.follow_type_id', '=', 'pwi_products.product_id')
                        ->where('pwi_follow.follow_user_id', '=', $user_id)
                        ->where('pwi_follow.follow_status', '=', 'active')
                        ->orderby('product_id', 'desc')->take(1)->get([
            'ORG.org_name', 'FILE.file_path', 'pwi_products.product_name', 'pwi_products.product_alias', 'pwi_products.product_sales_price', 'pwi_products.product_image_id'
        ]);

        $product_list = array();

        if (count($latest_product) > 0) {
            foreach ($latest_product as $product_value) {
                $product_tmp = array();
                if (file_exists(public_path() . Config::get('globals.orgImgPath') . $product_value->file_path)) {
                    $product_tmp['logoImgPath'] = Config::get("globals.orgImgPath") . $product_value->file_path;
                } else {
                    $product_tmp['logoImgPath'] = "/images/prodPlaceholder.png";
                    $product_tmp['logoImgPath'] = "/images/orgPlaceHolder.jpg";
                }

                $product_tmp['org_name'] = $product_value->org_name;
                $product_tmp['org_alias'] = $product_value->org_alias;
                $product_tmp['product_name'] = $product_value->product_name;
                $product_tmp['product_alias'] = $product_value->product_alias;
                $product_tmp['sales_price'] = $product_value->product_sales_price;
                $product_image = Files::where('file_id', $product_value->product_image_id)->first();
                if (file_exists(public_path() . Config::get('globals.prdImgPath') . $product_image->file_path)) {
                    $product_tmp['proImgPath'] = Config::get("globals.prdImgPath") . $product_image->file_path;
                } else {
                    $product_tmp['proImgPath'] = "/images/prodPlaceholder.png";
                }
                $product_list[] = $product_tmp;
            }
        }

        // Get Following Country  News 
        $c = 0;
        $country_name = '';
        $country_names = array();
        if (count($follow_country) > 0) {
            foreach ($follow_country as $follow_country_value) {
                if ($c < 1) {
                    $country_name = $follow_country_value->country_name;
                    $country_names = $country_name;
                }
                $c++;
            }
            $country_news = $this->newsRepo->getNews($country_name);
            // $country_news = $this->newsRepo->getNews(implode(', ', $country_names));
        } else {
            $country_news = array();
        }
        // end 
        // Follow Causes News
        $causes_name = '';
        if (count($follow_causes) > 0) {
            $cn = 0;
            foreach ($follow_causes as $causes_newsvalue) {
                if ($cn < 1) {
                    $causes_name = $causes_newsvalue->cause_name;
                }
                $cn++;
            }
            $cause_news = $this->newsRepo->getNews($causes_name);
        } else {
            $cause_news = array();
        }
        //end
        return view('user/dashboard')->with([
                    "meta" => array('title' => 'Dashboard | Project World Impact', 'description' => ''),
                    "donations" => $donations,
                    "total_donation" => $total_donation,
                    "my_reviews" => $reviews_list,
                    "follow_org" => $follow_org_list,
                    "follow_country" => $follow_country,
                    "follow_causes" => $follow_causes_list,
                    "causes_list" => $causesList,
                    "crowfund" => $user_cf,
                    "user_icon" => $user_icon,
                    "org_data" => $org_data,
                    "latest_video" => $latest_video_list,
                    "latest_photo" => $photo_list,
                    "user_news_feed" => $user_news_feed,
                    "new_organization" => $latest_org_list,
                    "country" => $country,
                    "latest_product" => $product_list,
                    "country_news" => $country_news,
                    "causes_news" => $cause_news,
                ])->with('causes_name', $causes_name)->with('country_name', $country_name);
    }

    public function org_latest_video(Request $request) {
        if ($request->ajax()) {
            $org_video_id = $request->input('org_video_id');
            $latest_video = Videos::where('org_video_id', $org_video_id)->first();
            $url = $this->helper->url2embed($latest_video->video_url);
            if (!empty($latest_video)) {
                $status = 'OK';
            } else {
                $status = 'FAIL';
            }
            return Response::json(array('status' => $status, 'url' => $url));
        } else {
            return view('errors.404');
        }
    }

    public function getmore_country_news(Request $request) {
        if ($request->ajax()) {
            $user_id = Auth::user()->user_id;
            $follow_country = Follow::orderby('pwi_follow.follow_id', 'desc')
                    ->join('pwi_country as CN', 'CN.country_id', '=', 'pwi_follow.follow_type_id')
                    ->where('pwi_follow.follow_user_id', $user_id)
                    ->where('pwi_follow.follow_status', '=', 'active')
                    ->distinct()
                    ->get(['CN.country_name', 'CN.country_iso_code']);
            // Get Following Country  News 
            $c = 0;
            foreach ($follow_country as $follow_country_value) {
                if ($c < 1) {
                    $country_name = $follow_country_value->country_name;
                }
                $c++;
            }
            $news = $this->newsRepo->getNews($country_name);
            if (!empty($news) && count($news) > 0) {
                $status = "OK";
                $news = $news;
            } else {
                $status = "FAIL";
                $news = "";
            }
            return Response::json(array(
                        'status' => $status,
                        'news' => $news
            ));
        } else {
            return view('errors.404');
        }
    }

    public function getmore_causesnews(Request $request) {
        if ($request->ajax()) {
            $user_id = Auth::user()->user_id;
            /*
             * user following causes name
             */
            $follow_causes = Follow::orderby('pwi_follow.follow_id', 'desc')
                            ->join('pwi_causes as CS', 'CS.cause_id', '=', 'pwi_follow.follow_type_id')
                            ->join("pwi_files AS LOGO", "LOGO.file_id", "=", "CS.cause_icon_img")
                            ->where('pwi_follow.follow_user_id', $user_id)
                            ->where('pwi_follow.follow_status', '=', 'active')
                            ->distinct()->get(['CS.cause_name', 'LOGO.file_orig_name', 'LOGO.file_path']);
            // Follow Causes News
            $cn = 0;
            foreach ($follow_causes as $causes_newsvalue) {
                if ($cn < 1) {
                    $causes_name = $causes_newsvalue->cause_name;
                }
                $cn++;
            }
            $cause_news = $this->newsRepo->getNews($causes_name);
            if (!empty($cause_news) && count($cause_news) > 0) {
                $status = "OK";
                $news = $cause_news;
            } else {
                $status = "FAIL";
                $news = "";
            }
            return Response::json(array(
                        'status' => $status,
                        'news' => $news
            ));
        } else {
            return view('errors.404');
        }
    }

    public function settings(Request $request) {
        $user_id = Auth::user()->user_id;
        if ($request->ajax()) {
            $first_name=$request->input('first_name');
            $last_name=$request->input('last_name');
            $user_gender=$request->input('user_gender');
            $date_of_birth=$request->input('date_of_birth');
            
            $curr_username = $request->input('curr_username');
            $new_username = $request->input('new_username');

            $curr_password = $request->input('curr_password');
            $new_password = $request->input('new_password');

            $curr_email = $request->input('curr_email');
            $new_email = $request->input('new_email');
            $user_bio = $request->input('user_bio');

            $user_data = User::find($user_id);
            $user_data->user_firstname = $first_name;
            $user_data->user_lastname = $last_name;
            $user_data->user_gender = $user_gender;
            $user_data->user_dob = $date_of_birth;
            $user_data->user_bio = $user_bio;
            if (!empty($new_password)) {
                $user_data->password = md5($new_password);
            }
            if (!empty($new_username)) {
                $username_data = User::where('user_username', $new_username)->where('user_id', '<>', $user_id)->first();
                if (empty($username_data)) {
                    $user_data->user_username = $new_username;
                } else {
                    return Response::json(array(
                                'msg' => "Username already exist.",
                                'status' => 'failed'
                    ));
                }
            }
            if (!empty($new_email)) {
                $user_emaildata = User::where('user_email', $new_email)->where('user_id', '<>', $user_id)->first();
                if (empty($user_emaildata)) {
                    $user_data->user_email = $new_email;
                } else {
                    return Response::json(array(
                                'msg' => "Email already exist",
                                'status' => 'failed'
                    ));
                }
            }
            if ($user_data->save()) {
                $msg = "Successfully Changed";
                $status = 'success';
                return Response::json(array(
                            'msg' => $msg,
                            'status' => $status
                ));
            }
        } else {
            /* login user details */
            $data = User::where('user_id', $user_id)->first();
            $user_icon = Files::where('file_id', $data->user_photo_id)->first();
            $shiping_data = UserAddress::where('user_addr_user_id', $user_id)->get(); // login user address
            /* get social data for loged in user */
            $social_data = DB::table('pwi_users_social')
                            ->leftJoin('pwi_social_media', 'pwi_social_media.social_media_id', '=', 'pwi_users_social.social_media_id')
                            ->where('pwi_users_social.user_id', $user_id)->get();

            $social_media = array();
            if (count($social_data) > 0) {
                foreach ($social_data as $key => $social_value) {
                    $social_media[$key] = array(
                        "name" => $social_value->social_media_name,
                        "status" => $social_value->social_media_user_status,
                        "media_id" => $social_value->social_media_id
                    );
                }
            }

            /* get total donation amount */
            $total_donation = Donations::where('user_id', $user_id)
                    ->where('donation_status', 1)
                    ->sum('donation_amount');
            $donations = $this->userRepo->getDonationInfo($user_id);
            return view('user.settings')->with([
                        "meta" => array('title' => 'Settings | Project World Impact', 'description' => ''),
                        'user_data' => $data,
                        'shiping_data' => $shiping_data,
                        'total_donation' => $total_donation,
                        'donations' => $donations,
                        'user_icon' => $user_icon,
                        "socialmedia" => $social_media
            ]);
        }
    }

    public function check_existing_email(Request $request) {
        if ($request->ajax()) {
            $user_id = Auth::user()->user_id;
            $new_email = $request->input('email');
            $user_emaildata = User::where('user_email', $new_email)->where('user_id', '<>', $user_id)->first();
            if (!empty($user_emaildata)) {
                return Response::json(array(
                            'msg' => "Email already exist",
                            'status' => 'FAILED'
                ));
            } else {
                return Response::json(array(
                            'msg' => "",
                            'status' => 'OK'
                ));
            }
        } else {
            return view('errors.404');
        }
    }

    public function check_user_name(Request $request) {
        if ($request->ajax()) {
            $user_id = Auth::user()->user_id;
            $username = $request->input('username');

            $username_data = User::where('user_username', $username)->where('user_id', '<>', $user_id)->first();
            if (!empty($username_data)) {
                return Response::json(array(
                            'msg' => "Username already exist",
                            'status' => 'FAILED'
                ));
            } else {
                return Response::json(array(
                            'msg' => "",
                            'status' => 'OK'
                ));
            }
        } else {
            return view('errors.404');
        }
    }

    public function change_profile_image(Request $request) {
        $output = array();
        $output['status'] = 'KO';
        if ($request->ajax()) {
            $user_photo_id = Auth::user()->user_photo_id;
            $user_id = Auth::user()->user_id;
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $extension = $file->getClientOriginalExtension();
                $mime_type = $file->getClientMimeType();
                $file_type = $file->getType();
                $file_size = $file->getClientSize();
                $directory = '/images/user';
                if (!is_dir(public_path('images/user'))) {
                    @mkdir(public_path('images/user'));
                }
                $data = getimagesize($file);
                if ($data) {
                    $width = $data[0];
                    $height = $data[1];
                    $filename = md5("pwi-user-$user_id") . ".{$extension}";

                    $output['url'] = asset('images/user/' . $filename . '?' . time());

                    $upload = Image::make($file, array(
                                'width' => 300,
                                'height' => 300,
                                'crop' => true,
                                'grayscale' => false
                            ))->save('images/user/' . $filename);
                    if ($upload) {
                        if (!empty($user_photo_id)) {
                            $file_data = Files::find($user_photo_id);
                            $file_data->file_orig_name = $filename;
                            $file_data->file_path = $filename;
                            $file_data->file_extension = $extension;
                            $file_data->file_mime_type = $mime_type;
                            $file_data->file_type = $file_type;
                            $file_data->file_size = $file_size;
                            $file_data->file_width = $width;
                            $file_data->file_height = $height;
                            if ($file_data->save()) {
                                $output['status'] = 'OK';
                                $output['msg'] = 'Successfully Changed Profile Picture';
                                $output['user_photo_id'] = $user_photo_id;
                            }
                        } else {
                            $save_data = new Files();
                            $save_data->file_orig_name = $filename;
                            $save_data->file_path = $filename;
                            $save_data->file_extension = $extension;
                            $save_data->file_mime_type = $mime_type;
                            $save_data->file_type = $file_type;
                            $save_data->file_size = $file_size;
                            $save_data->file_width = $width;
                            $save_data->file_height = $height;
                            if ($save_data->save()) {
                                $file_id = $save_data->file_id;
                                $update = User::find($user_id);
                                $update->user_photo_id = $file_id;
                                $update->save();
                                $output['status'] = 'OK';
                                $output['msg'] = 'Profile picture upload success!';
                            } else {
                                
                            }
                        }
                    } else {
                        $output['msg'] = $file->getErrorMessage();
                    }
                } else {
                    $output['msg'] = 'Selected file not valid image format!';
                }
            } else {
                $output['msg'] = 'Please select an image!';
            }
            return Response::json($output);
        } else {
            return view('errors.404');
        }
    }

    public function user_social_media(Request $request) {
        $user_id = Auth::user()->user_id;
        if ($request->ajax()) {
            $social_media_id = $request->input('social_media_id');
            $social_media_status = $request->input('social_status');
            $social_media_name = $request->input('social_name');
            $row = Media::find($social_media_id);
            $row->social_media_user_status = $social_media_status;
            if ($row->save()) {
                $user_social_data = UserSocialMedia::where('user_id', $user_id)->where('social_media_id', $social_media_id)->get();
                if (emptyArray($user_social_data)) {
                    $save_social_data = new UserSocialMedia();
                    $save_social_data->user_id = $user_id;
                    $save_social_data->social_media_id = $social_media_id;
                    $save_social_data->save();
                }
                if ($social_media_status == 'Y') {
                    $status = "Y";
                    $value = "Enabled";
                } else {
                    $status = "N";
                    $value = "Disabled";
                }
                $success_status = "OK";
            } else {
                $success_status = "FAIL";
                $value = "";
                $status = "";
            }
            return Response::json(array(
                        'success' => $success_status,
                        'status' => $status,
                        "media_name" => $social_media_name,
                        'value' => $value
            ));
        } else {
            return view('errors.404');
        }
    }

    public function user_billpref_address(Request $request) {
        if ($request->ajax()) {
            $address = $request->input('address');
            $city = $request->input('city');
            $state = $request->input('state');
            $zipcode = $request->input('zipcode');
            $save_data = new UserAddress();
            $save_data->user_addr_line1 = $address;
            $save_data->user_addr_city = $city;
            $save_data->user_addr_state = $state;
            $save_data->user_addr_zip = $zipcode;
            $save_data->user_addr_user_id = Auth::user()->user_id;
            $save_data->user_addr_address_type = 'billing';
            if ($save_data->save()) {
                $status = "success";
                $insert_id = $save_data->user_addr_id;
            } else {
                $status = "failed";
                $insert_id = 0;
            }
            $firstname = Auth::user()->user_firstname;
            $firstname = ($firstname == 'null' || $firstname == '' || $firstname == null) ? '' : $firstname;
            $lastname = Auth::user()->user_lastname;
            $lastname = ($lastname == 'null' || $lastname == '' || $lastname == null) ? '' : $lastname;
            return Response::json(array(
                        'status' => $status,
                        'insert_id' => $insert_id,
                        'first_name' => $firstname,
                        'last_name' => $lastname,
                        'address' => $address,
                        'city' => $city,
                        'state' => $state,
                        'zipcode' => $zipcode,
            ));
        } else {
            return view('errors.404');
        }
    }

    public function user_shippref_address(Request $request) {
        if ($request->ajax()) {
            $address = $request->input('address');
            $city = $request->input('city');
            $state = $request->input('state');
            $zipcode = $request->input('zipcode');
            $save_data = new UserAddress();
            $save_data->user_addr_line1 = $address;
            $save_data->user_addr_city = $city;
            $save_data->user_addr_state = $state;
            $save_data->user_addr_zip = $zipcode;
            $save_data->user_addr_user_id = Auth::user()->user_id;
            $save_data->user_addr_address_type = 'shipping';
            if ($save_data->save()) {
                $status = "success";
                $insert_id = $save_data->user_addr_id;
            } else {
                $status = "failed";
                $insert_id = 0;
            }
            $firstname = Auth::user()->user_firstname;
            $firstname = ($firstname == 'null' || $firstname == '' || $firstname == null) ? '' : $firstname;
            $lastname = Auth::user()->user_lastname;
            $lastname = ($lastname == 'null' || $lastname == '' || $lastname == null) ? '' : $lastname;
            return Response::json(array(
                        'status'     => $status,
                        'insert_id'  => $insert_id,
                        'first_name' => $firstname,
                        'last_name'  => $lastname,
                        'address'    => $address,
                        'city'       => $city,
                        'state'      => $state,
                        'zipcode'    => $zipcode,
            ));
        } else {
            return view('errors.404');
        }
    }

    public function user_shipp_address_delete(Request $request) {
        if ($request->ajax()) {
            $user_add_id = $request->input('user_add_id');
            $ship_address = UserAddress::find($user_add_id);
            if ($ship_address->delete()) {
                $status = "success";
                $msg = "Successfully deleted.";
            } else {
                $status = "failed";
                $msg = "Delete failed";
            }
            return Response::json(array(
                        'status' => $status,
                        'msg' => $msg
            ));
        } else {
            return view('errors.404');
        }
    }

    public function user_bill_address_delete(Request $request) {
        if ($request->ajax()) {
            $user_add_id = $request->input('user_add_id');
            $bill_address = UserAddress::find($user_add_id);
            if ($bill_address->delete()) {
                $status = 'success';
                $msg = "Successfully Delete";
            } else {
                $status = "failed";
                $msg = "Delete Failed";
            }
            return Response::json(array(
                        'status' => $status,
                        'msg' => $msg
            ));
        } else {
            return view('errors.404');
        }
    }

    public function news_letter(Request $request) {
        if ($request->ajax()) {
            $user_id = Auth::user()->user_id;
            $update_type = $request->input('update_type');
            $update = User::find($user_id);
            $update->news_update_type = $update_type;
            if ($update->save()) {
                $status = "Success";

                $msg = "Successfully Saved";
            } else {
                $status = "Failed";
                $msg = "Saved Failed";
            }
            return Response::json(array(
                        'update_type' => $update_type,
                        'status' => $status,
                        'msg' => $msg
            ));
        } else {
            return view('errors.404');
        }
    }

    public function orders(Request $request) {
        $user_id = Auth::user()->user_id;
        if ($request->ajax()) {
            $order_id = $request->input('oder_detail_id');
            $data = ProductDetail::where('order_id', $order_id)
                    ->join('pwi_organization as ORG', 'ORG.org_id', '=', 'pwi_order_details.org_id')
                    ->get();
            $order_data = ProductMaster::where('pwi_order_master.user_id', $user_id)
                    ->where('pwi_order_master.order_id', $order_id)
                    ->join('pwi_order_status', 'pwi_order_status.order_status_id', '=', 'pwi_order_master.order_status')
                    ->first();
            return view('user.order_details')->with('data', $data)->with('order_data', $order_data);
        } else {

            $data = ProductMaster::where('user_id', $user_id)->orderby('order_id', 'desc')
                    ->join('pwi_order_status', 'pwi_order_status.order_status_id', '=', 'pwi_order_master.order_status')
                    ->get();

            $latest_product = Products::orderby('product_id', 'desc')
                            ->join('pwi_follow as pro_follow', 'pro_follow.follow_type_id', '=', 'pwi_products.product_id')
                            ->join("pwi_files AS FILE", "FILE.file_id", "=", "pwi_products.product_image_id")
                            ->join("pwi_organization as ORG", 'ORG.org_id', '=', 'pwi_products.org_id')
                            ->where('pro_follow.follow_user_id', '=', $user_id)
                            ->where('pro_follow.follow_status', '=', 'active')
                            ->take(1)->get(['pwi_products.product_name', 'pwi_products.product_alias', 'pwi_products.product_sales_price',
                'ORG.org_name', 'ORG.org_alias', 'ORG.org_alias', 'FILE.file_path', 'ORG.org_logo'
            ]);
            $product_list = array();
            if (count($latest_product) > 0) {
                foreach ($latest_product as $product_value) {
                    $product_tmp = array();
                    if (file_exists(public_path() . Config::get('globals.prdImgPath') . $product_value->file_path)) {
                        $product_tmp['logoImgPath'] = Config::get("globals.prdImgPath") . $product_value->file_path;
                    } else {
                        $product_tmp['logoImgPath'] = "/images/prodPlaceholder.png";
                    }
                    $product_tmp['product_name'] = $product_value->product_name;
                    $product_tmp['product_alias'] = $product_value->product_alias;
                    $product_tmp['product_sales_price'] = $product_value->product_sales_price;
                    $product_tmp['org_name'] = $product_value->org_name;
                    $product_tmp['org_alias'] = $product_value->org_alias;
                    $org_images = Files::where('file_id', '=', $product_value->org_logo)->first();
                    if (file_exists(public_path() . Config::get('globals.orgImgPath') . $product_value->file_path)) {
                        $product_tmp['orgImgPath'] = Config::get("globals.orgImgPath") . $product_value->file_path;
                    } else {
                        $product_tmp['orgImgPath'] = "/images/orgPlaceHolder.jpg";
                    }
                }
                $product_list[] = $product_tmp;
            }
            return view('user.orders')->with([
                        "meta" => array('title' => 'Orders | Project World Impact', 'description' => ''),
                        'order' => $data,
                        'latest_product' => $product_list
            ]);
        }
    }

    public function oder_message(Request $request) {
        if ($request->ajax()) {
            $first_name = $request->input('first_name');
            $last_name = $request->input('last_name');
            $order_id = $request->input('order_ref');
            $order_msg = $request->input('order_msg');
            $user_id = Auth::user()->user_id;
            $save_data = new OrderMessage();
            $save_data->message_text = $order_msg;
            $save_data->user_id = $user_id;
            $save_data->order_id = $order_id;
            if ($save_data->save()) {
                return Response::json(array(
                            'status' => 'success',
                            'msg' => 'Successfully Send Contact Message'
                ));
            }
        } else {
            return view('errors.404');
        }
    }

    public function search_country(Request $request) {
        $return_array = array();
        if ($request->ajax()) {
            $term = Input::get('term');
            $data = DB::table("pwi_country")->distinct()->select('country_name')->where('country_name', 'LIKE', $term . '%')->groupBy('country_name')->take(10)->get();
            foreach ($data as $v) {
                $return_array[] = array('value' => $v->country_name);
            }
            return Response::json($return_array);
        } else {
            return view('errors.404');
        }
    }

    /**
     * Ajax function
     * @description save and get follow country
     * @param country_id int
     */
    public function follow_country(Request $request) {
        if ($request->ajax()) {
            $user_id = Auth::user()->user_id;
            $follow_type = 'country';
            $follow_started_from = date('Y-m-d h:i:s');
            $follow_status = 'active';
            $country_id = $request->input('country');
            if (count($country_id) > 0) {
                $getFollow = Follow::where('follow_type_id', $country_id)
                        ->where('follow_type', $follow_type)
                        ->where('follow_user_id', $user_id)
                        ->first();
                if (!empty($getFollow)) {
                    $follow_id = $getFollow->follow_id;
                    $update_follow_country = Follow::find($follow_id);
                    $update_follow_country->follow_status = $follow_status;
                    if ($update_follow_country->save()) {
                        $country = Country::where('country_id', $country_id)->get()->take(1);
                    }
                } else {
                    foreach ($country_id as $value) {
                        $save_data = new Follow();
                        $save_data->follow_type = $follow_type;
                        $save_data->follow_type_id = $value;
                        $save_data->follow_user_id = $user_id;
                        $save_data->follow_started_from = $follow_started_from;
                        $save_data->follow_status = $follow_status;
                        if ($save_data->save()) {
                            $country = Country::where('country_id', $country_id)->get()->take(1);
                        }
                    }
                }

                return Response::json(array('data' => $country, 'status' => "OK"));
            }
        } else {
            return view('errors.404');
        }
    }

    /*
     * Following causes
     */

    public function follow_causes(Request $request) {
        $output = array();
        $output['status'] = 'KO';
        $output['msg'] = '';

        if ($request->ajax()) {
            $user_id = Auth::user()->user_id;
            $follow_type = "cause";
            $follow_type_id = $request->input('causes_id');
            $follow_started_from = date('Y-m-d H:i:s');
            $follow_status = 'active';

            if (!empty($follow_type_id)) {
                $follow_causes = Causes::where('cause_id', $follow_type_id)->get(['cause_id', 'cause_name', 'cause_alias']);
                if (!$follow_causes->isEmpty()) {
                    $getFollow = Follow::where('follow_type_id', $follow_type_id)
                            ->where('follow_type', $follow_type)
                            ->where('follow_user_id', $user_id)
                            ->first();
                    if (!empty($getFollow)) {
                        $follow_id = $getFollow->follow_id;
                        $update_follow = Follow::find($follow_id);
                        $update_follow->follow_status = 'active';
                        if ($update_follow->save()) {
                            $output['status'] = 'OK';
                            $output['msg'] = 'Following ';
                            $list = array();
                            foreach ($follow_causes as $c_value) {
                                $tmpArr = array();
                                $tmpArr['cause_id'] = $c_value->cause_id;
                                $tmpArr['cause_name'] = $c_value->cause_name;
                                $tmpArr['cause_alias'] = $c_value->cause_alias;
                                $tmpArr['icon_class'] = $this->helper->getCauseIconClass($c_value->cause_id);
                                $causesList[] = $tmpArr;
                            }
                            $output['causes'] = $causesList;
                        } else {
                            $output['msg'] = 'Saving failed! Try again.';
                        }
                    } else {
                        $save_follow = new Follow();
                        $save_follow->follow_type = $follow_type;
                        $save_follow->follow_type_id = $follow_type_id;
                        $save_follow->follow_user_id = $user_id;
                        $save_follow->follow_started_from = $follow_started_from;
                        $save_follow->follow_status = $follow_status;
                        if ($save_follow->save()) {
                            $output['status'] = 'OK';
                            $output['msg'] = 'Following ';
                            $list = array();
                            foreach ($follow_causes as $c_value) {
                                $tmpArr = array();
                                $tmpArr['cause_id'] = $c_value->cause_id;
                                $tmpArr['cause_name'] = $c_value->cause_name;
                                $tmpArr['cause_alias'] = $c_value->cause_alias;
                                $tmpArr['icon_class'] = $this->helper->getCauseIconClass($c_value->cause_id);
                                $causesList[] = $tmpArr;
                            }
                            $output['causes'] = $causesList;
                        } else {
                            $output['msg'] = 'Saving failed! Try again.';
                        }
                    }
                } else {
                    $output['msg'] = 'Requested cause data not found!';
                }
            } else {
                $output['msg'] = 'Causes ID missing!';
            }
            return Response::json($output);
        } else {
            return view('errors.404');
        }
    }

    /*
     * Remove From Following List 
     */

    public function remove_follow(Request $request) {
        if ($request->ajax()) {
            $user_id = Auth::user()->user_id;
            $follow_type_id = $request->input('follow_type_id');
            $follow_type = $request->input('remove_from_follow');
            $follow_data = Follow::where('follow_type_id', $follow_type_id)
                    ->where('follow_type', $follow_type)
                    ->where('follow_user_id', $user_id)
                    ->first();

            $follow_status = Follow::find($follow_data->follow_id);
            $follow_status->follow_status = 'inactive';
            if ($follow_status->save()) {
                $cause = array();
                if ($follow_type == 'cause') {
                    $get_causes_item = Causes::where('cause_id', $follow_type_id)->get();
                    if (count($get_causes_item) > 0) {
                        foreach ($get_causes_item as $c_item_value) {
                            $cause['cause_id'] = $c_item_value->cause_id;
                            $cause['cause_name'] = $c_item_value->cause_name;
                            $cause['cause_alias'] = $c_item_value->cause_alias;
                            $cause['icon_class'] = $this->helper->getCauseIconClass($c_item_value->cause_id);
                        }
                    }
                }
                $status = "OK";
                $msg = "Successfully unfollowed";
            } else {
                $cause = array();
                $status = "FAIL";
                $msg = "Action Failed!";
            }
            return Response::json(array(
                        "status" => $status,
                        "list" => $cause,
                        'msg' => $msg
            ));
        } else {
            return view('errors.404');
        }
    }

}
