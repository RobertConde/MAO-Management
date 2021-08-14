<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
stylesheet();

$row = 1;
if (($handle = fopen($_SERVER['DOCUMENT_ROOT'] . "RESOURCES/import.csv", "r")) !== FALSE) {
	echo "<table>";
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$num = count($data);
		$row++;

		$id = $data[0];
		$first_name = $data[1];
		$middle_initial = $data[2];
		$last_name = $data[3];
		$email = $data[4];
		$phone = $data[5];
		$division = $data[6];
		$graduation_year = 0;

		echo "<tr>";
		$result = registerAccount($id, $first_name, $middle_initial, $last_name,
		$graduation_year, $email, $phone, '');

		if (!$result)
			echo "<td>BAD</td>";

		for ($c=0; $c < $num; $c++) {
			echo "<td>" . $data[$c] . "</td>";
		}
		echo "</tr>";
	}
	fclose($handle);
	echo "</table>";
}
