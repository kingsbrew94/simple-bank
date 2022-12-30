<?php namespace App\Actors\VDT;
use FLY\Libs\{ Validator, Request };

class TransactionHistoryVdt extends Validator {

	protected function error_report():array
	{
		return [
			'accId:alphaNum'          => 'Please enter account number',
			'tranType:(CREDIT,DEBIT)' => 'Please select the transaction type',
			'amount:num'              => 'Please enter transaction amount',
			'tranDescription:text'    => 'Please enter transaction description'
		];
	}

                            
                
	/**
	 * @param Request $request
	 *
	 * @return TransactionHistoryVdt|__anonymous@408
	 *
	 * @Todo Purposely to execute use cases with optional validations
	 */
	static function _(Request $request)
	{
		return new class($request) extends TransactionHistoryVdt {

			protected function error_report():array
			{
				return [
					'fieldName:?dataType' => 'error message here'
				];
			}

		};
	}
}