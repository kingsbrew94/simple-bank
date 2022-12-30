<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model
 * @version 3.0.0
 */

namespace FLY\Model;

use FLY_ENV\Util\Model\QueryBuilder;

/**
 * @class SQLPDOEngine
 * @todo Implements PDO's methods
 */
class SQLPDOEngine extends \PDO {
    
    /**
     * @var $activeModel
     * @todo Stores a query builder object or null
     */
    private ?QueryBuilder $activeModel;

    
    /**
     * @var PDO objects
     * @todo Store's PDO's object
     */
    private $pdo;
    

    /**
     * @method SQLEngine __construct()
     * @param QueryBuilder|null $activeModel
     * @param string $host
     * @param string $db
     * @param string $user
     * @param string $password
     * @return void
     */   
    public function __construct(?QueryBuilder $activeModel,string $host,string $db,string $user,string $password)
    {   
        try {
            parent::__construct('mysql:host='. $host . ';dbname=' . $db, $user, $password);
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->checkTransaction($activeModel);
        } catch (\PDOException $err) {
            die($err->getMessage());
        }
    }


    /**
     * @method SQLEngine __destruct()
     * @return void
     */
    public function __destruct()
    {
        if($this->activeModel !== null  && method_exists($this->activeModel, 'transactionActive')) {
            if($this->activeModel->transactionActive()) {
                $this->commit();
            }
        }
    }


    /**
     * @method void checkTransaction()
     * @param QueryBuilder|null $activeModel
     * @return void
     * @todo Check's to initialize transactions
     */ 
    private function checkTransaction(?QueryBuilder $activeModel) 
    {
        $this->activeModel = $activeModel;
        try {
            if($activeModel !== null && method_exists($activeModel, 'transactionActive')) {
                if($activeModel->transactionActive()) {
                    $this->beginTransaction();
                }
                $activeModel->setTransactionMode(TRUE);
            }
        } catch(\Exception $err) {
            $activeModel->setTransactionMode(FALSE);
            $this->rollBack();
        }
    }


    /**
     * @method object executeSearchQuery()
     * @param string $query
     * @return array
     * @todo Execute's search query
     */
    public function executeSearchQuery(string $query): array
    {
        $this->pdo = $this->prepare($query);
        $this->pdo->execute();
        $this->pdo->setFetchMode(\PDO::FETCH_OBJ);
        return $this->pdo->fetchAll();
    }


    /**
     * @method int executeSaveQuery()
     * @param string $query
     * @param array $fields
     * @return integer
     * @todo Execute's insert query
     */
    public function executeSaveQuery(string $query,array $fields): int
    {
        $this->pdo = $this->prepare($query);
        $index     = 1;
        if($this->pdo) {
            foreach($fields as $field) {
                $this->pdo->bindValue($index,$field);
                $index++;
            }
            $this->pdo->execute();
        }
        return $this->pdo->rowCount();
    }

    
    /**
     * @method bool executeUpdateQuery()
     * @param string $query
     * @return boolean
     * @todo Execute's update query
     */
    public function executeUpdateQuery(string $query): bool
    {
        return $this->executeCUD($query);
    }


    /**
     * @method bool executeDeleteQuery()
     * @param string $query
     * @return boolean
     * @todo Execute's delete query
     */
    public function executeDeleteQuery(string $query): bool
    {
        return $this->executeCUD($query);
    }


    /**
     * @method object executeProcedures
     * @param string $query
     * @return array
     * @todo Execute's Procedures through the search method
     */   
    public function executeProcedures(string $query): array
    {
        return $this->executeSearchQuery($query);
    }


    /**
     * @method bool executeCUD()
     * @param string $query
     * @return bool
     * @todo Execute's insert, update and delete query
     */  
    private function executeCUD(string $query): bool
    {
        $this->pdo = $this->prepare($query);
        $this->pdo->execute();
        return $this->pdo ? TRUE : FALSE;
    }


    /**
     * @method mixed executeCRUD()
     * @param mixed $query
     * @return bool
     * @todo Execute's create, insert, update and delete query
     */
    public function executeCRUD(string $query)
    {
        if(strpos($query,hex_str('53454c454354')) === 0) {
            return $this->executeSearchQuery($query);
        } else if(strpos($query,hex_str('43414c4c')) === 0) {
            return $this->executeProcedures($query);
        }
        return $this->executeCUD($query);
    }
}
