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

	$perm0_names = array("Login", "Logout" , "Register", "Update", "Payments", "Custom Report");
	$perm0_urls = array("user/login.php", "user/logout.php", "user/register.php", "user/update.php", "user/payments.php", "reports/custom.php");

	$links = "";
	for ($ind = 0; $ind < count($perm0_names); ++$ind)
		$links .= makeLink($perm0_names[$ind], $perm0_urls[$ind]) . "\n";

	/* OTHER PERMISSIONS */

	echo surrTags('div', surrTags('ul', $links), "class=\"nav-bar\"") . "\n";

//	echo surrTags('center', surrTags('div', "<img src=\"https://i.ibb.co/c1CWDg5/images.jpg\" alt=\"Trulli\" height=\"75px\">" . surrTags('ul', $links), "class=\"nav-bar\"")) . "\n";
}

// $tag is without carets or '/'
function surrTags($tag, $text, $tag_interior = '') : string
{
	// TODO: Reconsider placement (might need to move higher up in call list; ASK: "Should it be handled here?")
	if (is_null($text))
		$text = "<center><code style=\"color:#e11212;\"><i>null</i></code></center>";

	return "<$tag $tag_interior>$text</$tag>";
}

//function stylizedHeader() {
//	echo
//		"<head>
//		<!-- Required meta tags -->
//  		<meta charset=\"utf-8\">
//		<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
//		<link href=\"https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap\" rel=\"stylesheet\">
//
//		<link rel=\"stylesheet\" href=\"https://" . $_SERVER['HTTP_HOST'] . "/fonts/icomoon/style.css\">
//
//		<link rel=\"stylesheet\" href=\"https://" . $_SERVER['HTTP_HOST'] . "/css/owl.carousel.min.css\">
//
//		<!-- Bootstrap CSS -->
//		<link rel=\"stylesheet\" href=\"https://" . $_SERVER['HTTP_HOST'] . "/css/bootstrap.min.css\">
//
//		<!-- Style -->
//		<link rel=\"stylesheet\" href=\"https://" . $_SERVER['HTTP_HOST'] . "/css/style.css\">
//
//		<title>Table #17</title>
//		</head>";
//}

function sql_TH($sql_fields_array) : string
{
	$table_header_data = "";
	foreach ($sql_fields_array as $header_elem)
		$table_header_data .= surrTags('th', surrTags('center', is_string($header_elem) ? $header_elem : $header_elem->name));

	return surrTags('tr', $table_header_data);
}

//function sql_stylized_TH($sql_fields_array) : string
//{
//	$table_header_data = "";
//	foreach ($sql_fields_array as $header_elem)
//		$table_header_data .= surrTags('th',$header_elem->name);
//
//	return surrTags('thead', surrTags('tr', $table_header_data), "scope = \"col\"");
//
//}

function TR($row_array) : string
{
	$row_data = "";
	foreach ($row_array as $row_elem)
		$row_data .= surrTags('td', $row_elem);

	return surrTags('tr', $row_data);
}

//function stylized_TR($row_array) : string
//{
//	$row_data = "";
//	foreach ($row_array as $row_elem)
//		$row_data .= surrTags('td', $row_elem);
//
//	return surrTags('tr', $row_data, "scope=\"row\"");
//}

function sql_getTable($query) : string
{
//	echo $query;
	require_once $_SERVER['DOCUMENT_ROOT'] . "require/sql.php";

	$sql_conn = getDBConn();    // Get DB connection

	if (!is_a($result = $sql_conn->query($query), 'mysqli_result'))
		die("<p style=\"color:red;\">Get table function occurred an error upon execution of statement!</p>\n");

	$table_rows = sql_TH($result->fetch_fields()) ;
	while (!is_null($row_array = $result->fetch_row()))
		$table_rows .= TR($row_array) . "\n";

	return surrTags('table', $table_rows);
}

/* BEWARE OF POSSIBLE SQL INJECTION */
function getTable($table, $sort = array('TRUE')) : string
{
	$order_by = implode($sort, ", ");

	return sql_getTable("SELECT * FROM $table ORDER BY $order_by");
}

//function getStylizedTablePage($table) : string
//{
//	$sql_conn = getDBConn();    // Get DB connection
//
//	if (!is_a($result = $sql_conn->query("SELECT * FROM $table"), 'mysqli_result'))
//		die("<p style=\"color:red;\">Get table function occurred an error upon execution of statement!</p>\n");
//
//	require_once $_SERVER['DOCUMENT_ROOT'] . "require/htmlSnippets.php";
//	stylizedHeader();
//
//	$body_interior_pretable =
//		"<div class=\"content\">
//   		<div class=\"container\">
//     	<h2 class=\"mb-5\">Table #7</h2>
//
//    	<div class=\"table-responsive\">";
//
//	$table_rows = sql_stylized_TH($result->fetch_fields()) ;
//	while (!is_null($row_array = $result->fetch_row()))
//		$table_rows .= stylized_TR($row_array) . "\n";
//
//	return surrTags('body', $body_interior_pretable . surrTags('table', $table_rows, "class=\"table table-striped custom-table\""));
//}
