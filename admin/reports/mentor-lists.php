<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

// Get competition name if sent and is valid
$comp = null;
$sort_by = 'Name';
if (isset($_GET['comp_name'])) { // Check if `comp_id` was sent
	// Check if sent `comp_id` is valid
	if (!is_null(getDetail('competitions', 'competition_name', 'competition_name', $_GET['comp_name']))) {
		$comp = $_GET['comp_name'];

		if (isset($_GET['sort_by']))
			$sort_by = $_GET['sort_by'];
	}
}

if (is_null($comp))
	die("Competition name not specified.");
?>
    <style>
        td {
            border: none;
        }
    </style>

    <title>MAO | Mentor Lists [<?php echo $comp; ?>]</title>

    <div class="no-print">
        <h2><u><?php echo $comp; ?> - Mentor Lists</u></h2>

        <button type="button" onClick="window.print()" class="no-print">Print</button>
    </div>

<?php
$sql_conn = getDBConn();
$approved_IDs_stmt = $sql_conn->prepare(
	"SELECT 
                cd.id,
                cd.bus,
                CONCAT(p.last_name, ', ', p.first_name) AS name,
                ci.division,
                p.phone,
                cd.forms
            FROM competition_data cd
            INNER JOIN people p ON cd.id = p.id
            INNER JOIN competitor_info ci ON cd.id = ci.id
            WHERE competition_name = ? AND ci.division != 0
            ORDER BY division, last_name, first_name");
$approved_IDs_stmt->bind_param('s', $comp);
$approved_IDs_stmt->execute();
$approved_IDs_result = $approved_IDs_stmt->get_result();

$comp_nums = getCompNumber($comp);

$person_index = 1;
$curr_person = $approved_IDs_result->fetch_assoc();
do { // All tables for the report
	$table_division = $curr_person['division'];
	$table_count = getCompDivCount($comp, $table_division);

	do {  // All tables for a bus
		echo "<table class='page-break' style='font-size: small;'>";

		echo "<tr><th colspan='100'>$comp [Division: <u>" . DIVISIONS[$table_division] . "</u>]</th></tr>",
		"<tr>
                <th>$table_count</th>
                <th>Bus</th>
                <th>Name</th>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
                <th>Phone #</th>
                <th>ID</th>
            </tr>";

		$table_index = 0;
		do {
			$curr_person_comp_num = $comp_nums[$curr_person['id']]['comp_num'];
			$curr_person_comp_bus = $comp_nums[$curr_person['id']]['bus'];
			$curr_person_name = $curr_person['name'];
			$curr_person_id = $curr_person['id'];
			$curr_person_division = $curr_person['division'];
			$curr_person_division_name = ($curr_person_division != 0 ? DIVISIONS[$curr_person_division] : '');
			$curr_person_phone_formatted = formatPhoneNum($curr_person['phone']);
			$curr_person_forms = $curr_person['forms'];

			$curr_person_index = $person_index;
			if ($curr_person_division == 0)
				$curr_person_index = '';
			else
				++$person_index;

			echo "<tr>";

			// Person #
			echo "<td style='text-align: right;'>$curr_person_comp_num</td>";

			// Bus
			echo "<td>$curr_person_comp_bus</td>";

			// Name
			echo "<td style='text-align: left;'>$curr_person_name</td>";

			// Checkboxes
			echo str_repeat("<td class='checkoff' style='width: 20px;'></td>", 10);

//			// Division
//			echo "<td style='text-align: left;'>$curr_person_division_name</td>";

			// Phone #
			echo "<td>$curr_person_phone_formatted</td>";

			// ID #
			echo "<td>$curr_person_id</td>";

			echo "</tr>";
		} while (++$table_index < 45
		&& !is_null($curr_person = $approved_IDs_result->fetch_assoc())
		&& ($curr_person['division'] == $table_division));

		echo "</table>";
	} while (!is_null($curr_person)
	&& ($curr_person['division'] == $table_division)
	&& (!is_null($curr_person = $approved_IDs_result->fetch_assoc())
		&& ($curr_person['division'] == $table_division)));

} while (!is_null($curr_person));
