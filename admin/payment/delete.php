<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/checks.php";
checkPerms(OFFICER);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";

$deleted = null;
if (isset($_POST['delete']) && !is_null(getDetail('payment_details', 'payment_id', 'payment_id', $_POST['payment_id']))) { // If form is POSTed
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/payment/CUD.php";

	$deleted = deletePayment($_POST['payment_id']); // Delete payment
}

$payment_id = null;
if(isset($_GET['payment_id'])) {
	if (!is_null(getDetail('payment_details', 'payment_id', 'payment_id', $_GET['payment_id'])))
		$payment_id = $_GET['payment_id'];
}
?>

<html lang="en">
<h2><u>Delete Payment</u></h2>

<form method="get">
    <label for="payment_id"><i>Search Payment ID:</i></label>
    <input id="payment_id" name="payment_id" type="search">
    <input id="search" type="submit" value="Search">
</form>

<hr>

<form method="post" action="delete.php">
    <fieldset>
        <legend><b>Payment Information</b></legend>

        <label for="payment_id">Payment ID:</label>
        <input id= "payment_id" type="text" required
               value="<?php echo $payment_id ?>"
               disabled>
        <input name="payment_id" type="hidden" value="<?php echo $payment_id ?>"><br>
        <br>
        <label for="cost">Cost:</label>
        $<input id= "cost" name="cost" type="number" step="0.01" required
                value="<?php if (!is_null($payment_id)) echo sprintf('%01.2f', getDetail('payment_details', 'cost', 'payment_id', $payment_id)) ?>"
                disabled><br>
        <br>
        <label for="info">Information:</label>
        <br>
        <textarea id="info" name="info" rows="10" cols="50" required
                  disabled><?php echo getDetail('payment_details', 'info', 'payment_id', $payment_id) ?></textarea><br>
        <br>
    </fieldset><br>

    <h3><u><i>Read</i>, Before You Click (Delete):</u></h3>
    <ul>
        <li>Payment Details will be Deleted</li>
        <li>Payment Transactions will be Deleted</li>
    </ul>
    <?php
    if (!is_null($payment_id))
        echo "\n<input id='delete' name='delete' type='submit' value='Delete Payment'>\n";
    ?>
</form>
</html>

<?php
if (!is_null($deleted)) {
	echo $deleted ? "<p style=\"color:green;\">Successfully deleted.</p>\n" :
		"<p style=\"color:red;\">Failed to delete!</p>\n";
}

if (is_null($payment_id) && isset($_GET['payment_id']))
	echo "<i class='rainbow'>Payment not found!</i>\n";
