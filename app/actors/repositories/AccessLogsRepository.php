<?php namespace App\Actors\Repositories;

use FLY\Libs\CRUD\CRUDRepository;

interface AccessLogsRepository extends CRUDRepository 
{
    public function getAccessLogsByCustomerId(string $customerId);

	public function viewAccessLogs();
}