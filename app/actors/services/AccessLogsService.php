<?php namespace App\Actors\Services;

use App\Actors\DAO\AccessLogsDAO;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;

class AccessLogsService extends AppService 
{
	public function __construct() 
	{
		parent::__construct(new AccessLogsDAO());
	}

	public function deleteAccessLog(Request $request)
	{
		$dto = new Dto(false,'Invalid Request, unable to delete log');
		if($request::exists('accessId'))  {
			$this->deleteById($request::get('accessId'));
			$dto->setState(true);
			$dto->setMessage('Log deleted successfully');
		}
		return $dto;
	}

	public function getAccessLogsByCustomerId(string $customerId)
	{
		return $this->getRepo()->getAccessLogsByCustomerId($customerId);
	}

	public function viewAccessLogs()
	{
		return $this->getRepo()->viewAccessLogs();
	}

}