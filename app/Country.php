<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Country extends Model {

    protected $table = "pwi_country";
    protected $primaryKey = "country_id";

    public function data() {
        return $this->hasMany('App\CountryData', 'countryID');
    }

    public function reference() {
        return $this->hasMany('App\CountryData', 'countryID')
                        ->select("reference");
    }

    public function logo() {
        return $this->hasOne("App\Files", "file_id", "country_logo");
    }

    public function coverphoto() {
        return $this->hasOne("App\Files", "file_id", "country_cover_photo");
    }

    public function orgCountries() {
        return $this->hasMany("App\OrgSubCauseCountries", "org_sc_item_id", "country_id");
    }

    public function impactOrgs() {
        return $this->belongsToMany("App\Organizations", "pwi_org_subcause_countries", "org_sc_item_id", "org_id")
                        ->where("org_sc_status", "=", "active")
                        ->where("org_sc_type", "=", "country")
                        ->select("pwi_organization.org_id", "pwi_organization.org_name", "pwi_organization.org_desc", "pwi_organization.org_alias")
                        ->take(4)
                        ->groupBy("org_id")
                        ->orderBy("pwi_organization.org_grade", "DESC")
                        ->orderBy("pwi_organization.RandomSort");
    }

    public function causeData() {
        return $this->hasMany("App\CountryData", "countryID")
                        ->leftJoin("pwi_causes", function( $join) {
                            $join->on('pwi_causes.cause_id', '=', 'pwi_country_data.causeID');
                        })
                        ->select("pwi_causes.cause_name", "pwi_country_data.description as description", "pwi_causes.cause_alias", "pwi_country_data.reference");
    }

    public function crowdfunding() {
        return $this->belongsToMany("App\Projects", "pwi_project_cause_details", "project_cause_item_id", "project_id")
                        ->where("project_cause_status", "=", "active")
                        ->where("project_cause_type", "=", "country")
                        ->where("pwi_projects.project_status", "=", "active")
                        ->where("pwi_projects.project_end_date", ">", Carbon::now())
                        ->where("pwi_projects.project_start_date", "<", Carbon::now())
                        ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_projects.org_id")
                        ->select("pwi_projects.project_id", "pwi_projects.project_title", "pwi_projects.project_alias", "pwi_projects.project_fund_goal", "pwi_projects.project_amout_raised", "ORG.org_name", "ORG.org_alias")
                        ->take(2);
    }

    public function products() {
        return $this->belongsToMany("App\Products", "pwi_product_causes", "product_cause_item_id", "product_id")
                ->where("product_cause_status", "=", "active")
                ->where("product_cause_type", "=", "country")
                ->where("pwi_products.product_status", "=", "active")
                ->leftJoin("pwi_organization as ORG", "ORG.org_id", "=", "pwi_products.org_id")
                ->leftJoin("pwi_files as FILE", "FILE.file_id", "=", "pwi_products.product_image_id")
                ->select("pwi_products.product_id", "pwi_products.product_name", "pwi_products.product_alias", "pwi_products.product_sales_price", "pwi_products.product_short_desc", "ORG.org_name", "FILE.file_path as image")
                ->take(2);
    }

}
