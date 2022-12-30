<?php namespace App\Actors\Events;

use App\Actors\Services\AccountService;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;

class AccountCRUDEvent {

	private static ?AppService $service = null;

	public static function initialize(Request $request)
	{

		$service = self::$service <> null ? self::$service: new AccountService();

		Event::on('AccountValidate',function($validatorName) use ($request) {
			$accountValidator = new $validatorName($request);
			return $accountValidator->validate() ? $accountValidator->getMessage() : new Dto(TRUE,'',$accountValidator);
		}); 

		Event::on('CreateAccount',function($validatorName) use ($request,$service) {
			$accountValidator = new $validatorName($request);
			if($accountValidator->validate()) {
				return $accountValidator->getMessage();
			} 

			return $service->save();
		}); 

		Event::on('ReadAccount',function($validatorName) use ($request,$service) {
			$accountValidator = new $validatorName($request);
			if($accountValidator->validate()) {
				return $accountValidator->getMessage();
			} 

			return $service->findAll();
		}); 

		Event::on('UpdateAccount',function($validatorName) use ($request,$service) {
			$accountValidator = new $validatorName($request);
			if($accountValidator->validate()) {
				return $accountValidator->getMessage();
			} 

			return $service->update($request);
		}); 

		Event::on('DeleteAccount',function($validatorName) use ($request,$service) {
			$accountValidator = new $validatorName($request);
			if($accountValidator->validate()) {
				return $accountValidator->getMessage();
			} 

			return $service->delete($request);
		}); 
	}
}