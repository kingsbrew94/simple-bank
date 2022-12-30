<?php namespace App\Actors\Events;

use App\Actors\Services\AccessLogsService;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;

class AccessLogsCRUDEvent {

	private static ?AppService $service = null;

	public static function initialize(Request $request)
	{

		$service = self::$service <> null ? self::$service: new AccessLogsService();

		Event::on('AccessLogsValidate',function($validatorName) use ($request) {
			$accesslogsValidator = new $validatorName($request);
			return $accesslogsValidator->validate() ? $accesslogsValidator->getMessage() : new Dto(TRUE,'',$accesslogsValidator);
		}); 

		Event::on('CreateAccessLogs',function($validatorName) use ($request,$service) {
			$accesslogsValidator = new $validatorName($request);
			if($accesslogsValidator->validate()) {
				return $accesslogsValidator->getMessage();
			} 

			return $service->save();
		}); 

		Event::on('ReadAccessLogs',function($validatorName) use ($request,$service) {
			$accesslogsValidator = new $validatorName($request);
			if($accesslogsValidator->validate()) {
				return $accesslogsValidator->getMessage();
			} 

			return $service->findAll();
		}); 

		Event::on('UpdateAccessLogs',function($validatorName) use ($request,$service) {
			$accesslogsValidator = new $validatorName($request);
			if($accesslogsValidator->validate()) {
				return $accesslogsValidator->getMessage();
			} 

			return $service->update($request);
		}); 

		Event::on('DeleteAccessLogs',function($validatorName) use ($request,$service) {
			$accesslogsValidator = new $validatorName($request);
			if($accesslogsValidator->validate()) {
				return $accesslogsValidator->getMessage();
			} 

			return $service->delete($request);
		}); 
	}
}