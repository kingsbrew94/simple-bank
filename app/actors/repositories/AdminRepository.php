<?php namespace App\Actors\Repositories;

use FLY\Libs\CRUD\CRUDRepository;

interface AdminRepository extends CRUDRepository {
    public function adminloginCredentialsExists(string $username): bool;

    public function getAdminInfoByUsername(string $username): object;
}