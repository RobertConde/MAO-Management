<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
startSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBar();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

// Get competition name if sent and is valid
$comp = null;
if (isset($_GET['comp_name'])) { // Check if `comp_id` was sent
	// Check if sent `comp_id` is valid
	if (!is_null(getDetail('competitions', 'competition_name', 'competition_name', $_GET['comp_name'])))
		$comp = $_GET['comp_name'];
}
$pay_id = getDetail('competitions', 'payment_id', 'competition_name', $comp);

// Add student if is sent
$add_id = getSelectID('POST');
if (!is_null($add_id))
	addToComp($comp, $add_id);

// Remove competitor from competition data for comp
$remove_result = null;
if (isset($_POST['remove'])) {
	$remove_id = $_POST['id'];

	$remove_result = removeFromComp($comp, $remove_id);
}

// Update row of competitor competition data
$update_result = null;
if (isset($_POST['update'])) {
	$update_id = $_POST['id'];
	$update_approved = isset($_POST['approved']);
	$update_forms = isset($_POST['forms']);
	$update_bus = ($_POST['bus'] ?? '');
	$update_room = ($_POST['room'] ?? '');

	$updateCompDate = updateCompData($comp, $update_id, $update_forms, $update_bus, $update_room);

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	$update_paid_status = isset($_POST['paid']);
	$setTransactionStatus = setTransactionStatus($update_id, $pay_id, $update_paid_status);

	$update_result = ($updateCompDate && $setTransactionStatus);
	redirect(currentURL());
}
?>

<title>DB | Tracker</title>

<h2 style="text-align: center; margin: 6px;"><u>Competition Tracker</u></h2>

<!--    Competition Selector    -->
<form method="get" style="text-align: center; margin: 6px;" class="filled border">
    <fieldset>
        <legend><b>Competition</b></legend>

        <!--suppress HtmlFormInputWithoutLabel -->
        <select name="comp_name" onchange="this.form.submit()">
            <option selected disabled hidden></option>
			<?php
			$sql_conn = getDBConn();

			$comps_stmt = $sql_conn->prepare("SELECT competition_name AS comp_id FROM competitions;");
			$comps_stmt->bind_result($curr_comp_id);
			$comps_stmt->execute();

			// Add competition options and select the selected competition
			while ($comps_stmt->fetch()) {
				$curr_selected_status = ($curr_comp_id == $comp ? 'selected' : '');

				echo "<option value='$curr_comp_id' $curr_selected_status>$curr_comp_id</option>";
			}
			?>
        </select>
    </fieldset>
</form>
<br>

<?php personSelectForm('POST'); ?>

<!--    Actions     -->
<?php
compReportForm($comp, 'comp-selections');
compReportForm($comp, 'comp-posting');
compReportForm($comp, 'comp-checkoff');
?>

<form method="post" action="<?php echo relativeURL('admin/bubbles/createPDF?ref='), currentURL(); ?>"
      class="filled border no-print" <?php if (is_null($comp)) echo 'hidden'; ?>>

    <fieldset style="padding: 6px;">
        <legend><i>Actions</i></legend>

        <!--        Person Selector         -->
		<?php personSelect('Add'); ?>
        <br>

        <!--        Bubble Sheet Stuff          -->
		<?php
		// TODO: Make this better
		// Information for bubble sheet creator
		if (!is_null($comp)) {
			// Competition name for bubble sheet creator
			echo "<input name='test' type='hidden' value='$comp'>";

			// Selected students IDs for bubble sheet creator
			$select_studs_query =
				"SELECT cd.id
					FROM competition_data cd
					JOIN people p ON cd.id = p.id
					WHERE cd.competition_name = ?
					ORDER BY p.last_name, p.first_name";

			$select_studs_stmt = $sql_conn->prepare($select_studs_query);
			$select_studs_stmt->bind_param('s', $comp);
			$select_studs_stmt->bind_result($curr_id);
			$select_studs_stmt->execute();

			while ($select_studs_stmt->fetch())
				echo "<input name='selected[]' type='hidden' value='$curr_id' style='margin: 6px;'>";
		}
		?>

        <input type="submit" value="Create Bubble Sheets"><br>
        <br>

        <u>Reports</u><br>
        <input type="submit" form="comp-selections" value="Selections">
        <input type="submit" form="comp-posting" value="Posting">
        <input type="submit" form="comp-checkoff" value="Checkoff List">
    </fieldset>
</form><br class="no-print">

