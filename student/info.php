<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safeStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(STUDENT_PERMS);
?>

<title>MAO | Update Account</title>

<?php
//TODO: don't use $_POST['select-id']
if (isset($_POST['select-id']) && compareRank($_SESSION['id'], $_POST['select-id'], true)) {
	if (isset($_POST['update_person'])) {
		$updated_person = updatePerson(
			$_POST['select-id'],
			$_POST['first_name'], $_POST['middle_initial'], $_POST['last_name'], $_POST['graduation_year'],
			$_POST['email'], $_POST['phone'], $_POST['address']);
	} else if (isset($_POST['update_schedule']))
		$updated_schedule = updateSchedule(
			$_POST['select-id'],
			$_POST['p1'], $_POST['p2'], $_POST['p3'], $_POST['p4'], $_POST['p5'], $_POST['p6'], $_POST['p7'], $_POST['p8'],
			isset($_POST['is_p1_koski']), isset($_POST['is_p2_koski']), isset($_POST['is_p3_koski']), isset($_POST['is_p4_koski']),
			isset($_POST['is_p5_koski']), isset($_POST['is_p6_koski']), isset($_POST['is_p7_koski']), isset($_POST['is_p8_koski']));
	else if (isset($_POST['update_accounts']))
		$updated_accounts = updateAccounts(
			$_POST['select-id'],
			$_POST['moodle'], $_POST['alcumus'], $_POST['webwork']);
	else if (isset($_POST['update_parent']))
		$updated_parent = updateParent(
			$_POST['select-id'],
			$_POST['name'], $_POST['email'], $_POST['phone'], $_POST['alternate_phone'], $_POST['alternate_ride_home']);
	else if (isset($_POST['update_competitor_info'])) {
		if (getRank($_SESSION['id']) < OFFICER_RANK)
			$updated_competitor_info = updateCompetitorInfo_Student($_POST['select-id'], $_POST['tshirt']);
		else
			$updated_competitor_info = updateCompetitorInfo_Admin(
				$_POST['select-id'], $_POST['division'], $_POST['tshirt'], $_POST['mu_student_id'], $_POST['is_famat_member'],
				$_POST['is_national_member'], $_POST['has_medical'], $_POST['has_insurance'], $_POST['has_school_insurance']);
	}

	redirect(currentURL());
}

// View form (using correct ID)
$id = $_SESSION['id'];
$select_id = getSelectID();
if (!is_null($select_id)) {
	$rankComp = compareRank($_SESSION['id'], $select_id, true);

	if (!is_null($rankComp)) {
		if ($rankComp)
			$id = $select_id;
		else
			die("<p style='color:red;'>You do not have the required permissions!</p>");
	}
}
?>

<h2 style="text-align: center; margin: 6px;"><u>Update Account Information</u></h2>

<?php

