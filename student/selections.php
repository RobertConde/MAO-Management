<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
startSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBar();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
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
if (isset($_POST['comp_name'])) {    // Process POST update
	$updated = toggleSelection($_POST['comp_name'], $_SESSION['id']);

    redirect(currentURL());
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

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
?>

    <title>DB | Selections</title>

    <h2 style="margin: 6px;"><u>Competition Selections</u></h2>

    <form style="margin: 6px; display: inline-block;" class="filled border">
        <fieldset>
            <legend><i>Account Information</i></legend>

            <label for="first_name">First Name:</label>
            <input id="first_name" name="first_name" type="text" size="10"
                   value="<?php echo getAccountDetail('people', 'first_name', $id); ?>"
                   disabled><br>

            <label for="last_name">Last Name:</label>
            <input id="last_name" name="last_name" type="text" size="10"
                   value="<?php echo getAccountDetail('people', 'last_name', $id); ?>"
                   disabled><br>

            <label for="mu_student_id">Mu Student ID:</label>
            <input id="mu_student_id" name="mu_student_id" type="text" pattern="[0-9\s]{3}" size="3" disabled
                   value="<?php echo getAccountDetail('competitor_info', 'mu_student_id', $id); ?>"><br>

            <label for="division">Division:</label>
            <select id="division" name="division" disabled>
                <option value="1" <?php echo getAccountDetail('competitor_info', 'division', $id) == 1 ? "selected" : ""; ?>>
                    Algebra I
                </option>
                <option value="2" <?php echo getAccountDetail('competitor_info', 'division', $id) == 2 ? "selected" : ""; ?>>
                    Geometry
                </option>
                <option value="3" <?php echo getAccountDetail('competitor_info', 'division', $id) == 3 ? "selected" : ""; ?>>
                    Algebra II
                </option>
                <option value="4" <?php echo getAccountDetail('competitor_info', 'division', $id) == 4 ? "selected" : ""; ?>>
                    Precalculus
                </option>
                <option value="5" <?php echo getAccountDetail('competitor_info', 'division', $id) == 5 ? "selected" : ""; ?>>
                    Calculus
                </option>
                <option value="6" <?php echo getAccountDetail('competitor_info', 'division', $id) == 6 ? "selected" : ""; ?>>
                    Statistics
                </option>
                <option value="0" <?php echo getAccountDetail('competitor_info', 'division', $id) == 0 ? "selected" : ""; ?>>
                    Not a Student
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
if (!is_a($comps_stmt = $sql_conn->query("SELECT c.competition_name AS comp_name, c.competition_description AS comp_desc FROM competitions c LEFT OUTER JOIN competition_selections cs ON c.competition_name = cs.competition_name AND cs.id = $id ORDER BY c.competition_name;"), 'mysqli_result'))
	die("<p style='color:red;'>Get table function occurred an error upon execution of statement!</p>\n");

// TODO: Ugh, I hate how this works. "Efficient", but a waste.
$table_rows = sql_TH(array_merge($comps_stmt->fetch_fields(), array('selected')));
while (!is_null($row = $comps_stmt->fetch_assoc())) {
	$comp_name = $row['comp_name'];
	$comp_desc = $row['comp_desc'];

	$table_rows .= TR(array_merge($row,
			array(
				"<form method='post' class='center'>
                        <input name='comp_name' type='hidden' value='$comp_name'>
                        <input type='checkbox' onchange='this.form.submit()' " . (isSelected($comp_name, $id) ? 'checked' : '') . ">
                    </form>"))) . "\n";
}
echo surrTags('table', $table_rows, "class='center' style='margin-top: 6px; margin-bottom: 6px;'");
