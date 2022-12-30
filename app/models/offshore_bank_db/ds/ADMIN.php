<?php namespace App\Models\offshore_bank_db\DS;
use FLY\Model\Algorithm\Model_Controllers;
use FLY_ENV\Util\Model\QueryBuilder;

class ADMIN extends QueryBuilder {

/*        
	*******************************************************************************
	* can use transaction here                                                    *
	* example: use TRANSACTION;                                                   *
	* To use a transaction specify the namespace above this model class.          *
	* That is, copy and paste the namespace: use FLY\Model\Algorithm\TRANSACTION; * 
	* right above this model class.                                               *
	*******************************************************************************
*/

	protected $username;

	protected $password;


	use Model_Controllers;

	public function __construct($username="",$password="") 
	{
    	parent::__construct($this);
		$this->username = $username;
		$this->password = $password;

    	$this->pk_names=[ 'username' ];
    	
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