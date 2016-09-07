<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Donations;
use App\Organizations;
use App\Causes;
use App\Follow;
use App\Files;
use App\User;
use App\Country;
use Config;
use DB;

class UserRepository {

    /**
     * findByUserEmailorCreate	
     *
     * @param $userData - Socialite User
     *
     * @param $type - string depicting which social platform is being used
     *
     * @return User Object	
     */
    public function findByUserEmailOrCreate($userData, $type) {
        if (strtolower($type) == "twitter") {
            $userData->email = $userData->id . "_twitter@pwi.com";
        }

        $user = User::where('user_email', '=', $userData->email)
                ->leftJoin('pwi_organization AS ORG', function( $join) {
                    $join->on('pwi_users.user_org_id', '=', 'ORG.org_id')
                    ->where('ORG.org_status', '=', 'active');
                })
                ->select("pwi_users.*", "ORG.org_id AS orgAdminId")
                ->first();

        if (is_null($user)) {

            $id = $this->setUserData($userData, $type);

            return User::find($id);
        } else {

            $social_media_id = "";

            if ($type == "facebook") {
                $this->updateSocialAvatar($user->user_id, 1, $userData->avatar);
            } else if ($type == "twitter") {
                $this->updateSocialAvatar($user->user_id, 2, $userData->avatar);
            } else if ($type == "google") {
                $this->updateSocialAvatar($user->user_id, 4, $userData->avatar);
            }

            return $user;
        }
    }

