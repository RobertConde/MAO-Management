<?php
if (isset($_GET['table']) && isset($_GET['order_by'])) {
	$table = $_GET['table'];
	$order_by = ($_GET['order_by'] == "" ? '1' : $_GET['order_by']);

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$conn = getDBConn();

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
	$csv_filename = 'db_export_' . getCurrentDateTime() . '.csv';

	// create var to be filled with export data
	$csv_export = '';

	// query to get data from database
	$query = $conn->query("SELECT * FROM $table ORDER BY $order_by");


	// create line with field names
	$fields = $query->fetch_fields();
	foreach ($fields as $field)
		$csv_export .= $field->name . ',';
	$csv_export .= "\n";

	while ($row = $query->fetch_row()) {
		// create line with field values
		foreach ($row as $value)
			$csv_export .= '"' . $value . '",';
		$csv_export .= "\n";
	}

	// Export the data and prompt a csv file for download
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=$csv_filename");
	echo($csv_export);
}
