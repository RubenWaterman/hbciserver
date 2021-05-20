<?php

/** @noinspection PhpUnhandledExceptionInspection */

/**
 * SAMPLE - Displays the statement of account for a specific time range and account.
 */

// See login.php, it returns a FinTs instance that is already logged in.
/** @var \Fhp\FinTs $fints */
$fints = require_once 'Samples/login.php';

// Just pick the first account, for demonstration purposes. You could also have the user choose, or have SEPAAccount
// hard-coded and not call getSEPAAccounts() at all.
$getSepaAccounts = \Fhp\Action\GetSEPAAccounts::create();
$fints->execute($getSepaAccounts);
if ($getSepaAccounts->needsTan()) {
    handleTan($getSepaAccounts); // See login.php for the implementation.
}
$oneAccount = $getSepaAccounts->getAccounts()[0];

$from = (new \DateTime())->sub(new DateInterval('P1D'));;
$to = new \DateTime();
$getStatement = \Fhp\Action\GetStatementOfAccount::create($oneAccount, $from, $to);
$fints->execute($getStatement);
if ($getStatement->needsTan()) {
    handleTan($getStatement); // See login.php for the implementation.
}

$soa = $getStatement->getStatement();
$alltransactions = array();
$statementnumber = 0;
foreach ($soa->getStatements() as $statement) {
    foreach ($statement->getTransactions() as $transaction) {
        $statementnumber++;
        $date = $transaction->getValutaDate()->format('Ymd'); # getBookingDate lieferte schon einmal das falsche Jahr, lieber valutadate
		if (!isset($dateCounters[$date])) {
			$dateCounters[$date] = 0;
        }
        $dateCounters[$date]++;
        $uniqId = $date.sprintf('%05d', $statementnumber).sprintf('%05d', $dateCounters[$date]);
        $arr = array(
            "txn" => $uniqId,
            "amount" => ($transaction->getCreditDebit() == \Fhp\Model\StatementOfAccount\Transaction::CD_DEBIT ? '-' : '') . $transaction->getAmount(),
            "name" => $transaction->getName(),
            "purpose" => $transaction->getMainDescription(),
            "account_number" => $transaction->getAccountNumber(),
            "date" => $transaction->getBookingDate()->format('d-m-Y')
        );
        array_push($alltransactions, $arr);
    }
}
echo json_encode($alltransactions);