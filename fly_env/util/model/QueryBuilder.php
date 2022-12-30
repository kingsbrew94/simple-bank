<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model
 * @version 3.0.0
 */

namespace FLY_ENV\Util\Model;

use ArrayAccess;
use Countable;
use Error;
use Exception;
use FLY\Libs\Request;
use FLY\Model\Algorithm\{
    Config,
    DeleteQuery,
    IndexQueryParser,
    SaveQuery,
    SearchQuery,
    UpdateQuery
};
use FLY\Model\SQLPDOEngine;

/**
 * @class  QueryBuilder
 * @todo   Organizes model fields
 */

abstract class QueryBuilder extends Config implements ArrayAccess, Countable {

    /**
	 * @todo Stores table data in memory
	 */
	private array $dataList = [];

    /**
     * @todo Store pk map
     */
    private array $pkMap    = []; 
    /** 
     * @var array|null $methods
     * @todo Allocates memory for custom methods
     */
    private static ?array $methods                  = [];
    
    
    /** 
     * @var array $pk_names
     * @todo Holds model primary fields
     */
    protected array $pk_names                      = [];
    

    /** 
     * @var array $fk_names
     * @todo Holds model foreign key fields
     */
    protected array $fk_names                      = [];

    
    /**
     * @var string
     * @todo Stores active model name
     */
    private string $activeModelName                = '';


    /**
     * @var QueryBuilder|null 
     * @todo Stores a model object
     */
    private ?QueryBuilder $activeModelObject;

    /**
     * @var SQLPDOEngine|null 
     * @todo Stores a model object
     */
    private ?SQLPDOEngine $pdo;

    
    /**
     * @var array $activeModelVars
     * @todo Stores active model's fields 
     */
    private array $activeModelFields   = [];


    /**
     * @var object|null $fields_mem
     * @todo Stores model fields
     */
    private ?object $fields_mem        = null;

    
    use SearchQuery;

    use SaveQuery;

    use UpdateQuery;

    use DeleteQuery;

    /**
     * @param QueryBuilder $model
     */

    public function __construct(QueryBuilder $model)
    {
        $this->activeModelName   = $model->get_name();

        $this->activeModelObject = $model;
        $this->connectToDatabase();
        $this->setChildClassFields();
    }


    /**
     * @method void __destruct()
     * @return void
     */
    public function __destruct()
    {
        self::$methods = null;
    }

    private function commitFieldsToMem()
    {
        $this->fields_mem = (object) [];       
        foreach($this->activeModelFields as $fields_mem) {
            $this->fields_mem->{$fields_mem} = $this->{$fields_mem};
        }
    }

