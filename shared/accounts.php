<?php

function safelyStartSession()
{
	if (session_status() != PHP_SESSION_ACTIVE)
		session_start();
}

// TODO: implement into codebase
function existsPerson($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$find_person_stmt = $sql_conn->prepare("SELECT COUNT(*) FROM people WHERE id = ?");
	$find_person_stmt->bind_param('s', $id);
	$find_person_stmt->bind_result($num_people);

	if (!$find_person_stmt->execute())
		die("Error occurred checking if person exists: $find_person_stmt->error");
	$find_person_stmt->fetch();

	$sql_conn->close();
	return ($num_people == 1);
}

function getIDByName($last_name, $first_name)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$last_name = strtoupper($last_name);
	$first_name = strtoupper($first_name);

	$find_person_stmt = $sql_conn->prepare("SELECT id FROM people WHERE UPPER(last_name) = ? AND UPPER(first_name) = ?");
	$find_person_stmt->bind_param('ss', $last_name, $first_name);

	if (!$find_person_stmt->execute())
		die("Error occurred finding a person by name: $find_person_stmt->error");
	$find_person_result = $find_person_stmt->get_result();

	$find_person_count = $find_person_result->num_rows;
	return ($find_person_count == 1 ? $find_person_result->fetch_assoc()['id'] : null);
}

function registerAccount($id, $first_name, $middle_initial, $last_name, $school_code, $graduation_year, $email, $phone, $address): bool
{
	if (!preg_match('/[0-9]{7}/', $id))
		return false;

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	// People
	$people_stmt = $sql_conn->prepare(
		"INSERT INTO people (id, first_name, middle_initial, last_name, school_code, graduation_year, email, phone, address)
			   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

	/** @noinspection SpellCheckingInspection */
	$people_stmt->bind_param('sssssisss', $id, $first_name, $middle_initial, $last_name, $school_code, $graduation_year, $email, $phone, $address);

	// Empty rows
	$accounts_stmt = $sql_conn->prepare("INSERT INTO accounts (id) VALUES (?)");
	$competitor_info_stmt = $sql_conn->prepare("INSERT INTO competitor_info (id) VALUES (?)");
	$parents_stmt = $sql_conn->prepare("INSERT INTO parents (id) VALUES (?)");
	$schedules_stmt = $sql_conn->prepare("INSERT INTO schedules (id) VALUES (?)");

	$accounts_stmt->bind_param('s', $id);
	$competitor_info_stmt->bind_param('s', $id);
	$parents_stmt->bind_param('s', $id);
	$schedules_stmt->bind_param('s', $id);

	return $people_stmt->execute()
		&& ($accounts_stmt->execute() && $competitor_info_stmt->execute() && $parents_stmt->execute() && $schedules_stmt->execute());
}

/**
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendLoginCodeEmail($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/email.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

	return sendEmail(
		getAccountDetail('people', 'email', $id),
		"MAO - Login Code",
		"<b>Account ID#:</b> <code>$id</code><br><b>Login Code:</b> <code>" . getAccountDetail('login', 'code', $id) . "</code>");
}

/**
 * @throws \PHPMailer\PHPMailer\Exception
 */
function cycleLoginCode($id): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$cycle_statement = $sql_conn->prepare("INSERT INTO login(id, code) VALUES (?, ?) ON DUPLICATE KEY UPDATE code = ?");

	$new_code = strtoupper(substr(md5(rand()), 0, 6));

	$cycle_statement->bind_param('sss', $id, $new_code, $new_code);

	return $cycle_statement->execute() && sendLoginCodeEmail($id);
}

function updatePerson($id, $first_name, $middle_initial, $last_name, $school_code, $graduation_year,
                      $email, $phone, $address, $perms = null): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_people_stmt = $sql_conn->prepare("UPDATE people
		SET first_name = ?, middle_initial = ?, last_name = ?, school_code = ?, graduation_year = ?,
		    email = ?, phone = ?, address = ?, permissions = ? WHERE id = ?;");

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
	$perms = ($perms ?? getPerms($id));

	/** @noinspection SpellCheckingInspection */
	$update_people_stmt->bind_param('ssssisssis',
		$first_name, $middle_initial, $last_name, $school_code, $graduation_year,
		$email, $phone, $address, $perms,
		$id);

	return $update_people_stmt->execute();
}

