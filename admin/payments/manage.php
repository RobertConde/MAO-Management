<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
startSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBar();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER);

$post_id_is_real = isset($_POST['payment_id']) && !is_null(getDetail('payment_details', 'payment_id', 'payment_id', $_POST['payment_id']));

$created = null;
$updated = null;
$deleted = null;
if (isset($_POST['create'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/payments/CUD.php";

	$created = createPayment(  // Create payment
		$_POST['payment_id'],
		$_POST['cost'],
		$_POST['info']);

	if ($created)
		echo("<p style=\"color:red;\"><b>Redirecting!</b></p>\n" .
			"<meta http-equiv=\"refresh\" content=\"2; url=" . currentURL(false) . "?payment_id=" . $_POST['payment_id'] . "\" />");
} else if (isset($_POST['update']) && $post_id_is_real) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/payments/CUD.php";

	$updated = updatePayment(  // Update payment
		$_POST['payment_id'],
		$_POST['cost'],
		$_POST['info']);
} else if (isset($_POST['delete']) && $post_id_is_real) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/payments/CUD.php";

	$deleted = deletePayment($_POST['payment_id']); // Delete payment
}

$payment_id = null;
if (isset($_GET['payment_id'])) {
	if ($_GET['payment_id'] == '')  // Blank search
		redirect(currentURL(false));
	else {
		$get_id_is_real = !is_null(getDetail('payment_details', 'payment_id', 'payment_id', $_GET['payment_id']));

		if ($get_id_is_real)
			$payment_id = $_GET['payment_id'];
	}
}
?>

    <title>DB | Payments</title>

<h2 style="margin: 6px;"><u>Payments</u></h2>

<form method="get" style="margin: 6px;" class="filled border">
    <fieldset>
        <legend><b>Payment</b></legend>

        <!--suppress HtmlFormInputWithoutLabel -->
        <select name="payment_id" onchange="this.form.submit()" style="margin-bottom: 6px;">
            <option selected disabled hidden></option>
			<?php
			$sql_conn = getDBConn();

			$payments_query = "SELECT payment_id FROM payment_details";

			$payments_result = $sql_conn->query($payments_query);

			while (!is_null($row = $payments_result->fetch_assoc())) {
				echo "<option value=\"" . $row['payment_id'] . "\" "
					. ($payment_id == $row['payment_id'] ? 'selected' : '')
					. ">" . $row['payment_id'] . "</option>";
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

        <label for="payment_id">Payment ID:</label>
        <input id="payment_id" name="payment_id" type="text" required
               value="<?php echo $payment_id; ?>"
			<?php if (!is_null($payment_id)) echo 'disabled'; ?>>
		<?php
		if (!is_null($payment_id))
			echo "<input name=\"payment_id\" type=\"hidden\" value=\"$payment_id\">";
		?><br>

        <label for="cost">Cost:</label>
        $<input id="cost" name="cost" type="number" step="0.01" required
                value="<?php if (!is_null($payment_id)) echo sprintf('%01.2f', getDetail('payment_details', 'cost', 'payment_id', $payment_id)); ?>"><br>

        <label for="info" style="margin-top: 0; margin-bottom: 0;"><u>Information</u></label><br>
        <textarea id="info" name="info" rows="10" cols="50"
                  required><?php if (!is_null($payment_id)) echo getDetail('payment_details', 'info', 'payment_id', $payment_id); ?></textarea><br>
        <br>
        <input id="create" name="create" type="submit" value="Create"
               style="color: green; float: left;" <?php if (!is_null($payment_id)) echo 'disabled'; ?>>
        <input id="update" name="update" type="submit" value="Update"
               style="color: blue;" <?php if (is_null($payment_id)) echo 'disabled'; ?>>
        <input id="delete" name="delete" type="submit" value="Delete"
               style="color: red; float: right;" <?php if (is_null($payment_id)) echo 'disabled'; ?>>
    </fieldset>
</form>

<?php
if (!is_null($created)) {
	echo $created ? "<p style=\"color:green;\">Successfully created.</p>\n" :
		"<p style=\"color:red;\">Failed to create!</p>\n";
}

if (!is_null($updated)) {
	echo $updated ? "<p style=\"color:green;\">Successfully updated.</p>\n" :
		"<p style=\"color:red;\">Failed to update!</p>\n";
}

if (!is_null($deleted)) {
	echo $deleted ? "<p style=\"color:green;\">Successfully deleted.</p>\n" :
		"<p style=\"color:red;\">Failed to delete!</p>\n";
}

if (isset($_GET['payment_id']) && is_null($payment_id))
	echo "<i class='rainbow'>Payment not found!</i>\n";
