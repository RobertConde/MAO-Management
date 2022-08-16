<?php

// TODO: implement into codebase
function existsPayment($payment_id)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$find_payment_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM payment_details WHERE payment_id = ?");
	$find_payment_stmt->bind_param('s', $payment_id);
	$find_payment_stmt->bind_result($num_payments);

	if (!$find_payment_stmt->execute())
		die("Error occurred checking if payment exists: $find_payment_stmt->error");
	$find_payment_stmt->fetch();

	$sql_conn->close();
	return ($num_payments == 1);
}

function existsTransaction($id, $payment_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$find_transaction_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM transactions WHERE id = ? AND payment_id = ?");
	$find_transaction_stmt->bind_param('ss', $id, $payment_id);
	$find_transaction_stmt->bind_result($num_transactions); // Should be either 0 or 1

	if (!$find_transaction_stmt->execute())
		die("Error occurred checking if transaction exists: $find_transaction_stmt->error");
	else if ($num_transactions > 1) // This is just a precautionary measure
		die("Error: The number of transactions for id `$id` & payment_id `$payment_id` is $num_transactions!");
	$find_transaction_stmt->fetch();

	$sql_conn->close();
	return ($num_transactions == 1);
}

function getTransactionStatus($id, $payment_id)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$get_transaction_status_stmt = $sql_conn->prepare("SELECT owed, paid, modifiers FROM transactions WHERE id = ? AND payment_id = ?");
	$get_transaction_status_stmt->bind_param('ss', $id, $payment_id);
	$get_transaction_status_stmt->bind_result($owed, $paid, $modifiers);

	if (!$get_transaction_status_stmt->execute())
		die("Error getting transaction status: $get_transaction_status_stmt->error");
	$get_transaction_status_stmt->fetch();

	$sql_conn->close();
	return array('owed' => $owed, 'paid' => $paid, 'modifiers' => $modifiers);
}

//TODO: review
function logTransactionEvent($id, $payment_id, $action, $comment = null)
{
	if (existsTransaction($id, $payment_id)) {
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
		$sql_conn = getDBConn();

		$log_event = "";
		$curr_date = getCurrentDateTime(true);

		if (!is_null($action)) {    // Action and maybe comment
			$transaction_status = getTransactionStatus($id, $payment_id);
			$quantity_owed = $transaction_status['owed'];
			$quantity_paid = $transaction_status['paid'];

			$log_event .= "<i>$curr_date</i> => <b>[$action]</b>: Owed = $quantity_owed, Paid = $quantity_paid";  // Action
			if (!is_null($comment))
				$log_event .= " | <b>[COMMENT]</b>: $comment"; // Comment
			$log_event .= "<br>";
		} else  // Comment only
			$log_event .= "<i>$curr_date</i> => <b>[COMMENT]:</b> $comment<br>";

		$log_action_stmt = $sql_conn->prepare("UPDATE transactions SET log = CONCAT(log, ?) WHERE id = ? AND payment_id = ?");
		$log_action_stmt->bind_param('sss', $log_event, $id, $payment_id);

		if (!$log_action_stmt->execute())
			die("Error occurred logging ID `$id` & Payment ID `$payment_id` with log `$log_event`: $log_action_stmt->error");

		$sql_conn->close();
		return true;
	}

	return false;
}

function setTransaction($id, $payment_id, $owed, $paid, $modifiers): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
	if (!existsPerson($id) || !existsPayment($payment_id))
		return false;

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	if (existsTransaction($id, $payment_id)) {
		// Fix null values
		$transaction_status = getTransactionStatus($id, $payment_id);

		$owed = $owed ?? $transaction_status['owed'];
		$paid = $paid ?? $transaction_status['paid'];
		$modifiers = $modifiers ?? $transaction_status['modifiers'];

		$update_transaction_stmt = $sql_conn->prepare("UPDATE transactions SET owed = ?, paid = ?, modifiers = ? WHERE id = ? AND payment_id = ?");
		/** @noinspection SpellCheckingInspection */
		$update_transaction_stmt->bind_param('iisss', $owed, $paid, $modifiers, $id, $payment_id);

		if (!$update_transaction_stmt->execute())
			die("Error updating transaction: $update_transaction_stmt->error");

		logTransactionEvent($id, $payment_id, 'UPDATE');
	} else {
		$owed = $owed ?? 0;
		$paid = $paid ?? 0;
		$modifiers = $modifiers ?? '';
		$log = '';

		$insert_transaction_stmt = $sql_conn->prepare("INSERT INTO transactions (id, payment_id, owed, paid, modifiers, log) VALUES (?, ?, ?, ?, ?, ?)");
		/** @noinspection SpellCheckingInspection */
		$insert_transaction_stmt->bind_param('ssiiss', $id, $payment_id, $owed, $paid, $modifiers, $log);

		if (!$insert_transaction_stmt->execute())
			die("Error inserting transaction: $insert_transaction_stmt->error");

		logTransactionEvent($id, $payment_id, 'INSERT');
	}

	$sql_conn->close();
	return true;
}

function archiveTransaction($id, $payment_id): bool
{
	if (existsTransaction($id, $payment_id)) {
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
		$sql_conn = getDBConn();

		logTransactionEvent($id, $payment_id, 'ARCHIVE');

		// Insert into archive
		$insert_transaction_stmt = $sql_conn->prepare(
			"INSERT INTO transactions_archive (id, payment_id, owed, paid, modifiers, log) SELECT id, payment_id, owed, paid, modifiers, log FROM transactions WHERE id = ? AND payment_id = ?");
		$insert_transaction_stmt->bind_param('ss', $id, $payment_id);

		if (!$insert_transaction_stmt->execute())
			die("Error inserting transaction into archive: $insert_transaction_stmt->error");

		// Delete from transactions
		$delete_transaction_stmt = $sql_conn->prepare("DELETE FROM transactions WHERE id = ? AND payment_id = ?");
		$delete_transaction_stmt->bind_param('ss', $id, $payment_id);

		if (!$delete_transaction_stmt->execute())
			die("Error deleting transaction: $delete_transaction_stmt->error");

		return true;
	}

	return false;
}

function isPaid($id, $payment_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$is_paid_stmt = $sql_conn->prepare("SELECT (owed <= paid) is_paid FROM transactions WHERE id = ? AND payment_id = ?");
	$is_paid_stmt->bind_param('ss', $id, $payment_id);
	$is_paid_stmt->bind_result($is_paid);

	if (!$is_paid_stmt->execute())
		die("Error occurred checking if payment is paid: $is_paid_stmt->error");
	$is_paid_stmt->fetch();

	$is_paid_stmt->close();
	return $is_paid ?? false;
}