    /**
     * isFollowing
     *
     * @param User:$user
     *
     * @param string:$type
     *
     * @param integer:$id
     *
     * @return bool 	
     */
    public function isFollowing($user, $type, $id) {

        $following = Follow::where('follow_type', '=', $type)
                ->where('follow_type_id', '=', $id)
                ->where('follow_user_id', '=', $user->user_id)
                ->where('follow_status', '=', 'active')
                ->get();

        if (sizeof($following) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * getUserIcon
     *
     * @param integer:$user_id
     *
     * @param integer:$user_photo_id
     *
     * @return string 	
     */
    public function getUserIcon($user_id, $user_photo_id, Request $request) {
        if ($user_photo_id != NULL && $request->session()->has('social-platform')) {
            $request->session()->forget('social-platform');
        }
        if ($request->session()->has('social-platform')) {

            $platform = $request->session()->get('social-platform');

            $social_media_id = 0;

            switch (trim($platform)) {
                case "facebook":
                    $social_media_id = 1;
                    break;
                case "twitter":
                    $social_media_id = 2;
                    break;
                case "google":
                    $social_media_id = 4;
                    break;
            }
            $row = $this->getSocialAvatar($user_id, $social_media_id);
            if (!empty($row[0]->avatar)) {
                return $row[0]->avatar;
            }
        }

        $file = Files::find($user_photo_id);

        if (sizeof($file) > 0) {
            return Config::get("globals.usrImgPath") . $file->file_path;
        } else {
            return "";
        }
    }

    function getProfileImage($user_id, $user_photo_id) {
        $file = Files::find($user_photo_id);
        if (sizeof($file) > 0) {
            return Config::get("globals.usrImgPath") . $file->file_path;
        } else {
            return "";
        }
    }

    /**
     * PRIVATE getUnique
     *
     * @param $value 
     *
     * @param $field
     *
     * @return string 
     */
    private function getUnique($value, $field) {

        $user = User::where('user_' . $field, "=", $value)->first();

        if (is_null($user)) {
            return $value;
        } else {
            return $this->generateUnique($value, $field);
        }
    }

    /**
     * PRIVATE generateUnique
     *
     * @param $value
     *
     * @param $field
     *
     * @return string
     */
    private function generateUnique($value, $field) {

        $seed = 1;

        $unique = FALSE;

        $returnValue = "";

        while (!$unique) {
            $user = User::where('user_' . $field, "=", $value . $seed)->first();

            if (is_null($user)) {
                $unique = TRUE;
                $returnValue = $value . $seed;
            }
        }
        return $returnValue;
    }

    /**
     * PRIVATE setUserData
     *
     * @param $data - user object
     *
     * @param $type - string depicting which social platform is being used
     *
     * @return integer
     */
    private function setUserData($data, $type) {

        $insertData = array();
        $avatar = "";
        $id = "";
        $social_media_id = "";

        switch ($type) {

            case "twitter":
                $insertData = $this->twitter($data);
                $avatar = $data->avatar;
                $social_media_id = 2;
                break;
            case "facebook":
                $insertData = $this->facebook($data);
                $avatar = $data->avatar;
                $social_media_id = 1;
                break;
            case "google":
                $insertData = $this->google($data);
                $avatar = $data->user["image"]["url"];
                $social_media_id = 4;
                break;
        }

        $id = DB::table('pwi_users')->insertGetId($insertData);

        $this->setSocialMediaData($id, $data->id, $social_media_id, $avatar);

        return $id;
    }

    /**
     * PRIVATE setSocialMediaData
     *
     * @param $uId - integer:user id
     *
     * @param $fId - long int:id returned from social media
     *
     * @param $sId - integer:social media table id
     *
     * @param $avatar - string(url):avatar image url
     */
    private function setSocialMediaData($uId, $fId, $sId, $avatar) {

        DB::table('pwi_users_social')->insert(["user_id" => $uId, "social_id" => $fId, "social_media_id" => $sId, "avatar" => $avatar]);
    }

    /**
     * PRIVATE updateSocialAvatar
     *
     * @param $uId - integer:user id
     *
     * @param $sId - integer:social media table id
     *
     * @param $avatar - string(url):avatar image url
     */
    private function updateSocialAvatar($uId, $sId, $avatar) {

        DB::table('pwi_users_social')
                ->where("user_id", "=", $uId)
                ->where("social_media_id", "=", $sId)
                ->update(["avatar" => $avatar]);
    }

    private function getSocialAvatar($uId, $sId) {

        return DB::table("pwi_users_social")
                        ->select("avatar")
                        ->where("user_id", "=", $uId)
                        ->where("social_media_id", "=", $sId)
                        ->get();
    }

    /**
     * PRIVATE twitter
     *
     * @param $data - Socialite User
     *
     * @return array	
     */
    private function twitter($data) {

        $username = "";
        $user_alias = "";

        $user_name = $this->getUnique($data->nickname, "username");
        $user_alias = $this->getUnique($data->nickname, "alias");


        return ['user_email' => $data->email, 'user_type' => "user", 'user_username' => $user_name, 'user_alias' => $user_alias, 'user_email_notification_status' => 'N', 'user_anonymous_profile_status' => 'N', 'user_joinedon' => Carbon::now(), "user_status" => "active"];
    }

    /**
     * PRIVATE facebook
     *
     * @param $data - Socialite User
     *
     * @return array	
     */
    private function facebook($data) {

        list( $email_prefix, $email_suffix ) = explode("@", $data->email);

        $user_name = $this->getUnique($email_prefix, "username");
        $user_alias = $this->getUnique($data->id, "alias");

        if (!isset($data->user["first_name"])) {
            $data->user["first_name"] = "";
        }

        if (!isset($data->user["last_name"])) {
            $data->user["last_name"] = "";
        }

        return ['user_firstname' => $data->user["first_name"], 'user_lastname' => $data->user["last_name"], 'user_email' => $data->email, 'user_type' => "user", 'user_username' => $user_name, 'user_alias' => $user_alias, 'user_email_notification_status' => 'N', 'user_anonymous_profile_status' => 'N', 'user_joinedon' => Carbon::now(), "user_status" => "active"];
    }

    /**
     * PRIVATE google
     *
     * @param $data - Socialite User
     *
     * @return array	
     */
    private function google($data) {
        list( $email_prefix, $email_suffix ) = explode("@", $data->email);

        if (is_null($data->nickname)) {
            $user_name = $this->getUnique($data->nickname, "username");
        } else {
            $user_name = $this->getUnique($email_prefix, "username");
        }

        $user_alias = $this->getUnique($data->id, "alias");

        return ['user_firstname' => $data->user["name"]["givenName"], 'user_lastname' => $data->user["user"]["familyName"], 'user_email' => $data->email, 'user_type' => "user", 'user_username' => $user_name, 'user_alias' => $user_alias, 'user_email_notification_status' => 'N', 'user_anonymous_profile_status' => 'N', 'user_joinedon' => Carbon::now(), "user_status" => "active"];
    }

    public function userimage_resize($source_path) {

        list($source_width, $source_height, $source_type) = getimagesize($source_path);

        switch ($source_type) {
            case IMAGETYPE_GIF:
                $source_gdim = imagecreatefromgif($source_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gdim = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $source_gdim = imagecreatefrompng($source_path);
                break;
        }

        //calculating 16:9 ratio
        $new_width = $source_width;
        $new_height = 9 * $source_width / 16;

        //if output height is longer then width
        if ($source_height < $new_height) {
            $new_height = $source_height;
            $new_width = 16 * $new_height / 9;
        }

        $x_o = $source_width - $new_width;
        $y_o = $source_height - $new_height;

        if ($x_o + $new_width > $source_width)
            $new_width = $source_width - $x_o;

        if ($y_o + $new_height > $source_height)
            $new_height = $source_height - $y_o;



        define('DESIRED_IMAGE_WIDTH', $new_width);
        define('DESIRED_IMAGE_HEIGHT', $new_height);

        $source_aspect_ratio = $source_width / $source_height;
        $desired_aspect_ratio = DESIRED_IMAGE_WIDTH / DESIRED_IMAGE_HEIGHT;

        if ($source_aspect_ratio > $desired_aspect_ratio) {
            /*
             * Triggered when source image is wider
             */
            $temp_height = DESIRED_IMAGE_HEIGHT;
            $temp_width = (int) (DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else {
            /*
             * Triggered otherwise (i.e. source image is similar or taller)
             */
            $temp_width = DESIRED_IMAGE_WIDTH;
            $temp_height = (int) (DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
        }

        /*
         * Resize the image into a temporary GD image
         */

        $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);

        imagecopyresampled(
                $temp_gdim, $source_gdim, 0, 0, 0, 0, $temp_width, $temp_height, $source_width, $source_height
        );

        /*
         * Copy cropped region from temporary image into the desired GD image
         */

        $x0 = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
        $y0 = ($temp_height - DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim = imagecreatetruecolor(DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);
        echo "<pre>";
        imagecopy(
                $desired_gdim, $temp_gdim, 0, 0, $x0, $y0, DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT
        );

        header('Content-type: image/jpeg');
        var_dump(imagejpeg($desired_gdim, "images/filename.jpg"));
    }

    public function getCountryById($id) {
        $country = Country::where('country_id', $id)->first();
        return $country;
    }

    public function getCountryByAlias($country_alias) {
        $country = Country::where('country_alias', $country_alias)->first();
        return $country;
    }

    public function getDonationInfo($user_id) {
        $results = array();
        $output = array();
        $donations = Donations::where('user_id', $user_id)->where('donation_status', 1)->orderBy('item_id', 'ASC')->get()->toArray();
        $item_id = -1;

        if (count($donations) > 0) {
            foreach ($donations as $donation) {
                $results[$donation['item_id']][] = $donation;
            }
            foreach ($results as $com_id => $datas) {
                if ($datas[0]['item_type'] == 'organization') {
                    $name = Organizations::where('org_id', $com_id)->pluck('org_name')->toArray();
                    $name = (count($name) > 0) ? $name[0] : '';
                } else if ($datas[0]['item_type'] == 'cause') {
                    $name = Causes::where('cause_id', $com_id)->pluck('cause_name')->toArray();
                    $name = (count($name) > 0) ? $name[0] : '';
                } else if ($datas[0]['item_type'] == 'country') {
                    $name = Country::where('country_id', $com_id)->pluck('country_name')->toArray();
                    $name = (count($name) > 0) ? $name[0] : '';
                } else {
                    $name = '';
                }

                $don = array();
                if (count($datas) > 0) {
                    foreach ($datas as $donation) {
                        $don[] = array(
                            'title' => 'Donations',
                            'value' => $donation['donation_amount']
                        );
                    }
                }
                $company = array(
                    'company_name' => $name,
                    'impacts' => $don
                );
                $output[] = $company;
            }
        }
        return $output;
    }

}
