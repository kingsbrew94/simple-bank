<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

use FLY_ENV\Util\Model\QueryBuilder;

/**
 * @trait  UpdateQuery
 * @todo   Helps invoke sql query 
 */

trait UpdateQuery {

   public function update()
   {
      $data_fields     = $this->getNonEmptyFields();
      $where           = "";
      $set             = "";
      $count           = 1;
      $numOfPks        = count($this->pk_names);

      foreach($this->pk_names as $key => $pkname) {
         if(array_key_exists($pkname,$data_fields)) {
            $where .= "{$pkname}='{$data_fields[$pkname]}'";
            if($count++ < $numOfPks) { 
               if(array_key_exists($this->pk_names[$key+1],$data_fields)) {
                  $where.=hex_str("20414e4420");
               }
            }
            else $count = 1;
            unset($data_fields[$pkname]);
         }
      }
      $count = 0;
      $numOfDataFields = count($data_fields);
      foreach($data_fields as $field => $value) {
            $set .= "{$field}='{$value}'";
            if(++$count < $numOfDataFields) $set.=",";
      }
      
      return ($set<>"" && $where<>"") ? $this->set($set)->where($where)->end(): false;
   }

   public function set(...$set_fields)
   {
      return new class($this,$set_fields) {

         private string $query;

         private QueryBuilder $model;

         public function __construct(QueryBuilder $model,array $set_fields)
         {      
            $this->model = $model;
            $this->query = hex_str("55504441544520")."{$model->get_table_name()}".hex_str("2053455420");
            $this->query.= $model->find()->interpretExpress(...$set_fields);
         }

         public function where(...$expressions)
         {
            $this->query.=hex_str("20574845524520").$this->model->find()->interpretExpress(...$expressions);
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
                     $this->result     = $model->getPDO()->executeUpdateQuery($this->finalQuery);
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
