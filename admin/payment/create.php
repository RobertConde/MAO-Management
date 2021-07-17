<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/checks.php";
checkPerms(OFFICER);
?>

<html lang="en">


<h2><u>Create Payment</u></h2>
<form method="post">
    <fieldset>
        <legend><b>Payment Information</b></legend>

        <label for="payment_id">Payment ID:</label>
        <input id= "payment_id" name="payment_id" type="text" required><br>
        <br>
        <label for="cost">Cost:</label>
        $<input id= "cost" name="cost" type="number" step="0.01" required><br>
        <br>
        <label for="info">Information:</label>
        <br>
        <textarea id="info" name="info" rows="10" cols="50" required></textarea><br>
        <br>
    </fieldset><br>
    <br>
    <input id="create" name="create" type="submit" value="Create Payment">
</form>
</html>

<?php
if (isset($_POST['create'])) { // If form is POSTed
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/payment/CUD.php";

	$created = createPayment(  // Create payment
		$_POST['payment_id'],
		$_POST['cost'],
		$_POST['info']);

	if ($created)
		echo "<p style=\"color:green;\">Successfully created.</p>\n";
	else
		echo("<p style=\"color:red;\">Failed to create!</p>\n");
}
