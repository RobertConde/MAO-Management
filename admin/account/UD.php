<?php



function deleteAccount($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	// Account Information
	$delete_account_info_stmt = $sql_conn->prepare(
		"DELETE FROM people
			   WHERE id = ?");

	$delete_account_info_stmt->bind_param('s', $id);

	$result_account_info = $delete_account_info_stmt->execute();

	// Login Code
	$delete_login_stmt = $sql_conn->prepare(
		"DELETE FROM login
			   WHERE id = ?");

	$delete_login_stmt->bind_param('s', $id);

	$result_login = $delete_login_stmt->execute();

	// Transactions
	$delete_transactions_stmt = $sql_conn->prepare(
		"DELETE FROM transactions
			   WHERE id = ?");

	$delete_transactions_stmt->bind_param('s', $id);

	$result_transactions_info = $delete_transactions_stmt->execute();

	return ($result_account_info && $result_login && $result_transactions_info);
}