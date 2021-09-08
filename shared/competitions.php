<?php

function isSelected($comp, $id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	// Check if is already selected
	$find_selection_stmt = $sql_conn->prepare(
		"SELECT COUNT(*) FROM competition_selections
					WHERE id = ? AND competition_name = ?");

	$find_selection_stmt->bind_param('ss', $id, $comp);

	if (!$find_selection_stmt->execute())
		return false;

	$find_selection_stmt->bind_result($num_rows);

	$find_selection_stmt->fetch();

	return ($num_rows > 0);
}

function toggleSelection($comp, $id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();    

	if (!isSelected($comp, $id)) {
		$insert_selection_stmt = $sql_conn->prepare("INSERT INTO competition_selections(id, competition_name) VALUES (?, ?)");

		$insert_selection_stmt->bind_param('ss', $id, $comp);

		if (!$insert_selection_stmt->execute())
			return false;
	} else {
		$delete_selection_stmt = $sql_conn->prepare(
			"DELETE FROM competition_selections
						WHERE id = ? AND competition_name = ?");

		$delete_selection_stmt->bind_param('ss', $id, $comp);

		if (!$delete_selection_stmt->execute())
			return false;
	}

	return true;
}

function inComp($comp, $id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$in_comp_stmt = $sql_conn->prepare(
		"SELECT COUNT(id) FROM competition_data
					WHERE id = ? AND competition_name = ?");
	$in_comp_stmt->bind_param('ss', $id, $comp);
	$in_comp_stmt->bind_result($in_comp);
	$in_comp_stmt->execute();

	$in_comp_stmt->fetch();
	return $in_comp;
}

function addToComp($comp, $id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	if (!inComp($comp, $id)) {
		$add_person_stmt = $sql_conn->prepare("INSERT INTO competition_data (competition_name, id) VALUES (? , ?)");
		$add_person_stmt->bind_param('ss', $comp, $id);

		return $add_person_stmt->execute();
	}

	return false;
}

function removeFromComp($comp, $id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$delete_data_stmt = $sql_conn->prepare(
		"DELETE FROM competition_data
					WHERE id = ? AND competition_name = ?");
	$delete_data_stmt->bind_param('ss', $id, $comp);

	return $delete_data_stmt->execute();
}

function updateCompData($comp, $id, $approved, $forms, $bus, $room): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_data_stmt = $sql_conn->prepare(
		"UPDATE competition_data
					SET forms = ?, bus = ?, room = ?
					WHERE id = ? AND competition_name = ?");
	$update_data_stmt->bind_param('iisss', $forms, $bus, $room, $id, $comp);

	return $update_data_stmt->execute();
}



//function isApproved($id, $comp_id): bool
//{
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
//	$sql_conn = getDBConn();
//
//	// Check if is already approved
//	$find_approval_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM competition_approvals WHERE id = ? AND competition_name = ?");
//
//	$find_approval_stmt->bind_param('ss', $id, $comp_id);
//
//	if (!$find_approval_stmt->execute())
//		return false;
//
//	$find_approval_stmt->bind_result($num_rows);
//
//	$find_approval_stmt->fetch();
//
//	return ($num_rows > 0);
//}

//function toggleApproved($id, $comp_id): bool
//{
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
//	$sql_conn = getDBConn();
//
//	if (!isApproved($id, $comp_id)) {
//		$insert_approval_stmt = $sql_conn->prepare("INSERT INTO competition_approvals(id, competition_name) VALUES (?, ?)");
//
//		$insert_approval_stmt->bind_param('ss', $id, $comp_id);
//
//		if (!$insert_approval_stmt->execute())
//			return false;
//	} else {
//		$delete_approval_stmt = $sql_conn->prepare("DELETE FROM competition_approvals WHERE id = ? AND competition_name = ?");
//
//		$delete_approval_stmt->bind_param('ss', $id, $comp_id);
//
//		if (!$delete_approval_stmt->execute())
//			return false;
//	}
//
//	return true;
//}

//function isCompetitionPaid($id, $comp_id): bool
//{
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
//	$sql_conn = getDBConn();
//
//	// Find payment_id for competition
//	$find_payment_stmt = $sql_conn->prepare("SELECT payment_id FROM competitions WHERE competition_name = ?");
//
//	$find_payment_stmt->bind_param('s', $comp_id);
//
//	if (!$find_payment_stmt->execute())
//		return false;
//
//	$find_payment_stmt->bind_result($payment_id);
//
//	$find_payment_stmt->fetch();
//
//	if (is_null($payment_id))
//		return true;
//
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";
//
//	return isPaid($id, $payment_id);
//}

//function areFormsCollected($id, $comp_id): bool
//{
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
//	$sql_conn = getDBConn();
//
//	// Check if already turned in
//	$find_form_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM competition_forms WHERE id = ? AND competition_name = ?");
//
//	$find_form_stmt->bind_param('ss', $id, $comp_id);
//
//	if (!$find_form_stmt->execute())
//		return false;
//
//	$find_form_stmt->bind_result($num_rows);
//
//	$find_form_stmt->fetch();
//
//	return ($num_rows > 0);
//}

//function toggleFormStatus($id, $comp_id): bool
//{
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
//	$sql_conn = getDBConn();
//
//	if (!areFormsCollected($id, $comp_id)) {
//		$set_form_status_true_stmt = $sql_conn->prepare("INSERT INTO competition_forms(id, competition_name) VALUES (?, ?)");
//
//		$set_form_status_true_stmt->bind_param('ss', $id, $comp_id);
//
//		if (!$set_form_status_true_stmt->execute())
//			return false;
//	} else {
//		$delete_form_status_stmt = $sql_conn->prepare("DELETE FROM competition_forms WHERE id = ? AND competition_name = ?");
//
//		$delete_form_status_stmt->bind_param('ss', $id, $comp_id);
//
//		if (!$delete_form_status_stmt->execute())
//			return false;
//	}
//
//	return true;
//}
