<?php 
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

/**
 * @trait  ISearchQuery
 * @todo   Implements transactions
 */

trait TRANSACTION {

    private bool $mode = FALSE;

    public function setTransactionMode(bool $mode)
    {
        $this->mode = $mode;
    }

    public function transactionActive() 
    {
        return $this->mode;
    }
}