<?php

function registerAccount($id, $lname, $fname, $grade, $email, $phone, $division): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "require/sql.php";
	$sql_conn = getDBConn();    // Get DB connection

	$register_stmt = $sql_conn->prepare(
		"INSERT INTO people (id, lname, fname, grade, email, phone, division)
			   VALUES (?, ?, ?, ?, ?, ?, ?)");

	$register_stmt->bind_param("sssissi", $id, $lname, $fname, $grade, $email, $phone, $division);

//	if (!)
//		die("Error occurred while registering account: " . $sql_conn->error);

	return $register_stmt->execute();
}

function sendLoginCode($id, $new_code) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "require/sql.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "require/email.php";

	return sendEmail(
		getDetail('people', 'email', $id),
		"Login Code (" . gmdate('m/d/Y H:i:s') . " UTC)",
		"<b>Login Code:</b> <tt>" . getDetail('login', 'code', $id) . "</tt>");
}

function cycleLoginCode($id) : bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "require/sql.php";
	$sql_conn = getDBConn();    // Get DB connection

	$cycle_statement = $sql_conn->prepare("INSERT INTO login(id, code) VALUES (?, ?) ON DUPLICATE KEY UPDATE code = ?, time_created = NOW()");

	$new_code = substr(md5(rand()), 0, 6);

	$cycle_statement->bind_param("sss", $id, $new_code, $new_code);

//	if (!$cycle_statement->execute())
//		die("Error occurred updating login code: $cycle_statement->error.");

	if ($cycle_statement->execute())
		return sendLoginCode($id, $new_code);

	return false;
}

function updateLoginTime($id) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "require/sql.php";
	$sql_conn = getDBConn();    // Get DB connection

	$update_stmt = $sql_conn->prepare("UPDATE login SET time_last_login = NOW() WHERE id = ?");

	$update_stmt->bind_param("s", $id);

	return $update_stmt->execute();
}