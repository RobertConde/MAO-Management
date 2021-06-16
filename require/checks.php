<?php

function checkPerms($permmin = 1)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "require/sql.php";

	if (!isset($_SESSION['id']))
		die("<p style=\"color:red;\">You are not logged in!</p>\n");
	elseif (getDetail('people', 'perms', $_SESSION['id']) < $permmin)
		die("<p style=\"color:red;\">You do not have the required permissions!</p>\n");
}

function checkRankGreater($greater_id, $lower_id) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "require/sql.php";

	$greater_perms = getDetail('people', 'perms', $greater_id);
	$greater_rank = getRank($greater_id);

	$lower_perms = getDetail('people', 'perms', $lower_id);
	$lower_rank = getRank($lower_id);

	if (is_null($lower_perms))
		die("<p style=\"color:red;\">Account with ID = $lower_id does not exist!</p>\n");

	return ($greater_rank > $lower_rank);
}
