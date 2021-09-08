<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
startSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBar();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER);

$post_id_is_real = isset($_POST['competition_name']) && !is_null(getDetail('competitions', 'competition_name', 'competition_name', $_POST['competition_name']));

$created = null;
$updated = null;
$deleted = null;
if (isset($_POST['create'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/competitions/CUD.php";

	$created = createCompetition(  // Create competition
		$_POST['competition_name'],
		$_POST['payment_id'],
		$_POST['info']);

	if ($created)
		echo("<p style='color:red;'><b>Redirecting!</b></p>\n" .
			"<meta http-equiv='refresh' content='2; url=" . currentURL(false) . "?competition_name=" . $_POST['competition_name'] . "' />");
} else if (isset($_POST['update']) && $post_id_is_real) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/competitions/CUD.php";

	$updated = updateCompetition(  // Update competition
		$_POST['competition_name'],
		$_POST['payment_id'],
		$_POST['info']);
} else if (isset($_POST['delete']) && $post_id_is_real) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/competitions/CUD.php";

	$deleted = deleteCompetition($_POST['competition_name']); // Delete competition
}

$competition_name = null;
$payment_id = null;
if (isset($_GET['comp_name'])) {
	if ($_GET['comp_name'] == '')  // Blank search
		redirect(currentURL(false));
	else {
		$get_id_is_real = !is_null(getDetail('competitions', 'competition_name', 'competition_name', $_GET['comp_name']));

		if ($get_id_is_real) {
			$competition_name = $_GET['comp_name'];
			$payment_id = getDetail('competitions', 'payment_id', 'competition_name', $_GET['comp_name']);
		}
	}
}
?>

    <title>DB | Competitions</title>

    <h2 style="margin: 6px;"><u>Competitions</u></h2>

    <form method="get" style="margin: 6px;" class="filled border">
        <fieldset>
            <legend><b>Competition</b></legend>

            <select name="comp_name" onchange="this.form.submit()" style="margin-bottom: 6px;">
                <option selected disabled hidden></option>
				<?php
				$sql_conn = getDBConn();

				$competitions_query = "SELECT competition_name FROM competitions";

				$competitions_result = $sql_conn->query($competitions_query);

				while (!is_null($row = $competitions_result->fetch_assoc())) {
					echo "<option value='" . $row['competition_name'] . "' "
						. ($competition_name == $row['competition_name'] ? 'selected' : '')
						. ">" . $row['competition_name'] . "</option>";
				}
				?>
            </select>
            <br>

            <button onclick="location.href='<?php echo currentURL(false); ?>'" type="button">Deselect</button>
        </fieldset>
    </form>
    <br>

    <form method="post" class="filled border">
        <fieldset>
            <!--            TODO: Add tooltip! (why???; idk...)-->

            <label for="comp_name">Competition:</label>
            <input id="comp_name" name="comp_name" type="text" required
                   value="<?php echo $competition_name; ?>"
				<?php if (!is_null($competition_name)) echo 'disabled'; ?>>
			<?php
			if (!is_null($competition_name))
				echo "<input name='competition_name' type='hidden' value='$competition_name'>";
			?><br>

            <label for="payment_id">Payment ID:</label>
            <select id="payment_id" name="payment_id" style="margin-bottom: 6px;">
                <option selected></option>
				<?php
				$sql_conn = getDBConn();

				$payments_query = "SELECT payment_id FROM payment_details";

				$payments_result = $sql_conn->query($payments_query);

				while (!is_null($row = $payments_result->fetch_assoc())) {
					echo "<option value='" . $row['payment_id'] . "' "
						. ($payment_id == $row['payment_id'] ? 'selected' : '')
						. ">" . $row['payment_id'] . "</option>";
				}
				?>
            </select><br>

            <label for="info" style="margin-top: 0px; margin-bottom: 0px;"><u>Information</u></label><br>
            <textarea id="info" name="info" rows="10" cols="50"
                      required><?php if (!is_null($competition_name)) echo getDetail('competitions', 'competition_description', 'competition_name', $competition_name); ?></textarea><br>
            <br>
            <input id="create" name="create" type="submit" value="Create"
                   style="color: green; float: left;" <?php if (!is_null($competition_name)) echo 'disabled'; ?>>
            <input id="update" name="update" type="submit" value="Update"
                   style="color: blue;" <?php if (is_null($competition_name)) echo 'disabled'; ?>>
            <input id="delete" name="delete" type="submit" value="Delete"
                   style="color: red; float: right;" <?php if (is_null($competition_name)) echo 'disabled'; ?>>
        </fieldset>
    </form>

<?php
if (!is_null($created)) {
	echo $created ? "<p style='color:green;'>Successfully created.</p>\n" :
		"<p style='color:red;'>Failed to create!</p>\n";
}

if (!is_null($updated)) {
	echo $updated ? "<p style='color:green;'>Successfully updated.</p>\n" :
		"<p style='color:red;'>Failed to update!</p>\n";
}

if (!is_null($deleted)) {
	echo $deleted ? "<p style='color:green;'>Successfully deleted.</p>\n" :
		"<p style='color:red;'>Failed to delete!</p>\n";
}

if (isset($_GET['comp_name']) && is_null($competition_name))
	echo "<i class='rainbow'>Competition not found!</i>\n";
