<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

use Exception;
use FLY_ENV\Util\Model\QueryBuilder;

/**
 * @trait  SaveQuery
 * @todo   Helps invoke sql insert query
 */

 trait SaveQuery {


    /**
     * @var $activeModels
     * @todo Store's active models
     */
    private static array $activeModels = [];

    private array  $saveQueryFields    = [];

    private static array $modelSavings = [];


    private function getNonEmptyFields()
    {
        $data = [];
        foreach($this->activeModelFields as $var) {
            if(is_empty($this->{$var})) continue;
            $data[$var] = $this->{$var};      
        }
        return $data;
    }

    public function __set(string $var,$data)
    {
        $var = str_replace(':','',$var);
        if($this->field_is_valid($var,$this->getActiveModel())) {
            $this->{$var} = $data;
            $this->fields_mem->{$var} = $data;
        } else throw new \Exception("The field name '{$var}' does not exists in class '{$this->activeModelName}'.");
    }

    private function field_is_valid($field_name,$class_object = null)
	{
        if($class_object === null) $class_object = $this;

        return property_exists($class_object,$field_name);
    }

    public function append(...$fields)
    {
        foreach($fields as $key => $fd) {
            $fields[$key] = preg_replace('/^[:]/','',$fd);
        }
        return new class($this,$fields) {

            private string $query;

            private QueryBuilder $model;

            public function __construct(QueryBuilder $model,$fields)
            {
                $this->model = $model;
                if(count($fields) > 0) $fields = '('.implode(',',$fields).')';
                else $fields = '';
                $this->query = "INSERT INTO {$model->get_table_name()}{$fields} ";
            }

            public function __toString()
            {
                return $this->query;
            }

            public function distinct(...$fields)
            {
                return new class($this->model,$this->query,$fields) extends QueryServer implements ISearchQuery {

                    use SaveEnd;
                    /**
                     *
                     * @param string $class
                     * @param array ...$args
                     */
                    public function __construct(QueryBuilder $model,string $insert_query,array ...$args)
                    {
                        parent::__construct($model,$args,$insert_query,hex_str("44495354494e4354"));
                    }
                };
            }

            public function find(...$fields)
            {
                return new class($this->model,$this->query,$fields) extends QueryServer implements ISearchQuery {

                    use SaveEnd;
                    /**
                     *
                     * @param string $class
                     * @param array ...$args
                     */

                    public function __construct(QueryBuilder $model,string $insert_query,array ...$args)
                    {
                        parent::__construct($model,$args,$insert_query);
                    }
                };
            }
        };
    }

    public function include(QueryBuilder $model)
    {
        array_push(self::$activeModels,$model);
        return $this;
    }

    public function save_all()
    {
        $model = $this;
        self::$modelSavings[] = $model->save()->value();

        foreach(self::$activeModels as $loc => $model) {

            if(is_object($model) && $model instanceof QueryBuilder) {
                self::$modelSavings[] = $model->save()->value();
            }
            if(in_array(0,self::$modelSavings)) {
                $table_name = $model->get_table_name();
                $errMsg = 'Unable to save record in table: ';
                $errMsg.= '\''.$table_name.'\'';
                $errMsg.= ' kindly check the respective model query for table: ';
                $errMsg.= '\''.$table_name.'\'';
                throw new Exception($errMsg);
                return false;
            }
            unset(self::$activeModels[$loc]);
        }
        return true;
    }

    public function save()
    {
        $qry = $this->getSaveQuery();
        $result = !is_empty($qry) ? $this->getPDO()->executeSaveQuery($qry,$this->saveQueryFields): false;
        $model  = $this;
        return new class($model,$result) {

            private string $result;

            private QueryBuilder $model;

            private static QueryBuilder $_model;

            public function __construct(QueryBuilder $model,$result)
            {
                $this->model  = $model; 
                self::$_model = $model;
                $this->result = $result;
            }

            public function value() { return (int) $this->result;}

            public function __get($var)
            {
                return $this->model->{$var};
            }

            public function __set($var,$value)
            {
                $this->model->{$var} = $value;
            }

            public function __call($var,$args)
            {
                return $this->model->{$var}(...$args);
            }

            public static function __callStatic($var,$args)
            {
                return self::$_model->{$var}(...$args);
            }

            public function __toString()
            {
                return $this->result;
            }
        };
    }

    private function getSaveQuery()
    {
        $this->saveQueryFields = $this->getNonEmptyFields();
        $keys = array_keys($this->saveQueryFields);
        $values = null;
        $len = count($this->saveQueryFields);
        $query = "";
        $bittok = "494e5345525420494e544f20/2056414c55455320";
        $bits = explode('/',$bittok);
        for($i = 0; $i < $len; $i++) {
            $values .= '?';
            if(($i+1) < count($this->saveQueryFields)) {
                $values .= ', ';
            }
        }
        if(!is_empty($values) && !is_empty($keys))
            $query = hex_str($bits[0])."{$this->getActiveModelName()} (`" . implode('`,`', $keys) ."`) ".hex_str($bits[1])."({$values})";
        return $query;
    }
 }
