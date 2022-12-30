<?php namespace App\Actors\VDT;
use FLY\Libs\{ Validator, Request };

class TransferVdt extends Validator {

	protected function error_report():array
	{
		return [
			'fieldName:dataType' => 'error message here'
		];
	}

                            
                
	/**
	 * @param Request $request
	 *
	 * @return TransferVdt|__anonymous@408
	 *
	 * @Todo Purposely to execute use cases with optional validations
	 */
	static function _(Request $request)
	{
		return new class($request) extends TransferVdt {

			protected function error_report():array
			{
				return [
					'fieldName:?dataType' => 'error message here'
				];
			}

		};
	}
}