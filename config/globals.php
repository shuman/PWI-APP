<?php

return [
    /*
    * Prefix for tables in the PWI Database
    */
    "mysql_prefix" => "pwi_",
    /*
    * Country Image File Path
    */
    "countryImgPath" => "/images/country/",
    /*
    * Organization Image File Path
    */
    "orgImgPath" => "/images/organization/",
    /*
    * Project ( Crowdfunding ) Image File Path
    */
    "prjImgPath" => "/images/projects/",
    /*
    * Product Image File Path
    */
    "prdImgPath" => "/images/products/",
    /*
    * Cause Image Path
    */
    "cseImgPath" => "/images/causes/",
    /*
    * User Image Path
    */
    "usrImgPath" => "/images/user/",
    /*
	* Home Page Background Image Path
	*/
	"hpBgPath" => "/images/homepage/",
    /*
    * Instragram Access Token URL
    */
    "igLink"    => "https://instagram.com/oauth/authorize/?client_id=1677ed07ddd54db0a70f14f9b1435579&redirect_uri=http://instagram.pixelunion.net&response_type=token",
    /*
    * Site name for emails
    */
    "siteName"  => "Project World Impact",
    /*
    * Email Header graphic location
    */
    "emailHeader" => "/images/email-header.png",
    /*
    * Transnational Result Codes
    */
    "transNationalResultTable" => array(
        100 => "Transaction was approved.",
        200 => "Transaction was declined by processor.",
        201 => "Do not honor.",
        202 => "Insufficient funds.",
        203 => "Over limit.",
        204 => "Transaction not allowed.",
        220 => "Incorrect payment information.",
        221 => "No such card issuer",
        222 => "No card number on file with issuer",
        223 => "Expired card.",
        224 => "Invalid Expiration Date.",
        225 => "Invalid Security Code.",
        240 => "Call issuer for further information.",
        250 => "Pick up card.",
        251 => "Lost card.",
        252 => "Stolen Card.",
        253 => "Fraudulent card.",
        260 => "Declined with further instructions available.",
        261 => "Declined-Stop all recurring payments.",
        262 => "Declined-Stop this recurring program.",
        263 => "Declined-Update chardholder data available.",
        264 => "Declined-Retry in a few days.",
        300 => "Transaction was rejected by gateway.",
        400 => "Transaction error returned by proecessor.",
        410 => "Invalid merchant configuration.",
        420 => "Communication error.",
        430 => "Communication error with issuer.",
        440 => "Processor format error.",
        441 => "Invalid transaction information.",
        460 => "Processor feature not available.",
        461 => "Unsupported card type."
    ),
    /*
    * Transnational LLC Key
    */
    "transnationalAPIKeyLLC" => "hm8FmhRwU5y288E85229geCshFQxWGK7",
    /*
    * Transnational Foundation Key
    */
    "transnationalAPIKeyFoundation" => "8P88N3NvZh7Q85pB54exw9BC6U75W6X7",
    /*
    * Transnational test Key
    */
    "transnationalAPIKeyTest" => "2F822Rw39fx762MaV7Yy86jXGTC7sCDy",
    /*
    * Transnational URL
    */
    "transnationalUrl" => "https://secure.tnbcigateway.com/api/v2/three-step",
];
?>