<table class="border filled" <?php if (is_null($comp)) echo 'hidden'; ?>>
    <tr>
        <th class="no-print">❌</i></th>
        <th>ID</th>
        <th>Name</th>
        <th>Division</th>
        <th <?php if (is_null($pay_id)) echo 'hidden'; ?>>Paid</th>
        <th>Forms</th>
        <th>Bus</th>
        <th>Room</th>
        <th class="no-print">Update</th>
    </tr>

	<?php

	function getCompData($data_name, $comp, $id)
	{
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
		$sql_conn = getDBConn();

		$get_data_stmt = $sql_conn->prepare("SELECT $data_name FROM competition_data WHERE id = ? AND competition_name = ?");
		$get_data_stmt->bind_param('ss', $id, $comp);
		$get_data_stmt->bind_result($data);
		$get_data_stmt->execute();

		$get_data_stmt->fetch();
		return $data;
	}

	function getCheckbox($comp, $pay_id, $data_name, $id, $value = null): string
	{
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
		$sql_conn = getDBConn();

		if ($data_name == 'paid') {
			if (is_null($pay_id)) {
				$checkbox_status = 'checked';
			} else {
				if (is_null($value)) {
					$status_stmt = $sql_conn->prepare(
						"SELECT COUNT(id) FROM transactions
                            WHERE id = ? AND payment_id = ?");
					$status_stmt->bind_param('ss', $id, $pay_id);
					$status_stmt->bind_result($value);
					$status_stmt->execute();

					$status_stmt->fetch();
				}

				$checkbox_status = ($value ? 'checked' : '');
			}
		} else {
			if (is_null($value))
				$value = getCompData($data_name, $comp, $id);

			$checkbox_status = ($value ? 'checked' : '');
		}

		return "<!--suppress XmlInvalidId -->
                <input name='$data_name' type='checkbox' form='$id-update' style='margin: 0;' $checkbox_status>";
	}

	if (!is_null($comp)) {
		// TODO: do it right
		$approved_IDs_stmt = $sql_conn->prepare(
			"SELECT
                        cd.id,
                        CONCAT(last_name, ', ', first_name) AS name,
                        cd.forms,
                        cd.bus,
                        cd.room
                    FROM competition_data cd INNER JOIN people p ON cd.id = p.id WHERE competition_name = ?
                    ORDER BY last_name, first_name");
		$approved_IDs_stmt->bind_param('s', $comp);
		$approved_IDs_stmt->bind_result($id, $name, $forms, $bus, $room);
		$approved_IDs_stmt->execute();

		define("DIVISIONS", array(
			'Not A Student',
			'Algebra I',
			'Geometry',
			'Algebra 2',
			'Precalculus',
			'Calculus',
			'Statistics'));

		$rows = "";
		while (!is_null($person = $approved_IDs_stmt->fetch())) {
			// Table data
			$row_interior =
				"<!--suppress ALL -->
                   <div id='div-$id'>"
				. "<input name='id' type='hidden' form='$id-update' value='$id'>"
				. "<input name='id' type='hidden' form='$id-remove' value='$id'>"
				. "</div>";

			$row_interior .= surrTags('td', "<!--suppress XmlInvalidId --><input name='remove' type='checkbox' form='$id-remove' onchange='this.form.submit()'>", "class='no-print'");

			$row_interior .= surrTags('td', makeLink($id, "student/info?id=$id", '_blank'));

			$selected = (isSelected($comp, $id) ? "background-color: lightgreen;" : '');
			$row_interior .= surrTags('td', $name, "style='text-align: left; $selected'");

			$row_interior .= surrTags('td', DIVISIONS[getAccountDetail('competitor_info', 'division', $id)]);

			// If the competition doesn't have an assigned payment, don't show the 'Paid' columns
			if (!is_null($pay_id))
				$row_interior .= surrTags('td', getCheckbox($comp, $pay_id, 'paid', $id));

			$row_interior .= surrTags('td', getCheckbox($comp, $pay_id, 'forms', $id, $forms));

			$row_interior .= surrTags('td', "<!--suppress XmlInvalidId --><input name='bus' type='number' min='0' form='$id-update' style='width: 45px; text-align: center;' value='$bus'>");

			$row_interior .= surrTags('td', "<!--suppress XmlInvalidId --><input name='room' type='text' size='2' form='$id-update' style='text-align: center;' value='$room'>");

			$row_interior .= surrTags('td', "<!--suppress XmlInvalidId --><input name='update' type='submit' form='$id-update' value='Update'>", "class='no-print'");

			// Define form then add table row (wrap row interior by table row)
			$row = surrTags('tr',
				"<form id='$id-update' method='post'></form>"
				. "<form id='$id-remove' method='post'></form>"
				. $row_interior);

			echo $row;
		}
	}
	?>
</table>
