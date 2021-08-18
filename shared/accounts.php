<?php

function startSession() {
	if (session_status() != PHP_SESSION_ACTIVE)
		session_start();
}

function registerAccount($id, $first_name, $middle_initial, $last_name, $graduation_year, $email, $phone, $address): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();    // Get DB connection

	// People
	$people_stmt = $sql_conn->prepare(
		"INSERT INTO people (id, first_name, middle_initial, last_name, graduation_year, email, phone, address)
			   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

	$people_stmt->bind_param('ssssisss', $id, $first_name, $middle_initial, $last_name, $graduation_year, $email, $phone, $address);

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
	$sql_conn = getDBConn();    // Get DB connection

	$cycle_statement = $sql_conn->prepare("INSERT INTO login(id, code) VALUES (?, ?) ON DUPLICATE KEY UPDATE code = ?");

	$new_code = substr(md5(rand()), 0, 6);

	$cycle_statement->bind_param('sss', $id, $new_code, $new_code);

	return $cycle_statement->execute() && sendLoginCodeEmail($id);
}

// --Commented out by Inspection START (8/13/2021 7:20 PM):
///**
// * @throws \PHPMailer\PHPMailer\Exception
// */
//function sendUpdateEmail($id, $updater_id): bool
//{
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/email.php";
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
//
//	return sendEmail(
//		getAccountDetail('people', 'email', $id),
//		"MAO - Account Updated",
//		"<b>Account ID#:</b> <code>$id</code><br><b>Updated By (ID):</b> <code>$updater_id</code>");
//}
// --Commented out by Inspection STOP (8/13/2021 7:20 PM)

function updatePerson($id, $first_name, $middle_initial, $last_name, $graduation_year, $email, $phone, $address): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_people_stmt = $sql_conn->prepare("UPDATE people
		SET first_name = ?, middle_initial = ?, last_name = ?, graduation_year = ?,
		    email = ?, phone = ?, address = ? WHERE id = ?;");

	$update_people_stmt->bind_param('sssissss',
		$first_name, $middle_initial, $last_name, $graduation_year,
		$email, $phone, $address,
		$id);

	$update_people_stmt->execute();
	echo $update_people_stmt->error;
	return false;
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

	$update_parents_stmt->bind_param('ssssss',
		$name, $email, $phone, $alternate_phone, $alternate_ride_home,
		$id);

	return $update_parents_stmt->execute();
}

function updateCompetitorInfo_Student($id, $division): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_competitor_stmt = $sql_conn->prepare("UPDATE competitor_info
		SET division = ?
		WHERE id = ?");

	$update_competitor_stmt->bind_param('is',
		$division,
		$id);

	return $update_competitor_stmt->execute();
}

function updateCompetitorInfo_Admin($id, $division, $mu_student_id, $is_famat_member, $is_national_member,
                                    $has_medical, $has_insurance, $has_school_insurance): bool
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
	$sql_conn = getDBConn();

	$update_competitor_stmt = $sql_conn->prepare("UPDATE competitor_info
		SET division = ?, mu_student_id = ?, is_famat_member = ?, is_national_member = ?,
		    has_medical = ?, has_insurance = ?, has_school_insurance = ?
		WHERE id = ?");

	$update_competitor_stmt->bind_param('isiiiiis',
		$division, $mu_student_id, $is_famat_member, $is_national_member,
		$has_medical, $has_insurance, $has_school_insurance,
		$id);

	return $update_competitor_stmt->execute();
}

// TODO comp info updates (student and >=officer)

///**
// * @throws \PHPMailer\PHPMailer\Exception
// */
//function updateAccount_People($id, $first_name, $middle_initial, $last_name, $graduation_year, $email, $phone, $address, $updater_id): bool
//{
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
//	$sql_conn = getDBConn();    // Get DB connection
//
//	$update_stmt = $sql_conn->prepare("UPDATE people
//		SET first_name = ?, minitial = ?,  last_name = ?, email = ?, phone = ?, division = ?, grade = ?,
//		    p1 = ?, p2 = ?, p3 = ?, p4 = ?, p5 = ?, p6 = ?, p7 = ?, p8 = ?
//		WHERE id = ?");
//
//	$update_stmt->bind_param('sssssiissssssssi', $first_name, $minitial, $last_name, $email, $phone, $division, $grade,
//		$p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $id);
//
//	return $update_stmt->execute() && sendUpdateEmail($id, $updater_id);
//}

///**
// * @throws \PHPMailer\PHPMailer\Exception
// */
//function updateAccount_Admin($id, $permissions, $mu_student_id, $member_famat, $member_nation, $medical, $insurance, $school_insurance, $updater_id): bool
//{
//	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
//	$sql_conn = getDBConn();    // Get DB connection
//
//	$update_stmt = $sql_conn->prepare("UPDATE people
//		SET permissions = ?, mu_student_id = ?, member_famat = ?, member_nation = ?, medical = ?, insurance = ?, school_insurance = ?
//		WHERE id = ?");
//
//	// No one can demote an admin
//	if (getAccountDetail('people', 'permissions', $id) == 100)
//		$permissions = 100;
//
//	$update_stmt->bind_param('isiiiiis', $permissions, $mu_student_id, $member_famat, $member_nation, $medical, $insurance, $school_insurance, $id);
//
//	return $update_stmt->execute() && sendUpdateEmail($id, $updater_id);
//}

function getAccountDetail($table, $col, $id)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

	return getDetail($table, $col, 'id', $id);
}

function getRank($id)
{
	$permissions = getAccountDetail('people', 'permissions', $id);

	if ($permissions < 1)
		return -1;

	return floor(log10($permissions));
}

function getGrade($id): int
{
	$graduation_year = getAccountDetail('people', 'graduation_year', $id);

	// TODO: Rethink end of school year
	$now = new DateTime('now');
	$graduation_date = DateTime::createFromFormat('m/d/Y', "7/1/$graduation_year");

	$grade = 12 - (int)date_diff($now, $graduation_date)->format('%y');

	if ($graduation_date > $now && (6 <= $grade && $grade <= 12))
		return $grade;

	return 0;
}
