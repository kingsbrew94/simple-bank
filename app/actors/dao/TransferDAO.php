<?php namespace App\Actors\DAO;

use App\Actors\Repositories\TransferRepository;
use App\Models\offshore_bank_db\DS\TRANSFERS;
use FLY\Libs\CRUD\AppDAO;

class TransferDAO extends AppDAO implements TransferRepository 
{
	protected TRANSFERS $user;

	protected $modelName = TRANSFERS::class;
     
}