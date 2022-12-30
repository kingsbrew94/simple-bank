<?php namespace App\Actors\Events;

use App\Actors\Services\CustomerService;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;

class CustomerCRUDEvent {

	private static ?AppService $service = null;

	public static function initialize(Request $request)
	{

		$service = self::$service <> null ? self::$service: new CustomerService();

		Event::on('CustomerValidate',function($validatorName) use ($request) {
			$customerValidator = new $validatorName($request);
			return $customerValidator->validate() ? $customerValidator->getMessage() : new Dto(TRUE,'',$customerValidator);
		}); 

		Event::on('CreateCustomer',function($validatorName) use ($request,$service) {
			$customerValidator = new $validatorName($request);
			if($customerValidator->validate()) {
				return $customerValidator->getMessage();
			} 

			return $service->save();
		}); 

		Event::on('ReadCustomer',function($validatorName) use ($request,$service) {
			$customerValidator = new $validatorName($request);
			if($customerValidator->validate()) {
				return $customerValidator->getMessage();
			} 

			return $service->findAll();
		}); 

		Event::on('UpdateCustomer',function($validatorName) use ($request,$service) {
			$customerValidator = new $validatorName($request);
			if($customerValidator->validate()) {
				return $customerValidator->getMessage();
			} 

			return $service->update($request);
		}); 

		Event::on('DeleteCustomer',function($validatorName) use ($request,$service) {
			$customerValidator = new $validatorName($request);
			if($customerValidator->validate()) {
				return $customerValidator->getMessage();
			} 

			return $service->delete($request);
		}); 
	}
}