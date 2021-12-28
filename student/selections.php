<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safeStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(STUDENT_PERMS);

// Update process
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

$updated = null;
if (isset($_POST['comp'])) {    // Process POST update
	$updated = toggleSelection($_POST['comp'], $_SESSION['id']);

	refresh();
}

// View form (using correct ID)
$id = $_SESSION['id'];
if (isset($_GET['id'])) {
	$rankComp = compareRank($_SESSION['id'], $_GET['id'], true);

	if (!is_null($rankComp)) {
		if ($rankComp)
			$id = $_GET['id'];
		else
			die("<p style='color:red;'>You do not have the required permissions!</p>\n");
	}
}
?>

<title>MAO | Selections</title>

<h2 style="margin: 6px;"><u>Competition Selections</u></h2>

<form style="margin: 6px; display: inline-block;" class="filled border">
    <fieldset>
        <legend><i>Account Information</i></legend>

        <label for="id">ID:</label>
        <input id="id" type="text" size="7"
               value="<?php echo $id; ?>"
               disabled><br>

        <label for="first_name">First Name:</label>
        <input id="first_name" type="text" size="15"
               value="<?php echo getAccountDetail('people', 'first_name', $id); ?>"
               disabled><br>

        <label for="last_name">Last Name:</label>
        <input id="last_name" type="text" size="15"
               value="<?php echo getAccountDetail('people', 'last_name', $id); ?>"
               disabled><br>

        <label for="mu_student_id">Mu Student ID:</label>
        <input id="mu_student_id" type="text" size="3"
               value="<?php echo getAccountDetail('competitor_info', 'mu_student_id', $id); ?>"
               disabled><br>

        <label for="division">Division:</label>
        <input id="division" type="text" size="13"
               value="<?php echo DIVISIONS[getAccountDetail('competitor_info', 'division', $id)]; ?>"
               disabled>
    </fieldset>
</form><br>

<p style='color: violet;'><i><b>Note:</b> By selecting to go to a competition, you are agreeing to pay for the
        associated competition fee.</i></p>


<table>
    <tr>
        <th>Competition</th>
        <th>Date(s)</th>
        <th>Description</th>
        <th>Selected</th>
        <th>Registered</th>
        <th>Payment Status</th>
        <th>Forms</th>
        <th>Bus</th>
        <th>Room</th>
    </tr>

	<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

	$sql_conn = getDBConn();

	$stmt = $sql_conn->prepare("SELECT c.competition_name AS comp, c.description AS comp_desc, c.start_date, c.end_date,  (cs.unique_id IS NOT NULL) AS is_selected FROM competitions c LEFT OUTER JOIN competition_selections cs ON c.competition_name = cs.competition_name AND cs.id = ? ORDER BY c.start_date, c.end_date, c.competition_name");
	$stmt->bind_param('s', $id);
	$stmt->bind_result($comp, $comp_desc, $start_date, $end_date, $is_selected);
	$stmt->execute();

	while ($stmt->fetch()) {
		$row_interior = surrTags('td', $comp);

		$start_date_str = formatToUSDate($start_date);
		$end_date_str = formatToUSDate($end_date);
		$row_interior .= surrTags('td', "<b>Start:</b>&nbsp;$start_date_str<br><b>End:</b>&nbsp;$end_date_str", 'style="text-align: right;"');

		$row_interior .= surrTags('td', $comp_desc, 'style="text-align: left;"');

		$row_interior .= surrTags('td',
			"<form method='post'>" .
			"<input name='comp' type='hidden' value='$comp'>" .
			"<input type='checkbox' " . ($is_selected ? 'checked' : '') . " onchange='this.form.submit()'>" .
			"</form>",
			'style="text-align: center;"');

		$in_comp = inComp($comp, $id);
		$row_interior .= surrTags('td', '', 'style="background-color: ' . ($in_comp ? 'lightgreen' : '#ff6666') . ';"');

		// Paid
		$paid_text = '';
		$paid_color = 'black';
		if (!is_null($payment_id = getAssociatedCompInfo($comp, 'payment_id'))) {
			// Paid Color
			if (isCompPaid($id, $comp))
				$paid_color = 'lightgreen';
			else
				$paid_color = '#ff6666';

			// Paid Text
			$price = formatMoney(getDetail('payment_details', 'price', 'payment_id', $payment_id));
			$due_date = getDetail('payment_details', 'due_date', 'payment_id', $payment_id);

			$paid_text = "<b>$price</b><br><b>Due:</b> <input type='date' value='$due_date' style='text-align: center; background-color: $paid_color;' readonly required>";
		}
		$row_interior .= surrTags('td', $paid_text, "style='background-color: $paid_color; text-align: center;'");

		// Forms
		$forms_color = 'black';
		if ($in_comp && getAssociatedCompInfo($comp, 'show_forms')) {
			if (areFormsCollected($id, $comp))
				$forms_color = 'lightgreen';
			else
				$forms_color = '#ff6666';
		}
		$row_interior .= surrTags('td', '', "style='background-color: $forms_color;'");

		// Bus
		$bus = '';
		$bus_color = 'black';
		if ($in_comp && getAssociatedCompInfo($comp, 'show_bus')) {
			$bus_color = '';

			$bus = getBus($id, $comp);
		}

		$row_interior .= surrTags('td', $bus, 'style="text-align: center; ' . (!empty($bus_color) ? 'background-color: black;' : '') . '"');

		// Room
		$room = '';
		$room_color = 'black';
		if ($in_comp && getAssociatedCompInfo($comp, 'show_room')) {
			$room_color = '';

			$room = getRoom($id, $comp);
		}

		$row_interior .= surrTags('td', $room, 'style="text-align: center; ' . (!empty($room_color) ? 'background-color: black;' : '') . '"');

		echo surrTags('tr', $row_interior);
	}

	$sql_conn->close();
	?>
</table>
