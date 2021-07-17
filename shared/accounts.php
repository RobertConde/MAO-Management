<?php

function registerAccount($id, $fname, $lname, $grade, $email, $phone, $division): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$register_stmt = $sql_conn->prepare(
		"INSERT INTO people (id, fname, lname, grade, email, phone, division)
			   VALUES (?, ?, ?, ?, ?, ?, ?)");

	$register_stmt->bind_param('sssissi', $id, $fname, $lname, $grade, $email, $phone, $division);

	return $register_stmt->execute();
}

function updateCycleTime($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$update_time_stmt = $sql_conn->prepare("UPDATE login SET time_cycled = NOW() WHERE id = ?");

	$update_time_stmt->bind_param('s', $id);

	return $update_time_stmt->execute();
}

function sendLoginCodeEmail($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/email.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

	return sendEmail(
		getAccountDetail('people', 'email', $id),
		"MAO - Login Code",
		"<b>Account ID#:</b> <code>$id</code><br><b>Login Code:</b> <code>" . getAccountDetail('login', 'code', $id) . "</code>");
}

function cycleLoginCode($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$cycle_statement = $sql_conn->prepare("INSERT INTO login(id, code) VALUES (?, ?) ON DUPLICATE KEY UPDATE code = ?");

	$new_code = substr(md5(rand()), 0, 6);

	$cycle_statement->bind_param('sss', $id, $new_code, $new_code);

	return updateCycleTime($id) && $cycle_statement->execute() && sendLoginCodeEmail($id);
}

function updateLoginTime($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$update_time_stmt = $sql_conn->prepare("UPDATE login SET time_last_login = NOW() WHERE id = ?");

	$update_time_stmt->bind_param('s', $id);

	return $update_time_stmt->execute();
}

function updateUpdateTime($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$update_time_stmt = $sql_conn->prepare("UPDATE people SET time_updated = NOW() WHERE id = ?");

	$update_time_stmt->bind_param('s', $id);

	return $update_time_stmt->execute();
}

function sendUpdateEmail($id, $updater_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/email.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

	return sendEmail(
		getAccountDetail('people', 'email', $id),
		"MAO - Account Updated",
		"<b>Account ID#:</b> <code>$id</code><br><b>Updated By (ID):</b> <code>$updater_id</code>");
}

function updateAccount($id, $fname, $lname, $grade, $email, $phone, $division, $updater_id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$update_stmt = $sql_conn->prepare("UPDATE people
		SET fname = ?, lname = ?, grade = ?, email = ?, phone = ?, division = ?
		WHERE id = ?");

	$update_stmt->bind_param('ssissis', $fname, $lname, $grade, $email, $phone, $division, $id);

	return updateUpdateTime($id) && $update_stmt->execute() && sendUpdateEmail($id, $updater_id);
}

function getAccountDetail($table, $col, $id) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";

	return getDetail($table, $col, 'id', $id);
}

function getRank($id) {
	$perms = getAccountDetail('people', 'perms', $id);

	if ($perms < 1)
		return -1;

	return floor(log10($perms));
}
