<?php namespace App\Actors\Events;

use App\Actors\Services\TransactionHistoryService;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;

class TransactionHistoryCRUDEvent {

	private static ?AppService $service = null;

	public static function initialize(Request $request)
	{

		$service = self::$service <> null ? self::$service: new TransactionHistoryService();

		Event::on('TransactionHistoryValidate',function($validatorName) use ($request) {
			$transactionhistoryValidator = new $validatorName($request);
			return $transactionhistoryValidator->validate() ? $transactionhistoryValidator->getMessage() : new Dto(TRUE,'',$transactionhistoryValidator);
		}); 

		Event::on('CreateTransactionHistory',function($validatorName) use ($request,$service) {
			$transactionhistoryValidator = new $validatorName($request);
			if($transactionhistoryValidator->validate()) {
				return $transactionhistoryValidator->getMessage();
			} 

			return $service->save();
		}); 

		Event::on('ReadTransactionHistory',function($validatorName) use ($request,$service) {
			$transactionhistoryValidator = new $validatorName($request);
			if($transactionhistoryValidator->validate()) {
				return $transactionhistoryValidator->getMessage();
			} 

			return $service->findAll();
		}); 

		Event::on('UpdateTransactionHistory',function($validatorName) use ($request,$service) {
			$transactionhistoryValidator = new $validatorName($request);
			if($transactionhistoryValidator->validate()) {
				return $transactionhistoryValidator->getMessage();
			} 

			return $service->update($request);
		}); 

		Event::on('DeleteTransactionHistory',function($validatorName) use ($request,$service) {
			$transactionhistoryValidator = new $validatorName($request);
			if($transactionhistoryValidator->validate()) {
				return $transactionhistoryValidator->getMessage();
			} 

			return $service->delete($request);
		}); 
	}
}