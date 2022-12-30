<?php namespace App\Models\offshore_bank_db\DS;
use FLY\Model\Algorithm\Model_Controllers;
use FLY_ENV\Util\Model\QueryBuilder;

class ACCESS_LOGS extends QueryBuilder {

/*        
	*******************************************************************************
	* can use transaction here                                                    *
	* example: use TRANSACTION;                                                   *
	* To use a transaction specify the namespace above this model class.          *
	* That is, copy and paste the namespace: use FLY\Model\Algorithm\TRANSACTION; * 
	* right above this model class.                                               *
	*******************************************************************************
*/

	protected $logId;

	protected $cusId;

	protected $ip;

	protected $activity;

	protected $dateOfActivity;


	use Model_Controllers;

	public function __construct($logId="",$cusId="",$ip="",$activity="",$dateOfActivity="") 
	{
    	parent::__construct($this);
		$this->logId = $logId;
		$this->cusId = $cusId;
		$this->ip = $ip;
		$this->activity = $activity;
		$this->dateOfActivity = $dateOfActivity;

    	$this->pk_names=[ 'logId' ];
    	$this->fk_names=[ 'cusId'=>'cusId' ];
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