<?php

function createCompetition($competition_id, $payment_id, $competition_description): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$create_competition_stmt = $sql_conn->prepare(
		"INSERT INTO competitions (competition_id, payment_id, competition_description)
			   VALUES (?, ?, ?)");

	if ($payment_id == '')
		$payment_id = null;

	$create_competition_stmt->bind_param('sss', $competition_id, $payment_id, $competition_description);

	return $create_competition_stmt->execute();
}

function updateCompetition($competition_id, $payment_id, $competition_description): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$update_competition_stmt = $sql_conn->prepare(
		"UPDATE competitions SET payment_id = ?, competition_description = ?
			   WHERE competition_id = ?");

	if ($payment_id == '')
		$payment_id = null;

	$update_competition_stmt->bind_param('sss', $payment_id, $competition_description, $competition_id);

	return $update_competition_stmt->execute();
}

// Deletes corresponding competitions and transactions
function deleteCompetition($competition_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	// Competition
	$delete_competitions_stmt = $sql_conn->prepare(
		"DELETE FROM competitions
			   WHERE competition_id = ?");

	$delete_competitions_stmt->bind_param('s', $competition_id);

	$result_competitions = $delete_competitions_stmt->execute();

	// Competition Selections
	$delete_selections_stmt = $sql_conn->prepare(
		"DELETE FROM competition_approvals
			   WHERE competition_id = ?");

	$delete_selections_stmt->bind_param('s', $competition_id);

	$result_selections_details = $delete_selections_stmt->execute();

	// Competition Approvals
	$delete_approvals_stmt = $sql_conn->prepare(
		"DELETE FROM competition_approvals
			   WHERE competition_id = ?");

	$delete_approvals_stmt->bind_param('s', $competition_id);

	$result_approvals_details = $delete_approvals_stmt->execute();

	// Competition Forms
	$delete_forms_stmt = $sql_conn->prepare(
		"DELETE FROM competition_forms
			   WHERE competition_id = ?");

	$delete_forms_stmt->bind_param('s', $competition_id);

	$result_forms_details = $delete_forms_stmt->execute();

	return ($result_competitions && $result_selections_details && $result_approvals_details && $result_forms_details);
}