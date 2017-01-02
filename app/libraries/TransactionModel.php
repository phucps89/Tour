<?php

/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 12/15/2016
 * Time: 9:11 PM
 */
class TransactionModel
{
    protected $_transaction, $_value;

    /**
     * TransactionModel constructor.
     * @param $_transaction
     * @param $_value
     */
    public function __construct(array $_transaction, $_value)
    {
        $this->_transaction = new \Illuminate\Support\Collection($_transaction);
        $this->_value = $_value;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getTransaction()
    {
        return $this->_transaction;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }


}