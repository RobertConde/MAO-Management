<?php

function cycleLoginCode($id) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/sql/standardSQL.php";
	$sql_conn = getDBConn();    // Get DB connection

	$cycle_statement = $sql_conn->prepare("INSERT INTO login(id, code) VALUES (?, ?) ON DUPLICATE KEY UPDATE code = ?, time_created = NOW()");

	$new_code = substr(md5(rand()), 0, 6);

	$cycle_statement->bind_param("sss", $id, $new_code, $new_code);

//	if (!$cycle_statement->execute())
//		die("Error occurred updating login code: $cycle_statement->error.");

	return $cycle_statement->execute();
}

function cycleAndEmailLoginCode($id) {
	if (cycleLoginCode($id)) {
		require_once $_SERVER['DOCUMENT_ROOT'] . "/PHPMailer-6.4.1/sendEmail.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/people/helpers/peopleFunctions.php";

		$email_sent = sendEmail(
			getDetail('people', 'email', $id),
			"Login Code (" . gmdate('m/d/Y H:i:s') . " UTC)",
			"<b>Login Code:</b> <tt>" . getDetail('login', 'code', $id) . "</tt>");

		return $email_sent;
	}

	return false;
}