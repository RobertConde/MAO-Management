<?php

function createPayment($payment_id, $cost, $info): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$create_payment_stmt = $sql_conn->prepare(
		"INSERT INTO payment_details (payment_id, cost, info)
			   VALUES (?, ?, ?)");

	$create_payment_stmt->bind_param('sds', $payment_id, $cost, $info);

	return $create_payment_stmt->execute();
}

function updatePayment($payment_id, $cost, $info): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$update_payment_stmt = $sql_conn->prepare(
		"UPDATE payment_details SET cost = ?, info = ?
			   WHERE payment_id = ?");

	$update_payment_stmt->bind_param('dss', $cost, $info, $payment_id);

	return $update_payment_stmt->execute();
}

// Deletes corresponding payment_details and transactions
function deletePayment($payment_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	// Payment Details
	$delete_payment_details_stmt = $sql_conn->prepare(
		"DELETE FROM payment_details
			   WHERE payment_id = ?");

	$delete_payment_details_stmt->bind_param('s', $payment_id);

	$result_payment_details = $delete_payment_details_stmt->execute();

	// Transactions
	$delete_transactions_stmt = $sql_conn->prepare(
		"DELETE FROM transactions
			   WHERE payment_id = ?");

	$delete_transactions_stmt->bind_param('s', $payment_id);

	$result_transactions_details = $delete_transactions_stmt->execute();

	return ($result_payment_details && $result_transactions_details);
}