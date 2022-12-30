<?php namespace App\Actors\Services;

use App\Actors\DAO\CustomerDAO;
use App\Actors\Events\CustomerCRUDEvent;
use App\Actors\UserAuth;
use App\Actors\VDT\CustomerVdt;
use App\Models\offshore_bank_db\DS\ACCESS_LOGS;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\File_API\File;
use FLY\Libs\File_API\UploadImage;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;
use FLY\Routers\Redirect;
use FLY\Security\Crypto;
use FLY\Security\KeyGen;
use FLY\Security\Sessions;

class CustomerService extends AppService 
{
	private static $repo;

	private static Dto $validationState;

	private static $key = '12$^{wus83!';

	private $imageStatus;
	
	use UserAuth;

	public function __construct() 
	{
		parent::__construct(new CustomerDAO());
		self::$repo = $this->getRepo();
	}

	public function getValidationState(): Dto
	{
		return self::$validationState;
	}

	public function getImageStatus() 
	{
		return $this->imageStatus;
	}

	public function customerInfoValid(Request $request) 
	{
		CustomerCRUDEvent::initialize($request);
		$res = Event::trigger('CustomerValidate',CustomerVdt::class);
		self::$validationState = $res;
		return $res->getState();
	}

	public function imageUploaded(string $filename)
	{
		$upload = new UploadImage('images/avatar/');
		$status = $upload->upload_file($filename);
		$this->imageStatus = $status;
		return $status->state;
	}

	public function customerExists(Request $request)
	{
		$isNullValue = self::$repo->getUserInfoByEmail($request::get('email')) === null;
		return !$isNullValue;
	}

	public function addCustomer()
	{
		$request = Request::instance();
		$cusId = KeyGen::primary_key(15,'',true);
		$userId = $cusId;
		$request::set('cusId',$cusId);
		$request::set('userId',$userId);
		$password = $request::get('password');
		$hash = Crypto::lock($password,self::$key);
		$request::set('password',$hash);
		$this->save();
	}

	public function deleteCustomer(Request $request) 
	{
		$dto = new Dto(false,'Invalid Request, unable to delete customer account');
		if($request::exists('customer'))  {
			$data = $this->findById($request::get('customer'));
			if(!is_empty($data)) {
				$data = $data[0];
				$path = "app/statics/images/avatar/{$data->picName}";
				if(File::exists($path)) File::remove($path);
				$this->deleteById($request::get('customer'));
				$dto->setState(true);
				$dto->setMessage('Customer account deleted successfully');
			}
		}
		return $dto;
	}

	public function updateCustomerPicture(Request $request)
	{
		$data = $this->findById($request->cusId);
		$dto = new Dto(false,'Unable to update customer image: record not found');
		if(!is_empty($data)) {
			$dto = $this->setCustomerImage($request);
			$data = $data[0];
			if($dto->getState()=== true) {
				$path = "app/statics/images/avatar/{$data->picName}";
				if(File::exists($path)) File::remove($path);
				$this->update();
				$dto->setState(true);
				$dto->setMessage('Customer image updated successfully');
			}
		}
		return $dto;
	}

	public function setCustomerImage(Request $request)
	{
		$dto = new Dto();
		if(!$this->imageUploaded('customerImage')) {
			$status = $this->customerService->getImageStatus();
			$dto->setState(false);
			$dto->setMessage($status->message);
		} else {
			$request::set('picName',$this->getImageStatus()->filename);
			$dto->setState(true);
		}
		return $dto;
	}

	public function getCustomerElseLogout() 
	{
		$session = new Sessions;
		$flag = false;
		$userInfo = null;
		if(Sessions::exists('client_user')) {
			$userInfo = self::$repo->getUserInfoByEmail($session::get('client_user'));
			$flag = $userInfo === null;
        } if($flag === true || $userInfo === null) {
			Sessions::removeAll();
            Redirect::to(url(':home'));
		}
        return $userInfo;
	}

	public function login(Request $request) {
		CustomerCRUDEvent::initialize($request);
		$dto = new Dto(false,'Invalid account id or password');
		Sessions::start()::remove('client_user');
		if($this->authenticateUser($request)) {
			Sessions::start()::add('client_user',$request->email);
			$userInfo = self::$repo->getUserInfoByEmail($request::get('email'));
			$this->addAccessLog($userInfo->cusId,'login');
			$dto->setState(TRUE);
			$dto->setMessage('Success: Redirecting to dashboard');
		}
		return $dto;
	}

	private function addAccessLog(string $cusId, string $activity)
	{
		$logs = ACCESS_LOGS::instance();
		$logs->cusId = $cusId;
		$logs->ip = Request::getIPAddress();
		$logs->activity = $activity;
		$logs->save();
	}

	private function authenticateUser(Request $req): bool
	{
		$res = Event::trigger('CustomerValidate');
		$userExists = self::$repo->userloginCredentialsExists($req::get('email'));
		$userInfo = self::$repo->getUserInfoByEmail($req::get('email'));
		$passwordValid = $this->validatePassword($req->email,$req->password,$userInfo->password);
		return $userExists && $passwordValid && $res->getState();
	}

}