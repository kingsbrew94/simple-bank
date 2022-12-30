<?php namespace App\Controllers;

use App\Actors\Services\AdminService;
use App\Actors\Services\CustomerService;
use Exception;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;
use FLY\MVC\Controller;
use FLY\MVC\View;
use FLY\Routers\Redirect;
use FLY\Security\Sessions;

final class UserAuthenticationController extends Controller {

	private static AdminService $adminService;

	private static CustomerService $customerService;

	protected function __init__(Request $request)
	{
		View::save_context(['snackBarState'=> false,'snackBarMessage' => '']);
	}

	static function adminLoginSite() {
		self::render_view(['pageType' => 'adminLogin']);
	}

	static function adminLogin(Request $request)
	{
    	self::$adminService = new AdminService;
		$dto = new Dto;
		try {
			$dto = self::$adminService->login($request);
		} catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		return $dto;
	}

	static function userLogin(Request $request)
	{
		self::$customerService = new CustomerService;
        $dto = new Dto;
        try {
            $dto = self::$customerService->login($request);
        } catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		return $dto;
	}

	static function adminLogout() 
	{
		Sessions::start()::removeAll();
		Redirect::to(url(':adminHome'));
	}

	static function userLogout() 
	{
		Sessions::start()::removeAll();
		Redirect::to(url(':home'));
	}

}