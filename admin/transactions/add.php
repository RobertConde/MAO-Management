<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();
noNavBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);

// Get ID
if (isset($_GET['id']))
	$id = $_GET['id'];
else
	die('<script>window.close();</script>');

// Process actions
if (isset($_POST['add_id'])) {
	$addID = $_POST['add_id'];
	$addPaymentIDs = $_POST['add_payment_ids'] ?? array();

//	echo $addID, '<br>', implode($addPaymentIDs);

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";
	foreach ($addPaymentIDs as $currPaymentID)
		setTransaction($addID, $currPaymentID, 0, 0, '');

	die('<script>window.close();</script>');
}
?>

<title>MAO | Add Transaction(s) [ID: <?php echo $id; ?>]</title>

<h2 style="margin: 6px;"><u>Add Transaction(s)</u></h2>

<form style="margin: 6px; display: inline-block;" class="filled border">
    <fieldset>
        <legend><i>Account Information</i>
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
</form>

<form id="add_form" method="post">
    <input name="add_id" type="hidden" value="<?php echo $id; ?>">
</form>

<table class="border filled">
    <tr>
        <th>Add</th>
        <th>Payment</th>
        <th>Description</th>
        <th>Price</th>
        <!--        <th colspan="2">Quantity</th>-->
        <!--        <th>Total<br>Owed</th>-->
        <!--        <th>Modifiers</th>-->
        <!--        <th>Actions</th>-->
    </tr>

	<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$transactions_stmt = $sql_conn->prepare("SELECT pd.payment_id, pd.description, pd.price FROM payment_details pd WHERE NOT EXISTS (SELECT * FROM transactions t WHERE t.id = ? AND t.payment_id = pd.payment_id)");
	$transactions_stmt->bind_param('s', $id);
	$transactions_stmt->bind_result($payment_id, $desc, $price);

	if (!$transactions_stmt->execute())
		die("Error occurred getting transactions: $transactions_stmt->error");

	while ($transactions_stmt->fetch()) {
		$row_interior = surrTags('td', "<input name='add_payment_ids[]' form='add_form' type='checkbox' value='$payment_id'>", 'style="text-align: center;"');

		$row_interior .= surrTags('td', "<b>$payment_id</b>");

		$row_interior .= surrTags('td', $desc);

		$row_interior .= surrTags('td', formatMoney($price));

		echo surrTags('tr', $row_interior);
	}
	?>
</table>

<input form="add_form" type="submit" value="Add Transactions(s)">