<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/checks.php";
checkPerms(STUDENT);

//DEBUG
//echo "SESSION<br>";
//foreach ($_SESSION as $key => $value) {
//	echo "Key: $key; Value: $value<br>";
//}
//echo "<br>", "POST<br>";
//foreach ($_POST as $key => $value) {
//	echo "Key: $key; Value: $value<br>";
//}

// Update process
$updated = null;
if (isset($_POST['transaction'])) {  // Process POST update
	if ($_POST['id'] != $_SESSION['id'] && !checkCompareRank($_SESSION['id'], $_POST['id'], true))   // Confirm rank is higher (so that people can't update through POST requests without being logged into an account of higher rank)
		die("<p style=\"color:red;\">You do not have the required permissions!</p>\n");

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	$updated = toggleTransaction($_POST['id'], $_POST['transaction']);
}

// View form (using correct ID)
$id = $_SESSION['id'];
if (isset($_GET['id'])) {
	$rankComp = checkCompareRank($_SESSION['id'], $_GET['id'], true);

	if (!is_null($rankComp)) {
		if ($rankComp)
			$id = $_GET['id'];
		else
			die("<p style=\"color:red;\">You do not have the required permissions!</p>\n");
	}
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
?>

<title>DB | Transactions</title>

<body style="text-align: center;">

<h2 style="margin: 6px;"><u>Transactions</u></h2>

<?php
if ($id != $_SESSION['id'])
	echo "<p style=\"color:violet;\"><i><b>Note:</b> You are updating an account that isn't yours, and has a permission rank below you!</i></p>\n";
?>

<form>
    <fieldset>
        <legend><i>Account Information</i>&nbsp
                    <div class="tooltip"><i class="fa fa-question-circle"></i>
                        <span class="tooltiptext">Edit this information in update info.</span>
                    </div>
        </legend>

		<?php
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";

		echo "<label for='id'><i>ID:</i></label>\n";
		if (getRank($_SESSION['id']) >= 1)
			echo "<input id='id' name='id' type='search' pattern='[0-9]{7}' size='7' value='$id'>\n",
			"<input id='search' type='submit' value='Search'>\n";
		else
			echo "<input id='id' name='id' type='search' pattern='[0-9]{7}' size='7' value='$id' disabled>\n";
		?>

        <br>
        <br>
        <label for="fname"><i>First Name</i>:</label>
        <input id="fname" name="fname" type="text"
               value="<?php echo getAccountDetail('people', 'fname', $id) ?>"
               disabled><br>
        <br>
        <label for="lname"><i>Last Name</i>:</label>
        <input id="lname" name="lname" type="text"
               value="<?php echo getAccountDetail('people', 'lname', $id) ?>"
               disabled><br>
        <br>

        <label for="grade"><i>Grade</i>:</label>
        <select id="grade" name="grade" disabled> <!-- TODO: Form colors (uniformity) -->
            <option disabled></option>
            <option value="6" <?php echo getAccountDetail('people', 'grade', $id) == 6 ? "selected" : "" ?>>6th Grade
            </option>
            <option value="7" <?php echo getAccountDetail('people', 'grade', $id) == 7 ? "selected" : "" ?>>7th Grade
            </option>
            <option value="8" <?php echo getAccountDetail('people', 'grade', $id) == 8 ? "selected" : "" ?>>8th Grade
            </option>
            <option value="9" <?php echo getAccountDetail('people', 'grade', $id) == 9 ? "selected" : "" ?>>9th Grade
            </option>
            <option value="10" <?php echo getAccountDetail('people', 'grade', $id) == 10 ? "selected" : "" ?>>10th Grade
            </option>
            <option value="11" <?php echo getAccountDetail('people', 'grade', $id) == 11 ? "selected" : "" ?>>11th Grade
            </option>
            <option value="12" <?php echo getAccountDetail('people', 'grade', $id) == 12 ? "selected" : "" ?>>12th Grade
            </option>
            <option value="0" <?php echo getAccountDetail('people', 'grade', $id) == 0 ? "selected" : "" ?>>Not a
                Student
            </option>
        </select>
    </fieldset>
</form>

<?php
// Report if update was successful
if (isset($updated)) {
	echo $updated ?
		"<p style=\"color:green;\">Successfully updated payment (ID Updated = " . $_POST['id'] . ").</p>\n" :
		"<p style=\"color:red;\">Failed to update payment (ID = " . $_POST['id'] . ").</p>\n";
}

$sql_conn = getDBConn();
if (getRank($_SESSION['id']) > 0) {
//TODO: Better tables; no function will do! Function should ONLY be for custom reports!
	if (!is_a($payment_stmt = $sql_conn->query("SELECT pd.payment_id, pd.cost, pd.info, tr.time_paid FROM payment_details pd LEFT OUTER JOIN transactions tr ON pd.payment_id = tr.payment_id AND id = $id ORDER BY ISNULL(tr.time_paid), tr.time_paid, pd.payment_id;"), 'mysqli_result'))
		die("<p style=\"color:red;\">Get table function occurred an error upon execution of statement!</p>\n");

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	$table_rows = sql_TH(array_merge($payment_stmt->fetch_fields(), array('paid')));
	while (!is_null($row_array = $payment_stmt->fetch_row())) {
		$table_rows .= TR(array_merge($row_array,
				array(
					"\n<form id='$row_array[0]' method='post'>
                        <input id='id' name='id' type='hidden' value='$id'>
                        <input id='transaction' name='transaction' type='hidden' value='$row_array[0]'>
                        <input id='onchange' name='onchange' type='checkbox' onchange='document.getElementById(\"$row_array[0]\").submit()' " . (isPaid($id, $row_array[0]) ? "checked" : "") . ">
                    </form>")),
				true) . "\n";
	}

	echo surrTags('table', $table_rows, "class='center' style='margin-top: 6px; margin-bottom: 6px;'");
} else {
	$result = $sql_conn->query("SELECT pd.payment_id, pd.info, tr.time_paid FROM payment_details pd LEFT OUTER JOIN transactions tr ON pd.payment_id = tr.payment_id AND id = $id ORDER BY ISNULL(tr.time_paid), tr.time_paid, pd.payment_id;");

	echo getTableFromResult($result) . "\n";
}
?>
