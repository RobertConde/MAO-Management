<?php

function getNow() : DateTime
{
	return new DateTime('now', new DateTimeZone('EDT'));
}

function addDays($datetime, $days) : DateTime
{
    return $datetime->add(new DateInterval('P' . $days . 'D'));
}

function formatDateTime($datetime, $time = false) : string
{
	return $datetime->format('l, M-d-Y ' . ($time ? 'H:i:s ' : '') . 'T');
}

function formatDateTimeSQL($datetime) {
	return $datetime->format('Y-m-d H:i:s');
}


//echo formatDateTime(addDays(getNow(), 7)) . "<br>";

//require_once $_SERVER['DOCUMENT_ROOT'] . "/require/sql.php";
//
//$sql_conn = getDBConn();    // Get DB connection
//
//if ($result = $sql_conn->query("SELECT time_paid FROM transactions WHERE id = 0264171\n")) {
//	if ($result->num_rows == 0)
//		return null;
//	else if ($result->num_rows == 1)
//		echo formatSQL(new DateTime($result->fetch_row()[0], new DateTimeZone('EST')));
//}


function createEvent($title, $start, $end, $description, $location) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/require/sql.php";
	$sql_conn = getDBConn();

	$create_stmt = $sql_conn->prepare("INSERT INTO events (start, end, title, description, location) VALUES (?, ?, ?, ?, ?)");

	$create_stmt->bind_param('sssss', $title, $start, $end, $description, $location);

	return $create_stmt->execute();
}

function deleteEvent($event_id) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/require/sql.php";
	$sql_conn = getDBConn();

	$delete_stmt = $sql_conn->prepare("DELETE FROM events WHERE event_id = ?");

	$delete_stmt->bind_param('i', $event_id);

	return $delete_stmt->execute();
}

function updateEvent($event_id, $title, $start, $end, $description, $location) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/require/sql.php";
	$sql_conn = getDBConn();

	$create_stmt = $sql_conn->prepare("UPDATE events SET start = ?, end = ?, title = ?, description = ?, location = ? WHERE event_id = ?");

	$create_stmt->bind_param('sssssi', $title, $start, $end, $description, $location, $event_id);

	return $create_stmt->execute();
}