if (getRank($_SESSION['id']) > STUDENT_RANK) {
	personSelectForm();
	personSelect();

	if ($id != $_SESSION['id'])
		echo "<p style='text-align: center; color:violet;'><i><b>Note:</b> You are updating an account that isn't yours, and has a permission rank below you!</i></p>\n";
}
?>
<!-- TODO: This is bad and ugly and makes me want to cry. I hate my old code :( -->

<div style="display: flex; justify-content: center; margin: 6px; text-align: left;">
    <div style="margin: 6px; padding-right: 6px;">
        <form method="post" action="info.php?select-id=<?php echo $id; ?>" class="filled border">
            <fieldset>
                <legend><b>Personal Information</b></legend>

                <label for="select-id">ID:</label>
                <input id="select-id" type="text" pattern="[0-9]{7}" size="7" disabled
                       value="<?php echo $id; ?>">
                <input name="select-id" type="hidden" value="<?php echo $id; ?>"><br>

                <label for="first_name">First Name:</label>
                <input id="first_name" name="first_name" type="text" size="15" required
                       value="<?php echo getAccountDetail('people', 'first_name', $id); ?>"><br>

                <label for="middle_initial">Middle Initial:</label>
                <input id="middle_initial" name="middle_initial" type="text" pattern="[A-Z]{1}" size="1"
                       value="<?php echo getAccountDetail('people', 'middle_initial', $id); ?>"><br>

                <label for="last_name">Last Name:</label>
                <input id="last_name" name="last_name" type="text" size="15" required
                       value="<?php echo getAccountDetail('people', 'last_name', $id); ?>"><br>

                <label for="graduation_year">Grad. Year:</label>
                <input id="graduation_year" name="graduation_year" type="number" style="width: 4em;" required
                       value="<?php echo getAccountDetail('people', 'graduation_year', $id); ?>"><br>

                <label for="email">Email:</label>
                <input id="email" name="email" type="email" size="25" required
                       value="<?php echo getAccountDetail('people', 'email', $id); ?>"><br>

                <label for="phone">Phone #:</label>
                <input id="phone" name="phone" type="tel" pattern="[0-9]{10}" size="10" required
                       value="<?php echo getAccountDetail('people', 'phone', $id); ?>"><br>

                <label for="address">Address:</label>
                <input id="address" name="address" type="text" size="35" required
                       value="<?php echo getAccountDetail('people', 'address', $id); ?>"><br>

                <input name="update_person" type="submit" value="Update" style="margin-top: 6px">
            </fieldset>
        </form>
        <br>

        <br>

        <form method="post" action="info.php?select-id=<?php echo $id; ?>" class="filled border">
            <fieldset>
                <legend><b>School Schedule</b>
                    <i class="fa fa-question-circle"
                       title="Check the box if it is a Koski period. Include room # and course name."></i>
                </legend>

                <input name="select-id" type="hidden" value="<?php echo $id; ?>">

                <label for="p1">Period 1:</label>
                <input id="is_p1_koski" name="is_p1_koski" type="checkbox" title="Is a Koski period?"
					<?php if (getAccountDetail('schedules', 'is_p1_koski', $id) == 1) echo 'checked'; ?>>
                <input id="p1" name="p1" type="text" size="30"
                       value="<?php echo getAccountDetail('schedules', 'p1', $id); ?>"><br>

                <label for="p2">Period 2:</label>
                <input id="is_p2_koski" name="is_p2_koski" type="checkbox" title="Is a Koski period?"
					<?php if (getAccountDetail('schedules', 'is_p2_koski', $id) == 1) echo 'checked'; ?>>
                <input id="p2" name="p2" type="text" size="30"
                       value="<?php echo getAccountDetail('schedules', 'p2', $id); ?>"><br>

                <label for="p3">Period 3:</label>
                <input id="is_p3_koski" name="is_p3_koski" type="checkbox" title="Is a Koski period?"
					<?php if (getAccountDetail('schedules', 'is_p3_koski', $id) == 1) echo 'checked'; ?>>
                <input id="p3" name="p3" type="text" size="30"
                       value="<?php echo getAccountDetail('schedules', 'p3', $id); ?>"><br>

                <label for="p4">Period 4:</label>
                <input id="is_p4_koski" name="is_p4_koski" type="checkbox" title="Is a Koski period?"
					<?php if (getAccountDetail('schedules', 'is_p4_koski', $id) == 1) echo 'checked'; ?>>
                <input id="p4" name="p4" type="text" size="30"
                       value="<?php echo getAccountDetail('schedules', 'p4', $id); ?>"><br>

                <label for="p5">Period 5:</label>
                <input id="is_p5_koski" name="is_p5_koski" type="checkbox" title="Is a Koski period?"
					<?php if (getAccountDetail('schedules', 'is_p5_koski', $id) == 1) echo 'checked'; ?>>
                <input id="p5" name="p5" type="text" size="30"
                       value="<?php echo getAccountDetail('schedules', 'p5', $id); ?>"><br>

                <label for="p6">Period 6:</label>
                <input id="is_p6_koski" name="is_p6_koski" type="checkbox" title="Is a Koski period?"
					<?php if (getAccountDetail('schedules', 'is_p6_koski', $id) == 1) echo 'checked'; ?>>
                <input id="p6" name="p6" type="text" size="30"
                       value="<?php echo getAccountDetail('schedules', 'p6', $id); ?>"><br>

                <label for="p7">Period 7:</label>
                <input id="is_p7_koski" name="is_p7_koski" type="checkbox" title="Is a Koski period?"
					<?php if (getAccountDetail('schedules', 'is_p7_koski', $id) == 1) echo 'checked'; ?>>
                <input id="p7" name="p7" type="text" size="30"
                       value="<?php echo getAccountDetail('schedules', 'p7', $id); ?>"><br>

                <label for="p8">Period 8:</label>
                <input id="is_p8_koski" name="is_p8_koski" type="checkbox" title="Is a Koski period?"
					<?php if (getAccountDetail('schedules', 'is_p8_koski', $id) == 1) echo 'checked'; ?>>
                <input id="p8" name="p8" type="text" size="30"
                       value="<?php echo getAccountDetail('schedules', 'p8', $id); ?>"><br>

                <input name="update_schedule" type="submit" value="Update" style="margin-top: 6px">
            </fieldset>
        </form>
        <br>

        <br>

        <form method="post" action="info.php?select-id=<?php echo $id; ?>" class="filled border">
            <fieldset>
                <legend><b>Accounts</b></legend>

                <input name="select-id" type="hidden" value="<?php echo $id; ?>">

                <label for="moodle">Moodle:</label>
                <input id="moodle" name="moodle" type="text" size="10"
                       value="<?php echo getAccountDetail('accounts', 'moodle', $id); ?>"><br>

                <label for="alcumus">Alcumus:</label>
                <input id="alcumus" name="alcumus" type="text" size="10"
                       value="<?php echo getAccountDetail('accounts', 'alcumus', $id); ?>"><br>

                <label for="webwork">WebWork:</label>
                <input id="webwork" name="webwork" type="text" size="10"
                       value="<?php echo getAccountDetail('accounts', 'webwork', $id); ?>"><br>

                <input name="update_accounts" type="submit" value="Update" style="margin-top: 6px">
            </fieldset>
        </form>
    </div>

    <div style="margin: 6px;">
        <form method="post" action="info.php?select-id=<?php echo $id; ?>" class="filled border">
            <fieldset style="display: block;">
                <legend><b>Parent/Ride Home Information</b></legend>

                <input name="select-id" type="hidden" value="<?php echo $id; ?>">

                <label for="name">Name:</label>
                <input id="name" name="name" type="text" size="20" required
                       value="<?php echo getAccountDetail('parents', 'name', $id); ?>"><br>

                <label for="email">Email:</label>
                <input id="email" name="email" type="email" size="25" required
                       value="<?php echo getAccountDetail('parents', 'email', $id); ?>"><br>

                <label for="phone">Phone #:</label>
                <input id="phone" name="phone" type="tel" pattern="[0-9]{10}" size="10" required
                       value="<?php echo getAccountDetail('parents', 'phone', $id); ?>"><br>

                <label for="alternate_phone">Alt. Phone #:</label>
                <input id="alternate_phone" name="alternate_phone" type="tel" pattern="[0-9]{10}" size="10" required
                       value="<?php echo getAccountDetail('parents', 'alternate_phone', $id); ?>"><br>

                <label for="alternate_ride_home">Alt. Ride Home:</label>
                <input id="alternate_ride_home" name="alternate_ride_home" type="text" size="20" required
                       value="<?php echo getAccountDetail('parents', 'alternate_ride_home', $id); ?>"><br>

                <input name="update_parent" type="submit" value="Update" style="margin-top: 6px">

            </fieldset>
        </form>
        <br>

        <br>

        <form method="post" action="info.php?select-id=<?php echo $id; ?>" class="filled border">
            <fieldset>
                <legend><b>Competitor Information</b></legend>

                <input name="select-id" type="hidden" value="<?php echo $id; ?>">

                <!--suppress SpellCheckingInspection -->
                <label for="tshirt">T-Shirt Size:</label>
                <!--suppress SpellCheckingInspection -->
                <select id="tshirt" name="tshirt" required>
                    <option disabled selected></option>

                    <option value="S" <?php if (getAccountDetail('competitor_info', 'tshirt_size', $id) == 'S') echo 'selected'; ?>>
                        Small
                    </option>

                    <option value="M" <?php if (getAccountDetail('competitor_info', 'tshirt_size', $id) == 'M') echo 'selected'; ?>>
                        Medium
                    </option>

                    <option value="L" <?php if (getAccountDetail('competitor_info', 'tshirt_size', $id) == 'L') echo 'selected'; ?>>
                        Large
                    </option>

                    <option value="XL" <?php if (getAccountDetail('competitor_info', 'tshirt_size', $id) == 'XL') echo 'selected'; ?>>
                        X-Large
                    </option>
                </select>

                <hr>

                <label for="division" style="color: red;">Division:</label>
                <select id="division" name="division"
                        required <?php if (getRank($_SESSION['id']) < OFFICER_RANK) echo 'disabled'; ?>>
                    <option value="0" disabled selected></option>

                    <option value="1" <?php if (getAccountDetail('competitor_info', 'division', $id) == 1) echo 'selected'; ?>>
                        Algebra 1
                    </option>

                    <option value="2" <?php if (getAccountDetail('competitor_info', 'division', $id) == 2) echo 'selected'; ?>>
                        Geometry
                    </option>

                    <option value="3" <?php if (getAccountDetail('competitor_info', 'division', $id) == 3) echo 'selected'; ?>>
                        Algebra 2
                    </option>

                    <option value="4" <?php if (getAccountDetail('competitor_info', 'division', $id) == 4) echo 'selected'; ?>>
                        Precalculus
                    </option>

                    <option value="5" <?php if (getAccountDetail('competitor_info', 'division', $id) == 5) echo 'selected'; ?>>
                        Calculus
                    </option>

                    <option value="6" <?php if (getAccountDetail('competitor_info', 'division', $id) == 6) echo 'selected'; ?>>
                        Statistics
                    </option>

                    <option value="0" <?php if (getAccountDetail('competitor_info', 'division', $id) == 0) echo 'selected'; ?>>
                        Not a Student
                    </option>
                </select><br>

                <label for="mu_student_id" style="color: red;">Mu Student ID:</label>
                <input id="mu_student_id" name="mu_student_id" type="text" pattern="([0-9]{3}|[\s]{3})" size="3"
                       required
                       value=
                       "<?php $mu_student_id = getAccountDetail('competitor_info', 'mu_student_id', $id);
				       if (empty($mu_student_id))
					       $mu_student_id = '   ';
				       echo $mu_student_id; ?>"
					<?php if (getRank($_SESSION['id']) < OFFICER_RANK) echo 'disabled'; ?>><br>

                <label for="is_famat_member" style="color: red;">Is FAMAT Member:</label>
                <select id="is_famat_member" name="is_famat_member"
					<?php if (getRank($_SESSION['id']) < OFFICER_RANK) echo 'disabled'; ?>>
                    <option value="0" <?php if (getAccountDetail('competitor_info', 'is_famat_member', $id) == 0) echo 'selected'; ?>>
                        No
                    </option>

                    <option value="1" <?php if (getAccountDetail('competitor_info', 'is_famat_member', $id) == 1) echo 'selected'; ?>>
                        Yes
                    </option>
                </select><br>

                <label for="is_national_member" style="color: red;">Is National Member:</label>
                <select id="is_national_member" name="is_national_member"
					<?php if (getRank($_SESSION['id']) < OFFICER_RANK) echo 'disabled'; ?>>
                    <option value="0" <?php if (getAccountDetail('competitor_info', 'is_national_member', $id) == 0) echo 'selected'; ?>>
                        No
                    </option>

                    <option value="1" <?php if (getAccountDetail('competitor_info', 'is_national_member', $id) == 1) echo 'selected'; ?>>
                        Yes
                    </option>
                </select><br>

                <label for="has_medical" style="color: red;">Medical:</label>
                <select id="has_medical" name="has_medical"
					<?php if (getRank($_SESSION['id']) < OFFICER_RANK) echo 'disabled'; ?>>
                    <option value="0" <?php if (getAccountDetail('competitor_info', 'has_medical', $id) == 0) echo 'selected'; ?>>
                        No
                    </option>

                    <option value="1" <?php if (getAccountDetail('competitor_info', 'has_medical', $id) == 1) echo 'selected'; ?>>
                        Yes
                    </option>
                </select><br>

                <label for="has_insurance" style="color: red;">Insurance:</label>
                <select id="has_insurance" name="has_insurance"
					<?php if (getRank($_SESSION['id']) < OFFICER_RANK) echo 'disabled'; ?>>
                    <option value="0" <?php if (getAccountDetail('competitor_info', 'has_insurance', $id) == 0) echo 'selected'; ?>>
                        No
                    </option>

                    <option value="1" <?php if (getAccountDetail('competitor_info', 'has_insurance', $id) == 1) echo 'selected'; ?>>
                        Yes
                    </option>
                </select><br>

                <label for="has_school_insurance" style="color: red;">School Insurance:</label>
                <select id="has_school_insurance" name="has_school_insurance"
					<?php if (getRank($_SESSION['id']) < OFFICER_RANK) echo 'disabled'; ?>>
                    <option value="0" <?php if (getAccountDetail('competitor_info', 'has_school_insurance', $id) == 0) echo 'selected'; ?>>
                        No
                    </option>

                    <option value="1" <?php if (getAccountDetail('competitor_info', 'has_school_insurance', $id) == 1) echo 'selected'; ?>>
                        Yes
                    </option>
                </select><br>

                <input name="update_competitor_info" type="submit" value="Update" style="margin-top: 6px">
            </fieldset>
        </form>
    </div>
</div>
