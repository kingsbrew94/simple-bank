<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

use FLY_ENV\Util\Model\QueryBuilder;

/**
 * @trait  DeleteQuery
 * @todo   Helps invoke sql query 
 */


trait DeleteQuery {

   public function pop(int $index = -1)
   {
      if($index > -1) {
         return $this->popByIndex($index);
      } else if($this->primary_keys_empty()) {
         return $this->popById();
      }
      return $this->autoPop();
   }

   public function pop_first()
   {
      return $this->indexPopper(static::first());
   }

   public function pop_second()
   {
      return $this->indexPopper(static::second());
   }

   public function pop_third()
   {
      return $this->indexPopper(static::third());
   }

   public function pop_middle()
   {
      return $this->indexPopper(static::middle());
   }

   private function primary_keys_empty()
   {
      $data_fields     = $this->getNonEmptyFields();
      $primaryKeysEmpty= false;

      foreach($this->pk_names as $pkname) {
         $primaryKeysEmpty = array_key_exists($pkname,$data_fields);
         if($primaryKeysEmpty) break;
      }
      return $primaryKeysEmpty;
   }

   private function popById()
   {
      $data_fields     = $this->getNonEmptyFields();
      $where           = "";
      $count           = 1;
      $numOfPks        = count($this->pk_names);
      foreach($this->pk_names as $key => $pkname) {
         if(array_key_exists($pkname,$data_fields)) {
            $where .= "{$pkname}='{$data_fields[$pkname]}'";
            if($count++ < $numOfPks){
               if(array_key_exists($this->pk_names[$key+1],$data_fields)) {
                  $where.=hex_str('20414e4420');
               }
            }
            else $count = 1;
            unset($data_fields[$pkname]);
         }
      }
      return $where<>""?$this->delete()->where($where)->end():false;
   }

   private function popByIndex(int $index)
   {
      return $this->indexPopper(static::index($index));
   }

   public function indexPopper(object $data) 
   {
      foreach($data as $key => $dt) {
         if(in_array($key,$this->activeModelFields)) {
            $this->{":$key"} = $dt;
         }
      }
      return $this->popById();
   }

   private function autoPop()
   {
      return $this->indexPopper(static::last());
   }

   public function delete() 
   {
      return new class($this) {

         private string $query;

         private QueryBuilder $model;

         public function __construct(QueryBuilder $model)
         {
            $this->model = $model;
            $this->query = hex_str('44454c4554452046524f4d20')."{$model->get_table_name()} ";
         }

         public function alias(string $alias)
         {
            $this->query.= $alias;
            return $this;
         }

         public function where(...$expressions)
         {
            $this->query .=hex_str('574845524520').$this->model->find()->interpretExpress(...$expressions);

            return $this;
         }
         
         /**
          * @method object whereId()
          * @param mixed $idValue
          * @return object
          */
         public function whereId($idValue): object 
         {
            $this->query.= hex_str('20574845524520').$this->model->getPks()[0]."='".$idValue."'";
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
               if($counter < $pkLen) $construct .=hex_str("20414e4420");
            }

            $this->query.= hex_str("20574845524520").$construct;
            return $this;
         }
         
         public function __toString()
         {
            return $this->query;
         }

         public function end() 
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


                  public function __construct($self,QueryBuilder $model)
                  {
                     $this->finalQuery = $self;
                     $this->result     = $model->getPDO()->executeDeleteQuery($this->finalQuery);
                  }

                  public function value()
                  {
                     return $this->result;
                  }

                  public function __toString()
                  {
                     return (string) $this->result;
                  }
            };
         }
      
      };
   }
}

