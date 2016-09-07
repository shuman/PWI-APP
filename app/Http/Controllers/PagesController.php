<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository as UserRepository;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Helper;
use App\Content;
use DB;
use Config;

class PagesController extends Controller {

    //
    /**
     * @var $user	
     */
    private $user = null;

    /**
     * @var $userImage   
     */
    private $userImage = "";

    /**
     * @var $helper
     */
    private $helper;

    /**
     * __construct
     *
     * @param UserRepository object
     *
     * @param Request $request
     *
     */
    function __construct(UserRepository $userObj, Request $request, Helper $helper) {

        $this->user = $request->instance()->query('user');

        $this->helper = $helper;
    }

    function index() {
        
    }

    function page($alias) {

        $pageData;

        try {
            $pageData = Content::where('cnt_alias', '=', $alias)->firstOrFail();

            $metaData = $this->helper->getMetaData("general", str_replace("-", "_", $alias))->toArray();

            return view("pages.page")->with([
                        "meta" => $metaData[0],
                        "data" => $pageData
            ]);
        } catch (\Exception $e) {


            \App::abort('404');
        }
    }

}
