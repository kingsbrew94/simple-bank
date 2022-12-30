<?php namespace App\Actors\DAO;

use App\Actors\Repositories\CustomerRepository;
use App\Models\offshore_bank_db\DS\CUSTOMER;
use FLY\Libs\CRUD\AppDAO;

class CustomerDAO extends AppDAO implements CustomerRepository 
{
	protected CUSTOMER $user;

	protected $modelName = CUSTOMER::class;

	public function userloginCredentialsExists(string $email): bool
	{
		$data = $this->user["[*]?email='{$email}'"];
		return isset($data[0]); 
	}
    
	public function getUserInfoByEmail(string $email)
	{
		$data = $this->user["[*]?email='{$email}'"];
		return isset($data[0]) ? $data[0] : null; 
	}
}