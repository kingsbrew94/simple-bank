<?php namespace App\Actors\Repositories;

use FLY\Libs\CRUD\CRUDRepository;

interface CustomerRepository extends CRUDRepository {
    public function userloginCredentialsExists(string $email): bool;

    public function getUserInfoByEmail(string $email);
}