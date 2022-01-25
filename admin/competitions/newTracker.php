<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

// Get competition name if sent and is valid
$comp = null;
$sort_by = 'Name';
if (isset($_GET['comp_name'])) { // Check if `comp_id` was sent
	// Check if sent `comp_id` is valid
	if (!is_null(getDetail('competitions', 'competition_name', 'competition_name', $_GET['comp_name']))) {
		$comp = $_GET['comp_name'];

		if (isset($_GET['sort_by']))
			$sort_by = $_GET['sort_by'];
	}
}
$pay_id = getAssociatedCompInfo($comp, 'payment_id');

// Fields to show
$show_forms = getAssociatedCompInfo($comp, 'show_forms');
$show_bus = getAssociatedCompInfo($comp, 'show_bus');
$show_room = getAssociatedCompInfo($comp, 'show_room');

// Add student if is sent
$add_id = getSelectID('POST');
if (!is_null($add_id)) {
	addToComp($comp, $add_id);
	refresh();
}

// Remove competitor from competition data for comp
$remove_result = null;
if (isset($_POST['remove'])) {
	$remove_id = $_POST['id'];

	$remove_result = removeFromComp($comp, $remove_id);
	refresh();
}

// Update row of competitor competition data
$update_result = null;
if (isset($_POST['update'])) {
	$update_id = $_POST['id'];

	$update_forms = $show_forms && isset($_POST['forms']);
	$update_bus = ($_POST['bus'] ?? '');
	$update_room = ($_POST['room'] ?? '');

	$updateCompData = updateCompData($comp, $update_id, $update_forms, $update_bus, $update_room);

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	$update_paid_status = isset($_POST['paid']);
	$setTransactionStatus = setTransaction($update_id, $pay_id, 1, ($update_paid_status ? 1 : 0), '');   // TODO: Integrate "Competition Fee" (not a generic payment) so that it is either paid or not

	$update_result = ($updateCompData && $setTransactionStatus);
	redirect(currentURL()); // Prevents go-back "submit form again" prompt by re-requesting the page through a GET request
}
?>

<title>MAO | Tracker</title>

<h2 style="text-align: center; margin: 6px;"><u>Competition Tracker</u></h2>

<!--    Competition Selector    -->
<form id="comp-stats" method="get" action="<?php echo currentURL(false); ?>"></form>

<form method="get" style="text-align: center; margin: 6px;" class="filled border">
    <fieldset>
        <legend><b>Competition</b></legend>

        <!--suppress HtmlFormInputWithoutLabel -->
        <select name="comp_name" onchange="this.form.submit()">
            <option selected disabled hidden></option>
			<?php
			$sql_conn = getDBConn();

			$comps_stmt = $sql_conn->prepare("SELECT competition_name AS comp_id FROM competitions ORDER BY start_date, end_date;");
			$comps_stmt->bind_result($curr_comp_id);
			$comps_stmt->execute();

			// Add competition options and select the selected competition
			while ($comps_stmt->fetch()) {
				$curr_selected_status = ($curr_comp_id == $comp ? 'selected' : '');

				echo "<option value='$curr_comp_id' $curr_selected_status>$curr_comp_id</option>";
			}
			?>
        </select>

        <input type="submit" form="comp-stats" value="📊">

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

<form id="sort-form" method="get">
    <input type="hidden" name="comp_name" value="<?php echo $comp; ?>">
</form>

