<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safeStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(STUDENT_PERMS);

// Process actions
$is_viewer_a_student = (getRank() == STUDENT_RANK);
if (isset($_POST['payment_id']) && !$is_viewer_a_student) {
	if ($_POST['id'] != $_SESSION['id'] && getRank($_SESSION['id']) >= OFFICER_PERMS) // Officers and admins can update transactions
		die("<p style='color:red;'>You do not have the required permissions!</p>");

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	if (isset($_POST['update']))
		setTransaction($_POST['id'], $_POST['payment_id'], $_POST['owed'], $_POST['paid'], $_POST['modifiers']);
	else
		archiveTransaction($_POST['id'], $_POST['payment_id']);

	redirect(currentURL());
}

// View form (using correct ID)
$id = $_SESSION['id'];
if (isset($_GET['select-id'])) {
	$select_id = getSelectID();

	if (getRank($id) >= OFFICER_RANK)
		$id = $select_id;
	else
		die("<p style='color:red;'>You do not have the required permissions!</p>");
}
?>

<title>MAO | Transactions</title>

<h2 style="margin: 6px;"><u>Transactions</u></h2>

<?php
if (getRank($_SESSION['id']) >= OFFICER_RANK) {
	personSelectForm();
	personSelect();
	echo '<br>';

	if ($id != $_SESSION['id'])
		echo "<p style='color: violet;'><i><b>Note:</b> You are updating an account that isn't yours, and has a permission rank below you!</i></p>";
}
?>

<form style="margin: 6px; display: inline-block;" class="filled border">
    <fieldset>
        <legend><i>Account</i>
            <i class="fa fa-question-circle" title="Edit this information in update info."></i>
        </legend>

        <label for="id">ID:</label>
        <input id="id" name="id" type="search" pattern="[0-9]{7}" size="7" value="<?php echo $id; ?>" disabled><br>

        <label for="first_name">First Name:</label>
        <input id="first_name" name="first_name" type="text" size="15"
               value="<?php echo getAccountDetail('people', 'first_name', $id); ?>"
               disabled><br>

        <label for="last_name">Last Name:</label>
        <input id="last_name" name="last_name" type="text" size="15"
               value="<?php echo getAccountDetail('people', 'last_name', $id); ?>"
               disabled><br>

        <label for="grade">Grade:</label>
        <input id="grade" name="grade" type="text" size="4" value="<?php echo formatOrdinalNumber(getGrade($id)); ?>"
               disabled>
    </fieldset>

    <fieldset style="text-align: center;" <?php if ($is_viewer_a_student) echo 'hidden'; ?>>
        <legend><i>Actions</i></legend>

        <input type="button"
               onclick='window.open("<?php echo relativeURL("admin/transactions/add?id=$id"); ?>", "_blank", "location=yes,height=500px,width=500,scrollbars=yes,status=yes,menubar=no")'
               value="Add Transaction(s)">
    </fieldset>
</form>
<br>

<table class="border filled">
    <tr>
        <th>Payment</th>
        <th>Due Date</th>
        <th>Description</th>
        <th>Price</th>
        <th colspan="2">Quantity</th>
        <th>Total<br>Owed</th>
        <th>Modifiers</th>
        <th <?php if ($is_viewer_a_student) echo 'hidden'; ?>>Actions</th>
    </tr>

	<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$transactions_stmt = $sql_conn->prepare(
		"SELECT pd.payment_id, pd.due_date, pd.description, pd.price, t.owed, t.paid, t.modifiers
                        FROM payment_details pd
                        JOIN transactions t ON pd.payment_id = t.payment_id
                        WHERE t.id = ?
                        ORDER BY due_date");
	$transactions_stmt->bind_param('s', $id);
	$transactions_stmt->bind_result($payment_id, $due_date, $desc, $price, $owed, $paid, $modifiers);

	if (!$transactions_stmt->execute())
		die("Error occurred getting transactions: $transactions_stmt->error");

	while ($transactions_stmt->fetch()) {
		$total_owed = ($owed - $paid) * $price;
		$readonly_if_student = ($is_viewer_a_student ? 'readonly' : '');

		// Form for update and archive buttons
		$row_interior = "<form id='$payment_id' method='post'></form>" .
			"<input name='id' form='$payment_id' type='hidden' value='$id'>" .
			"<input name='payment_id' form='$payment_id' type='hidden' value='$payment_id'>";

		// Payment
		$row_interior .= surrTags('td', "<b>$payment_id</b>");

		// Due Date
		try {   // TODO: fix
			$datetime_days_left = date_diff(new DateTime('now'), new DateTime($due_date))->days;
		} catch (Exception $e) {
			die("Error occurred finding the difference between two dates: $e");
		}
        
		$datetime_style = '';   // Not paid and has >= three days to pay
		if ($total_owed <= 0)   // Paid
			$datetime_style = 'style="background-color: lightgreen; text-align: left;"';
		else if ($datetime_days_left <= 0)   //  Not paid and is either overdue or due today
			$datetime_style = 'style="background-color: #ff6666; text-align: left;"';
		else if ($datetime_days_left < 3)  // Not paid and due in less than three days
			$datetime_style = 'style="background-color: gold; text-align: left;"';
		$row_interior .= surrTags('td', "<input id='due_date' name='due_date' type='date' value='$due_date' readonly required><br>", $datetime_style);

		// Description
		$row_interior .= surrTags('td', $desc, 'style="text-align: left;"');

		// Price
		$row_interior .= surrTags('td', formatMoney($price));

		// Quantity (Owed & Paid)
		$row_interior .= surrTags('td', "<b>Owed:<br>Paid:</b>", 'style="text-align: right; border-right: none;"');
		$row_interior .= surrTags('td',
			"<input name='owed' form='$payment_id' type='number' min='0' value='$owed' size='1' $readonly_if_student><br>" .
			"<input name='paid' form='$payment_id' type='number' min='0' value='$paid' size='1' $readonly_if_student><br>",
			'style="text-align: left; border-left: none;"');

		// Total Owed
		$total_bg_color = '';
		if ($total_owed > 0)
			$total_bg_color = '#ff6666';
		else if ($total_owed < 0)
			$total_bg_color = 'lightgreen';
		$row_interior .= surrTags('td', formatMoney($total_owed),
			"style='background-color: $total_bg_color; text-align: right;'");

		$row_interior .= surrTags('td', "<input name='modifiers' form='$payment_id' type='text' size='5' value='$modifiers' $readonly_if_student>", 'style="text-align: center;"');

		// Actions (Update & Archive)
		if (!$is_viewer_a_student) {
			$row_interior .= surrTags('td',
				"<input name='update' form='$payment_id' type='submit' value='Update'><br><p></p>" .
				"<input name='archive' form='$payment_id' type='submit' value='Archive'>");
		}

		echo surrTags('tr', $row_interior);
	}
	?>
</table>
