<?php namespace App\Models\offshore_bank_db\DS;
use FLY\Model\Algorithm\Model_Controllers;
use FLY_ENV\Util\Model\QueryBuilder;

class TRANSFERS extends QueryBuilder {

/*        
	*******************************************************************************
	* can use transaction here                                                    *
	* example: use TRANSACTION;                                                   *
	* To use a transaction specify the namespace above this model class.          *
	* That is, copy and paste the namespace: use FLY\Model\Algorithm\TRANSACTION; * 
	* right above this model class.                                               *
	*******************************************************************************
*/

	protected $transfId;

	protected $accId;

	protected $bankName;

	protected $bankAddress;

	protected $accountName;

	protected $accountNumber;

	protected $routingNumber;

	protected $dateTransfered;

	protected $amount;

	protected $description;


	use Model_Controllers;

	public function __construct($transfId="",$accId="",$bankName="",$bankAddress="",$accountName="",$accountNumber="",$routingNumber="",$dateTransfered="",$amount="",$description="") 
	{
    	parent::__construct($this);
		$this->transfId = $transfId;
		$this->accId = $accId;
		$this->bankName = $bankName;
		$this->bankAddress = $bankAddress;
		$this->accountName = $accountName;
		$this->accountNumber = $accountNumber;
		$this->routingNumber = $routingNumber;
		$this->dateTransfered = $dateTransfered;
		$this->amount = $amount;
		$this->description = $description;

    	$this->pk_names=[ 'transfId' ];
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