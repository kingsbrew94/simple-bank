<?php namespace App\Actors\DAO;

use App\Actors\Repositories\AccountRepository;
use App\Models\offshore_bank_db\DS\ACCOUNT;
use FLY\Libs\CRUD\AppDAO;

class AccountDAO extends AppDAO implements AccountRepository 
{
	protected ACCOUNT $user;

	protected $modelName = ACCOUNT::class;

	public function viewAccounts()
	{
		return $this->user->View_Accounts();
	}

	public function viewAccountsById(string $accountId)
	{
		return $this->user->View_Accounts_ById($accountId);
	}

	public function getAccountByNumber(string $accountNumber)
	{
		$data = $this->user["[*]?accNumber='{$accountNumber}'"];
		return isset($data[0]) ? $data[0] : null; 
	}

    public function getAccountByPin(string $pinNumber)
	{
		$data = $this->user["[*]?pin='{$pinNumber}'"];
		return isset($data[0]) ? $data[0] : null;
	}
     
}