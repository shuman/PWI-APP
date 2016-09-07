<?php

$mode = "sandbox";

if( env("APP_ENV") == "live" ){
    $mode = "live";
}

return array(
    // set your paypal credential
    'client_id' => 'AXmtcZPTz05xSYrXiLAJnQH-7RS3GJdECTIhLdzFXDGl0fYa8d1oKSNKvdBSyIHFmLIUy3ykQDSxn2Er',
    'secret' => 'EHKaAjW6YjncLEVL5abrIRs-a5VbFPyWO3Qo0x2EPL7SL5d1AYXhZu8uV2KWlxZ3oGWIAeBvGqx6Wnav',
 
    /**
     * SDK configuration 
     */
    'settings' => array(
        /**
         * Available option 'sandbox' or 'live'
         */
        'mode' => $mode,
 
        /**
         * Specify the max request time in seconds
         */
        'http.ConnectionTimeOut' => 30,
 
        /**
         * Whether want to log to a file
         */
        'log.LogEnabled' => true,
 
        /**
         * Specify the file that want to write on
         */
        'log.FileName' => storage_path() . '/logs/paypal.log',
 
        /**
         * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
         *
         * Logging is most verbose in the 'FINE' level and decreases as you
         * proceed towards ERROR
         */
        'log.LogLevel' => 'FINE'
    ),
);