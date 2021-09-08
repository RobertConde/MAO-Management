<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
startSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBar();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER);

$comp_id = null;
$payment_id = null;
if (isset($_GET['comp_name']) && $_GET['comp_name'] != '') {
	$comp_id = $_GET['comp_name'];
	$payment_id = getDetail('competitions', 'payment_id', 'competition_name', $comp_id);
}
$comp_id_valid = (!is_null($comp_id) && getDetail('competitions', 'competition_name', 'competition_name', $comp_id) == $comp_id);

?>

    <title>DB | Custom Report</title>

    <h2 style="margin: 6px;"><u>Checkoff List</u></h2>

    <form id="table-form" method="get" style="margin: 6px;" class="filled border">
        <fieldset>
            <legend><b>Report Parameters</b></legend>

            <select id="comps" name="comp_name" style="margin: 6px;">
                <option value="" disabled hidden selected>Choose a Competition...</option>
				<?php
				$sql_conn = getDBConn();

				$comps_query = "SELECT competition_name FROM competitions;";

				$comps_result = $sql_conn->query($comps_query);

				while (!is_null($row = $comps_result->fetch_assoc())) {
					echo "<option value=\"" . $row['competition_name'] . "\" "
						. ($comp_id == $row['competition_name'] ? 'selected' : '')
						. ">" . $row['competition_name'] . "</option>";
				}
				?>
            </select>
            <label for="comps" style="color: red;">*</label><br>

            <input type="submit" value="Get!">
        </fieldset>
    </form>

<?php
if (is_null($comp_id))
    die("<p style=\"color:red; text-align:center; margin: 6px;\">A table must be specified!</p>\n");
else {
    $checkbox = "<p style=\"font-size: 30px;\">☐</p>";

    $checkoff_info = "SELECT
        p.id,
        p.last_name,
        p.first_name,
        ci.division,
        EXISTS (SELECT * FROM transactions t WHERE t.payment_id = '$payment_id' AND t.id = p.id) AS paid,
        EXISTS (SELECT * FROM competition_forms t WHERE t.competition_name = '$comp_id' AND t.id = p.id) AS forms,
        '$checkbox' AS '1',
        '$checkbox' AS '2',
        '$checkbox' AS '3',
        '$checkbox' AS '4',
        '$checkbox' AS '5',
        '$checkbox' AS '6',
        '$checkbox' AS '7',
        '$checkbox' AS '8',
        '$checkbox' AS '9',
        '$checkbox' AS '10'
    FROM people p
    JOIN competitor_info ci
    ON p.id = ci.id
    WHERE EXISTS (SELECT * FROM competition_approvals cs WHERE cs.competition_name = '$comp_id' AND cs.id = p.id) 
    ORDER BY last_name, first_name, division, id;";

    $checkoff_info_result = $sql_conn->query($checkoff_info);

    echo getTableFromResult($checkoff_info_result);
}
