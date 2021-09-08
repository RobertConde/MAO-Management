<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(ADMIN);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

$row = 1;
if (($handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/RESOURCES/import.csv", "r")) !== FALSE) {
	echo "<table>";

	fgetcsv($handle); // Skip header;
	while (($data = fgetcsv($handle)) !== FALSE) {
		$num = count($data);
		$row++;

		$id = sprintf("%07d",$data[0]);
		$first_name = $data[1];
		$middle_initial = $data[2];
		$last_name = $data[3];
		$email = $data[4];
		$phone = $data[5];
		$division = $data[6];
		$graduation_year = $data[7];

		echo "<tr>";
		$result = registerAccount($id, $first_name, $middle_initial, $last_name,
		$graduation_year, $email, $phone, '');

		if (!$result)
			echo "<td>BAD</td>";
		else
			echo "<td></td>";

		for ($c=0; $c < $num; $c++) {
			echo "<td>" . $data[$c] . "</td>";
		}
		echo "</tr>";
	}

	fclose($handle);
	echo "</table>";
}
