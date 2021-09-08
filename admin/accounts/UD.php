<?php

function deleteAccount($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$tables = array('accounts', 'competition_approvals', 'competition_forms', 'competition_selections', 'competitor_info',
		'login', 'parents', 'people', 'schedules', 'transactions');

	$result = true;
	foreach ($tables as $table) {
		/** @noinspection SqlResolve */
		$delete_table_stmt = $sql_conn->prepare(
			"DELETE FROM $table WHERE id = ?");

		$delete_table_stmt->bind_param('s', $id);

		$result = ($result & $delete_table_stmt->execute());
	}

	// Logout if is current user
	if ($_SESSION['id'] == $id)
		session_destroy();

	return $result;
}
