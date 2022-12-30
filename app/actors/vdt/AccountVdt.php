<?php namespace App\Actors\VDT;
use FLY\Libs\{ Validator, Request };

class AccountVdt extends Validator {

	protected function error_report():array
	{
		return [
			'accNumber:num'                    => 'Please enter a account number',
			'pin:num'                          => 'Please enter pin number',
			'accStatus:(active,notactive)'     => 'Please enter accout status',
			'accType:(PERSONAL,BUSINESS)'      => 'Please select type of account: personal or business',
			'accTypeType:(SAVINGS,CURRENT)'    => 'Please select the state of account: savings or current',
			'balance:num'                      => 'Please add a balance or an amount to account',
			'accCurrency:(DOLLAR,POUNDS,EURO,YUAN)' => 'Please select currency kind to create account'
		];
	}

                            
                
	/**
	 * @param Request $request
	 *
	 * @return AccountVdt|__anonymous@408
	 *
	 * @Todo Purposely to execute use cases with optional validations
	 */
	static function _(Request $request)
	{
		return new class($request) extends AccountVdt {

			protected function error_report():array
			{
				return [
					'fieldName:?dataType' => 'error message here'
				];
			}

		};
	}
}