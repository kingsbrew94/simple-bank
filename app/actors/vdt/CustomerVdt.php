<?php namespace App\Actors\VDT;
use FLY\Libs\{ Validator, Request };

class CustomerVdt extends Validator {

	protected function error_report():array
	{
		$zipCode='zipCode:%^(?:[+]|[0-9][0-9])[0-9]+$';
		return [
			'logId:text'      			 => 'Access denied: invalid access log identity',
			'firstName:alpha' 			 => 'Please enter first name',
			'lastName:alpha'  			 => 'Please enter last name',
			'dob:date'                   => 'Please enter date of birth',
			'phoneNum:tel'    			 => 'Please enter phone number',
			'email:email'     			 => 'Please enter email address',
            'password:text'              => 'Please enter password',
			'confirmPassword:{password}' => 'Confirmation password do not match password',
			'zipCode:max|6|'             => 'Country zip code must not exceed six characters',
			$zipCode                     => 'Please enter zip code',
		    'gender:(M,F)'    			 => 'Please select gender',
			'occupation:alpha'           => 'Please enter occupation',
			'address:text'    			 => 'Please enter address',
			'state:alpha'     			 => 'Please enter state',
			'city:alpha'     			 => 'Please enter city',
			'country:alpha'              => 'Please enter country',
			'blockTransfer:(1,0)'        => 'Please set transfer restriction',
			'transferDisplay:text'       => 'Please enter transfer restriction message'
		];
	}

                            
                
	/**
	 * @param Request $request
	 *
	 * @return CustomerVdt|__anonymous@408
	 *
	 * @Todo Purposely to execute use cases with optional validations
	 */
	static function _(Request $request)
	{
		return new class($request) extends CustomerVdt {

			protected function error_report():array
			{
				return [
					'fieldName:?dataType' => 'error message here'
				];
			}

		};
	}
}