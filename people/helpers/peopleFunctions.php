<?php

function registerAccount($id, $lname, $fname, $grade, $email, $phone, $division): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/sql/standardSQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$register_stmt = $sql_conn->prepare(
		"INSERT INTO people (id, lname, fname, grade, email, phone, division)
			   VALUES (?, ?, ?, ?, ?, ?, ?)");

	$register_stmt->bind_param("sssissi", $id, $lname, $fname, $grade, $email, $phone, $division);

//	if (!)
//		die("Error occurred while registering account: " . $sql_conn->error);

	return $register_stmt->execute();
}
