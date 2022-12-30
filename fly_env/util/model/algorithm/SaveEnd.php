<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

use FLY_ENV\Util\Model\QueryBuilder;

trait SaveEnd {

    /**
     * @method object end()
     *
     * @return void
     */
           
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


            private $model;

            public function __construct($self,QueryBuilder $model)
            {
                $this->finalQuery = $self;
                $this->model      = $model;
                $this->result     = $model->getPDO()->executeSaveQuery($this->finalQuery,[]);
            }

            public function value()
            {
                return $this->result;
            }

            public function reset()
            {
                return $this->model;
            }

            public function __toString()
            {
                return (string)$this->result;
            }
        };
    }
}