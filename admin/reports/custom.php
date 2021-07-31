<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/checks.php";
checkPerms(OFFICER);

$table = "";
if (isset($_GET['table']))
	$table = $_GET['table'];

$order_by = "";
if (isset($_GET['order_by']))
	$order_by = $_GET['order_by'];
?>

    <title>DB | Custom Report</title>

    <h2 style="text-align: center; margin: 6px;"><u>Custom Report</u></h2>

    <form id="table-form" method="get" style="text-align: center; margin: 6px;">
        <fieldset>
            <legend><b>Report Parameters</b></legend>

            <select id="tables" name="table" style="margin: 6px;">
                <option value="" disabled hidden selected>Choose a table...</option>
				<?php
				$sql_conn = getDBConn();

				$tables_query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '" . getDBName() . "'";

				$tables_result = $sql_conn->query($tables_query);

				while (!is_null($row = $tables_result->fetch_assoc())) {
					echo "<option value=\"" . $row['TABLE_NAME'] . "\" "
						. ($table == $row['TABLE_NAME'] ? 'selected' : '')
						. ">" . $row['TABLE_NAME'] . "</option>";
				}
				?>
            </select>
            <label for="tables" style="color: red;">*</label><br>

            <input id="order_by" name="order_by" type="text" placeholder="SQL Order By Expression" style="margin: 6px;"
                   value="<?php echo $order_by; ?>">
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Beware of SQL Injection!</span>
            </div>
            <br>

            <input type="submit" value="Get!">
        </fieldset>
    </form>

<?php
if (isset($_GET['table'])) {
	if ($_GET['table'] == '')
		die("<p style=\"color:red; text-align:center; margin: 6px;\">A table must be specified!</p>\n");
	else
		echo surrTags('center', ($order_by == "" ? getTable($_GET['table']) : getTable($_GET['table'], $order_by)));
}
