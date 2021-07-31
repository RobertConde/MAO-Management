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
	$result_transactions = $delete_transactions_stmt->execute();

	// Competition Selections
	$delete_comp_selections_stmt = $sql_conn->prepare(
		"DELETE FROM competition_selections
			   WHERE id = ?");

	$delete_comp_selections_stmt->bind_param('s', $id);
	$result_comp_selections = $delete_comp_selections_stmt->execute();

	// Competition Forms
	$delete_comp_forms_stmt = $sql_conn->prepare(
		"DELETE FROM competition_forms
			   WHERE id = ?");

	$delete_comp_forms_stmt->bind_param('s', $id);
	$result_comp_forms = $delete_comp_forms_stmt->execute();

	// Logout if is current user
	if ($_SESSION['id'] == $id)
		session_destroy();

	return ($result_account_info && $result_login
		&& $result_transactions
		&& $result_comp_selections && $result_comp_forms);
}