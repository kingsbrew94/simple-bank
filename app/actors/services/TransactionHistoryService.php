<?php namespace App\Actors\Services;

use App\Actors\DAO\TransactionHistoryDAO;
use App\Actors\Events\TransactionHistoryCRUDEvent;
use App\Actors\VDT\TransactionHistoryVdt;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;
use FLY\Security\KeyGen;

class TransactionHistoryService extends AppService 
{
	private static $validationState;

	public function __construct() 
	{
		parent::__construct(new TransactionHistoryDAO());
	}

	public function viewTransactionHistory() 
	{
		return $this->getRepo()->viewTransactionHistory();
	}

	public function deleteTransaction(Request $request)
	{
		$dto = new Dto(false,'Invalid Request, unable to delete transaction');
		if($request::exists('tranId'))  {
			$this->deleteById($request::get('tranId'));
			$dto->setState(true);
			$dto->setMessage('Transaction deleted successfully');
		}
		return $dto;
	}

	public function getValidationState()
	{
		return self::$validationState;
	}

	public function validateHistoryData(Request $request)
	{
		TransactionHistoryCRUDEvent::initialize($request);
		$res = Event::trigger('TransactionHistoryValidate',TransactionHistoryVdt::class);
		self::$validationState = $res;
		return $res->getState();
	}

	public function addTransactionHistory()
	{
		$request = Request::instance();
		$tranHist = KeyGen::primary_key(15,'',true);
		$request::set('tranHist',$tranHist);
		$this->save();
	}
}