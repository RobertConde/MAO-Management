<?php

function isSelected($id, $comp_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();
//	echo "ID:$id;COMP_ID:$comp_id<br>";

	// Check if is already selected
	$find_selection_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM competition_selections WHERE id = ? AND competition_id = ?");

	$find_selection_stmt->bind_param('ss', $id, $comp_id);

	if (!$find_selection_stmt->execute())
		return false;
//		die("Error occurred checking if competition selection was made: $find_selection_stmt->error.");

	$find_selection_stmt->bind_result($num_rows);

	$find_selection_stmt->fetch();

	return ($num_rows > 0);
}

function toggleSelection($id, $comp_id):bool
{
//	echo "ID:$id;COMP_ID:$comp_id<br>";
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	if (!isSelected($id, $comp_id)) {
		$insert_selection_stmt = $sql_conn->prepare("INSERT INTO competition_selections(id, competition_id) VALUES (?, ?)");

		$insert_selection_stmt->bind_param('ss', $id, $comp_id);

		if (!$insert_selection_stmt->execute())
			return false;
//			die("Error occurred inserting competition selection: $insert_transaction_stmt->error.");
	} else {
//		echo "222";
		$delete_selection_stmt = $sql_conn->prepare("DELETE FROM competition_selections WHERE id = ? AND competition_id = ?");

		$delete_selection_stmt->bind_param('ss', $id, $comp_id);

		if (!$delete_selection_stmt->execute())
			return false;
//			die("Error occurred deleting competition selection: $delete_transaction_stmt->error.");
	}

	return true;
}

function isApproved($id, $comp_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();

	// Check if is already approved
	$find_approval_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM competition_approvals WHERE id = ? AND competition_id = ?");

	$find_approval_stmt->bind_param('ss', $id, $comp_id);

	if (!$find_approval_stmt->execute())
		return false;

	$find_approval_stmt->bind_result($num_rows);

	$find_approval_stmt->fetch();

	return ($num_rows > 0);
}

function toggleApproved($id, $comp_id):bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	if (!isApproved($id, $comp_id)) {
		$insert_approval_stmt = $sql_conn->prepare("INSERT INTO competition_approvals(id, competition_id) VALUES (?, ?)");

		$insert_approval_stmt->bind_param('ss', $id, $comp_id);

		if (!$insert_approval_stmt->execute())
			return false;
	} else {
		$delete_approval_stmt = $sql_conn->prepare("DELETE FROM competition_approvals WHERE id = ? AND competition_id = ?");

		$delete_approval_stmt->bind_param('ss', $id, $comp_id);

		if (!$delete_approval_stmt->execute())
			return false;
	}

	return true;
}

function isCompetitionPaid($id, $comp_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();

	// Find payment_id for competition
	$find_payment_stmt = $sql_conn->prepare("SELECT payment_id FROM competitions WHERE competition_id = ?");

	$find_payment_stmt->bind_param('s', $comp_id);

	if (!$find_payment_stmt->execute())
		return false;

	$find_payment_stmt->bind_result($payment_id);

	$find_payment_stmt->fetch();

	if (is_null($payment_id))
		return true;

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	return isPaid($id, $payment_id);
}

function areFormsCollected($id, $comp_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();

	// Check if already turned in
	$find_form_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM competition_forms WHERE id = ? AND competition_id = ?");

	$find_form_stmt->bind_param('ss', $id, $comp_id);

	if (!$find_form_stmt->execute())
		return false;

	$find_form_stmt->bind_result($num_rows);

	$find_form_stmt->fetch();

	return ($num_rows > 0);
}

function toggleFormStatus($id, $comp_id):bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	if (!areFormsCollected($id, $comp_id)) {
		$set_form_status_true_stmt = $sql_conn->prepare("INSERT INTO competition_forms(id, competition_id) VALUES (?, ?)");

		$set_form_status_true_stmt->bind_param('ss', $id, $comp_id);

		if (!$set_form_status_true_stmt->execute())
			return false;
	} else {
		$delete_form_status_stmt = $sql_conn->prepare("DELETE FROM competition_forms WHERE id = ? AND competition_id = ?");

		$delete_form_status_stmt->bind_param('ss', $id, $comp_id);

		if (!$delete_form_status_stmt->execute())
			return false;
	}

	return true;
}
