<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

use FLY_ENV\Util\Model\QueryBuilder;

/**
 * @class  QueryServer
 * @todo   Helps invoke sql query 
 */

 abstract class QueryServer {

    /**
     * @var string $query
     * @todo Stores query
     */
    protected string $query          = '';


    /**
     * @var string $query
     * @todo Stores initial query string
     */
    protected string $initial_query  = '';


    /**
     * @var string $initial_source
     * @todo Stores the 'FROM [table name|tuple]' query
     */
    private string $initial_source = '';


    /**
     * @var QueryBuilder $model
     * @todo Store's query builder model
     */
    protected QueryBuilder $model;


    /**
     * @var array $search_models
     * @todo Store's search models
     */
    private array $search_models = [];

    
    /**
     * @var array $models_descriptions
     * @todo Store's models descriptions
     */
    private array $model_descriptions = [];


    /**
     * @var array $aliases
     * @todo Stores tables aliases
     */
    private array $aliases            = [];


    /**
     * @var bool $aliases
     * @todo Stores additional query
     */
    private bool $is_distinct        = false;

    use QueryParser;

     /**
      * @param QueryBuilder $model
      * @param array ...$args
      * @param string $add_query
      * @param string $distinct
      */

    public function __construct(QueryBuilder $model, array $args, string $add_query="",$distinct="")
    {  
        $this->model = $model;
        
        if(is_array($args[0]) && count($args[0]) === 0) $args[0][0] = '*';

        $this->initial_query = $add_query.hex_str('53454c45435420').$distinct.' '.$this->setFields($args[0]);
        $this->initial_source= hex_str("2046524f4d20")."{$model->get_table_name()} ";
        $this->query         = $this->initial_query.$this->initial_source;
        array_push($this->search_models,$model->get_table_name());
    }

    public function find(...$args)
    {
        return new Self($this->model,$args,"",$this->is_distinct ? hex_str("44495354494e4354"):"");
    }

    public function distinct(...$args)
    {
        $this->is_distinct = true;
        return $this->find($args);
    }


    /**
     * @method string __toString
     * @return string
     */

    public function __toString()
    {
        return $this->query;
    }

    public function blueprint(): array
    {
        foreach($this->search_models as $model) {
            $this->model_descriptions[$model] = $this->model->getPDO()->executeSearchQuery(hex_str("444553435249424520")."{$model}");
        }
        return $this->model_descriptions;
    }

     /**
      * @method string fieldIsQuery()
      * @param string $fieldText
      * @return bool
      * @todo Returns the whether the select field for a search is a query
      */

    private function fieldIsQuery(string $fieldText): bool
    {
        return preg_match('/^(?:\s*[^\w\d_:])/',$fieldText);
    }


    /**
     * @method string methodName()
     * @param string $fieldText
     * @return string
     * @todo Returns a parsed sub query
     */

    private function getFieldQuery(string $fieldText): string 
    {
        return preg_replace('/^\s*(.*)\|.*/','$1',$fieldText);
    }


    /**
     * @method boolean fieldHasAlias
     * @param string $fieldText
     * @return boolean
     * @todo Returns determines whether a field has an alias
     */

    private function fieldHasAlias(string $fieldText): bool 
    {
        return count($this->getFieldSections($fieldText)) === 2;
    }


    /**
     * @method boolean fieldHasAggregate()
     * @param string $fieldText
     * @return boolean
     * @todo Checks whether query field has an aggregate
     */

    private function fieldHasAggregate(string $fieldText): bool 
    {
        $test = trim($this->getFieldAggregate($fieldText));
        return $test <>"" && !is_numeric($test);
    }


    /**
     * @method string getFieldAlias()
     * @param string $fieldText
     * @return string
     * @todo Get's query field alias from a search query
     */

    private function getFieldAlias(string $fieldText): string
    {
        return $this->getFieldSections($fieldText)[1];
    }


    /**
     * @method string getFieldSections()
     * @param string $fieldText
     * @return array
     * @todo It's split query field into sections
     */

    private function getFieldSections(string $fieldText): array
    {
        return explode('|',$fieldText);
    }


    /**
     * @method array getFieldNameAndAgregate()
     * @param string $fieldText
     * @return array
     */

    private function getFieldNameAndAgregate(string $fieldText): array
    {
        return explode(':',$this->getFieldSections($fieldText)[0]);
    }


    /**
     * @method string getFieldName()
     * @param string $fieldText
     * @return string
     */

    private function getFieldName(string $fieldText): string 
    {
        return $this->getFieldNameAndAgregate($fieldText)[1]??$this->getFieldNameAndAgregate($fieldText)[0];
    }


     /**
      * @method string getFieldAggregate()
      * @param string $fieldText
      * @return string
      */

    private function getFieldAggregate(string $fieldText): string 
    {
        return $this->getFieldNameAndAgregate($fieldText)[0];
    }


     /**
      * @method string getPropsByIndex
      * @param string $field_name
      * @param callable $callback
      * @return string
      */

    private function getPropsByIndex(string $field_name,callable $callback): string
    {
        $splitance = explode('.',$field_name);

        if(count($splitance) ===  2 && is_numeric($splitance[0] = str_replace(':','',$splitance[0])) && trim($splitance[1])<>"") {
            $alias = $callback()[((int) $splitance[0]) - 1]?? ''; 
            return $alias <> ''?$alias.'.'.$splitance[1] : $splitance[1];
        }
        return $field_name;
    }


     /**
      * @method string getPropsByIndices
      * @param array $fields
      * @param callable $callback
      * @param string $glue
      * @return string
      */

    private function getPropsByIndices(array $fields,callable $callback,string $glue=' '): string
    {
        
        foreach($fields as $key => $field) {
            $field = trim($field);
            if(preg_match('/^[:][1-9]+[.]/',$field)) {
                $fields[$key] = $this->getPropsByIndex($field,$callback);
            }
        }
        return implode($glue,$fields);
    }


     /**
      * @method object from
      * @param string $model_name
      * @return object
      */

    public function from(string $model_name): object
    {
        $model_name = $this->model->getTableName($model_name);
        $this->query = str_replace(
            $this->initial_query.$this->initial_source,
            $this->initial_query.hex_str('2046524f4d20').$model_name.' ',
            $this->query
        );
        return $this;
    }


    /**
     * @method group_by()
     * @param array $filter
     * @return object
     */

    public function group_by(...$filter): object 
    {
        $this->query.= hex_str('2047524f555020425920').$this->empowerExpressions($filter,',');
        return $this;
    }
    

    /**
     * @method array order_by()
     * @param array $filter
     * @return object
     */

    public function order_by(...$filter): object 
    {
        $this->query.= hex_str('204f5244455220425920'). $this->empowerExpressions($filter,',');
        return $this;
    }

    
    /**
     * @method asc()
     * @return object
     */

    public function asc(): object
    {
        $this->query.= hex_str('2041534320');   
        return $this;
    }


    /**
     * @method object desc()
     * @return object
     */

    public function desc(): object
    {
        $this->query.= hex_str('204445534320');   
        return $this;
    }


    /**
     * @method object alias
     * @param string $name
     * @return object
     */

    public function alias(string $name): object 
    {
        $this->query.= $name;
        array_push($this->aliases,$name);
        return $this;
    }


    /**
     * @method string getAliasByIndex()
     * @param  string $field_name
     * @return string
     */

    public function getAliasByIndex(string $field_name): string 
    {
        return $this->getPropsByIndex($field_name,fn() => $this->aliases);
    }

    public function getModelByIndex(string $field_name): string
    {
        return $this->getPropsByIndex($field_name,fn() => $this->search_models);
    }


     /**
      * @method object on()
      * @param mixed ...$test
      * @return object
      */

    public function on(...$test): object 
    {
        $this->query .= ' ON '.$this->empowerExpressions($test);
        return $this;
    }


    /**
     * @method object join()
     * @param string $md
     * @param string $type
     * @return object
     */

    private function joins(string $md, string $type=''): object
    {
        switch(strtoupper($type)) {
            case 'L': 
                $type = '4c45465420';  break;

            case 'R':
                $type = '524947485420'; break;

            case 'I': 
                $type = '494e4e455220'; break;

            case 'O':
                $type = '4f5554455220'; break;

            default: 
                $type = '';      break;
        }
        $type = hex_str($type);
        $md = $this->model->getTableName($md);
        $this->query.= " {$type}".hex_str("4a4f494e20")."{$md} ";
        array_push($this->search_models,$md);
        return $this;
    }


    /**
     * @method object join()
     * @param string $md
     * @param array  $expressions
     * @return object
     */

    public function join(string $md,...$expressions): object 
    {
        $self = $this;
        $vals = explode('|',$md);
        $last = trim(end($vals));      
        if(count($vals) >= 2 && $last<>"") {
            $self->joins($vals[0],'');
            $self->alias($last);
        } else $self->joins($md,''); 

        if(!empty($expressions)) $self->on(...$expressions);
        
        return $self;
    }

    /**
     * @method string left_join()
     * @param string $md
     * @param array  $expressions
     * @return object
     */

    public function left_join(string $md,...$expressions): object 
    {
        $self = $this;
        $vals = explode('|',$md);
        $last = trim(end($vals));      
        if(count($vals) >= 2 && $last<>"") {
            $self->joins($vals[0],'L');
            $self->alias($last);
        } else $self->joins($md,'L'); 

        if(!empty($expressions)) $self->on(...$expressions);
        
        return $self;
    }


    /**
     * @method string right_join()
     * @param string $md
     * @param array  $expressions
     * @return object
     */

    public function right_join(string $md,...$expressions): object 
    {
        $self = $this;
        $vals = explode('|',$md);
        $last = trim(end($vals));      
        if(count($vals) >= 2 && $last<>"") {
            $self->joins($vals[0],'R');
            $self->alias($last);
        } else $self->joins($md,'R'); 

        if(!empty($expressions)) $self->on(...$expressions);
        
        return $self;
    }
    

    /**
     * @method string inner_join()
     * @param string $md
     * @param array  $expressions
     * @return object
     */

    public function inner_join(string $md,...$expressions): object 
    {
        $self = $this;
        $vals = explode('|',$md);
        $last = trim(end($vals));      
        if(count($vals) >= 2 && $last<>"") {
            $self->joins($vals[0],'I');
            $self->alias($last);
        } else $self->joins($md,'I'); 

        if(!empty($expressions)) $self->on(...$expressions);
        
        return $self;
    }


    /**
     * @method object outer_join()
     * @param string $md
     * @param array  $expressions
     * @return object
     */

    public function outer_join(string $md,...$expressions): object 
    {
        $self = $this;
        $vals = explode('|',$md);
        $last = trim(end($vals));      
        if(count($vals) >= 2 && $last<>"") {
            $self->joins($vals[0],'O');
            $self->alias($last);
        } else $self->joins($md,'O'); 

        if(!empty($expressions)) $self->on(...$expressions);
        
        return $self;
    }


    /**
     * @method object where()
     * @param array $expressions
     * @return object
     */

    public function where(...$expressions): object 
    {
        $this->query.= hex_str('20574845524520').$this->empowerExpressions($expressions);
        return $this;
    }
 
    /**
     * @method object whereId()
     * @param mixed $idValue
     * @return object
     */
    public function whereId($idValue): object 
    {
        $this->query.= hex_str('20574845524520'). $this->model->getPks()[0]."='".$idValue."'";
        return $this;
    }

     /**
     * @method object whereIds()
     * @param mixed $idValues
     * @return object
     */
    public function whereIds(array $idValues): object 
    {
        $construct = "";
        $pks = $this->model->getPks();
        $pkLen = count($pks);
        $counter = 0;
        foreach($pks as $field) {
            ++$counter;
            if(!isset($idValue[$field])) continue;
            $value = is_numeric($idValues[$field]) ? $idValues[$field]: "'".$idValues[$field]."'";
            $construct .= $field."='".$value."'";
            if($counter < $pkLen) $construct .= " AND ";
        }

        $this->query.= hex_str('20574845524520'). $construct;
        return $this;
    }

    /**
     * @method object having()
     * @param array $expression
     * @return object
     */

    public function having(...$expression): object 
    {
        if(isset($expression[0])) {
            $expression[0] = $this->parseDeepFilterQuery($expression[0]);
        }
        $this->query.= hex_str('20484156494e4720').$this->empowerExpressions($expression);
        return $this;
    }


    /**
     * @method object limit
     * @param integer $lim
     * @return object
     */

    public function limit(int $lim): object 
    {
        $this->query.= hex_str("204c494d495420")."{$lim}";
        return $this;
    }


    /**
     * @method object wrap()
     * @return object
     */

    public function wrap(): object
    {
        $this->query = '('.$this->query.')';
        return $this;
    }

    public function interpretExpress(...$expressions)
    {
        return $this->empowerExpressions($expressions);
    }

    private function empowerExpressions(array $expressions, string $glue=' ')
    {
        if(is_array($expressions[0]??null) && preg_match('/^[:][a-zA-Z_][a-zA-Z_\s]*/',array_key_first($expressions[0]))) {
            $deepQuery = $this->deepFilterProcessor($expressions[0]);
            unset($expressions[0]);
            $expressions = [...$deepQuery,...$expressions];
        }
        
        return $this->getPropsByIndices($expressions,fn() => $this->aliases,$glue);
    }

    private function deepFilterProcessor(array $search_query)
    {
        $expressions = array(
            'val'    => '=',
            'value'  => '=',
            'is'     => '=',
            'gt'     => '>',
            'lt'     => '<',
            'gte'    => '>=',
            'lte'    => '<=',
            'match'  => 'LIKE',
            'like'   => 'LIKE',
            'in'     => 'IN',
            'qry'    => '',
            'query'  => '',
            '?'      => ''
        );

        $conditions_and_operators = array(
            '&'      => 'AND',
            '|'      => 'OR',
            '!'      => '!',
            '<>'     => '<>',
            'and'    => 'AND',
            'or'     => 'OR',
            'not'    => 'NOT',
            '-'      => '-',
            '+'      => '+',
            '/'      => '/',
            '*'      => '*',
            'not eq' => '!=',
            'NOT EQUAL' => '!='   
        );

        $queries= [];

        foreach($search_query as $fieldKey => $fieldVals) {
            $fieldKey = trim($this->getPropsByIndex($fieldKey,fn() => $this->aliases));
            if(preg_match('/^[:](?:(?:[a-zA-Z_]*[.])|[a-zA-Z_])[a-zA-Z0-9\s]*/',$fieldKey)) {
                if(is_object($fieldVals)) {
                    $this->deepFilterProcessorExpHelper(
                        $expressions,
                        $conditions_and_operators,
                        $queries,
                        $fieldKey,
                        (array) $fieldVals
                    ); 
                }
                else if(is_array($fieldVals)) {
                    foreach($fieldVals as $key => $exps) {
                        if(is_object($exps)) $exps = (array) $exps;
                        if(is_array($exps) && is_numeric($key)) {
                            $this->deepFilterProcessorExpHelper(
                                $expressions,
                                $conditions_and_operators,
                                $queries,
                                $fieldKey,
                                (array) $exps,
                                true
                            );
                        }  else if(is_numeric($key) && isset($conditions_and_operators[$exps])) {
                            array_push($queries,$conditions_and_operators[$exps]);
                        }
                    }
                } 
            } else if(is_numeric($fieldKey) && !is_array($fieldVals) && isset($conditions_and_operators[$fieldVals])) {
                array_push($queries,$conditions_and_operators[$fieldVals]);
            }
           
        }
        return $queries;
    }


    private function deepFilterProcessorExpHelper(array $expressions,array $conditions_and_operators,array &$queries,string $fieldKey,array $exps,bool $limit = false)
    {
        foreach($exps as $key => $value) {
            $valueIsTuple = false;
            $key          = strtolower($key);
            if(is_array($value)) {
                foreach($value as $tupKey => $tupVal) {
                    if($tupKey === 'qry' || $tupKey === 'query' || $tupKey === '?') {
                        continue;
                    } else if(!is_numeric($tupVal)) $value[$tupKey] = "'{$tupVal}'";
                }
                $value = "(".implode(',',$value).")";
                $valueIsTuple = true;
            }

            $key   = trim($key);
            $value = trim($this->getPropsByIndex($value,fn() => $this->aliases));

            if(!is_numeric($key) && isset($expressions[$key])) {    
                array_push($queries,str_replace(':','',$fieldKey));
                array_push($queries,$expressions[$key]);

                if($key === 'qry' || $key === 'query' || $key === '?') {
                    $value = "(".$value.")";
                }

                array_push($queries,
                    (
                        $key === 'qry'   || 
                        $key === 'query' || 
                        $key === '?'     ||
                        $key === 'is'    ||
                        $valueIsTuple 
                    )
                    ? $value
                    : "'{$value}'"
                );
                
            } else if(!$limit && isset($conditions_and_operators[$value])) {
                array_push($queries,$conditions_and_operators[$value]);
            } else if(!is_numeric($key) && !$limit) {
                array_push($queries,str_replace(':','',$fieldKey));
                array_push($queries,strtoupper($key));
                array_push(
                    $queries,
                    !preg_match('/^[:][a-zA-Z_][.]?[a-zA-Z_]*$/',$value)
                    && !$valueIsTuple
                    ? "'{$value}'"
                    : $value
                );
            }
            if($limit) break;
        }
    }

    private function parseDeepFilterQuery(array $search_query)
    {
        $keys        = array_keys($search_query);
        $result      = explode(',',$this->setFields($keys));
        $parsedQuery = [];

        foreach($result as $index => $fields) {
            if(is_numeric($fields)) continue;
            $parsedQuery[':'.$fields] = $search_query[$keys[$index]];
        }
        return $parsedQuery;
    }
 }
