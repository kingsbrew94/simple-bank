<?php namespace App\Actors\DAO;

use App\Actors\Repositories\AdminRepository;
use App\Models\offshore_bank_db\DS\ADMIN;
use FLY\Libs\CRUD\AppDAO;

class AdminDAO extends AppDAO implements AdminRepository 
{
	protected ADMIN $user;

	protected $modelName = ADMIN::class;

	public function adminloginCredentialsExists(string $username): bool
	{
		return $this->user::value_exists($username);
	}

	public function getAdminInfoByUsername(string $username): object 
	{
		return $this->user::get($username);
	}
}