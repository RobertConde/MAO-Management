<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
$sql_conn = getDBConn();

if (isset($_REQUEST['term'])) {
	$query = "SELECT last_name, first_name, id as name FROM people WHERE (first_name LIKE ?) OR (last_name LIKE ?) OR (id LIKE ?) LIMIT 10";

	if ($stmt = $sql_conn->prepare($query)) {
		$stmt->bind_param('sss', $param_term, $param_term, $param_term);
		$stmt->bind_result($last_name, $first_name, $id);

		$param_term = $_REQUEST['term'] . '%';

		if ($stmt->execute()) {
			while (!is_null($stmt->fetch()))
				echo "<p>$last_name, $first_name [$id]</p>";
		} else
			echo "Error searching: $sql_conn->error";
	}

	$stmt->close();
}

$sql_conn->close();
