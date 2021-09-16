<?php
const STUDENT = 1;
const OFFICER = 10;
const ADMIN = 100;

function checkPerms($permMin): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
	startSession();

	if (!isset($_SESSION['id'])) {
		die("<p style='color:red;'><b>Redirecting:</b> <i>You are not logged in [Session Status: " . session_status() . "] !</i></p>\n" .
			"<meta http-equiv='refresh' content='2; url=https://" . $_SERVER['HTTP_HOST'] . "/' />");
	} elseif (getAccountDetail('people', 'permissions', $_SESSION['id']) < $permMin)
		die("<p style='color:red;'><b>You do not have the required permissions!</b></p>\n");

	return true;
}

function checkCompareRank($greater_id, $lower_id, $equal = false): ?bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

	$greater_permissions = getAccountDetail('people', 'permissions', $greater_id);
	$greater_rank = getRank($greater_id);

	$lower_permissions = getAccountDetail('people', 'permissions', $lower_id);
	$lower_rank = getRank($lower_id);

	if (is_null($greater_permissions) || is_null($lower_permissions))
		return null;

	return (bool) (($greater_rank > $lower_rank) ^ ($equal && $greater_rank == $lower_rank));
}
