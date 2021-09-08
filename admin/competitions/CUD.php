<?php

function createCompetition($competition_name, $payment_id, $competition_description): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$create_competition_stmt = $sql_conn->prepare(
		"INSERT INTO competitions (competition_name, payment_id, competition_description)
			   VALUES (?, ?, ?)");

	if ($payment_id == '')
		$payment_id = null;

	$create_competition_stmt->bind_param('sss', $competition_name, $payment_id, $competition_description);

	return $create_competition_stmt->execute();
}

function updateCompetition($competition_name, $payment_id, $competition_description): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_competition_stmt = $sql_conn->prepare(
		"UPDATE competitions SET payment_id = ?, competition_description = ?
			   WHERE competition_name = ?");

	if ($payment_id == '')
		$payment_id = null;

	$update_competition_stmt->bind_param('sss', $payment_id, $competition_description, $competition_name);

	return $update_competition_stmt->execute();
}

// Deletes corresponding competitions and transactions
function deleteCompetition($competition_name): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	// Competition
	$delete_comp_stmt = $sql_conn->prepare(
		"DELETE FROM competitions
			   WHERE competition_name = ?");

	$delete_comp_stmt->bind_param('s', $competition_name);

	$result_comp = $delete_comp_stmt->execute();

	// Competition Data
	$delete_comp_data_stmt = $sql_conn->prepare(
		"DELETE FROM competition_data
			   WHERE competition_name = ?");

	$delete_comp_data_stmt->bind_param('s', $competition_name);

	$result_comp_data = $delete_comp_data_stmt->execute();

	// Competition Selections
	$delete_comp_selections_stmt = $sql_conn->prepare(
		"DELETE FROM competition_selections
			   WHERE competition_name = ?");

	$delete_comp_selections_stmt->bind_param('s', $competition_name);

	$result_comp_selections= $delete_comp_data_stmt->execute();

	return ($result_comp && $result_comp_data && $result_comp_selections);
}
