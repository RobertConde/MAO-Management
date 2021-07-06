<?php

function checkPerms($permmin = 1) : bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/require/sql.php";

	if (!isset($_SESSION['id'])) {
		die("<p style=\"color:red;\"><b>Redirecting:</b> <i>You are not logged in!</i></p>\n" .
			"<meta http-equiv=\"refresh\" content=\"2; url=https://" . $_SERVER['HTTP_HOST'] . "/user/login.php\" />");
	} elseif (getDetail('people', 'perms', $_SESSION['id']) < $permmin)
		die("<p style=\"color:red;\"><b>You do not have the required permissions!</b></p>\n");

	return true;
}

function checkCompareRank($greater_id, $lower_id, $equal = false): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/require/sql.php";

	$greater_perms = getDetail('people', 'perms', $greater_id);
	$greater_rank = getRank($greater_id);

	$lower_perms = getDetail('people', 'perms', $lower_id);
	$lower_rank = getRank($lower_id);

	if (is_null($greater_perms) || is_null($lower_perms))
		die("<p style=\"color:red;\">Account with ID = $lower_id does not exist!</p>\n");

	return ($greater_rank > $lower_rank) ^ ($equal && $greater_rank == $lower_rank);
}
