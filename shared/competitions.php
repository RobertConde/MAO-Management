<?php

const DIVISIONS = array(
	'Not A Student',
	'Algebra 1',
	'Geometry',
	'Algebra 2',
	'Precalculus',
	'Calculus',
	'Statistics');

function existsComp($comp)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$find_comp_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM competitions WHERE competition_name = ?");
	$find_comp_stmt->bind_param('s', $comp);
	$find_comp_stmt->bind_result($num_comps);

	if (!$find_comp_stmt->execute())
		die("Error occurred determining if a competition exists: $find_comp_stmt->error");
	$find_comp_stmt->fetch();

	return ($num_comps == 1);
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

		if ($add_person_stmt->execute()) {
			$payment_id = getAssociatedCompInfo($comp, 'payment_id');

			if (!is_null($payment_id)) {
				require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

				return setTransaction($id, $payment_id, 1, null, null);
			}

			return true;
		} else
			return false;
	}

	return false;
}

function removeFromComp($comp, $id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$payment_id = getAssociatedCompInfo($comp, 'payment_id');
	if (!is_null($payment_id)) {
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

		if (isPaid($id, $payment_id))
			setTransaction($id, $payment_id, 0, null, null);
		else
			archiveTransaction($id, $payment_id);
	}

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

function getAssociatedCompInfo($comp, $info_col)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	// Find info for competition
	// TODO: enumerate possibilities
	$find_payment_stmt = $sql_conn->prepare("SELECT $info_col FROM competitions WHERE competition_name = ?");

	$find_payment_stmt->bind_param('s', $comp);
	$find_payment_stmt->bind_result($info);

	if (!$find_payment_stmt->execute())
		return false;
	$find_payment_stmt->fetch();

	return $info;
}


function isSelected($comp, $id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	// Check if is already selected
	$find_selection_stmt = $sql_conn->prepare(
		"SELECT COUNT(*) FROM competition_selections
					WHERE id = ? AND competition_name = ?");
	$find_selection_stmt->bind_param('ss', $id, $comp);
	$find_selection_stmt->bind_result($num_selections);

	if (!$find_selection_stmt->execute())
		die("Error occurred determining if a selection exists: $find_selection_stmt->error");
	$find_selection_stmt->fetch();

	return ($num_selections == 1);
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

function getCompCount($comp, $include_not_students = true)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$registered_stmt = $sql_conn->prepare(
		"SELECT COUNT(*) FROM competition_data cd
					INNER JOIN competitor_info ci ON ci.id = cd.id
	                WHERE cd.competition_name = ?" . (!$include_not_students ? ' AND ci.division != 0' : ''));
	$registered_stmt->bind_param('s', $comp);
	$registered_stmt->bind_result($registered);
	$registered_stmt->execute();

	$registered_stmt->fetch();

	return $registered;
}

function isCompPaid($id, $comp): ?bool
{
	$payment_id = getAssociatedCompInfo($comp, 'payment_id');

	if (is_null($payment_id))
		return null;

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	return isPaid($id, $payment_id);
}

function areFormsCollected($id, $comp): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	// Check if already turned in
	$find_form_stmt = $sql_conn->prepare("SELECT forms FROM competition_data WHERE id = ? AND competition_name = ?");
	$find_form_stmt->bind_param('ss', $id, $comp);
	$find_form_stmt->bind_result($forms);

	if (!$find_form_stmt->execute())
		die("Error occurred checking if forms are collected: $find_form_stmt->error");
	$find_form_stmt->fetch();

	$sql_conn->close();
	return ($forms ?? false);
}


function getBus($id, $comp)
{
	if (inComp($comp, $id)) {
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
		$sql_conn = getDBConn();

		$get_bus_stmt = $sql_conn->prepare("SELECT bus FROM competition_data WHERE id = ? AND competition_name = ?");
		$get_bus_stmt->bind_param('ss', $id, $comp);
		$get_bus_stmt->bind_result($bus);

		if (!$get_bus_stmt->execute())
			die("Error occurred getting bus field: $get_bus_stmt->error");
		$get_bus_stmt->fetch();

		return $bus;
	}

	return null;
}

function getRoom($id, $comp)
{
	if (inComp($comp, $id)) {
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
		$sql_conn = getDBConn();

		$get_room_stmt = $sql_conn->prepare("SELECT room FROM competition_data WHERE id = ? AND competition_name = ?");
		$get_room_stmt->bind_param('ss', $id, $comp);
		$get_room_stmt->bind_result($room);

		if (!$get_room_stmt->execute())
			die("Error occurred getting room field: $get_room_stmt->error");
		$get_room_stmt->fetch();

		return $room;
	}

	return null;
}

function getBusCount($comp, $bus): int
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$bus_counts_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM competition_data cd
                                                INNER JOIN competitor_info ci ON ci.id = cd.id
                                                WHERE competition_name = ? AND cd.bus = ?  AND ci.division != 0");
	$bus_counts_stmt->bind_param('si', $comp, $bus);
	$bus_counts_stmt->bind_result($bus_count);
	$bus_counts_stmt->execute();
	$bus_counts_stmt->fetch();

	return ($bus_count ?? 0);
}