function updateSchedule($id, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8,
                        $is_p1_koski, $is_p2_koski, $is_p3_koski, $is_p4_koski, $is_p5_koski, $is_p6_koski, $is_p7_koski, $is_p8_koski): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_schedule_stmt = $sql_conn->prepare("UPDATE schedules
		SET p1 = ?, p2 = ?, p3 = ?, p4 = ?, p5 = ?, p6 = ?, p7 = ?, p8 = ?,
            is_p1_koski = ?, is_p2_koski = ?, is_p3_koski = ?, is_p4_koski = ?,
		    is_p5_koski = ?, is_p6_koski = ?, is_p7_koski = ?, is_p8_koski = ?
		WHERE id = ?;");

	/** @noinspection SpellCheckingInspection */
	$update_schedule_stmt->bind_param('ssssssssiiiiiiiis',
		$p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8,
		$is_p1_koski, $is_p2_koski, $is_p3_koski, $is_p4_koski, $is_p5_koski, $is_p6_koski, $is_p7_koski, $is_p8_koski,
		$id);

	return $update_schedule_stmt->execute();
}

function updateAccounts($id, $moodle, $alcumus, $webwork): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_accounts_stmt = $sql_conn->prepare("UPDATE accounts
		SET moodle = ?, alcumus = ?, webwork = ?
		WHERE id = ?");

	$update_accounts_stmt->bind_param('ssss',
		$moodle, $alcumus, $webwork,
		$id);

	return $update_accounts_stmt->execute();
}

function updateParent($id, $name, $email, $phone, $alternate_phone, $alternate_ride_home): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_parents_stmt = $sql_conn->prepare("UPDATE parents
		SET name = ?, email = ?, phone = ?, alternate_phone = ?, alternate_ride_home = ?
		WHERE id = ?");

	/** @noinspection SpellCheckingInspection */
	$update_parents_stmt->bind_param('ssssss',
		$name, $email, $phone, $alternate_phone, $alternate_ride_home,
		$id);

	return $update_parents_stmt->execute();
}

function updateCompetitorInfo_Student($id, $tshirt): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_competitor_stmt = $sql_conn->prepare("UPDATE competitor_info
		SET tshirt_size = ?
		WHERE id = ?");

	$update_competitor_stmt->bind_param('iss', $tshirt, $id);

	return $update_competitor_stmt->execute();
}

function updateCompetitorInfo_Admin($id, $division, $tshirt, $mu_student_id, $is_famat_member, $is_national_member,
                                    $has_medical, $has_insurance, $has_school_insurance): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	if ($mu_student_id == '   ')
		$mu_student_id = '';

	$update_competitor_stmt = $sql_conn->prepare("UPDATE competitor_info
		SET division = ?, tshirt_size = ?, mu_student_id = ?, is_famat_member = ?, is_national_member = ?,
		    has_medical = ?, has_insurance = ?, has_school_insurance = ?
		WHERE id = ?");

	/** @noinspection SpellCheckingInspection */
	$update_competitor_stmt->bind_param('issiiiiis',
		$division, $tshirt, $mu_student_id, $is_famat_member, $is_national_member,
		$has_medical, $has_insurance, $has_school_insurance,
		$id);

	return $update_competitor_stmt->execute();
}

// TODO comp info updates (student and >=officer)

function getAccountDetail($table, $col, $id)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

	return getDetail($table, $col, 'id', $id);
}

function getGrade($id): int
{
	if (!is_null($id)) {
		$graduation_year = getAccountDetail('people', 'graduation_year', $id);

		// TODO: Rethink end of school year
		$now = new DateTime('now');
		$graduation_date = DateTime::createFromFormat('m/d/Y', "7/1/$graduation_year");

		$grade = 12 - (int)date_diff($now, $graduation_date)->format('%y');

		if ($graduation_date > $now && (6 <= $grade && $grade <= 12))
			return $grade;
	}

	return 0;
}

function setFAMAT_ID($id, $famat_id): bool
{
	if (strlen($famat_id) != 9)
		die ("Bad FAMAT ID for with ID = $id!");

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_people_stmt = $sql_conn->prepare("UPDATE competitor_info
		SET mu_student_id = ?, division = ? WHERE id = ?;");

	$mu_student_id = substr($famat_id, 4, 3);
	$division = (int)substr($famat_id, 7, 1);
	$update_people_stmt->bind_param('sis', $mu_student_id, $division, $id);

	return $update_people_stmt->execute();
}
