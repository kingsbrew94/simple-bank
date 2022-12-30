<?php namespace App\Models;

use App\Models\offshore_bank_db\DS\ACCESS_LOGS;
use App\Models\offshore_bank_db\DS\ACCOUNT;
use App\Models\offshore_bank_db\DS\CUSTOMER;
use App\Models\offshore_bank_db\DS\TRANSACTION_HISTORY;
use FLY_ENV\Util\Model\QueryBuilder;

trait Model_Handles 
{
    /**
     * @return void
     * @Todo It executes model customized procedures, queries and methods
     */
    private function main(): void
    {
        // Write your model procedures, queries and methods here
        ACCOUNT::createProc('View_Accounts',function(QueryBuilder $self) {
            $qry = $self->find(
                ':CR.firstName',':CR.lastName',':AC.accNumber|accountNumber',':AC.accCurrency|currency',
                ':AC.balance|accountBalance', ':AC.accTypeType|moneyMarket',':AC.accType|accountType',
                ':AC.accStatus|accountStatus',':AC.balance|accountBalance', ':AC.pin|accountPin',':AC.accId|accountId',
                ':CR.cusId|customerId'
            )
                ->alias('AC')
                ->inner_join(CUSTOMER::alias('CR'),'CR.cusId','=','AC.userId');
            return $qry->end()->value();
        });

        ACCOUNT::createProc('View_Accounts_ById',function(QueryBuilder $self, $accountId) {
            $qry = $self->find()
                ->alias('AC')
                ->inner_join(CUSTOMER::alias('CR'),'CR.cusId','=','AC.userId')
                ->where('AC.accId','=',$accountId);
            return $qry->end()->value();
        });

        TRANSACTION_HISTORY::createProc('View_Transactions',function(QueryBuilder $self) {
            $qry = $self->find(
                ':CR.firstName',':CR.lastName',':CR.email',':TX.*'
            )
                ->alias('TX')
                ->inner_join(ACCOUNT::alias('AC'),'TX.accId','=','AC.accId')
                ->inner_join(CUSTOMER::alias('CR'),'CR.cusId','=','AC.userId');
            return $qry->end()->value();
        });

        ACCESS_LOGS::createProc('View_Logs',function(QueryBuilder $self) {
            $qry = $self->find(
                ':CR.firstName',':CR.lastName',':LS.*'
            )
                ->alias('LS')
                ->inner_join(CUSTOMER::alias('CR'),'CR.cusId','=','LS.cusId');
            return $qry->end()->value();
        });


    }
}