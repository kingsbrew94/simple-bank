<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

use FLY_ENV\Util\Model\QueryBuilder;

trait SearchEnd {

    /**
     * @method object end()
     *
     * @return void
     */
    public function end(): object 
    {

        return new class($this,$this->model) {
            
            /**
             * @var string
             * @todo Holds process query
             */
            private string $finalQuery;


            /**
             * 
             * @var mixed
             * @todo Hold's result of a query being executed
             */
            private $result;


            /** 
             * @var mixed
             * @todo Hold's find object
             */
            private $self;


            public function __construct($self,QueryBuilder $model)
            {
                $this->finalQuery = $self;
                $this->self       = $self;
                $this->result     = $model->getPDO()->executeSearchQuery($this->finalQuery);
            }

            public function reset()
            {
                return $this->self;
            }

            public function count() 
            {
                return count($this->result);
            }

            public function reverse()
            {
                return array_reverse($this->result,true);
            }

            public function filter(array $search_query)
            {
                $result = [];
            
                if(array_key_exists('index',$search_query)) {
                    $result = $this->predictiveFilter($search_query);
                } else if(array_key_exists('field',$search_query) && array_key_exists('value',$search_query)) {
                    $result = $this->keyValueFilter($search_query,'all');
                } else if(array_key_exists('field',$search_query)) {
                    $result = $this->keyValueFilter($search_query,'field');
                } else if(array_key_exists('value',$search_query)) {
                    $result = $this->keyValueFilter($search_query,'value');
                } else if(array_key_exists('match_field',$search_query) && array_key_exists('match_value',$search_query)) {
                    $result = $this->matchKeyValueFilter($search_query,'all');
                } else if(array_key_exists('match_field',$search_query)) {
                    $result = $this->matchKeyValueFilter($search_query,'field');
                } else if(array_key_exists('match_value',$search_query)) {
                    $result = $this->matchKeyValueFilter($search_query,'value');
                } 
                return $result;
            }

            private function predictiveFilter(array $search): object
            {
                return $this->result[$search['index']-1]?? (object) [];
            }

            private function keyValueFilter(array $search,$type='all'): array
            {
                $result = [];
                foreach($this->result as $data) {
                    $array_data = (array) $data;
                    $dataObject  = null;
                    switch($type) {
                        case 'field':
                            if(array_key_exists($search['field'],$array_data)) 
                                $dataObject = (object) [
                                    $search['field'] => $data->{$search['field']}
                                ];
                        break;
                        case 'value':
                            $field = array_search($search['value'],$array_data);
                            if($field) 
                                $dataObject = $data->{$field};
                        break;
                        default:
                            if(
                                array_key_exists($search['field'],$array_data) && array_search($search['value'],$array_data)
                            )
                                $dataObject = $data;
                            else $dataObject = null;
                        break;
                    }
                    if($dataObject <> null) array_push($result,$dataObject);
                }
                return $result;
            }

            private function matchKeyValueFilter(array $search,$type): array
            {
                $result = [];
                foreach($this->result as $data) {

                    $array_data = (array) $data;
                    
                    foreach($array_data as $matchKey => $matchValue) {
                        
                        $dataObject  = null;
                    
                        switch($type) {
                            case 'field':
                                if(isset($search['match_field']) && preg_match($search['match_field'],$matchKey)) 
                                    $dataObject = (object) [
                                        $matchKey => $matchValue
                                    ];
                            break;
                            case 'value':
                                if(isset($search['match_value']) && preg_match($search['match_value'],$matchValue)) 
                                    $dataObject = $matchValue;
                            break;
                            default:
                                if(isset($search['match_field']) && isset($search['match_value']) && preg_match($search['match_field'],$matchKey) && preg_match($search['match_value'],$matchValue)) 
                                    $dataObject = $data;
                                else $dataObject = null;
                            break;
                        }
                        if($dataObject <> null) array_push($result,$dataObject);
                    }
                    
                }
                return $result;   
            }

            public function __toString()
            {
                return json_encode($this->result);
            }

            public function value()
            {
                return $this->result;
            }

            public function by_index($index): object 
            {
                return $this->filter(['index' => $index]);
            }

            public function by_field($field): array
            {
                return $this->filter(['field' => $field]);
            }

            public function by_value($value): array
            {
                return $this->filter(['value' => $value]);
            }

            public function by_field_value($field,$value): array
            {
                return $this->filter(['field' => $field,'value' => $value]);
            }

            public function by_match_field(string $field_pattern): array
            {
                return $this->filter(['match_field' => $field_pattern]);
            }

            public function by_match_value(string $value_pattern): array
            {
                return $this->filter(['match_value' => $value_pattern]);
            }

            public function by_match_field_value($field_pattern,$value_pattern): array
            {
                return $this->filter(['match_field' => $field_pattern,'match_value' => $value_pattern]);
            }

            public function first_record(): object 
            {
                return $this->filter(['index' => 1]);
            }

            public function second_record(): object 
            {
                return $this->filter(['index' => 2]);
            }

            public function third_record(): object
            {
                return $this->filter(['index' => 3]);
            }

            public function middle_record(): object
            {
                return $this->filter(['index' => (int) ($this->count() / 2)]);
            }

            public function last_record(): object 
            {
                return !empty($this->result) ? end($this->result): (object) [] ;
            }

            public function is_empty(): bool 
            {
                return $this->count() === 0;
            }

            public function fields(): array 
            {
                $fields = [];
                if(isset($this->result[0])) {
                    $fields = array_keys((array) $this->result[0]);
                }
                return $fields;
            }

            public function value_exists(string $value, string $field_name='')
            {
                $qry = ['value' => $value];
                if(!is_empty($field_name)) $qry['field'] = $field_name;

                return count($this->filter($qry)) > 0;
            }

            public function field_exists(string $field_name) 
            {
                return isset($this->result[0]) && array_key_exists($field_name,(array) $this->result[0]); 
            }

            public function field_size(int $position,string $field_name) 
            {
                $size = null;
                if($this->field_exists($field_name)) {
                    $field = (array) $this->filter(['index' => $position, 'value' => $field_name]);
                    $size  = strlen((string) ($field[$field_name]??0));
                }
                return $size;
            }

            public function field_type(string $field_name) 
            {
                return $this->blueprintSearch(
                    'Type',
                    $field_name,
                    fn($type) => preg_replace('/[(](?:.*)[)]/','',$type)
                );
            }


            public function field_capacity(string $field_name) 
            {
                return $this->blueprintSearch(
                    'Type',
                    $field_name,
                    fn($type) => preg_replace('/.*[\(](.*)[\)]/','$1',$type)
                );
            }

            
            public function field_null(string $field_name)
            {
                return $this->blueprintSearch(
                    'Null',
                    $field_name,
                    fn($type) => $type === 'YES'
                );
            }

            public function field_default(string $field_name)
            {
                return $this->blueprintSearch(
                    'Default',
                    $field_name,
                    fn($type) => $type
                );
            }

            public function field_extra(string $field_name)
            {
                return $this->blueprintSearch(
                    'Extra',
                    $field_name,
                    fn($type) => $type
                );
            }

            public function blueprint()
            {
                return $this->reset()->blueprint();
            }

            private function blueprintSearch(string $searchType,string $field_name,$callback) 
            {
                $blueprints = $this->reset()->blueprint();
                $explode    = explode('.',$this->self->getModelByIndex($field_name));
                $type  = [];

                if(count($explode) === 2) {
                    $found = false;
                    $table = strtoupper($explode[0]);
                    $field = $explode[1];   

                    foreach($blueprints as $table_name => $descriptions) {
                        if($table === strtoupper($table_name)) {
                            foreach($descriptions as $desc) {
                                if($desc->Field === $field) {
                                    $type  = $callback($desc->{$searchType});
                                    $found = true;
                                break;
                                }
                            }
                        }
                        if($found) break;
                    }
                } else if(count($explode) === 1) {
                    $field = $explode[0];

                    foreach($blueprints as $table_name => $descriptions) {
                        foreach($descriptions as $desc) {
                            if($desc->Field === $field) {
                                $type[$table_name] = [
                                    $field => $callback($desc->Type)
                                ];
                            }
                        }
                    }
                }
                return $type;
            }
        };
    }
}