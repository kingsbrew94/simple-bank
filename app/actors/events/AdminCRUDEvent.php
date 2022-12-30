<?php namespace App\Actors\Events;

use App\Actors\Services\AdminService;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;

class AdminCRUDEvent {

	private static ?AppService $service = null;

	public static function initialize(Request $request)
	{

		$service = self::$service <> null ? self::$service: new AdminService();

		Event::on('AdminValidate',function($validatorName) use ($request) {
			$adminValidator = new $validatorName($request);
			return $adminValidator->validate() ? $adminValidator->getMessage() : new Dto(TRUE,'',$adminValidator);
		}); 

		Event::on('CreateAdmin',function($validatorName) use ($request,$service) {
			$adminValidator = new $validatorName($request);
			if($adminValidator->validate()) {
				return $adminValidator->getMessage();
			} 

			return $service->save();
		}); 

		Event::on('ReadAdmin',function($validatorName) use ($request,$service) {
			$adminValidator = new $validatorName($request);
			if($adminValidator->validate()) {
				return $adminValidator->getMessage();
			} 

			return $service->findAll();
		}); 

		Event::on('UpdateAdmin',function($validatorName) use ($request,$service) {
			$adminValidator = new $validatorName($request);
			if($adminValidator->validate()) {
				return $adminValidator->getMessage();
			} 

			return $service->update($request);
		}); 

		Event::on('DeleteAdmin',function($validatorName) use ($request,$service) {
			$adminValidator = new $validatorName($request);
			if($adminValidator->validate()) {
				return $adminValidator->getMessage();
			} 

			return $service->delete($request);
		}); 
	}
}