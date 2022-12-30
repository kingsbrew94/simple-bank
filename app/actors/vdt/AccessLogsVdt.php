<?php namespace App\Actors\VDT;
use FLY\Libs\{ Validator, Request };

class AccessLogsVdt extends Validator {

	protected function error_report():array
	{
		return [
			'fieldName:dataType' => 'error message here'
		];
	}

                            
                
	/**
	 * @param Request $request
	 *
	 * @return AccessLogsVdt|__anonymous@408
	 *
	 * @Todo Purposely to execute use cases with optional validations
	 */
	static function _(Request $request)
	{
		return new class($request) extends AccessLogsVdt {

			protected function error_report():array
			{
				return [
					'fieldName:?dataType' => 'error message here'
				];
			}

		};
	}
}