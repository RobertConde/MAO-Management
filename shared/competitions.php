<?php

const DIVISIONS = array(
	'Not A Student',
	'Algebra I',
	'Geometry',
	'Algebra 2',
	'Precalculus',
	'Calculus',
	'Statistics');

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

function numUnaddedSelections($comp)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$selections_stmt = $sql_conn->prepare(
		"SELECT COUNT(*) FROM competition_selections cs
                        WHERE cs.competition_name = ? AND
                              NOT EXISTS(SELECT NULL FROM competition_data cd
                                            WHERE cd.competition_name = cs.competition_name AND 
                                                  cd.id = cs.id)");
	$selections_stmt->bind_param('s', $comp);
	$selections_stmt->bind_result($unadded);
	$selections_stmt->execute();

	$selections_stmt->fetch();

	return $unadded;
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

function updateCompData($comp, $id, $forms, $bus, $room): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_data_stmt = $sql_conn->prepare(
		"UPDATE competition_data
					SET forms = ?, bus = ?, room = ?
					WHERE id = ? AND competition_name = ?");
	/** @noinspection SpellCheckingInspection */
	$update_data_stmt->bind_param('iisss', $forms, $bus, $room, $id, $comp);

	return $update_data_stmt->execute();
}

function numRegisteredForComp($comp)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$registered_stmt = $sql_conn->prepare(
		"SELECT COUNT(*) FROM competition_data cd
                        WHERE cd.competition_name = ?");
	$registered_stmt->bind_param('s', $comp);
	$registered_stmt->bind_result($registered);
	$registered_stmt->execute();

	$registered_stmt->fetch();

	return $registered;
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

function isCompPaid($id, $comp_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	// Find payment_id for competition
	$find_payment_stmt = $sql_conn->prepare("SELECT payment_id FROM competitions WHERE competition_name = ?");

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
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	// Check if already turned in
	$find_form_stmt = $sql_conn->prepare("SELECT forms FROM competition_data WHERE id = ? AND competition_name = ?");

	$find_form_stmt->bind_param('ss', $id, $comp_id);

	if (!$find_form_stmt->execute())
		return false;

	$find_form_stmt->bind_result($forms);

	$find_form_stmt->fetch();

	return ($forms ?? false);
}

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
