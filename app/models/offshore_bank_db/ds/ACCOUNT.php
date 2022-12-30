<?php namespace App\Models\offshore_bank_db\DS;
use FLY\Model\Algorithm\Model_Controllers;
use FLY_ENV\Util\Model\QueryBuilder;

class ACCOUNT extends QueryBuilder {

/*        
	*******************************************************************************
	* can use transaction here                                                    *
	* example: use TRANSACTION;                                                   *
	* To use a transaction specify the namespace above this model class.          *
	* That is, copy and paste the namespace: use FLY\Model\Algorithm\TRANSACTION; * 
	* right above this model class.                                               *
	*******************************************************************************
*/

	protected $accId;

	protected $userId;

	protected $accNumber;

	protected $pin;

	protected $accType;

	protected $accTypeType;

	protected $accStatus;

	protected $balance;

	protected $accCurrency;


	use Model_Controllers;

	public function __construct($accId="",$userId="",$accNumber="",$pin="",$accType="",$accTypeType="",$accStatus="",$balance="",$accCurrency="") 
	{
    	parent::__construct($this);
		$this->accId = $accId;
		$this->userId = $userId;
		$this->accNumber = $accNumber;
		$this->pin = $pin;
		$this->accType = $accType;
		$this->accTypeType = $accTypeType;
		$this->accStatus = $accStatus;
		$this->balance = $balance;
		$this->accCurrency = $accCurrency;

    	$this->pk_names=[ 'accId','userId' ];
    	$this->fk_names=[ 'userId'=>'cusId' ];
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