    private function connectToDatabase()
    {
        $config = $this->getConfigurations();   
        $this->pdo = new SQLPDOEngine(
            $this->activeModelObject,
            $config->getHost(),
            $config->getModel(),
            $config->getUser(),
            $config->getPassword()
        );
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function getPks()
    {
        return $this->pk_names;
    }

    public function getActiveModel()
    {
        return $this->activeModelObject;
    }

    public function get_active_model_fields()
    {
        return $this->activeModelFields;
    }

    private function getConfigurations(): Config 
    {
        $this->setUp(
            $this->activeModelObject->connection()
        );
        return $this;
    }

    private function getActiveModelName()
    {
        return $this->getTableName($this->activeModelName);
    }

    static protected function searchModelName(string $class_name)
    {
        $splitance = explode('\\',$class_name);
        return end($splitance);
    }

    public function getTableName(string $activeModelName)
    {
        return self::searchModelName($activeModelName);
    }

    public function get_table_name()
    {
        return $this->getActiveModelName();
    }

    /**
     * @method void createProc()
     * @param string $name
     * @param callable $callback
     * @return void
     */
    public static function createProc(string $name,callable $callback)
    {
        self::$methods[$name] = (object)[
            'action' => $callback,
            'type'   => 'proc'
        ];
    }

    public static function createQuery(string $name,callable $callback)
    {
        self::$methods[$name] = (object)[
            'action' => $callback,
            'type'   => 'qry'
        ];
    }

    /**
     * @method void createMethod
     * @param string $name
     * @param callable $callback
     * @return void
     */
    public static function createMethod(string $name,callable $callback)
    {
        self::$methods[$name] = (object)[
            'action' => $callback,
            'type'   => 'method'
        ]; 
    }


    /**
     * @method void __call()
     * @param string $name
     * @param array $arguments
     * @return QueryBuilder
     * @throws Exception
     */
    public function __call(string $name, array $arguments)
    {
        if(is_array(self::$methods) && array_key_exists($name,self::$methods)) {

            $action = self::$methods[$name]->action;
            
            switch(self::$methods[$name]->type) {
                
                case 'proc':
                    $this->cleanProcedureArgs($arguments);
                    if(!empty($arguments)) return $action($this,...$arguments);
                    return $action($this,$arguments);
                    
                case 'qry':
                    if(!empty($arguments)) return $action($this,...$arguments);
                    return $action($this,$arguments);

                default:
                    if(!empty($arguments)) $action($this,...$arguments);
                    else $action($this,$arguments);
                    
                    return $this;
            }
        }
        throw new Exception("The method '{$name}' does not exists");
    }


    /**
     * @return array
     */
	public function toArray(): array 
	{
        $this->loadList();

		return $this->dataList;
	}


    /**
     * @param integer $offset
     * @return void
     */
    public function commit(int $offset)
    {
        if(isset($this->dataList[$offset])) {
            return self::make_auto_update((array)$this->dataList[$offset],$this);
        }
        return false;
    }


    /**
     * @param int $offset
     * @param object|array $value
     * @return void
     */
	public function offsetSet($offset, $value) 
	{
        $offsetIsAcceptableString = in_array($offset,['save','update']);
        if(is_int($offset) || $offsetIsAcceptableString) {
            $this->loadList();
            if(is_int($offset)) $this->setPkMap($offset);
        }

        if (is_null($offset) || ($offsetIsAcceptableString && $offset == 'save')) {
            $value = (array) $value;
            if(!$this->recordExists($value) && ($this->recordHasValidIds($value) || (is_null($offset)) && $this->pkIsAutoIncrement())) {
                if(!($offsetIsAcceptableString && $offset == 'save') && $value instanceof QueryBuilder) {
                    self::make_auto_append($value->get_table_name(), $this);
                } else {
                    self::make_auto_save($value,$this);
                }
                $this->loadList();
            }
        } else if(isset($this->dataList[$offset]) || ($offsetIsAcceptableString && $offset == 'update')) {
            if(is_int($offset)) {
                $this->dataList[$offset] = array_merge($this->pkMap,(array)$value);
                self::make_auto_update((array)$this->dataList[$offset],$this);
            } else self::make_auto_update((array)$value,$this);
            $this->loadList();
        } else if(preg_match(IndexQueryParser::getMultiPattern(),$offset)) {
            $this->updateByQueryIndex($offset,$value);
        } else if(!isset($this->dataList[$offset])) throw new Error("Undefined index $offset");
    }


    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset) 
	{
		if(is_int($offset)) $this->loadList();
        return isset($this->dataList[$offset]);
    }


    /**
     * @param int $offset
     * @return void
     */
    public function offsetUnset($offset) 
	{
		if(is_int($offset)) $this->loadList();
		if(isset($this->dataList[$offset])) {
		    self::_delete_when((object) $this->dataList[$offset],$this);
            unset($this->dataList[$offset]);
        } else if(preg_match(IndexQueryParser::getDeletePattern(),$offset)) {
            $qio = new IndexQueryParser;
            $qio = $qio->interpretDelete($offset);
            if($qio <> null && $qio->hasFields() && !is_empty($qio->getFields()) && trim($qio->getFields()) === '*') {
                $self = $this->delete();
                if($qio->hasSearch()) {
                    $self = $self->where($qio->getSearch());
                }
                if($self->end()->value()) $this->loadList();
            }
        }
    }


    /**
     * @param int $offset
     * @return object
     */
    public function offsetGet($offset) 
	{
		if(is_int($offset)) {
            $this->loadList();
            $this->setPkMap($offset);
        }
        $patternString = null;
        if(preg_match(IndexQueryParser::getMultiPattern(),$offset)) {
            $patternString = $offset;
            $offset        = 'ismulpattern';
        } else if(preg_match(IndexQueryParser::getSinglePattern(),$offset)) {
            $patternString = $offset;
            $offset        = 'issgpattern';
        }
       // var_dump($match);
        return match($offset) {
            'save'                               => (function(){$res = null; $value = Request::all(); if(!$this->recordExists($value)) {$res = self::make_auto_save($value,$this); $this->loadList();} return $res;})(),
            'update'                             => (function(){$res = self::make_auto_update(Request::all(),$this); $this->loadList(); return $res;})(),
            'ismulpattern'                       => $this->getMultiDataByQueryIndex($patternString),
            'issgpattern'                        => $this->getSingleDataByQueryIndex($patternString),
            is_null($offset) && !is_int($offset) => $this,
            default                              => isset($this->dataList[$offset]) ? $this->dataList[$offset] : null 
        };
		
    }


