<?php

function stylesheet() {
	echo "<link rel=\"stylesheet\" href=\"https://" . $_SERVER['HTTP_HOST'] . "/style.css\">\n";
}

function makeLink($name, $relative_path = "") : string
{
	return "<a href=\"https://" . $_SERVER['HTTP_HOST'] . "/$relative_path\">$name</a>";
}

function navigationBar() {
	require_once $_SERVER['DOCUMENT_ROOT'] . "require/sql.php";

	echo "<ul>\n";

	$perm0_names = array("Login", "Logout" , "Register");
	$perm0_urls = array("user/login.php", "user/logout.php", "user/register.php");

	for ($ind = 0; $ind < count($perm0_names); ++$ind)
		echo makeLink($perm0_names[$ind], $perm0_urls[$ind]) . "\n";

	/* OTHER PERMISSIONS */

	echo "</ul>\n";
}
