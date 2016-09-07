<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Organizations;
use App\ProductDetail;
use App\ProductMaster;
use App\ProjectMaster;
use App\EmailQueue;
use App\Donations;
use Carbon\Carbon;
use App\Projects;
use App\Country;
use App\Causes;
use Config;
use Mail;
use Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call( function( ){

            Log::info( "inside call function for every five minutes."  );

            $queue = EmailQueue::where("sent","=","0")->get( );

            

            foreach( $queue as $item ){

                switch( $item->type ){

                    case "donation":

                        $record = Donations::find( $item->type_id );
                        
                        $recipient;

                        $item_name = "";

                        $noItem = FALSE;

                        switch( $record->item_type ){
                            case "cause":
                                $recipient = Causes::find( $record->item_id );

                                if( sizeof( $recipient ) == 0){
                                    $noItem = TRUE;
                                }else{
                                    $item_name = $recipient->cause_name;    
                                }
                            break;
                            case "country":
                                $recipient = Country::find( $record->item_id );

                                if( sizeof( $recipient ) == 0 ){
                                    $noItem = TRUE;
                                }else{
                                    $item_name = $recipient->country_name;
                                }
                            break;
                            case "organization":
                                $recipient = Organizations::find( $record->item_id );
                                
                                if( sizeof( $recipient ) == 0 ){
                                    $noItem = TRUE;
                                }else{  
                                    $item_name = $recipient->org_name;
                                }
                            break;
                        }

                        if( is_null( $record->email ) || empty( $record->email ) ){

                            $item->sent = -1;
                            $item->message = "No Email Address.";

                        }else if( $noItem ){
                            $item->sent = -1;
                            $item->message = "No Recipient.";
                        }else{
                            $mail = Mail::send("email.user-donation", [
                                "record"    => $record,
                                "item_name" => $item_name
                            ], function( $m ) use( $record ){
                                $m->to( $record->email )->subject("Thank you for your Donation!");
                            });

                            if( $mail->getStatusCode( ) == 200 ){
                                $item->sent = 1;
                                $item->message = $mail->getReasonPhrase( );
                                $item->date_sent = Carbon::now( );
                            }else{
                                $item->sent = -1;

                                $item->message = $mail->getReasonPhrase( );
                            }    
                        }

                        $item->save( );

                    break;
                    case "order":
                        $master = ProductMaster::where( "order_id", "=", $item->type_id )
                            ->leftJoin("pwi_state AS STATE", "STATE.state_id", "=", "pwi_order_master.billing_state")
                            ->leftJoin("pwi_state AS STATE2", "STATE2.state_id", "=", "pwi_order_master.shipping_state")
                            ->leftJoin("pwi_country AS CNTY","CNTY.country_id", "=", "pwi_order_master.billing_country")
                            ->leftJoin("pwi_country AS CNTY2", "CNTY2.country_id", "=", "pwi_order_master.shipping_country")
                            ->select("billing_full_name", "billing_email", "billing_address_line1", "billing_address_line2", "billing_city", "STATE.state_code as billing_state", "billing_zip", "CNTY.country_name as billing_country", "shipping_full_name", "shipping_address_line1", "shipping_address_line2", "shipping_city", "STATE2.state_code as shipping_state", "shipping_zip", "CNTY2.country_name as shipping_country", "order_item_total", "order_shipping_cost", "order_tax", "order_cost")
                            ->firstOrFail( );

                        $orderDetails = ProductDetail::where("order_id", $master->order_id)
                            ->leftJoin("pwi_organization as ORG", "pwi_order_details.org_id", "=", "ORG.org_id")
                            ->select("product_name", "product_sku", "modifier_name", "quantity", "product_price", "product_shipping", "ORG.org_name", "ORG.org_alias")
                            ->get( );

                        $mail = Mail::send("email.mailcontainer-order", [
                            "record"  => $master,
                            "details" => $orderDetails,
                            "site"    => Config::get("globals.siteName"),
                            "img"     => Config::get("globals.emailHeader")
                        ], function( $m ) use( $master ){
                            $m->to( $master->billing_email )->subject("Thank you for your Order!");
                        });

                        if( $mail->getStatusCode( ) == 200 ){
                            $item->sent = 1;
                            $item->message = $mail->getReasonPhrase( );
                            $item->date_sent = Carbon::now( );
                        }else{
                            $item->sent = -1;

                            $item->message = $mail->getReasonPhrase( );
                        }

                        $item->save( );
                    break;
                    case "crowdfunding":

                        $projectDonation = ProjectMaster::find( $item->type_id );

                        $mail = Mail::send("email.user-funding", [
                            "name"      => $projectDonation->billing_full_name,
                            "amount"    => $projectDonation->donation_amount,
                            "item_name" => $projectDonation->project_title,
                            "site"      => Config::get("globals.siteName"),
                            "img"       => Config::get("globals.emailHeader")
                        ], function( $m ) use( $projectDonation ){
                            $m->to( $projectDonation->billing_email )->subject("Thank you for your Donation to " . $projectDonation->project_title . "!");
                        });

                        if( $mail->getStatusCode( ) == 200 ){
                            $item->sent = 1;
                            $item->message = $mail->getReasonPhrase( );
                            $item->date_sent = Carbon::now( );
                        }else{
                            $item->sent = -1;

                            $item->message = $mail->getReasonPhrase( );
                        }

                        $item->save( );
                    break;
                }
            }
        })->everyFiveMinutes( );


        $schedule->call( function( ){

            $projects = Projects::where("project_status", "=", "active")
                ->whereDate("project_end_date", "<", Carbon::now( ) )
                ->leftJoin("pwi_organization AS ORG", "ORG.org_id", "=", "pwi_projects.org_id")
                ->select("project_id", "project_title", "project_fund_goal", "project_amout_raised", "ORG.org_email")
                ->get( );

            foreach( $projects as $project ){

                $mail = Mail::send("email.project-expiry-notarget",[
                    "projectName"   => $project->project_title,
                    "projectTarget" => $project->project_fund_goal
                    ], function( $m ) use(  $project ){
                        $m->to( $project->org_email )->subject("Expiration Notice: " . $project->project_title);
                });

                if( $mail->getStatusCode( ) == 200 ){
                    $project->project_status = 'expired';

                    $project->save( );
                }
            }
        })->dailyAt('07:00');
    }
}
