<?php namespace App\Models\offshore_bank_db\DS;
use FLY\Model\Algorithm\Model_Controllers;
use FLY_ENV\Util\Model\QueryBuilder;

class CUSTOMER extends QueryBuilder {

/*        
	*******************************************************************************
	* can use transaction here                                                    *
	* example: use TRANSACTION;                                                   *
	* To use a transaction specify the namespace above this model class.          *
	* That is, copy and paste the namespace: use FLY\Model\Algorithm\TRANSACTION; * 
	* right above this model class.                                               *
	*******************************************************************************
*/

	protected $cusId;

	protected $firstName;

	protected $lastName;

	protected $phoneNum;

	protected $blockTransfer;

	protected $transferDisplay;

	protected $email;

	protected $zipCode;

	protected $gender;

	protected $occupation;

	protected $address;

	protected $state;

	protected $city;

	protected $country;

	protected $dob;

	protected $picName;

	protected $password;


	use Model_Controllers;

	public function __construct($cusId="",$firstName="",$lastName="",$phoneNum="",$blockTransfer="",$transferDisplay="",$email="",$zipCode="",$gender="",$occupation="",$address="",$state="",$city="",$country="",$dob="",$picName="",$password="") 
	{
    	parent::__construct($this);
		$this->cusId = $cusId;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->phoneNum = $phoneNum;
		$this->blockTransfer = $blockTransfer;
		$this->transferDisplay = $transferDisplay;
		$this->email = $email;
		$this->zipCode = $zipCode;
		$this->gender = $gender;
		$this->occupation = $occupation;
		$this->address = $address;
		$this->state = $state;
		$this->city = $city;
		$this->country = $country;
		$this->dob = $dob;
		$this->picName = $picName;
		$this->password = $password;

    	$this->pk_names=[ 'cusId' ];
    	
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