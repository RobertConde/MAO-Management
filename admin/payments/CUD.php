<?php

function createPayment($payment_id, $due_date, $price, $desc): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$create_payment_stmt = $sql_conn->prepare(
		"INSERT INTO payment_details (payment_id, due_date, price, description)
			   VALUES (?, ?, ?, ?)");

	$create_payment_stmt->bind_param('ssds', $payment_id, $due_date, $price, $desc);

	return $create_payment_stmt->execute();
}

function updatePayment($payment_id, $due_date, $price, $desc): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_payment_stmt = $sql_conn->prepare(
		"UPDATE payment_details SET due_date = ?, price = ?, description = ?
			   WHERE payment_id = ?");

	$update_payment_stmt->bind_param('sdss', $due_date, $price, $desc, $payment_id);

	return $update_payment_stmt->execute();
}

// Deletes corresponding payment_details and transactions
function deletePayment($payment_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	// Archive transactions
	$select_trans_IDs_stmt = $sql_conn->prepare("SELECT id FROM transactions WHERE payment_id = ?");
	$select_trans_IDs_stmt->bind_param('s', $payment_id);
	$select_trans_IDs_stmt->bind_result($archive_id);
	$execute_archive_trans = $select_trans_IDs_stmt->execute();

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";
	while ($select_trans_IDs_stmt->fetch())
		$execute_archive_trans &= archiveTransaction($archive_id, $payment_id);

	// Delete payment details
	$delete_pd_stmt = $sql_conn->prepare("DELETE FROM payment_details WHERE payment_id = ?");
	$delete_pd_stmt->bind_param('s', $payment_id);
	$execute_delete_pd = $delete_pd_stmt->execute();

	// Unset any competitions
	$unset_comps_stmt = $sql_conn->prepare("UPDATE competitions SET payment_id = NULL WHERE payment_id = ?");
	$unset_comps_stmt->bind_param('s', $payment_id);
	$execute_unset_comps = $unset_comps_stmt->execute();

	return ($execute_archive_trans && $execute_delete_pd && $execute_unset_comps);
}
