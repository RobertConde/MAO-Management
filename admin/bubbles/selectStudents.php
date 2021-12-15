<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safeStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);
?>

<title>MAO | Bubbles - Select Student</title>

<h2 style="margin: 6px;"><u>Bubbles From Selection</u></h2>

<?php
if (isset($_GET['return'])) {
	if ($_GET['return'] == 'true')
		echo "<p style='color:green;'>Successfully created bubble sheets.</p>\n";
	else if ($_GET['return'] == 'false')
		echo "<p style='color:red;'>Failed to create bubble sheets!</p>\n";
}
?>

<form method="post" action="createPDF.php?ref=<?php echo currentURL(false); ?>" class="filled border">
    <fieldset>
        <label for="test">Test Name:</label>
        <input name="test" id="test" type="text"><br>
        <br>

		<?php
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
		$sql_conn = getDBConn();

		$students_result = $sql_conn->query(
			"SELECT p.id, CONCAT(p.first_name, ' ', p.last_name) AS name, ci.division
            FROM people p
            JOIN competitor_info ci ON p.id = ci.id 
            WHERE ci.division != 0;");
		if (!is_a($students_result, 'mysqli_result'))
			die("<p style='color:red;'>Get table function occurred an error upon execution of statement!</p>\n");

		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

		$table_rows = sql_TH(array_merge($students_result->fetch_fields(), array('grade', 'select')));
		while ($row_array = $students_result->fetch_assoc()) {
			if (getGrade($row_array['id']) != 0)
				$table_rows .= TR(array_merge($row_array,
					array(getGrade($row_array['id']),
						'<input name="selected[]" type="checkbox" value="' . $row_array['id'] . '">')),
					true);
		}
		$sql_conn->close();

		echo surrTags('table', $table_rows),
		'<br>',
		'<input type="submit" value="Create">';
		?>
    </fieldset>
</form><br>
<br>

<a href="https://github.com/AnirudhRahul/FAMATBubbler" class="rainbow">
    ♥ Credit Where Credit Is Due ♥️</a>