    /**
     * @return int
     */
    public function count() 
    {
        return $this['$[count(*)]'];
    }


    /**
     * @param string $index_query
     * @param [type] $value
     * @return void
     */
    private function updateByQueryIndex(string $index_query, $value)
    {
        $qio = new IndexQueryParser;
        $qio = $qio->interpretUpdate($index_query);
        if($qio <> null && $qio->hasFields()) {
            $fieldstr = trim($qio->getFields());
            $self     = $this;
            if($fieldstr <> '*' && !is_empty($fieldstr)) {
                $fields = explode(',',$fieldstr);
                if(!is_array($value) && !is_object($value) && !in_array(trim($fields[0]),$this->getPks())) {
                    $self = $self->set("{$fields[0]}='$value'");
                } else if(is_array($value)) {
                    $isFields = false;
                    $setup = '';
                    foreach($fields as $key => $name) {

                        if(!isset($value[$key]) || in_array(trim($name),$this->getPks())) continue;

                        if($isFields === false) {
                            $setup .= "{$name}='$value[$key]'";
                            $isFields = count($fields) > 1;
                        } else {
                            $setup .= ",{$name}='$value[$key]'";
                        }
                    }
                    if(!is_empty($setup)) $self = $self->set($setup);
                    else $self = null;
                }
            } else if(is_array($value) || is_object($value)) {
                $isFields = false;
                $setup = '';
                $mdf = $this->get_active_model_fields();
                foreach($value as $fd => $val) {
                    if(in_array(trim($fd),$this->getPks()) || !in_array(trim($fd),$mdf)) continue;
                    if(preg_match('/[_a-zA-Z][_a-zA-Z0-9]*/',$fd)) {
                        if($isFields === false) {
                            $setup .= "{$fd}='$val'";
                            $isFields = count($value) > 1;
                        } else {
                            $setup .= ",{$fd}='$val'";
                        }
                    }
                }
                if(!is_empty($setup)) $self = $self->set($setup);
                else $self = null;
            }
            if($qio->hasSearch() && !is_empty($qio->getSearch()) && $self <> null) {
                $self = $self->where(preg_replace('/\s+([a-zA-Z_][a-zA-Z0-9_]*\s*[=])\s*([0-9]*)\s+/ixm',' $1\'$2\' ',' '.$qio->getSearch().' '));
            }
            if($self <> null) $self->end();

        }
    }


    /**
     * @param string|null $index_query
     * @return array|null
     */
    private function getMultiDataByQueryIndex(?string $index_query)
    {
        if($index_query <> null) {
            $qio = new IndexQueryParser;
            $qio = $qio->interpret($index_query);
            if($qio <> null && $qio->hasFields()) {
                $self = $this->find(...explode(',',$qio->getFields()));
                if($qio->hasSearch()) {
                    $self = $self->where($qio->getSearch());
                }
                return $self->end()->value(); 
            }
        }
        return null; 
    }


    /**
     * @param string|null $index_query
     * @return array|null
     */
    private function getSingleDataByQueryIndex(?string $index_query)
    {
        if($index_query <> null) {
            $qio = new IndexQueryParser;
            $qio = $qio->interpret($index_query);
            if($qio <> null && $qio->hasFields()) {
                $self = $this->find(':'.$qio->getFields());
                if($qio->hasSearch()) {
                    $self = $self->where($qio->getSearch());
                }
                $res  = $self->end()->value();
                if(!is_empty($res)) {
                    return $res[0]->{$qio->getFields()};
                }
            }
        }
        return null; 
    }

    
    /**
     * @param array $record
     * @return boolean
     */
    private function recordHasValidIds(array $record): bool 
    {
        $flag = true;
        $record_keys = array_keys($record);
        foreach($this->getPks() as $key) {
            $flag = $flag && in_array($key,$record_keys) && isset($record[$key]) && (!is_empty($record[$key]));
        }
        return $flag && count($this->getPks()) > 0;
    }


