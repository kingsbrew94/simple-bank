<?php namespace App\Models\offshore_bank_db\DS;
use FLY\Model\Algorithm\Model_Controllers;
use FLY_ENV\Util\Model\QueryBuilder;

class TRANSACTION_HISTORY extends QueryBuilder {

/*        
	*******************************************************************************
	* can use transaction here                                                    *
	* example: use TRANSACTION;                                                   *
	* To use a transaction specify the namespace above this model class.          *
	* That is, copy and paste the namespace: use FLY\Model\Algorithm\TRANSACTION; * 
	* right above this model class.                                               *
	*******************************************************************************
*/

	protected $tranHist;

	protected $accId;

	protected $tranType;

	protected $amount;

	protected $tranDescription;

	protected $dateOfTran;


	use Model_Controllers;

	public function __construct($tranHist="",$accId="",$tranType="",$amount="",$tranDescription="",$dateOfTran="") 
	{
    	parent::__construct($this);
		$this->tranHist = $tranHist;
		$this->accId = $accId;
		$this->tranType = $tranType;
		$this->amount = $amount;
		$this->tranDescription = $tranDescription;
		$this->dateOfTran = $dateOfTran;

    	$this->pk_names=[ 'tranHist' ];
    	$this->fk_names=[ 'accId'=>'accId' ];
    	$this->apply();
	}


	/**
	 * @return string[]
	 * @Todo It returns the model connection credentials
	 */
	protected function connection(): array
	{
    	return array(
			'host'

				=> 'default',

			'user'

				=> 'default',

			'password'

				=> 'default',

			'model'

				=> 'default'
		);
	}
}