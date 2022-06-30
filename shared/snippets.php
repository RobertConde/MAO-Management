<?php

$SORT_OPTIONS = array('Name', 'Division', 'Division, Grade', 'Bus, Name [Bus List]', 'Bus, Division, Grade', 'Room, Name [Rooming List]', 'Grade', 'ID');
const SORT_ORDER_BY = array(
	'Name' => 'p.last_name, p.first_name',
	'Division' => 'ci.division, p.last_name, p.first_name',
	'Division, Grade' => 'ci.division, p.graduation_year DESC, p.last_name, p.first_name',
	'Bus, Name [Bus List]' => 'cd.bus, (ci.division = 0) DESC, p.last_name, p.first_name',
	'Bus, Division, Grade' => 'cd.bus, ci.division, p.graduation_year DESC, p.last_name, p.first_name',
	'Room, Name [Rooming List]' => 'cd.room, p.last_name, p.first_name',
	'Grade' => 'p.graduation_year DESC, p.last_name, p.first_name',
	'ID' => 'p.id, p.last_name, p.first_name');

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

function refresh()
{
	redirect(currentURL());
}

function navigationBarAndBootstrap()
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
	echo "<form method='$method' id='person-select-form' style='display: none;'></form>";
}

function personSelect($button_text = 'Go')
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets/personSelect.php";

	echo "<input type='submit' value='$button_text' form='person-select-form' style='padding: 1px;'>";
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
	if (empty($phone))
		return '';

	return '(' . substr($phone, 0, 3) . ') '
		. substr($phone, 3, 3) . '-'
		. substr($phone, 6, 4);
}

function formatOrdinalNumber($num): string
{
	$suffixes = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');

	if ((($num % 100) >= 11) && (($num % 100) <= 13))
		return $num . 'th';
	else
		return $num . $suffixes[$num % 10];
}

function formatMoney($amount): string
{
	return ($amount < 0 ? '−' : '') . '$' . number_format(abs($amount), 2);
}

function formatDateTime($date_time, $second = false)
{
	return $date_time->format('n/j/Y @ g:i' . ($second ? ':s' : '') . ' A');
}

function getCurrentDateTime($second = false): string
{
	date_default_timezone_set('America/New_York');

	return formatDateTime(new DateTime('now'), $second);
}

/* TODO: use `second` parameter?? */
/** @noinspection PhpUnusedParameterInspection */
function formatToUSDate($date_str, $second = false): string
{
	$datetime = date_create_from_format('Y-m-d', $date_str);

	return $datetime->format('n/j/Y');
}

function noNavBar()
{
	echo "<style>.navbar {display: none;} body {padding: 0;}</style>";
}

function loginBackground()
{
	$custom_config = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/config.ini", true)['custom'];

	if ($custom_config['background'])
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets/login-background.php";
}

function calendar()
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/custom/calendar.php";
}
