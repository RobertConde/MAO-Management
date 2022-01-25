<?php
const STUDENT_PERMS = 1;
const OFFICER_PERMS = 10;
const ADMIN_PERMS = 100;

const STUDENT_RANK = 0;
const OFFICER_RANK = 1;
const ADMIN_RANK = 2;

function getPerms($id = null): ?int
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
	safelyStartSession();

	if (is_null($id) && isset($_SESSION['id']))
		$id = $_SESSION['id'];

	return getAccountDetail('people', 'permissions', $id);
}

function checkPerms($permMin): bool
{
	$perms = getPerms();
	if ($perms == -1) {
		die("<p style='color:red;'><b>Redirecting:</b> <i>You are not logged in [Session Status: " . session_status() . "] !</i></p>\n" .
			"<meta http-equiv='refresh' content='2; url=https://" . $_SERVER['HTTP_HOST'] . "/' />");
	} elseif ($perms < $permMin)
		die("<p style='color:red;'><b>You do not have the required permissions!</b></p>\n");

	return true;
}

function getRank($id = null)
{
	$perms = getPerms($id);

	if ($perms < 1)
		return -1;

	return floor(log10($perms));
}

function compareRank($greater_id, $lower_id, $equal = false): ?bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

	$greater_permissions = getAccountDetail('people', 'permissions', $greater_id);
	$greater_rank = getRank($greater_id);

	$lower_permissions = getAccountDetail('people', 'permissions', $lower_id);
	$lower_rank = getRank($lower_id);

	if (is_null($greater_permissions) || is_null($lower_permissions))
		return null;

	return (bool)(($greater_rank > $lower_rank) ^ ($equal && $greater_rank == $lower_rank));
}
