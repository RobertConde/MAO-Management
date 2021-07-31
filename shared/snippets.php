<?php

function stylesheet()
{
	echo "<link rel=\"stylesheet\" href=\"https://" . $_SERVER['HTTP_HOST'] . "/style.css\">\n";
}

function makeLink($name, $relative_path = ""): string
{
	return "<a href=\"https://" . $_SERVER['HTTP_HOST'] . "/$relative_path\">$name</a>";
}

function currentURL($request = true): string
{
	$currentURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	if (!$request && strpos($currentURL, '?') != false)
		return substr($currentURL, 0, strpos($currentURL, '?'));

	return $currentURL;
}

function redirect($url)
{
	header("location: $url");
}

function navigationBar()
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";

	$perm0_names = array(
		'Register',
		'Login',
		'Logout',
		'Update Info',
		'|',
		'Delete Account',
		'|',
		'Payments',
		'Transactions',
		'Manage Competitions',
		'Competition Tracker',
		'Create Bubble Sheets',
		'Custom Report');
	$perm0_urls = array(
		'account/register',
		'account/login',
		'account/logout',
		'student/updateInfo',
		'',
		'admin/accounts/delete',
		'',
		'admin/payments/payments',
		'student/transactions',
		'admin/competitions/manage',
		'admin/competitions/tracker',
		'admin/bubbles/selectStudents',
		'admin/reports/custom');

	$links = "";
	for ($ind = 0; $ind < count($perm0_names); ++$ind)
		$links .= makeLink($perm0_names[$ind], $perm0_urls[$ind]) . "\n";

	/* OTHER PERMISSIONS */

	echo surrTags('div', surrTags('ul', $links), "class=\"nav-bar noprint\"") . "\n";
}

// $tag is without carets or '/'
function surrTags($tag, $text, $tag_interior = ''): string
{
	// TODO: Reconsider placement (might need to move higher up in call list; ASK: "Should it be handled here?")
	if (is_null($text))
		$text = "<code style=\"color:#e11212; text-align: center\"><i>null</i></code>";

	return "<$tag $tag_interior>$text</$tag>";
}

function sql_TH($sql_fields_array): string
{
	$table_header_data = "";
	foreach ($sql_fields_array as $header_elem)
		$table_header_data .= surrTags('th', surrTags('center', is_string($header_elem) ? $header_elem : $header_elem->name));

	return surrTags('tr', $table_header_data);
}

function TR($row_array): string
{
	$row_data = "";
	foreach ($row_array as $row_elem)
		$row_data .= surrTags('td', $row_elem);

	return surrTags('tr', $row_data);
}

function getTableFromResult($result): string
{
	if (!is_a($result, 'mysqli_result'))
		die("<p style=\"color:red;\">Get table function occurred an error upon execution of statement!</p>\n");

	$table_rows = sql_TH($result->fetch_fields());
	while (!is_null($row_array = $result->fetch_row()))
		$table_rows .= TR($row_array) . "\n";

	return surrTags('table', $table_rows);
}

/* BEWARE OF POSSIBLE SQL INJECTION */
function getTable($table_name, $order_by = "1"): string
{
	$sql_conn = getDBConn();

	$result = $sql_conn->query("SELECT * FROM $table_name ORDER BY $order_by");

	return getTableFromResult($result);
}

function getDBName():string
{
	$sql_config = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config.ini", true)['sql'];

	return $sql_config['database'];
}
