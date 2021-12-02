<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safeStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);

$post_id_is_real = isset($_POST['payment_id']) && !is_null(getDetail('payment_details', 'payment_id', 'payment_id', $_POST['payment_id']));

$created = null;
$updated = null;
$deleted = null;
if (isset($_POST['create'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/payments/CUD.php";

	createPayment(  // Create payment
		$_POST['payment_id'],
		$_POST['due_date'],
		$_POST['price'],
		$_POST['desc']);

	redirect(currentURL(false) . '?payment_id=' . rawurlencode($_POST['payment_id']));
} else if (isset($_POST['update']) && $post_id_is_real) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/payments/CUD.php";

	$updated = updatePayment(  // Update payment
		$_POST['payment_id'],
		$_POST['due_date'],
		$_POST['price'],
		$_POST['desc']);
} else if (isset($_POST['delete']) && $post_id_is_real) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/payments/CUD.php";

	$deleted = deletePayment($_POST['payment_id']); // Delete payment
}

$payment_id = null;
if (isset($_GET['payment_id'])) {
	if (empty($_GET['payment_id']))  // Blank search
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

    <form method="get" class="filled border" style="text-align: center; margin: 6px;">
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
					echo "<option value='" . $row['payment_id'] . "' "
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

<?php
$due_date = getDetail('payment_details', 'due_date', 'payment_id', $payment_id);
$price = (!is_null($payment_id) ? sprintf('%01.2f', getDetail('payment_details', 'price', 'payment_id', $payment_id)) : '');
$description = getDetail('payment_details', 'description', 'payment_id', $payment_id);
?>

    <form method="post" class="filled border" style="text-align: center;">
        <fieldset>
            <h4 style="margin-top: 2px;"><u>Information</u></h4>

            <div style="display: inline-block; text-align: left">
                <label for="payment_id">Payment ID:</label>
                <input id="payment_id" name="payment_id" type="text" required
                       value="<?php echo $payment_id; ?>"
					<?php if (!is_null($payment_id)) echo 'disabled'; ?>>
				<?php
				if (!is_null($payment_id))
					echo "<input name='payment_id' type='hidden' value='$payment_id'>";
				?><br>

                <label for="due_date">Due Date:</label>
                <input id="due_date" name="due_date" type="date"
                       value="<?php echo $due_date; ?>"><br>

                <label for="price">Price:</label>
                $<input id="price" name="price" type="number" step="0.01" size="4" required
                        value="<?php echo $price; ?>"><br>
            </div>
            <br>

            <h4><u>Description</u></h4>
            <!--suppress HtmlFormInputWithoutLabel -->
            <textarea id="desc" name="desc" rows="10" cols="50"><?php echo $description; ?></textarea><br>
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

if (isset($_GET['payment_id']) && is_null($payment_id))
	echo "<i class='rainbow'>Payment not found!</i>\n";
