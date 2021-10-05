<?php

function stylesheet()
{
	echo "<link rel='stylesheet' href='https://" . $_SERVER['HTTP_HOST'] . "/style.css'>";
}

function relativeURL($relative_path = ''): string
{
	return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/$relative_path";
}

function makeLink($name, $relative_path = '', $target = '_self'): string
{
	return "<a href='" . relativeURL($relative_path) . "' target='$target'>$name</a>";
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
	echo "<script> window.location.replace('$url') </script>";
}

function navigationBar()
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets/navbar.php";
}

// $tag is without carets or '/'
function surrTags($tag, $text, $tag_interior = ''): string
{
	// TODO: Reconsider placement (might need to move higher up in call list; ASK: "Should it be handled here?")
	if (is_null($text))
		$text = "<code style='color:#e11212; text-align: center'><i>null</i></code>";

	return "<$tag $tag_interior>$text</$tag>";
}

function sql_TH($sql_fields_array): string
{
	$table_header_data = "";
	foreach ($sql_fields_array as $header_elem)
		$table_header_data .= surrTags(
			'th',
			is_string($header_elem) ? $header_elem : $header_elem->name,
			"style='text-align: center;'");

	return surrTags('tr', $table_header_data);
}

function TR($row_array, $center = false): string
{
	$row_data = "";
	foreach ($row_array as $row_elem) {
		if ($center)
			$row_data .= surrTags('td', $row_elem, "style='text-align: center;'");
		else
			$row_data .= surrTags('td', $row_elem);
	}

	return surrTags('tr', $row_data);
}

function getTableFromResult($result): string
{
	if (!is_a($result, 'mysqli_result'))
		die("<p style='color:red;'>Get table function occurred an error upon execution of statement!</p>\n");

	$table_rows = sql_TH($result->fetch_fields());
	while (!is_null($row_array = $result->fetch_row()))
		$table_rows .= TR($row_array) . "\n";

	return surrTags('table', $table_rows);
}

/* BEWARE OF POSSIBLE SQL INJECTION */
function getTable($table_name, $order_by = "1"): string
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$result = $sql_conn->query("SELECT * FROM $table_name ORDER BY $order_by");

	return getTableFromResult($result);
}

function getDBName(): string
{
	$sql_config = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config.ini", true)['sql'];

	return $sql_config['database'];
}

function personSelectForm($method = 'GET')
{
	echo "<form method='$method' id='person-select' style='display: none;'></form>";
}

function personSelect($button_text = 'Go')
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets/personSelect.php";
	
	echo "<!--suppress XmlInvalidId --><input type='submit' value='$button_text' form='person-select' style='padding: 1px;'>";
}

function getSelectID($method = 'GET')
{
	$select_id = null;

	$temp = null;
	if (strtoupper($method) == 'GET' && isset($_GET['select-id']))
		$temp = $_GET['select-id'];
	else if (strtoupper($method) == 'POST' && isset($_POST['select-id']))
		$temp = $_POST['select-id'];

	if (!is_null($temp)) {
		if (preg_match('/^[0-9]{7}/', $temp))
			$select_id = $temp;
		else {
			$ind = strpos($temp, '[') + 1;

			$select_id = substr($temp, $ind, 7);
		}
	}

	return $select_id;
}

function compReportForm($comp, $report_name)
{
	echo
	"<form id='$report_name' action='", relativeURL("admin/reports/$report_name"), "' target='_blank' style='display: none;'>",
	"<input name='comp_name' type='hidden' value='$comp'>",
	"</form>";
}

function formatPhoneNum($phone): string
{
	if ($phone == '')
		return '';

	return '(' . substr($phone, 0, 3) . ') '
			. substr($phone, 3, 3) . '-'
			. substr($phone, 6, 4);
}