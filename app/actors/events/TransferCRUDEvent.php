<?php namespace App\Actors\Events;

use App\Actors\Services\TransferService;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;

class TransferCRUDEvent {

	private static ?AppService $service = null;

	public static function initialize(Request $request)
	{

		$service = self::$service <> null ? self::$service: new TransferService();

		Event::on('TransferValidate',function($validatorName) use ($request) {
			$transferValidator = new $validatorName($request);
			return $transferValidator->validate() ? $transferValidator->getMessage() : new Dto(TRUE,'',$transferValidator);
		}); 

		Event::on('CreateTransfer',function($validatorName) use ($request,$service) {
			$transferValidator = new $validatorName($request);
			if($transferValidator->validate()) {
				return $transferValidator->getMessage();
			} 

			return $service->save();
		}); 

		Event::on('ReadTransfer',function($validatorName) use ($request,$service) {
			$transferValidator = new $validatorName($request);
			if($transferValidator->validate()) {
				return $transferValidator->getMessage();
			} 

			return $service->findAll();
		}); 

		Event::on('UpdateTransfer',function($validatorName) use ($request,$service) {
			$transferValidator = new $validatorName($request);
			if($transferValidator->validate()) {
				return $transferValidator->getMessage();
			} 

			return $service->update($request);
		}); 

		Event::on('DeleteTransfer',function($validatorName) use ($request,$service) {
			$transferValidator = new $validatorName($request);
			if($transferValidator->validate()) {
				return $transferValidator->getMessage();
			} 

			return $service->delete($request);
		}); 
	}
}