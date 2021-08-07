<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/checks.php";
checkPerms(STUDENT);

////DEBUG
//echo "SESSION<br>";
//foreach ($_SESSION as $key => $value) {
//	echo "Key: $key; Value: $value<br>";
//}
//echo "<br>", "POST<br>";
//foreach ($_POST as $key => $value) {
//	echo "Key: $key; Value: $value<br>";
//}

// Update process
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

$updated = null;
if (isset($_POST['competition_id'])) {  // Process POST update
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

	$updated = toggleSelection($_SESSION['id'], $_POST['competition_id']);
}

// View form (using correct ID)
$id = $_SESSION['id'];
if (isset($_GET['id'])) {
	$rankComp = checkCompareRank($_SESSION['id'], $_GET['id'], true);

	if (!is_null($rankComp)) {
		if ($rankComp)
			$id = $_GET['id'];
		else
			die("<p style='color:red;'>You do not have the required permissions!</p>\n");
	}
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
?>

<title>DB | Selections</title>

<body style="text-align: center;">

<h2 style="margin: 6px;"><u>Competition Selections</u></h2>

<form style="margin: 6px;">
    <fieldset>
        <legend><i>Account Information<i>&nbsp
                    <div class="tooltip"><i class="fa fa-question-circle"></i>
                        <span class="tooltiptext">Edit this information in update info.</span>
                    </div>
        </legend>

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

        <label for="mu_student_id">Mu Student ID:</label>
        <input id="mu_student_id" name="mu_student_id" type="text" pattern="[0-9\s]{3}" size="3" disabled
               value="<?php echo getAccountDetail('people', 'mu_student_id', $id) ?>"><br>
        <br>

        <label for="division">Division:</label>
        <select id="division" name="division" disabled>
            <option value="1" <?php echo getAccountDetail('people', 'division', $id) == 1 ? "selected" : "" ?>>
                Algebra I
            </option>
            <option value="2" <?php echo getAccountDetail('people', 'division', $id) == 2 ? "selected" : "" ?>>
                Geometry
            </option>
            <option value="3" <?php echo getAccountDetail('people', 'division', $id) == 3 ? "selected" : "" ?>>
                Algebra II
            </option>
            <option value="4" <?php echo getAccountDetail('people', 'division', $id) == 4 ? "selected" : "" ?>>
                Precalculus
            </option>
            <option value="5" <?php echo getAccountDetail('people', 'division', $id) == 5 ? "selected" : "" ?>>
                Calculus
            </option>
            <option value="6" <?php echo getAccountDetail('people', 'division', $id) == 6 ? "selected" : "" ?>>
                Statistics
            </option>
            <option value="0" <?php echo getAccountDetail('people', 'division', $id) == 0 ? "selected" : "" ?>>Not a
                Student
            </option>
        </select>
    </fieldset>
</form>

<?php
// Report if update was successful
if (isset($updated)) {
	echo $updated ?
		"<p style='color:green;'>Successfully updated competition selection (ID Updated = " . $_SESSION['id'] . ").</p>\n" :
		"<p style='color:red;'>Failed to update competition selection (ID = " . $_SESSION['id'] . ").</p>\n";
}

//TODO: Better tables; no function will do! Function should ONLY be for custom reports!
$sql_conn = getDBConn();
if (!is_a($payment_stmt = $sql_conn->query("SELECT c.competition_id, c.competition_description FROM competitions c LEFT OUTER JOIN competition_selections cs ON c.competition_id = cs.competition_id AND cs.id = $id ORDER BY c.competition_id;"), 'mysqli_result'))
	die("<p style='color:red;'>Get table function occurred an error upon execution of statement!</p>\n");

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

$table_rows = sql_TH(array_merge($payment_stmt->fetch_fields(), array('selected')));
while (!is_null($row_array = $payment_stmt->fetch_row())) {
	$table_rows .= TR(array_merge($row_array,
			array(
				"<form id='$row_array[0]' method='post' class='center'>
                        <input id='competition' name='competition_id' type='hidden' value='$row_array[0]'>
                        <input id='onchange' name='onchange' type='checkbox' onchange=\"document.getElementById('$row_array[0]').submit()\" " . (isSelected($id, $row_array[0]) ? "checked" : "") . ">
                    </form>"))) . "\n";
}
echo surrTags('table', $table_rows, "class='center' style='margin-top: 6px; margin-bottom: 6px;'");