<form method="post" action="<?php echo relativeURL('admin/bubbles/createPDF?ref='), currentURL(); ?>"
      class="filled border no-print" style="text-align: center;"
	<?php if (is_null($comp)) echo 'hidden'; ?>>

    <fieldset style="padding: 6px;">
        <legend><i>Actions</i></legend>

        <!--        OPTIONS         -->
        <u>Options</u><br>

        <b><i>Sort By:</i></b>
        <!--suppress HtmlFormInputWithoutLabel -->
        <select name="sort_by" form="sort-form" onchange="this.form.submit()">
			<?php
			/** @noinspection PhpUndefinedVariableInspection */
			foreach ($SORT_OPTIONS as $curr_sort_option) {
				$curr_selected_status = ($curr_sort_option == $sort_by ? 'selected' : '');

				echo "<option value='$curr_sort_option' $curr_selected_status>$curr_sort_option</option>";
			}
			?>
        </select><br>
        <br>

        <!--        ACTIONS         -->
        <u>Reports</u><br>

        <input type="submit" form="comp-selections" value="Selections [<?php echo numUnaddedSelections($comp); ?>]">
        <input type="submit" form="comp-posting" value="Posting">
        <input type="submit" form="comp-checkoff" value="Checkoff List"><br>
        <br>

        <u>Other Actions</u><br>

        <!--        Person Selector         -->
		<?php personSelect('Add'); ?>
        <br>

        <!--        Bubble Sheet Stuff          -->
		<?php
		// TODO: Make this better, this is really, really bad!
		// Information for bubble sheet creator
		if (!is_null($comp)) {
			// Competition name for bubble sheet creator
			echo "<input name='test' type='hidden' value='$comp'>";

			// Selected students IDs for bubble sheet creator
			$select_studs_query =
				"SELECT cd.id
					FROM competition_data cd
					INNER JOIN people p ON p.id = cd.id
                    INNER JOIN competitor_info ci ON ci.id = cd.id
					WHERE cd.competition_name = ? AND ci.division != 0
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
    </fieldset>
</form><br class="no-print">

<table class="border filled" <?php if (is_null($comp)) echo 'hidden'; ?>>
    <tr>
        <th class="no-print">❌</i></th>
        <th><?php echo getCompCount($comp); ?></th>
        <th>ID</th>
        <th>Name</th>
        <th>Grade</th>
        <th>Division</th>
        <th <?php if (is_null($pay_id)) echo 'hidden'; ?>>Paid</th>
        <th <?php if (!$show_forms) echo 'hidden'; ?>>Forms</th>
        <th <?php if (!$show_bus) echo 'hidden'; ?>>Bus</th>
        <th <?php if (!$show_room) echo 'hidden'; ?>>Room</th>
        <th class="no-print">Action</th>
    </tr>

	<?php
	//TODO: Move functions out

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

	// TODO: I REALLY don't like this! I REALLY DON'T!!!
	function getCheckbox($comp, $payment_id, $data_name, $id, $value = null): string
	{
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
		$sql_conn = getDBConn();

		if ($data_name == 'paid') {
			if (is_null($payment_id)) {
				$checkbox_status = 'checked';
			} else {
				require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

				$checkbox_status = (isPaid($id, $payment_id) ? 'checked' : '');
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
		/** @noinspection PhpUndefinedVariableInspection */
		$approved_IDs_stmt = $sql_conn->prepare(
			"SELECT
                        cd.id,
                        CONCAT(p.last_name, ', ', p.first_name) AS name,
                        cd.forms,
                        cd.bus,
                        cd.room
                    FROM competition_data cd
                    INNER JOIN people p ON cd.competition_name = ? AND p.id = cd.id
                    JOIN competitor_info ci ON ci.id = cd.id
                    ORDER BY " . SORT_ORDER_BY[$sort_by]);
		$approved_IDs_stmt->bind_param('s', $comp);
		$approved_IDs_stmt->bind_result($id, $name, $forms, $bus, $room);
		$approved_IDs_stmt->execute();

		$index = 1;
		$rows = "";
		while (!is_null($person = $approved_IDs_stmt->fetch())) {
			// Table data
			$row_interior =
				"<div id='div-$id'>"
				. "<input name='id' type='hidden' form='$id-update' value='$id'>"
				. "<input name='id' type='hidden' form='$id-remove' value='$id'>"
				. "</div>";

			$row_interior .= surrTags('td', "<!--suppress XmlInvalidId --><input name='remove' type='checkbox' form='$id-remove' onchange='this.form.submit()'>", 'class="no-print" style="text-align: center;"');

			$row_interior .= surrTags('td', $index++, 'style="text-align: right;"');

			$row_interior .= surrTags('td', makeLink($id, "student/info?select-id=$id", '_blank'));

			$selected = (isSelected($comp, $id) ? "background-color: lightgreen;" : '');
			$row_interior .= surrTags('td', $name, "style='text-align: left; $selected'");

			$row_interior .= surrTags('td', formatOrdinalNumber(getGrade($id)), 'style="text-align: center;"');

			$row_interior .= surrTags('td', DIVISIONS[getAccountDetail('competitor_info', 'division', $id)]);

			// If the competition doesn't have an assigned payment, don't show the 'Paid' columns
			if (!is_null($pay_id))
				$row_interior .= surrTags('td', getCheckbox($comp, $pay_id, 'paid', $id), 'style="text-align: center;"');

			if ($show_forms)
				$row_interior .= surrTags('td', getCheckbox($comp, $pay_id, 'forms', $id, $forms), 'style="text-align: center;"');

			if ($show_bus)
				$row_interior .= surrTags('td', "<input name='bus' type='number' min='0' form='$id-update' style='width: 35px; text-align: center;' value='$bus'>");

			if ($show_room)
				$row_interior .= surrTags('td', "<input name='room' type='text' size='2' form='$id-update' value='$room'>");

			$row_interior .= surrTags('td', "<input name='update' type='submit' form='$id-update' value='Update'>", "class='no-print'");

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
