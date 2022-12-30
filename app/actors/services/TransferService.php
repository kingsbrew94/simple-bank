<?php namespace App\Actors\Services;

use App\Actors\DAO\TransferDAO;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Request;
use FLY\Libs\Restmodels\Dto;

class TransferService extends AppService 
{
	public function __construct() 
	{
		parent::__construct(new TransferDAO());
	}
	
	public function deleteTransfer(Request $request)
	{
		$dto = new Dto(false,'Invalid Request, unable to delete transfer');
		if($request::exists('transferId'))  {
			$this->deleteById($request::get('transferId'));
			$dto->setState(true);
			$dto->setMessage('Transfer deleted successfully');
		}
		return $dto;
	}
}