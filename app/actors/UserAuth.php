<?php namespace App\Actors;

use FLY\Security\Crypto;

trait UserAuth 
{
    private function validatePassword(string $key,string $password, string $hash): bool
	{
		return Crypto::verify($password,$hash,$key.$password);
	}
}