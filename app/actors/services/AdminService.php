<?php namespace App\Actors\Services;

use App\Actors\DAO\AdminDAO;
use App\Actors\Events\AdminCRUDEvent;
use App\Actors\UserAuth;
use App\Actors\VDT\AdminVdt;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;
use FLY\Routers\Redirect;
use FLY\Security\Crypto;
use FLY\Security\Sessions;

class AdminService extends AppService 
{
	private CustomerService $customerService;

	private AccountService $accountService;

	private static $adminValidation;

	private static $repo;

	use UserAuth;

	public function __construct() 
	{
		parent::__construct(new AdminDAO());
		self::$repo = $this->getRepo();
		$this->accountService = new AccountService;
		$this->customerService = new CustomerService;
	}

	public function getAdminValidation()
	{
		return self::$adminValidation;
	}

	public function getAccounts()
	{
		return $this->accountService;
	}

	public function deleteCustomerDetails(Request $request)
	{ 
		return $this->customerService->deleteCustomer($request);
	}

	public function updateCustomerData(Request $request)
	{
		$dto = new Dto();
		$request::set('gender',['male' => 'M', 'female' => 'F'][strtolower($request::get('gender'))]);
		if(!$this->customerService->customerInfoValid($request)) {
			return $this->customerService->getValidationState();
		}
		if(!$this->accountService->accountInfoValid($request)) {
			return $this->accountService->getValidationState();
		}
		$this->customerService->update();
		$this->accountService->update();
		$dto->setState(true);
		$dto->setMessage('Customer info updated successfully');
		return $dto;
	}

	public function updateCustomerImage(Request $request)
	{
		return $this->customerService->updateCustomerPicture($request);
	}

	public function restrictTransfers(Request $request)
	{
		$dto = new Dto();
		if(!$this->customerService->customerInfoValid($request)) {
			return $this->customerService->getValidationState();
		}
		$this->customerService->update();
		$dto->setState(true);
		$dto->setMessage('Transfer restriction updated successfully');
		return $dto;
	}

	public function addNewCustomer(Request $request) 
	{
		$dto = new Dto();
		if(!$this->customerService->customerInfoValid($request)) {
			return $this->customerService->getValidationState();
		}
		if(!$this->accountService->accountInfoValid($request)) {
			return $this->accountService->getValidationState();
		}
		if($this->customerService->customerExists($request)) {
			$dto->setState(false);
			$dto->setMessage('Unable to add customer: Email address already exists.');
			return $dto;
		}
		if($this->accountService->accountNumberExists($request)) {
			$dto->setState(false);
			$dto->setMessage('Unable to add customer: Account number already exists.');
			return $dto;
		}
		if($this->accountService->pinExists($request)) {
			$dto->setState(false);
			$dto->setMessage('Unable to add customer: PIN number already exists.');
			return $dto;
		}

		if(!$this->customerService->imageUploaded('customerImage')) {
			$status = $this->customerService->getImageStatus();
			$dto->setState(false);
			$dto->setMessage($status->message);
			return $dto;
		} else {
			$request::set('picName',$this->customerService->getImageStatus()->filename);
		}
		
		$this->customerService->addCustomer();
		$this->accountService->addAccount();
		$dto->setState(true);
		$dto->setMessage('New Customer Info Added Successfully.');
		return $dto;
	}

	public function login(Request $req) {
		AdminCRUDEvent::initialize($req);
		$dto = new Dto(false,'Invalid username or password');
		Sessions::start()::remove('admin');
		if(($req::exists('username') && $req::exists('password')) && $this->authenticateAdmin($req)) {
			Sessions::start()::add('admin',$req->username);
			$dto->setState(TRUE);
			$dto->setMessage('Success: Redirecting to dashboard');
		}
		return $dto;
	}

	public function validateAdminInfo(Request $request)
	{
		AdminCRUDEvent::initialize($request);
		$res = Event::trigger('AdminValidate',AdminVdt::class);
		self::$adminValidation = $res;
		return $res->getState();
	}

	public function changePassword(Request $request)
	{
	    $dto = new Dto;
		$session = Sessions::start();
		$request::set('username',$session::get('admin'));
		if(!$this->validateAdminInfo($request)) {
			return $this->getAdminValidation();
		}
		if(!$this->authenticateAdmin($request)) {
			$dto->setState(false);
			$dto->setMessage('Old password is invalid');
			return $dto;
		}
		$dto->setState(true);
		$dto->setMessage('Password was changed successfully');
		$this->updatePassword();
		return $dto;
	}

	public function updatePassword()
	{
		$request = Request::instance();
		$key = $request::get('username');
		$newPassword = $request::get('newPassword');
		$newHash= Crypto::lock($request::get('newPassword'),$key.$newPassword);
		$request::set('password',$newHash);
		$this->update();
	}



	public function getAdminElseLogout()
	{
		$session = new Sessions;
		$flag = false;
		$adminInfo = null;
        if(Sessions::exists('admin')) {
			$adminInfo = self::$repo->getAdminInfoByUsername($session::get('admin'));
			$flag = trim($adminInfo->username) === '';
        } if($flag === true || $adminInfo === null){
			Sessions::removeAll();
            Redirect::to(url(':adminHome'));
		}
        return $adminInfo;
	}

	private function authenticateAdmin(Request $req): bool
	{
		$res = Event::trigger('AdminValidate',AdminVdt::class);
		$adminUserExists = self::$repo->adminloginCredentialsExists($req->username);
		$adminInfo = self::$repo->getAdminInfoByUsername($req->username);
		$passwordValid = $adminUserExists ? $this->validatePassword($req->username,$req->password,$adminInfo->password): $adminUserExists;
		return $adminUserExists && $passwordValid && $res->getState();
	}
}