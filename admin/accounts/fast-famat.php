<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);
?>

    <title>🗃️ Fast FAMAT! 📇️</title>

    <h2><u>🗃️ Fast FAMAT! 📇️</u></h2>
    <form method="post" action="" enctype="multipart/form-data"
          class="filled border">
        <fieldset>
            <label for="famatInfoCSV"><u>CSV Upload</u></label>
            <input id="famatInfoCSV" name="famatInfoCSV" type="file" required><br>

            <input type="submit" value="Set FAMAT IDs">
        </fieldset>
    </form><br>
    <br>

<?php
// Process upload and redirect to create bubble sheets
$attribute_name = 'famatInfoCSV';
if (isset($_FILES[$attribute_name])) {
	$errors = array();
	$file_name = $_FILES[$attribute_name]['name'];
	$file_size = $_FILES[$attribute_name]['size'];
	$file_tmp = $_FILES[$attribute_name]['tmp_name'];
	$file_type = $_FILES[$attribute_name]['type'];

	$temp_array = explode('.', $_FILES[$attribute_name]['name']);
	$file_ext = strtolower(end($temp_array));

	if ($file_ext != 'csv')
		die("Error: File uploaded must be a CSV!");

	if (empty($errors)) {
		$new_filename = md5(rand());

		// File is in correct place, so start setting FAMAT IDs
		require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

		$checked_fields = false;
		if ($handle = fopen($file_tmp, "r")) {
			while (($row = fgetcsv($handle)) !== false) {
				// Check fields (first row)
				if (!$checked_fields) {
					$fields = $row;
					$row[0] = substr($row[0], 1);

					if ($fields !== array('Last Name', 'First Name', 'FAMAT ID'))   // Make sure the right fields are being used and that they're in the right order (second is not necessary, but helpful for consistency)
						die("Field names are incorrect! They should be as follows: 'Last Name', 'First Name', and 'FAMAT ID'.");

					$checked_fields = true;
					continue;
				}

				$last_name = $row[0];
				$first_name = $row[1];
				$famat_id = $row[2];

				$person_id = getIDByName($last_name, $first_name);
				if (is_null($person_id))
					echo "Couldn't find a <i>unique</i> person named '$last_name, $first_name'!<br>";
				else
					setFAMAT_ID($person_id, $famat_id);

			}
			if (!feof($handle))
				die("Error: unexpected fgets() fail\n");

			fclose($handle);
		}
	}
}