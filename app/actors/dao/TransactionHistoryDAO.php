<?php namespace App\Actors\DAO;

use App\Actors\Repositories\TransactionHistoryRepository;
use App\Models\offshore_bank_db\DS\TRANSACTION_HISTORY;
use FLY\Libs\CRUD\AppDAO;

class TransactionHistoryDAO extends AppDAO implements TransactionHistoryRepository 
{
	protected TRANSACTION_HISTORY $user;

	protected $modelName = TRANSACTION_HISTORY::class;

	public function viewTransactionHistory() 
	{
		return $this->user->View_Transactions();
	}
     
}