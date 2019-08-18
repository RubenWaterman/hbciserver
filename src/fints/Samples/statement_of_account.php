<?php

/**
 * SAMPLE - Displays the statement of account for a specific time range and account.
 */


require '../vendor/autoload.php';

use Fhp\FinTs;
use Fhp\Model\StatementOfAccount\Statement;
use Fhp\Model\StatementOfAccount\Transaction;

define('FHP_BANK_URL', getenv('FINTEX_BANK_URL'));                # HBCI / FinTS Url can be found here: https://www.hbci-zka.de/institute/institut_auswahl.htm (use the PIN/TAN URL)
define('FHP_BANK_PORT', 443);              # HBCI / FinTS Port can be found here: https://www.hbci-zka.de/institute/institut_auswahl.htm
define('FHP_BANK_CODE', getenv('FINTEX_BANK_CODE'));               # Your bank code / Bankleitzahl
define('FHP_ONLINE_BANKING_USERNAME', getenv('FINTEX_BANK_USERNAME')); # Your online banking username / alias
define('FHP_ONLINE_BANKING_PIN', getenv('FINTEX_BANK_PIN'));      # Your online banking PIN (NOT! the pin of your bank card!)

$fints = new FinTs(
    FHP_BANK_URL,
    FHP_BANK_PORT,
    FHP_BANK_CODE,
    FHP_ONLINE_BANKING_USERNAME,
    FHP_ONLINE_BANKING_PIN
);

$accounts = $fints->getSEPAAccounts();

$oneAccount = $accounts[0];
$from = new \DateTime('2016-01-01');
$to   = new \DateTime();
$soa = $fints->getStatementOfAccount($oneAccount, $from, $to);
$alltransactions = array();

foreach ($soa->getStatements() as $statement) {
    foreach ($statement->getTransactions() as $transaction) {
        $arr = array(
          "Amount" => ($transaction->getCreditDebit() == Transaction::CD_DEBIT ? '-' : '') . $transaction->getAmount(),
          "Name" => $transaction->getName(),
          "Description" => $transaction->getDescription1(),
          "IBAN" => $transaction->getAccountNumber(),
          "Date" => $transaction->getBookingDate()->format('d-m-Y')
        );
        array_push($alltransactions, $arr);
    }
}
echo json_encode($alltransactions, JSON_PRETTY_PRINT);
