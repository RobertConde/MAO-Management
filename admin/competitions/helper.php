<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

// Get competition name if sent and is valid
$comp = null;
$sort_by = 'Name';
if (isset($_GET['comp'])) { // Check if `comp_id` was sent
	// Check if sent `comp_id` is valid
	if (!is_null(getDetail('competitions', 'competition_name', 'competition_name', $_GET['comp']))) {
		$comp = $_GET['comp'];

		if (isset($_GET['sort_by']))
			$sort_by = $_GET['sort_by'];
	}
}

$pay_id = getAssociatedCompInfo($comp, 'payment_id');

// Fields to show
$show_forms = getAssociatedCompInfo($comp, 'show_forms');

// Add student if is sent
$add_id = getSelectID('POST');
$add_result = null;
if (!is_null($add_id)) {
	addToComp($comp, $add_id);
}
// Remove competitor from competition data for comp
$remove_result = null;
if (isset($_POST['remove'])) {
	$remove_id = $_POST['do-id'];

	$remove_result = removeFromComp($comp, $remove_id);
}

// Update row of competitor competition data
$update_result = null;
if (isset($_POST['update'])) {
	$update_id = $_POST['do-id'];

	$update_forms = $show_forms && isset($_POST['forms']);
	$update_bus = ($_POST['bus'] ?? '');
	$update_room = ($_POST['room'] ?? '');

	$updateCompData = updateCompData($comp, $update_id, $update_forms, $update_bus, $update_room);

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/transactions.php";

	$update_paid_status = isset($_POST['paid']);
	$setTransactionStatus = setTransaction($update_id, $pay_id, 1, ($update_paid_status ? 1 : 0), '');   // TODO: Integrate "Competition Fee" (not a generic payment) so that it is either paid or not

	$update_result = ($updateCompData && $setTransactionStatus);
}
if (!is_null($add_result))
	echo "ADD: ", $add_result ? "OK" : "BAD";
if (!is_null($remove_result))
	echo "REMOVE: ", $remove_result ? "OK" : "BAD";
else if (!is_null($update_result))
	echo "UPDATE: ", $update_result ? "OK" : "BAD";
