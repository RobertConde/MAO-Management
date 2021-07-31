<?php

function isSelected($id, $comp_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();
//	echo "ID:$id;COMP_ID:$comp_id<br>";

	// Check if already if already selected
	$find_selection_statement = $sql_conn->prepare("SELECT COUNT(*) FROM competition_selections WHERE id = ? AND competition_id = ?");

	$find_selection_statement->bind_param('ss', $id, $comp_id);

	if (!$find_selection_statement->execute())
		return false;
//		die("Error occurred checking if competition selection was made: $find_selection_statement->error.");

	$find_selection_statement->bind_result($num_rows);

	$find_selection_statement->fetch();

	return ($num_rows > 0);
}

function toggleSelected($id, $comp_id):bool
{
//	echo "ID:$id;COMP_ID:$comp_id<br>";
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	if (!isSelected($id, $comp_id)) {
		$insert_selection_statement = $sql_conn->prepare("INSERT INTO competition_selections(id, competition_id) VALUES (?, ?)");

		$insert_selection_statement->bind_param('ss', $id, $comp_id);

		if (!$insert_selection_statement->execute())
			return false;
//			die("Error occurred inserting competition selection: $insert_transaction_statement->error.");
	} else {
//		echo "222";
		$delete_selection_statement = $sql_conn->prepare("DELETE FROM competition_selections WHERE id = ? AND competition_id = ?");

		$delete_selection_statement->bind_param('ss', $id, $comp_id);

		if (!$delete_selection_statement->execute())
			return false;
//			die("Error occurred deleting competition selection: $delete_transaction_statement->error.");
	}

	return true;
}

function formCollected($id, $comp_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();

	// Check if already if already turned in
	$find_form_statement = $sql_conn->prepare("SELECT COUNT(*) FROM competition_forms WHERE id = ? AND competition_id = ?");

	$find_form_statement->bind_param('ss', $id, $comp_id);

	if (!$find_form_statement->execute())
		return false;

	$find_form_statement->bind_result($num_rows);

	$find_form_statement->fetch();

	return ($num_rows > 0);
}

function toggleFormStatus($id, $comp_id):bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	if (!formCollected($id, $comp_id)) {
		$set_form_status_true_statement = $sql_conn->prepare("INSERT INTO competition_forms(id, competition_id) VALUES (?, ?)");

		$set_form_status_true_statement->bind_param('ss', $id, $comp_id);

		if (!$set_form_status_true_statement->execute())
			return false;
	} else {
		$delete_form_status_statement = $sql_conn->prepare("DELETE FROM competition_forms WHERE id = ? AND competition_id = ?");

		$delete_form_status_statement->bind_param('ss', $id, $comp_id);

		if (!$delete_form_status_statement->execute())
			return false;
	}

	return true;
}
