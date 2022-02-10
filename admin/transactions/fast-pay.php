<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);
?>
    <title>💸 Fast Pay! 🏃💨</title>

    <h2><u>💸 Fast Pay! 🏃💨</u></h2>
    <form method="post" class="filled border" style="display: inline-block;">
        <fieldset>
            <label for="payment_id">Payment ID:</label>
            <select id="payment_id" name="payment_id" style="margin-bottom: 6px;" required>
                <option selected disabled hidden></option>

				<?php
				require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
				$sql_conn = getDBConn();

				$payments_query = "SELECT payment_id FROM payment_details";

				$payments_result = $sql_conn->query($payments_query);

				while (!is_null($row = $payments_result->fetch_assoc())) {
					$curr_pay_id = $row['payment_id'];

					echo "<option value='$curr_pay_id'>$curr_pay_id</option>";
				}
				?>
            </select>

            <hr>

            <label for="id-list"><i>List IDs that should be marked as paid (one per line):</i></label>
            <br>
            <div style="text-align: center;">
                <textarea id="id-list" name="id-list" rows="25" cols="8" required></textarea><br>
                <br>

                <input name="fast-pay" type="submit" value="Mark as Paid">
            </div>
        </fieldset>
    </form>

<?php
if (isset($_POST['fast-pay'])) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	$payment_id = $_POST['payment_id'];
	$ids_to_mark_paid = explode("\n", str_replace("\r", "", $_POST['id-list']));

	foreach ($ids_to_mark_paid as $curr_id) {
		if (!setTransaction($curr_id, $payment_id, null, 1, null))
			echo "Error marking payment as paid for payment ID `$payment_id` for ID `$curr_id`!";
	}
}
