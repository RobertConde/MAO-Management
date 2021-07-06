<?php

function getDBConn() {
	$sql_config = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config.ini", true)['sql'];

	$sql_conn = new mysqli($sql_config['hostname'], $sql_config['username'], $sql_config['password'], $sql_config['database']);

	if ($sql_conn->connect_errno)   // If there was a connection error, terminate execution
		die("Error occurred connection to MySQL database: " . $sql_conn->connect_error);

	return $sql_conn;
}

function getDetail($table, $col, $id) {
	$sql_conn = getDBConn();    // Get DB connection

	if ($result = $sql_conn->query("SELECT $col FROM $table WHERE id = $id\n")) {
		if ($result->num_rows == 0)
			return null;
		else if ($result->num_rows == 1)
			return $result->fetch_row()[0];
		else
			die("<p style=\"color:red;\">Get detail function fetched a non-singular result: num_rows = $result->num_rows.</p>\n");
	} else
		die("<p style=\"color:red;\">Get detail function occurred an error upon query!</p>\n");
}

function getRank($id) {
	$perms = getDetail('people', 'perms', $id);

	if ($perms < 1)
		return -1;

	return floor(log10($perms));
}
