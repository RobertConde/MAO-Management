<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
startSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBar();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/competitions.php";

// Get competition name if sent and is valid
$comp = null;
if (isset($_GET['comp_name'])) { // Check if `comp_id` was sent
	// Check if sent `comp_id` is valid
	if (!is_null(getDetail('competitions', 'competition_name', 'competition_name', $_GET['comp_name'])))
		$comp = $_GET['comp_name'];
}

if (is_null($comp))
	die("Competition name not specified.");

$pay_id = getDetail('competitions', 'payment_id', 'competition_name', $comp);
?>

    <style>
        td {
            border: none;
        }
    </style>

    <title>DB | Posting [<?php echo $comp ?>]</title>

    <div class="no-print">
        <h2><u><?php echo $comp; ?> - Posting</u></h2>

        &nbsp;<button type="button" onClick="window.print()" class="no-print">Print</button>
    </div>

<?php
$sql_conn = getDBConn();
$approved_IDs_stmt = $sql_conn->prepare(
	"SELECT 	cd.id,
                        p.last_name,
       					p.first_name,
                        cd.forms
                FROM competition_data cd INNER JOIN people p ON cd.id = p.id WHERE competition_name = ?
                ORDER BY last_name, first_name");
$approved_IDs_stmt->bind_param('s', $comp);
$approved_IDs_stmt->bind_result($id, $last_name, $first_name, $forms);
$approved_IDs_stmt->execute();

$person = '';
$table_num = 0;
$page = "";
while (!is_null($person)) {
//    echo "ITT";
	$table_header_row =
		"<tr><th colspan='5'>$comp</th></tr>
        <tr>
            <th>ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th " . (is_null($pay_id) ? 'hidden' : '') . ">Paid</th>
            <th>Forms</th>
        </tr>";

	$i = 0;
    $table = "";
	while (++$i < 50 && !is_null($person = $approved_IDs_stmt->fetch())) {
//        echo "T#$table_num";
		if ($i == 1)
			$table = $table_header_row;

		// Table data
		$row_interior = surrTags('td', $id, "style='padding: 1 2px;'");

		$row_interior .= surrTags('td', $last_name, "style='text-align: left; padding: 1 2px;'");

		$row_interior .= surrTags('td', $first_name, "style='text-align: left; padding: 1 2px;'");

		// If the competition doesn't have an assigned payment, don't show the 'Paid' columns
		if (!is_null($pay_id))
			$row_interior .= surrTags('td', isCompPaid($id, $comp) ? '✔️' : '', "style='padding: 1 2px;'");

		$row_interior .= surrTags('td', areFormsCollected($id, $comp) ? '✔️' : '', "style='padding: 1 2px; '");

		// Define form then add table row (wrap row interior by table row)
		$row = surrTags('tr', $row_interior);

		$table .= $row;
	}

	$page .= surrTags('table', $table, "class='filled' style='font-size: small; display: inline-block; vertical-align: top;'");
//    $page .= $table_num;
	if (++$table_num % 2 == 0 || is_null($person)) {
		echo $page, '<br>';

		$page = "";
	} else
		$page .= '&nbsp;';
}
