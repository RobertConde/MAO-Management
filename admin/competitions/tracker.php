<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";

$comp_id = null;
$payment_id = null;
if (isset($_GET['comp']) && $_GET['comp'] != '') {
	$comp_id = $_GET['comp'];
	$payment_id = getDetail('competitions', 'payment_id', 'competition_id', $comp_id);
}
$comp_id_valid = (!is_null($comp_id) && getDetail('competitions', 'competition_id', 'competition_id', $comp_id) == $comp_id);

// Process toggles
$post_id = null;
if (isset($_POST['id']))
	$post_id = $_POST['id'];

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
/* TODO: Error handling (x4) */
if (isset($_POST['approved'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

	toggleApproved($post_id, $comp_id);
	redirect(currentURL());

} else if (isset($_POST['paid'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	toggleTransaction($post_id, $payment_id);
	redirect(currentURL());

} else if (isset($_POST['forms'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

	toggleFormStatus($post_id, $comp_id);
	redirect(currentURL());

}

stylesheet();
navigationBar();
?>

<title>DB | Tracker</title>

<h2 style="text-align: center; margin: 6px;"><u>Competition Tracker</u></h2>

<form method="get" style="text-align: center; margin: 6px;">
    <fieldset>
        <legend><b>Competition</b></legend>

        <select name="comp" onchange="this.form.submit()">
            <option selected disabled hidden></option>
			<?php
			$sql_conn = getDBConn();

			$comps_query = "SELECT competition_id FROM competitions";

			$comps_result = $sql_conn->query($comps_query);

			while (!is_null($row = $comps_result->fetch_assoc())) {
				echo "<option value=\"" . $row['competition_id'] . "\" "
					. ($comp_id == $row['competition_id'] ? ' selected' : '')
					. ">" . $row['competition_id'] . "</option>";
			}
			?>
        </select>
    </fieldset>
</form>

<form method="post" action="../bubbles/createPDF.php?ref=<?php echo currentURL(); ?>"
      style="text-align: center; margin: 6px;" <?php if (!$comp_id_valid) echo 'hidden'; ?>>
    <fieldset style="padding: 6px;">
        <legend><i>Actions</i></legend>

		<?php
		if ($comp_id_valid) {
			echo '<input name="test" type="hidden" value="' . $comp_id . '">';

			$selected_query = "SELECT cs.id FROM competition_approvals cs JOIN people p ON cs.id = p.id WHERE cs.competition_id = '$comp_id' ORDER BY p.last_name, p.first_name";

			$selected_result = $sql_conn->query($selected_query);

			while (!is_null($row = $selected_result->fetch_assoc()))
				echo '<input name="selected[]" type="hidden" value="' . $row['id'] . '">';
		}
		?>
        <input type="submit" value="Create Bubble Sheets">
    </fieldset>
</form>

<?php

if ($comp_id_valid) {
	/* BEWARE OF SQL INJECTION */
	$sql_query = "SELECT
		p.id,
	    p.last_name,
	    p.first_name,
	    ci.division,
        EXISTS (SELECT * FROM competition_selections cs WHERE cs.competition_id = '$comp_id' AND cs.id=p.id) AS selected,
	    EXISTS (SELECT * FROM competition_approvals cs WHERE cs.competition_id = '$comp_id' AND cs.id=p.id) AS approved,
	    EXISTS (SELECT * FROM transactions t WHERE t.payment_id = '$payment_id' AND t.id=p.id) AS paid,
	    EXISTS (SELECT * FROM competition_forms t WHERE t.competition_id = '$comp_id' AND t.id=p.id) AS forms
	FROM people p
    JOIN competitor_info ci
    ON p.id = ci.id
	ORDER BY last_name, first_name, division, id;";

	$sql_result = $sql_conn->query($sql_query);

	$table_interior = "";

	$header_interior = "";
	$header_obj_array = $sql_result->fetch_fields();
	foreach ($header_obj_array as $obj) {
		$header_interior .= surrTags('th', $obj->name, "style=\"text-align: center;\"");
	}
	$table_interior .= surrTags('tr', $header_interior);

	while (($sql_row = $sql_result->fetch_row()) != null) {
		$len = count($sql_row);
		$curr_id = $sql_row[0];

		$row_interior = "";
		for ($i = 0; $i < $len; ++$i) {
			$elem = $sql_row[$i];
			if ($i < $len - 4)
				$row_interior .= surrTags('td', $elem,
					"style='text-align: center;'");
			else if ($i == $len - 4)
				$row_interior .= surrTags('td', '', "style='background-color:" . ($elem == '1' ? 'green' : 'red') . ";'");
			else
				$row_interior .= surrTags('td',
					"<form method='post' class='center'>" .
					"<input name='id' type='hidden' value='$curr_id'>" .
					"<input name='" . $header_obj_array[$i]->name . "' type='hidden'>" .
					"<input type='checkbox' " . ($elem == '1' ? 'checked' : 'unchecked') . " onchange='this.form.submit()'>" .
					"</form>");
		}

		$table_interior .= surrTags('tr', $row_interior) . "\n";
	}

	echo surrTags('table', $table_interior, "class=\"center\"");
}
?>
