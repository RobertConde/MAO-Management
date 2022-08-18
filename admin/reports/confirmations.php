<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(ADMIN_PERMS);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";


if (isset($_POST['reset_confirmation_id'])) {
	resetConfirmation($_POST['reset_confirmation_id']);
	redirect(currentURL());
}

$sort_by = 'name';
if (isset($_GET['sort_by']))
	$sort_by = $_GET['sort_by'];
?>
<style>
    td {
        border: none;
    }
</style>

<title>MAO | Account Confirmations</title>

<div class="no-print">
    <h2><u>Account Confirmations</u></h2>

    <!--    Sort By Selector    -->
    <form method="get" style="text-align: center; margin: 6px;" class="filled border">
        <fieldset>
            <legend><b>Sort</b></legend>

            <!--suppress HtmlFormInputWithoutLabel -->
            <select name="sort_by" onchange="this.form.submit()">
                <option value="id"
					<?php if ($sort_by == 'id') echo 'selected'; ?>>ID
                </option>
                <option value="name"
					<?php if ($sort_by == 'name') echo 'selected'; ?>>Name
                </option>
                <option value="p.graduation_year DESC"
					<?php if ($sort_by == 'p.graduation_year DESC') echo 'selected'; ?>>Grade
                </option>
                <option value="division"
					<?php if ($sort_by == 'division') echo 'selected'; ?>>Division
                </option>
            </select>
        </fieldset>
    </form>
    <br>

    <button type="button" onClick="window.print()" class="no-print">Print</button>
</div>

<div>
	<?php
	$sql_conn = getDBConn();
	$approved_IDs_stmt = $sql_conn->prepare(
		"SELECT 
                ci.id,
                CONCAT(p.last_name, ', ', p.first_name) AS name,
                ci.division,
                p.phone,
                p.email,
                p.is_confirmed
            FROM people p
            INNER JOIN competitor_info ci ON ci.id = p.id
            ORDER BY p.is_confirmed, $sort_by");
	$approved_IDs_stmt->execute();
	$approved_IDs_result = $approved_IDs_stmt->get_result();


	// Not Confirmed Table
	echo "<table class='border filled page-break' style='font-size: small;'>",
	"<tr><th colspan='100'>Not Confirmed Accounts</th></tr>",
	"<tr>",
	"<th>ID</th>",
	"<th>Name</th>",
	"<th>Grade</th>",
	"<th>Division</th>",
	"<th>Phone #</th>",
	"<th>Email</th>",
	"</tr>";
	while (($curr_person = $approved_IDs_result->fetch_assoc()) && !$curr_person['is_confirmed']) {
		$curr_person_id = $curr_person['id'];
		if (($curr_person_grade = getGrade($curr_person_id)) != 0) {
			$curr_person_name = $curr_person['name'];
			$curr_person_grade = formatOrdinalNumber($curr_person_grade);
			$curr_person_division = DIVISIONS[$curr_person['division']];
			$curr_person_phone = formatPhoneNum($curr_person['phone']);
			$curr_person_email = $curr_person['email'];

			echo "<tr>",
			"<td >$curr_person_id</td>",
			"<td style='text-align: left;'>$curr_person_name</td>",
			"<td>$curr_person_grade</td>",
			"<td style='text-align: left;'>$curr_person_division</td>",
			"<td style='text-align: left;'>$curr_person_phone</td>",
			"<td style='text-align: left;'>$curr_person_email</td>",
			"</tr>";
		}
	}
	echo "</table>";


	echo "<form id='reset_confirmation_form' method='post'></form>",
	"<table class='border filled page-break' style='font-size: small;'>",
	"<tr><th colspan='100'>Confirmed Accounts</th></tr>",
	"<tr>",
	"<th>ID</th>",
	"<th>Name</th>",
	"<th>Grade</th>",

	"<th>Division</th>",
	"<th>Phone #</th>",
	"<th>Email</th>",
	"<th class='no-print'>Actions</th>",
	"</tr>";

	if ($curr_person) {
		do {
			$curr_person_id = $curr_person['id'];
			if (($curr_person_grade = getGrade($curr_person_id)) != 0) {
				$curr_person_name = $curr_person['name'];
				$curr_person_grade = formatOrdinalNumber($curr_person_grade);
				$curr_person_division = DIVISIONS[$curr_person['division']];
				$curr_person_phone = formatPhoneNum($curr_person['phone']);
				$curr_person_email = $curr_person['email'];

				echo "<tr>",
				"<td >$curr_person_id</td>",
				"<td style='text-align: left;'>$curr_person_name</td>",
				"<td>$curr_person_grade</td>",
				"<td style='text-align: left;'>$curr_person_division</td>",
				"<td style='text-align: left;'>$curr_person_phone</td>",
				"<td style='text-align: left;'>$curr_person_email</td>",
				"<td class='no-print'>",
				"    <button name='reset_confirmation_id' type='submit' form='reset_confirmation_form' value='$curr_person_id' class='btn-xs btn-danger'>Reset Confirmation</button>",
				"</td>",
				"</tr>";
			}
		} while ($curr_person = $approved_IDs_result->fetch_assoc());
	}
	echo "</table>";
	?>
</div>
