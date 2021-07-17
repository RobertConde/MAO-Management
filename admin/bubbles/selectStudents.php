<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/checks.php";
checkPerms(OFFICER);

$currentURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";  // TODO: Remove "?return=false&....."
?>

<form method="post" action="createPDF.php?ref=<?php echo $currentURL; ?>">
    <fieldset>
        <legend><b>Bubble Selection</b></legend>

        <label for="test">Test Name: </label>
        <input name="test" id="test" type="text"><br>
        <br>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
$sql_conn = getDBConn();

$students_result = $sql_conn->query("SELECT id, CONCAT(fname, ' ', lname) AS name, grade, division FROM people WHERE division != 0;");
if (!is_a($students_result, 'mysqli_result'))
	die("<p style=\"color:red;\">Get table function occurred an error upon execution of statement!</p>\n");

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

$table_rows = sql_TH(array_merge($students_result->fetch_fields(), array('select')));
while (!is_null($row_array = $students_result->fetch_assoc())) {
	$table_rows .= TR(array_merge($row_array,
			array("<input name=\"selected[]\" type=\"checkbox\" value=\"" . $row_array['id'] . "\">")));
}

echo surrTags('table', $table_rows), "<br>",
        "<br>",
        "<input type='submit' value='Create'>",
    "</fieldset>",
"</form>";

if (isset($_GET['return'])) {
	if ($_GET['return'] == 'true')
	    echo "<p style=\"color:green;\">Successfully created bubble sheets.</p>\n";
	else if ($_GET['return'] == 'false')
		echo "<p style=\"color:red;\">Failed to create bubble sheets!</p>\n";
}
