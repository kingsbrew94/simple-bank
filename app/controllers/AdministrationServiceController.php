<?php namespace App\Controllers;

use App\Actors\Libs;
use App\Actors\Services\AccessLogsService;
use App\Actors\Services\AdminService;
use Exception;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;
use FLY\MVC\Controller;
use FLY\MVC\View;
use FLY\Routers\Redirect;
use FLY\Security\Sessions;

final class AdministrationServiceController extends Controller {

	private static AdminService $adminService;

	private static AccessLogsService $accessLogsService;

	private static Sessions $session;

	private static $admin;

	use Libs;

	protected function __init__(Request $request)
	{
		self::$adminService = new AdminService;
		self::$accessLogsService = new AccessLogsService;
		self::$admin = self::$adminService->getAdminElseLogout();
		self::$session = new Sessions();
		
		View::save_context(['pageType' => '','page' => 'dashboard','session' => self::$session,'snackBarState'=> false,'snackBarMessage' => '']);
	}

	static function addCustomerAccount(Request $request)
	{
    	$dto = new Dto;
		self::$session::add('cached_request',$request::all());
		try {
			$dto = self::$adminService->addNewCustomer($request);
		} catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
	    Redirect::to(url(':add_acc'));
	}

	static function viewAccountDetails(Request $request) 
	{
		if(!$request::exists('account')) {
			Redirect::to(url(':add_acc'));
		}
		$account = self::$adminService->getAccounts()->viewAccountsById($request::get('account'));
		if(is_empty($account)) {
			Redirect::to(url(':add_acc'));
		}
		$details = $account[0];
		$accessLogs = self::$accessLogsService->getAccessLogsByCustomerId($details->cusId);
		self::render_view(['record' => $details,'accessLogs'=>$accessLogs,'accountId' => $request::get('account')]);
		self::setSnackMessage();
	}

	static function editAccount(Request $request)
	{
		if(!$request::exists('account')) {
			Redirect::to(url(':add_acc'));
		}
		$account = self::$adminService->getAccounts()->viewAccountsById($request::get('account'));
		if(is_empty($account)) {
			Redirect::to(url(':add_acc'));
		}
		$details = $account[0];
		self::render_view(['record' => $details]);
		self::setSnackMessage();
	}

	static function creditDebitAccount(Request $request)
	{
		$dto = new Dto;
		self::$session::add('cached_request',$request::all());
		try {
			$dto = self::$adminService->getAccounts()->creditDebitAccount($request);
		} catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
	    Redirect::to(url(':credit'));
	}

	static function changePassword(Request $request)
	{
		$dto = new Dto;
		try {
			$dto = self::$adminService->changePassword($request);
		} catch(Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
		Redirect::to(url(':change_pass'));
	}

	static function deleteTransaction(Request $request)
	{
		$dto = null;
		try {
			$transaction = self::$adminService->getAccounts()->getTransactionHistory();
			$dto = $transaction->deleteTransaction($request);
		} catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
		Redirect::to(url(':view_tranx'));
	}

	static function deleteAccessLog(Request $request)
	{
		$dto = null;
		try {
			$accessLog = self::$adminService->getAccounts()->getAccessLogs();
			$dto = $accessLog->deleteAccessLog($request);
		} catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
		Redirect::to(url(':access_logs'));
	}

	static function deleteTransfer(Request $request)
	{
		$dto = null;
		try {
			$transfer = self::$adminService->getAccounts()->getTransfers();
			$dto = $transfer->deleteTransfer($request);
		} catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
		Redirect::to(url(':view_transfer'));
	}

	static function editCustomerAccount(Request $request)
	{
		$dto = new Dto;
		try {
			$dto = self::$adminService->updateCustomerData($request);
		} catch(Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
		Redirect::to(url(':edit_acc').'?account='.$request::get('accId'));
	}

	static function updateCustomerImage(Request $request) 
	{
		$dto = new Dto;
		try {
			$dto = self::$adminService->updateCustomerImage($request);
		} catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
		Redirect::to(url(':edit_cus_image').'?account='.$request::get('accId'));
	}

	static function deleteCustomerAccount(Request $request)
	{
		$dto = new Dto;
		try {
			$dto = self::$adminService->deleteCustomerDetails($request);
		} catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
		Redirect::to(url(':view_acc'));
	}

	static function updateTransferRestriction(Request $request)
	{
		$dto = new Dto;
		try {
			$dto = self::$adminService->restrictTransfers($request);
		} catch (Exception $ex) {
			error_log($ex);
			$dto = new Dto(false,'Oops, something is wrong! please try again');
		}
		self::$session::add('snackBarState',$dto->getState());
		self::$session::add('snackBarMessage',$dto->getMessage());
		Redirect::to(url(':edit_acc').'?account='.$request::get('accId'));
	}

}