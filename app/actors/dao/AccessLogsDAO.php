<?php namespace App\Actors\DAO;

use App\Actors\Repositories\AccessLogsRepository;
use App\Models\offshore_bank_db\DS\ACCESS_LOGS;
use FLY\Libs\CRUD\AppDAO;

class AccessLogsDAO extends AppDAO implements AccessLogsRepository 
{
	protected ACCESS_LOGS $user;

	protected $modelName = ACCESS_LOGS::class;

    public function getAccessLogsByCustomerId(string $customerId) 
	{
		return $this->user["[*]?cusId='{$customerId}'"];
	}

	public function viewAccessLogs()
	{
		return $this->user->View_Logs();
	}
     
}