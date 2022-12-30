<?php namespace App\Actors;

use FLY\Security\Sessions;

trait Libs
{
    private static Sessions $session;

    private static function setSnackMessage()
	{
		$snackBarState = self::$session::exists('snackBarState') ? self::$session::get('snackBarState') : false;
		$snackBarMessage = self::$session::exists('snackBarMessage') ? self::$session::get('snackBarMessage') : '';
		$request = self::$session::exists('cached_request') ? self::$session::get('cached_request') : null;
		self::add_context(['request' => $request,'snackBarState'=> $snackBarState,'snackBarMessage' => $snackBarMessage]);
		
		if(self::$session::exists('snackBarState') && self::$session::get('snackBarState')) {
			self::$session::remove('cached_request');
		}
		self::$session::remove('snackBarState');
		self::$session::remove('snackBarMessage');
	}
}