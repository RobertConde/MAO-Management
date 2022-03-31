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

$pay_id = getDetail('competitions', 'payment_id', 'competition_name', $comp);
?>

    <style>
        td {
            border: none;
        }
    </style>

    <title>MAO | Unpaid [<?php echo $comp ?>]</title>

    <div class="no-print">
        <h2><u><?php echo $comp; ?> - Unpaid</u></h2>

        <!--    Sort By Selector    -->
        <form method="get" style="text-align: center; margin: 6px;" class="filled border">
            <fieldset>
                <legend><b>Sort</b></legend>

                <input type="hidden" name="comp_name" value="<?php echo $comp; ?>">

                <!--suppress HtmlFormInputWithoutLabel -->
                <select name="sort_by" onchange="this.form.submit()">
					<?php
					/** @noinspection PhpUndefinedVariableInspection */
					foreach ($SORT_OPTIONS as $curr_sort_option) {
						$curr_selected_status = ($curr_sort_option == $sort_by ? 'selected' : '');

						echo "<option value='$curr_sort_option' $curr_selected_status>$curr_sort_option</option>";
					}
					?>
                </select>
            </fieldset>
        </form>
        <br>

        <button type="button" onClick="window.print()" class="no-print">Print</button>
    </div>

<?php
$sql_conn = getDBConn();
$approved_IDs_stmt = $sql_conn->prepare(
	"SELECT
                    cd.id,
                    p.last_name,
                    p.first_name,
                    cd.forms
                FROM competition_data cd
                INNER JOIN people p ON cd.id = p.id
                INNER JOIN competitor_info ci ON cd.id = ci.id
                LEFT JOIN transactions t ON t.id = cd.id
                WHERE competition_name = ? AND payment_id = ? AND (t.owed > t.paid) AND ci.division != 0

                ORDER BY " . SORT_ORDER_BY[$sort_by]);
$approved_IDs_stmt->bind_param('ss', $comp, $pay_id);
$approved_IDs_stmt->bind_result($id, $last_name, $first_name, $forms);
$approved_IDs_stmt->execute();

$show_forms = getAssociatedCompInfo($comp, 'show_forms');

$person = '';
$table_num = 0;
$page = "";
while (!is_null($person)) { // For multi-column posting report
	$table_header_row =
		"<tr><th colspan='100'>$comp [<u>Unpaid</u>]</th></tr>
        <tr>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Grade</th>
            <th>Division</th>
        </tr>";

	$i = 0;
	$table = "";
	while ($i++ < 45 && !is_null($person = $approved_IDs_stmt->fetch())) {
		if ($i == 1)
			$table = $table_header_row;

		// Table data
		$row_interior = surrTags('td', $last_name, "style='text-align: left; padding: 1 2px;'");

		$row_interior .= surrTags('td', $first_name, "style='text-align: left; padding: 1 2px;'");

		$row_interior .= surrTags('td', formatOrdinalNumber(getGrade($id)));

		$row_interior .= surrTags('td', DIVISIONS[getAccountDetail('competitor_info', 'division', $id)]);

		// Define form then add table row (wrap row interior by table row)
		$row = surrTags('tr', $row_interior);

		$table .= $row;
	}

	$page .= surrTags('table', $table, "class='filled' style='font-size: small; display: inline-block; vertical-align: top;'");

	if (++$table_num % 2 == 0 || is_null($person)) {
		echo $page, '<br>';

		$page = "";
	} else
		$page .= '&nbsp;';
}
