<?php

function requirePerms($permmin = 1) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/sql/standardSQL.php";

	if (getDetail('people', 'perms', $_SESSION['id']) <  $permmin)
		die("You do not have the required permissions!");
}
