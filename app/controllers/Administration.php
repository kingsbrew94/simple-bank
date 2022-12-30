<?php namespace App\Controllers;

use App\Actors\Libs;
use App\Actors\Services\AdminService;
use FLY\Libs\Request;
use FLY\MVC\Controller;
use FLY\MVC\View;
use FLY\Security\Sessions;

final class Administration extends Controller {

	private static AdminService $adminService;

	private static $admin;

	use Libs;

	protected function __init__(Request $request)
	{
		self::$adminService = new AdminService;
		self::$admin = self::$adminService->getAdminElseLogout();
		self::$session = new Sessions();
		View::save_context(['pageType' => '','page' => 'dashboard','snackBarState'=> false,'snackBarMessage' => '']);
	}

	protected function __deinit__()
	{
		if(self::$session::exists('snackBarState') && self::$session::get('snackBarState')) {
			self::$session::remove('cached_request');
		}
		self::$session::remove('snackBarState');
		self::$session::remove('snackBarMessage');
	}

	static function index()
	{
		$accounts = self::$adminService->getAccounts()->viewAccounts();
    	self::render_view(['pageType' => 'adminDashboard','accounts' => $accounts]);
		self::setSnackMessage();
	}

	static function addAccount()
	{
		self::render_view(['page' => 'add']);
		self::setSnackMessage();
	}

	static function addTransactionCodes()
	{
		self::render_view(['page' => 'transcode']);
	}

	static function creditDebit()
	{
		$account = self::$adminService->getAccounts();
		self::render_view(['page' => 'credit','accounts' => $account->findAll()]);
		self::setSnackMessage();
	}

	static function onOff()
	{
		self::render_view(['page' => 'onoff']);
	}

	static function iban()
	{
		self::render_view(['page' => 'iban']);
	}

	static function viewTransfer()
	{
		$transfers = self::$adminService->getAccounts()->getTransfers();
		self::render_view(['page' => 'viewtrans','transfers' => $transfers->findAll()]);
		self::setSnackMessage();
	}

	static function viewTransaction()
	{
		$transactions = self::$adminService->getAccounts()->getTransactionHistory();
		self::render_view(['page' => 'viewtransactions','transactions' => $transactions->viewTransactionHistory()]);
		self::setSnackMessage();
	}

	static function accessLogs()
	{
		$accessLogs = self::$adminService->getAccounts()->getAccessLogs();
		self::render_view(['page' => 'accesslogs','accessLogs' => $accessLogs->viewAccessLogs()]);
		self::setSnackMessage();
	}

	static function changePassword()
	{
		self::render_view(['page' => 'changepass']);
		self::setSnackMessage();
	}
}