<?php

function isPaid($id, $payment_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	// Check if already if already is paid
	$find_payment_statement = $sql_conn->prepare("SELECT COUNT(*) FROM transactions WHERE id = ? AND payment_id = ?");

	$find_payment_statement->bind_param('ss', $id, $payment_id);

	if (!$find_payment_statement->execute())
		die("Error occurred checking if payment was made: $find_payment_statement->error.");

	$find_payment_statement->bind_result($num_rows);

	$find_payment_statement->fetch();

	return ($num_rows > 0);
}

function toggleTransaction($id, $payment_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	// If not already set as 'paid', then insert transaction (indicates 'paid'); else (currently indicating 'paid'), delete transaction
	if (!isPaid($id, $payment_id)) {
		$insert_transaction_statement = $sql_conn->prepare("INSERT INTO transactions(id, payment_id) VALUES (?, ?)");

		$insert_transaction_statement->bind_param('ss', $id, $payment_id);

		if (!$insert_transaction_statement->execute())
			return false;
	} else {
		$delete_transaction_statement = $sql_conn->prepare("DELETE FROM transactions WHERE id = ? AND payment_id = ?");

		$delete_transaction_statement->bind_param('ss', $id, $payment_id);

		if (!$delete_transaction_statement->execute())
			return false;
	}

	return true;
}
