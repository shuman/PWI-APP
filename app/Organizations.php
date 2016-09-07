<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Organizations extends Model {

    protected $table = "pwi_organization";
    protected $primaryKey = "org_id";
    protected $fillable = [];

    public function logo() {
        return $this->hasOne("App\Files", "file_id", "org_logo");
    }

    public function coverphoto() {
        return $this->hasOne("App\Files", "file_id", "org_cover_image");
    }

    public function content() {
        return $this->hasMany("App\OrganizationContent", "org_id")
                        ->select("org_content_description", "org_content_type");
    }

    public function impactCountries() {
        return $this->belongsToMany("App\Country", "pwi_org_subcause_countries", "org_id", "org_sc_item_id")
                        ->where("org_sc_status", "=", "active")
                        ->where("org_sc_type", "=", "country")
                        ->select("pwi_country.country_name", "pwi_country.latitude", "pwi_country.longitude", "pwi_country.country_iso_code", "pwi_country.country_alias", "pwi_org_subcause_countries.cause_id", "pwi_org_subcause_countries.org_sc_item_id")
                        ->groupby("country_name")
                        ->orderby("country_name");
    }

    public function causes() {
        return $this->belongsToMany("App\Causes", "pwi_org_cause", "org_id", "cause_id")
                        ->where("org_cause_status", "=", "active")
                        ->select("pwi_causes.cause_id", "pwi_causes.cause_name", "pwi_org_cause.org_cause_description as description", "pwi_causes.cause_alias", "pwi_causes.cause_parent_id", "pwi_causes.cause_instagram_hashtag", "pwi_org_cause.org_cause_id AS orgCauseId");
    }

    public function rating() {
        return $this->hasMany("App\Rating", "comment_item_id", "org_id");
    }

    public function photos() {
        return $this->belongsToMany("App\Files", "pwi_org_photos", "org_id", "file_id")
                    ->where("pwi_org_photos.org_photo_status", "=", "Y")
                    ->orderBy("createdatetime", "desc");
    }

    public function videos() {
        return $this->hasMany("App\Videos", "org_id")
                    ->where("org_video_status", "=", "Y")
                    ->orderBy("createdatetime", "desc");
    }

    public function crowdfunding() {
        return $this->hasMany("App\Projects", "org_id")
                        ->where("pwi_projects.project_end_date", ">", Carbon::now())
                        ->where("pwi_projects.project_start_date", "<", Carbon::now())
                        ->leftJoin("pwi_files AS FILE", "FILE.file_id", "=", "pwi_projects.project_icon")
                        ->leftJoin("pwi_organization as ORG", "ORG.org_id", "=", "pwi_projects.org_id")
                        ->select("pwi_projects.*", "FILE.file_path", "ORG.org_name");
    }

    public function products() {
        return $this->hasMany("App\Products", "org_id")
        ->where("pwi_products.product_status", "=", "active")
        ->leftJoin("pwi_files AS FILE", "FILE.file_id", "=", "pwi_products.product_image_id")
        ->select("pwi_products.product_id", "pwi_products.product_name", "pwi_products.product_alias", "pwi_products.product_sales_price", "pwi_products.product_short_desc", "FILE.file_path");
    }

    public function archivedProducts() {
        return $this->hasMany("App\Products", "org_id")
        ->where("pwi_products.product_status", "<>", "active")
        ->where("pwi_products.product_status", "<>", "pending")
        ->leftJoin("pwi_files AS FILE", "FILE.file_id", "=", "pwi_products.product_image_id")
        ->select("pwi_products.product_id", "pwi_products.product_name", "pwi_products.product_alias", "pwi_products.product_sales_price", "pwi_products.product_short_desc", "FILE.file_path");
    }

    public function socialmedia() {
        return $this->hasMany("App\SocialMedia", "org_id")
                        ->where("org_sm_status", "Y")
                        ->leftJoin("pwi_social_media AS SM", "SM.social_media_id", "=", "pwi_org_social_media.social_media_id")
                        ->select("pwi_org_social_media.org_sm_url", "SM.social_media_name", "pwi_org_social_media.org_sm_pageid");
    }

    public function state() {
        return $this->hasOne("App\States", "state_id", "org_state");
    }

    public function country() {
        return $this->hasOne("App\Country", "country_id", "org_country");
    }

    public function reviews() {
        return $this->hasMany("App\Rating", "comment_item_id", "org_id")
                        ->where("comment_item", "=", "organization")
                        ->orderBy("comment_date", "desc");
    }

}
