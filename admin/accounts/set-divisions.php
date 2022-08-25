<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);
?>

<title>MAO | Tracker</title>

<h2 style="text-align: center; margin: 6px;"><u>Set Divisions</u></h2>


<table class="border filled">
    <thead>
    <tr>
        <th>ID</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Division</th>
    </tr>
    </thead>

    <tbody>
	<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

	$sql_conn = getDBConn();
	$approved_IDs_stmt = $sql_conn->prepare(
		"SELECT
                p.id,
                p.last_name,
                p.first_name,
                ci.division
            FROM people p
            JOIN competitor_info ci ON ci.id = p.id
            ORDER BY p.last_name, p.first_name");
	$approved_IDs_stmt->bind_result($id, $last_name, $first_name, $division);
	$approved_IDs_stmt->execute();

	while ($approved_IDs_stmt->fetch()) {
		if (getGrade($id) == 0)
			continue;

		echo "<tr>",
		"<td>$id</td>",
		"<td style='text-align: left;'>$last_name</td>",
		"<td style='text-align: left;'>$first_name</td>",
		"<td>",
		"<select name='division' form='$id-division'>
                <option value='1'", (getDivision($id) == 1 ? 'selected' : ''), ">
                    Algebra 1
                </option>

                <option value='2'", (getDivision($id) == 2 ? 'selected' : ''), ">
                    Geometry
                </option>

                <option value='3'", (getDivision($id) == 3 ? 'selected' : ''), ">
                    Algebra 2
                </option>

                <option value='4'", (getDivision($id) == 4 ? 'selected' : ''), ">
                    Precalculus
                </option>

                <option value='5'", (getDivision($id) == 5 ? 'selected' : ''), ">
                    Calculus
                </option>

                <option value='6'", (getDivision($id) == 6 ? 'selected' : ''), ">
                    Statistics
                </option>

                <option value='0'", (getDivision($id) == 0 ? 'selected' : ''), ">
                    Not a Student
                </option>
            </select>",
		"</td>",
		"</tr>",
		"<form id='$id-division' name='update' method='post'>",
            "<input name='id' type='hidden' value='$id'>",
        "</form>";
	}
	?>

    </tbody>
</table>

<script>
	$("select[name=division]").on("change", function (event) {
		let form = event.originalEvent.target.form;


		// let updateButton = document.getElementById(form.id + '-submit');

		// let cell = updateButton.parentElement;

		// Clear cell and indicate that the updating process has begun
		// tableCellInProgress(cell);

		let dataString = $(form).serialize();
		console.log(event);
		console.log(form);
		console.log(this);
		console.log(dataString);
		$.ajax({
			type: "POST",
			url: "helper-division",
			data: dataString,
		});
	});

	//$("form[name=remove]").on("submit", function (event) {
	//	event.preventDefault();
    //
	//	let form = event.originalEvent.target;
	//	let submitButton = document.getElementById(form.id + '-submit');
    //
	//	let cell = submitButton.parentElement;
    //
	//	tableCellInProgress(cell);
	//	const dataString = $(this).serialize();
	//	$.ajax({
	//		type: "POST",
	//		url: "helper?comp=<?php //echo $comp; ?>//",
	//		data: dataString,
	//		success: function () {
	//			tableCellSuccess(cell);
    //
	//			tableCellTrash(cell);
	//		},
	//		error: function () {
	//			tableCellError(cell);
	//		}
	//	});
	//});
</script>