    /**
     * @return boolean
     */
    private function pkIsAutoIncrement(): bool 
    {
        $blueprint = self::_describe($this);
        if(isset($blueprint[$this->get_table_name()])) {
            $bp = $blueprint[$this->get_table_name()];
            foreach($bp as $data) {
                if(preg_match('/^PRI/i',$data->{'Key'}) && preg_match('/auto_increment/i',$data->{'Extra'})) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * @param array $record
     * @return boolean
     */
    private function recordExists(array $record): bool 
    {
        $query = [];
        $fieldMany = false;
        foreach($record as $key => $value) {
            if(in_array($key,$this->getPks())) {
                if(!$fieldMany) {
                    $query[":{$key}"] = (object) [
                        'val' => $value
                    ];
                    $fieldMany = count($record) > 1;
                } else {
                    array_push($query,'AND');
                   $query[":{$key}"] = (object) [
                       'val' => $value
                   ];
                }
            }
        }
        return !is_empty($query) && !is_empty($this->find()->where($query)->end()->value());
    }


    /**
     * @return void
     */
    private function loadList() 
	{
		$this->dataList = $this['[*]'];
	}


    /**
     * @param integer $offset
     * @return void
     */
    private function setPkMap(int $offset)
    {
        foreach($this->dataList[$offset] as $key => $value) {
            if(in_array($key,$this->pk_names)) {
                $this->pkMap = [$key => $this->dataList[$offset]->{$key}];
                break;
            }
        }
    }


    /**
     * @param array $args
     * @return void
     */
    private function cleanProcedureArgs(array &$args)
    {
        foreach($args as $key => $arg) {

            if(is_object($arg)) continue;
            if(is_array($arg)) {
                $this->cleanProcedureArgs($arg);
                $args[$key] = $arg;
            } else $args[$key] = "'{$arg}'";
        }
    }

    
    /**
     * @method void setChildClassFields
     * @return void
     * @todo Set's enlist active model fields
     */
    private function setChildClassFields()
    {
        $child_model_vars        = get_class_vars($this->activeModelName);
        $builder_vars            = get_class_vars(QueryBuilder::class);
        
        foreach($child_model_vars as $key => $val) {
            if(!\array_key_exists($key,$builder_vars)) {
                $this->activeModelFields[] = $key;
            }
        }
    }

    static protected function make_auto_save(array $data, QueryBuilder $model)
    {
        foreach($data as $key => $value) {
            if(!property_exists($model,$key)) continue;
            $model->{":$key"} = $value;
        }
        return $model->save();
    }

    static protected function make_auto_update(array $data, QueryBuilder $model)
    {
        foreach($data as $key => $value) {
            if(!property_exists($model,$key)) continue;
            $model->{":$key"} = $value;
        }
        return $model->update();
    }

    static protected function make_auto_set(array $data, QueryBuilder $model)
    {
        foreach($data as $key => $value) {
            if(!property_exists($model,$key)) continue;
            $model->{":$key"} = $value;
        }
        return $model;
    }

    static private function generateAppendTuples(array $data,$field)
    {
        $result = [];
        foreach($data as $dt) {
            array_push($result,$dt->{$field});
        }
        return $result;
    }

    static protected function _append_process(string $reference_model_name,$model,$callback)
    {
        $pks = $model->getPks();
        $numOfPks = count($pks);
        $hasTuple = false;
        if($numOfPks > 0) {
            $fql       = [];
            $count     = 1;    
            foreach($pks as $pkname) {
                $refModelIds = $callback($pkname);
                
                $refModelIds = self::generateAppendTuples($refModelIds,$pkname);
                if(count($refModelIds) > 0) {
                    $fql[":{$pkname}"] = (object)[
                        'not in'=> $refModelIds
                    ];
                    if($count++ < $numOfPks) {
                        array_push($fql,'&');
                    }
                    $hasTuple = true;
                }
            }
            $query = $model->append()
                    ->find()
                    ->from($reference_model_name);
            if($hasTuple){ 
                $query = $query->where($fql);
            }
            return $query->end();
        } else throw new Exception("Error: Primary key fields of the {$model->get_table_name()} model.");
    }

    static protected function _append_where(string $reference_model_name,array $where,$model)
    {
        $data_exists = $model->find()->where(...$where)->end()->count() > 0;
        if(!$data_exists) {
            return $model->append()
                    ->find()
                    ->from($reference_model_name)
                    ->where(...$where)
                    ->end();
        }
        return null;
    }

    static protected function make_auto_append(string $reference_model_name, QueryBuilder $model)
    {
        try {
            return self::_append_process(
                $reference_model_name,
                $model,
                fn($pkname) => $model->find()->end()->by_field($pkname)
            );
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function apply()
    {
        $this->commitFieldsToMem();
    }

    public function mem()
    {
        return $this->fields_mem;
    }

    public function where(array $search_query)
    {
        $result  = [];
        if($this->isShallowFiltering($search_query)) {
            $result = $this->find()->end()->filter($search_query);
        } else if($this->isDeepFiltering($search_query)){
            $result = $this->find()->where($search_query)->end()->value();
        }
        return $result;
    }

    protected static function _clear(QueryBuilder $model)
    {
        return $model->delete()->end();
    }

    protected static function _index(QueryBuilder $self,int $index)
    {
        return $self->find()->end()->by_index($index);
    }

    protected static function _field(QueryBuilder $self,string $field_name)
    {
        return $self->find()->end()->by_field($field_name);
    }

    protected static function _value(QueryBuilder $self,string $field_value)
    {
        return $self->find()->end()->by_value($field_value);
    }

    protected static function _field_value(QueryBuilder $self,string $field_name,string $field_value)
    {
        return $self->find()->end()->by_field_value($field_name, $field_value);
    }

    protected static function _match_field(QueryBuilder $self,string $pattern)
    {
        return $self->find()->end()->by_match_field($pattern);
    }

    protected static function _match_value(QueryBuilder $self,string $pattern)
    {
        return $self->find()->end()->by_match_value($pattern);
    }

    protected static function _match_field_value(QueryBuilder $self,string $field_pattern,string $value_pattern)
    {
        return $self->find()->end()->by_match_field_value($field_pattern,$value_pattern);
    }

    protected static function _all(QueryBuilder $self)
    {
        return $self->find()->end()->value();
    }

    protected static function _first(QueryBuilder $self)
    {
        return $self->find()->end()->first_record();
    }

    protected static function _second(QueryBuilder $self)
    {
        return $self->find()->end()->second_record();
    }

    protected static function _third(QueryBuilder $self)
    {
        return $self->find()->end()->third_record();
    }

    protected static function _middle(QueryBuilder $self)
    {
        return $self->find()->end()->middle_record();
    }

    protected static function _last(QueryBuilder $self)
    {
        return $self->find()->end()->last_record();
    }

    protected static function _count(QueryBuilder $self)
    {
        return $self->find()->end()->count();
    }

    protected static function _reverse(QueryBuilder $self)
    {
        return $self->find()->end()->reverse();
    }

    protected static function _field_null(QueryBuilder $self,string $field_name)
    {
        return $self->find()->end()->field_null($field_name);
    }

    protected static function _field_capacity(QueryBuilder $self,string $field_name)
    {
        return $self->find()->end()->field_capacity($field_name);
    }

    protected static function _field_type(QueryBuilder $self,string $field_name)
    {
        return $self->find()->end()->field_type($field_name);
    }

    protected static function _field_default(QueryBuilder $self,string $field_name)
    {
        return $self->find()->end()->field_default($field_name);
    }

    protected static function _field_extra(QueryBuilder $self,string $field_name)
    {
        return $self->find()->end()->field_extra($field_name);
    }

    protected static function _describe(QueryBuilder $self)
    {
        return $self->find()->end()->blueprint();
    }

    protected static function _is_empty(QueryBuilder $self)
    {
        return $self->find()->end()->is_empty();
    }

    protected static function _field_exists(QueryBuilder $self,string $field_name)
    {
        return $self->find()->end()->field_exists();
    }

    protected static function _value_exists(QueryBuilder $self,string $value,string $field_name)
    {
        return $self->find()->end()->value_exists($value,$field_name);
    }

    protected static function _fields(QueryBuilder $self)
    {
        return $self->find()->end()->fields();
    }
    
    protected static function _delete_where(array $expressions, QueryBuilder $model)
    {
        return $model->delete()->where(...$expressions)->end();
    }

    protected static function _delete_when(object $data, QueryBuilder $model)
    {
        return $model->indexPopper($data);
    }

    protected static function _query(string $query, QueryBuilder $model)
    {
        return $model->getPDO()->executeCRUD($query);
    }

    private function isDeepFiltering(array $search_query)
    {
        return preg_match('/^[:][a-zA-Z_][a-zA-Z0-9]*/',array_key_first($search_query));
    }

    private function isShallowFiltering(array $search_query)
    {
        return (
            array_key_exists('key',$search_query)   ||
            array_key_exists('value',$search_query) ||
            array_key_exists('match_key',$search_query) 
        );
    }
}