<?php namespace App\Actors\VDT;
use FLY\Libs\{ Validator, Request };

class AdminVdt extends Validator {

	protected function error_report():array
	{
		return array(
			'username:text' => 'Invalid username or password',
            'password:text' => 'Invalid username or password',
			'newPassword:text'   			  => 'Provide your new password',
			'confirmNewPassword:{newPassword}'=> 'Confirmation password does not match your new password'
		);
	}

                            
                
	/**
	 * @param Request $request
	 *
	 * @return AdminVdt|__anonymous@408
	 *
	 * @Todo Purposely to execute use cases with optional validations
	 */
	static function _(Request $request)
	{
		return new class($request) extends AdminVdt {

			protected function error_report():array
			{
				return [
					'username:text'                   => 'Invalid administrative identification',
					'password:text'      			  => 'Provide your old password',
					'newPassword:text'   			  => 'Provide your new password',
					'confirmNewPassword:{newPassword}'=> 'Confirmation password does not match your new password'
				];
			}

		};
	}
}