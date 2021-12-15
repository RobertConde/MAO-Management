<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safeStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);

if (isset($_POST['create'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/competitions/CUD.php";

	$created = createCompetition(  // Create competition
		$_POST['comp_name'], $_POST['start_date'], $_POST['end_date'], $_POST['payment_id'],
		isset($_POST['forms']), isset($_POST['bus']), isset($_POST['rooms']),
		$_POST['info']);

	redirect(currentURL(false) . '?comp_name=' . rawurlencode($_POST['comp_name'])); // Redirect to created competition manage page
} else if (isset($_POST['update'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/competitions/CUD.php";

	updateCompetition(  // Update competition
		$_POST['comp_name'], $_POST['start_date'], $_POST['end_date'], $_POST['payment_id'],
		isset($_POST['forms']), isset($_POST['bus']), isset($_POST['room']),
		$_POST['info']);

	redirect(currentURL());
} else if (isset($_POST['delete'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/competitions/CUD.php";

	deleteCompetition($_POST['comp_name']); // Delete competition

	redirect(currentURL(false));    // Redirect to deselected manage page
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

$comp = null;
if (isset($_GET['comp_name']) && existsComp($_GET['comp_name']))
	$comp = $_GET['comp_name']
?>

<title>MAO | Competitions</title>

<h2 style="margin: 6px;"><u>Competitions</u></h2>

<form method="get" class="filled border" style="text-align: center; margin: 6px;">
    <fieldset>
        <legend><b>Competition</b></legend>

        <!--suppress HtmlFormInputWithoutLabel -->
        <select name="comp_name" onchange="this.form.submit()" style="margin-bottom: 6px;">
            <option selected disabled hidden></option>
			<?php
			$sql_conn = getDBConn();

			$comp_names_stmt = $sql_conn->prepare("SELECT competition_name FROM competitions");
			$comp_names_stmt->bind_result($curr_comp_name);
			$comp_names_stmt->execute();

			while ($comp_names_stmt->fetch()) {
				if ($curr_comp_name == $comp)
					echo "<option value='$curr_comp_name' selected>$curr_comp_name</option>";
				else
					echo "<option value='$curr_comp_name'>$curr_comp_name</option>";
			}
			?>
        </select>
        <br>

        <button onclick="location.href='<?php echo currentURL(false); ?>'" type="button">Deselect</button>
    </fieldset>
</form>
<br>

<?php
$start_date = getAssociatedCompInfo($comp, 'start_date');
$end_date = getAssociatedCompInfo($comp, 'end_date');
$payment_id = getAssociatedCompInfo($comp, 'payment_id');

$check_status_forms = (getAssociatedCompInfo($comp, 'show_forms') ?? true ? 'checked' : 'unchecked');
$check_status_bus = (getAssociatedCompInfo($comp, 'show_bus') ?? true ? 'checked' : 'unchecked');
$check_status_room = (getAssociatedCompInfo($comp, 'show_room') ?? true ? 'checked' : 'unchecked');

$description = getDetail('competitions', 'description', 'competition_name', $comp);
?>

<form method="post" class="filled border" style="text-align: center;">
    <fieldset>
        <h4 style="margin-top: 2px;"><u>Information</u></h4>

        <div style="display: inline-block; text-align: left;">
            <label for="comp_name">Competition:</label>
            <input id="comp_name" name="comp_name" type="text" required
                   value="<?php echo $comp; ?>"
				<?php if (!is_null($comp)) echo 'disabled'; ?>>
			<?php
			if (!is_null($comp))
				echo "<input name='comp_name' type='hidden' value='$comp'>";
			?><br>

            <label for="start_date">Start Date:</label>
            <input id="start_date" name="start_date" type="date"
                   value="<?php echo $start_date; ?>"><br>

            <label for="end_date">End Date:</label>
            <input id="end_date" name="end_date" type="date"
                   value="<?php echo $end_date; ?>"><br>

            <label for="payment_id">Payment ID:</label>
            <select id="payment_id" name="payment_id">
                <option selected></option>
				<?php
				$payments_stmt = $sql_conn->prepare("SELECT payment_id FROM payment_details");
				$payments_stmt->bind_result($curr_payment_id);
				$payments_stmt->execute();

				while ($payments_stmt->fetch()) {
					if ($curr_payment_id == $payment_id)
						echo "<option value='$curr_payment_id' selected>$curr_payment_id</option>";
					else
						echo "<option value='$curr_payment_id'>$curr_payment_id</option>";
				}

				$sql_conn->close();
				?>
            </select>
        </div>
        <br>

        <h4><u>Show Fields</u><br></h4>

        <div style="display: inline-block; text-align: left;">
            <input id="forms" name="forms" type="checkbox" <?php echo $check_status_forms ?>>
            <label for="forms">Forms Given/Submitted</label><br>

            <input id="bus" name="bus" type="checkbox" <?php echo $check_status_bus ?>>
            <label for="bus">Bus #</label><br>

            <input id="room" name="room" type="checkbox" <?php echo $check_status_room ?>>
            <label for="room">Room #</label><br>
        </div>
        <br>

        <h4><u>Description</u></h4>
        <!--suppress HtmlFormInputWithoutLabel -->
        <textarea id="info" name="info" rows="10" cols="50"><?php echo $description; ?></textarea><br>
        <br>
        <input id="create" name="create" type="submit" value="Create"
               style="color: green; float: left;" <?php if (!is_null($comp)) echo 'disabled'; ?>>
        <input id="update" name="update" type="submit" value="Update"
               style="color: blue;" <?php if (is_null($comp)) echo 'disabled'; ?>>
        <input id="delete" name="delete" type="submit" value="Delete"
               style="color: red; float: right;" <?php if (is_null($comp)) echo 'disabled'; ?>>
    </fieldset>
</form>