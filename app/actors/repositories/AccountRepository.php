<?php namespace App\Actors\Repositories;

use FLY\Libs\CRUD\CRUDRepository;

interface AccountRepository extends CRUDRepository 
{
    public function viewAccounts();

    public function viewAccountsById(string $accountId);

    public function getAccountByNumber(string $accountNumber);

    public function getAccountByPin(string $pinNumber);


}