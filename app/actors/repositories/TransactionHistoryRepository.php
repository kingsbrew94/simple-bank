<?php namespace App\Actors\Repositories;

use FLY\Libs\CRUD\CRUDRepository;

interface TransactionHistoryRepository extends CRUDRepository 
{
    public function viewTransactionHistory();
}