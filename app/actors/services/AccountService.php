<?php namespace App\Actors\Services;

use App\Actors\DAO\AccountDAO;
use App\Actors\Events\AccountCRUDEvent;
use App\Actors\VDT\AccountVdt;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;
use FLY\Security\KeyGen;

class AccountService extends AppService 
{
	private static Dto $validationState;

	private TransactionHistoryService $transactionHistoryService;

	private TransferService $transferService;

	private AccessLogsService $accessLogsService;

	public function __construct() 
	{
		parent::__construct(new AccountDAO());
		$this->transactionHistoryService = new TransactionHistoryService;
		$this->transferService = new TransferService;
		$this->accessLogsService = new AccessLogsService;
	}

	public function getAccessLogs()
	{
		return $this->accessLogsService;
	}

	public function accountNumberExists(Request $request) 
	{
		$isNullValue = $this->getRepo()->getAccountByNumber($request::get('accNumber')) === null;
		return !$isNullValue;
	}

	public function pinExists(Request $request)
	{
		$isNullValue = $this->getRepo()->getAccountByPin($request::get('pin')) === null;
		return !$isNullValue;
	}

	public function getTransfers()
	{
		return  $this->transferService;
	}

	public function getTransactionHistory()
	{
		return $this->transactionHistoryService;
	}

	public function getValidationState(): Dto
	{
		return self::$validationState;
	}

	public function creditDebitAccount(Request $request)
	{
		$dto = new Dto(true);
		$dto->setMessage('Transaction completed successfully');
		if(!$this->transactionHistoryService->validateHistoryData($request)) {
			return $this->transactionHistoryService->getValidationState();
		}
		$data = $this->findById($request::get('accId'))[0];
		if(strtolower($request::get('tranType')) === 'credit') {
			$newBalance = ((double)$data->balance) + ((double)$request->amount);
			$request::set('balance', $newBalance);
			$this->update();
		} else if(strtolower($request::get('tranType')) === 'debit') {
			$actualBalance = (double)$data->balance;
			$requestAmount = (double)$request->amount;
			if($actualBalance > $requestAmount) {
				$newBalance = $actualBalance - $requestAmount;
				$request::set('balance', $newBalance);
				$this->update();
			} else {
				$dto->setState(false);
				$dto->setMessage('Insufficient funds: Unable to perform a debit transation.');
			}
		}
		if($dto->getState() === true) {
			$this->transactionHistoryService->addTransactionHistory();
		}
		return $dto;
	}

	public function viewAccounts() 
	{
		return $this->getRepo()->viewAccounts();
	}

	public function viewAccountsById(string $accountId)
	{
		return $this->getRepo()->viewAccountsById($accountId);
	}

	public function accountInfoValid(Request $request)
	{
		AccountCRUDEvent::initialize($request);
		$res = Event::trigger('AccountValidate',AccountVdt::class);
		self::$validationState = $res;
		return $res->getState();
	}

	public function addAccount()
	{
		$request = Request::instance();
		$accId = KeyGen::primary_key(15,'',true);
		$request::set('accId',$accId);
		$this->save();
	}
}