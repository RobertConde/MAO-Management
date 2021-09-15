<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(ADMIN);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

$row = 1;
if (($handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/import.csv", "r")) !== FALSE) {
	echo "<table>";

	fgetcsv($handle); // Skip header;
	while (($data = fgetcsv($handle)) !== FALSE) {
		$num = count($data);
		$row++;

		$id = sprintf("%07d",$data[0]);

		$last_name = $data[1];
		$middle_initial = $data[2];
		$first_name = $data[3];

		$division = $data[4];

		$email = $data[5];
		$phone = $data[6];
		$address = $data[7];

		$parent_name = $data[8];
		$parent_phone = $data[9];
		$alternate_phone = $data[10];
		$parent_email = $data[11];
		$alternate_ride = $data[12];

		$moodle = $data[13];
		$alcumus = $data[14];
		$webwork = $data[15];

		$graduation_year = $data[16];

		echo "<tr>";
		$register = registerAccount($id, $first_name, $middle_initial, $last_name,
		$graduation_year, $email, $phone, $address);

		$update1 = updateParent($id, $parent_name, $parent_email, $parent_phone, $alternate_phone, $alternate_ride);

		$update2 = updateAccounts($id, $moodle, $alcumus, $webwork);

		$update3 = updateCompetitorInfo_Student($id, $division);

		if (!$register)
			echo "<td>❌</td>";
		else
			echo "<td></td>";

		if (!$update1)
			echo "<td>❌</td>";
		else
			echo "<td></td>";

		if (!$update2)
			echo "<td>❌</td>";
		else
			echo "<td></td>";

		if (!$update3)
			echo "<td>❌</td>";
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
