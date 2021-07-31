<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/checks.php";
checkPerms(STUDENT);
?>

<title>DB | Update Account</title>

<?php
// Update student process
$updated_student = null;
if (isset($_POST['update_student'])) {  // Process POST update
	if ($_SESSION['id'] != $_POST['id'] && !checkCompareRank($_SESSION['id'], $_POST['id']))   // Confirm rank is higher (so that people can't update through POST requests without being logged into an account of higher rank)
		die("<p style=\"color:red;\">You do not have the required permissions!</p>\n");

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

	$updated_student = updateAccount_Student($_POST['id'], $_POST['fname'], $_POST['minitial'], $_POST['lname'], $_POST['email'], $_POST['phone'], $_POST['division'], $_POST['grade'],
		$_POST['p1'], $_POST['p2'], $_POST['p3'], $_POST['p4'], $_POST['p5'], $_POST['p6'], $_POST['p7'], $_POST['p8'],
		$_SESSION['id']);
}

// Update admin process
$updated_admin = null;
if (isset($_POST['update_admin'])) {  // Process POST update
	if ($_SESSION['id'] != $_POST['id'] && !checkCompareRank($_SESSION['id'], $_POST['id']))   // Confirm rank is higher (so that people can't update through POST requests without being logged into an account of higher rank)
		die("<p style=\"color:red;\">You do not have the required permissions!</p>\n");

	require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";

	$updated_admin = updateAccount_Admin($_POST['id'], $_POST['perms'], $_POST['mu_student_id'], $_POST['member_famat'], $_POST['member_nation'], $_POST['medical'], $_POST['insurance'], $_POST['school_insurance'], $_SESSION['id']);
}

// View form (using correct ID)
$id = $_SESSION['id'];
if (isset($_GET['id'])) {
	$rankComp = checkCompareRank($_SESSION['id'], $_GET['id'], true);

	if (!is_null($rankComp)) {
		if ($rankComp)
			$id = $_GET['id'];
		else
			die("<p style=\"color:red;\">You do not have the required permissions!</p>\n");
	}
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
?>

<h2><u>Update Account Information</u></h2>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";
if (getRank($_SESSION['id']) >= 1)
	echo "<form method=\"get\">\n",
	"<label for=\"id\"><i>Search ID:</i></label>\n",
	"<input id=\"id\" name=\"id\" type=\"search\" pattern=\"[0-9]{7}\">\n",
	"<input id=\"search\" type=\"submit\" value=\"Search\">\n",
	"</form>\n",
	"<hr>\n";
?>

<?php
if ($id != $_SESSION['id'])
	echo "<p style=\"color:violet;\"><i><b>Note:</b> You are updating an account that isn't yours, and has a permission rank below you!</i></p>\n";

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
?>

<form method="post" action="updateInfo.php?id=<?php echo $id ?>" style="float: left;">
    <fieldset>
        <legend><b>Student Information</b></legend>

        <label for="id"><i>ID</i>
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext"><i>Cannot</i> be updated!<br>Ask admin.</span>
            </div>
            :
        </label>
        <input id="id" type="text" pattern="[0-9]{7}" required
               value="<?php echo $id ?>"
               disabled>
        <input name="id" type="hidden" value="<?php echo $id ?>"><br>
        <br>

        <label for="fname">First Name:</label>
        <input id="fname" name="fname" type="text" required
               value="<?php echo getAccountDetail('people', 'fname', $id) ?>"><br>
        <br>

        <label for="minitial">Middle Initial:</label>
        <input id="minitial" name="minitial" type="text" maxlength="1" size="1"
               value="<?php echo getAccountDetail('people', 'minitial', $id) ?>"><br>
        <br>

        <label for="lname">Last Name:</label>
        <input id="lname" name="lname" type="text" required
               value="<?php echo getAccountDetail('people', 'lname', $id) ?>"><br>
        <br>

        <label for="email">Email
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Will be used to send you <i>login codes</i> for your account!</span>
            </div>
            :
        </label>
        <input id="email" name="email" type="email" required
               value="<?php echo getAccountDetail('people', 'email', $id) ?>"><br>
        <br>

        <label for="phone">Phone Number
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Must be a 10-digit US phone number. Only type the digits!</span>
            </div>
            :
        </label>
        <input id="phone" name="phone" type="tel" pattern="[0-9]{10}" required
               value="<?php echo getAccountDetail('people', 'phone', $id) ?>"><br>
        <br>

        <label for="division">Division
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Select the division you <i>will</i> compete the most this competition cycle.</span>
            </div>
            :
        </label>
        <select id="division" name="division" required>
            <option value="1" <?php echo getAccountDetail('people', 'division', $id) == 1 ? "selected" : "" ?>>
                Algebra I
            </option>
            <option value="2" <?php echo getAccountDetail('people', 'division', $id) == 2 ? "selected" : "" ?>>
                Geometry
            </option>
            <option value="3" <?php echo getAccountDetail('people', 'division', $id) == 3 ? "selected" : "" ?>>
                Algebra II
            </option>
            <option value="4" <?php echo getAccountDetail('people', 'division', $id) == 4 ? "selected" : "" ?>>
                Precalculus
            </option>
            <option value="5" <?php echo getAccountDetail('people', 'division', $id) == 5 ? "selected" : "" ?>>
                Calculus
            </option>
            <option value="6" <?php echo getAccountDetail('people', 'division', $id) == 6 ? "selected" : "" ?>>
                Statistics
            </option>
            <option value="0" <?php echo getAccountDetail('people', 'division', $id) == 0 ? "selected" : "" ?>>Not a
                Student
            </option>
        </select><br>
        <br>

        <label for="grade">Grade:</label>
        <select id="grade" name="grade" required>
            <option value="6" <?php echo getAccountDetail('people', 'grade', $id) == 6 ? "selected" : "" ?>>6th
                Grade
            </option>
            <option value="7" <?php echo getAccountDetail('people', 'grade', $id) == 7 ? "selected" : "" ?>>7th
                Grade
            </option>
            <option value="8" <?php echo getAccountDetail('people', 'grade', $id) == 8 ? "selected" : "" ?>>8th
                Grade
            </option>
            <option value="9" <?php echo getAccountDetail('people', 'grade', $id) == 9 ? "selected" : "" ?>>9th
                Grade
            </option>
            <option value="10" <?php echo getAccountDetail('people', 'grade', $id) == 10 ? "selected" : "" ?>>10th
                Grade
            </option>
            <option value="11" <?php echo getAccountDetail('people', 'grade', $id) == 11 ? "selected" : "" ?>>11th
                Grade
            </option>
            <option value="12" <?php echo getAccountDetail('people', 'grade', $id) == 12 ? "selected" : "" ?>>12th
                Grade
            </option>
            <option value="0" <?php echo getAccountDetail('people', 'grade', $id) == 0 ? "selected" : "" ?>>Not a
                Student
            </option>
        </select><br>
        <br>

        <fieldset>
            <legend><b><i>School Schedule</i></b>
                <div class="tooltip"><i class="fa fa-question-circle"></i>
                    <span class="tooltiptext">Include room # and course name.</span>
                </div>
                <b><i>:</i></b>
            </legend>


            <label for="p1">Period 1:</label>
            <input id="p1" name="p1" type="text"
                   value="<?php echo getAccountDetail('people', 'p1', $id) ?>"><br>

            <label for="p2">Period 2:</label>
            <input id="p2" name="p2" type="text"
                   value="<?php echo getAccountDetail('people', 'p2', $id) ?>"><br>

            <label for="p3">Period 3:</label>
            <input id="p3" name="p3" type="text"
                   value="<?php echo getAccountDetail('people', 'p3', $id) ?>"><br>

            <label for="p4">Period 4:</label>
            <input id="p4" name="p4" type="text"
                   value="<?php echo getAccountDetail('people', 'p4', $id) ?>"><br>

            <label for="p5">Period 5:</label>
            <input id="p5" name="p5" type="text"
                   value="<?php echo getAccountDetail('people', 'p5', $id) ?>"><br>

            <label for="p6">Period 6:</label>
            <input id="p6" name="p6" type="text"
                   value="<?php echo getAccountDetail('people', 'p6', $id) ?>"><br>

            <label for="p7">Period 7:</label>
            <input id="p7" name="p7" type="text"
                   value="<?php echo getAccountDetail('people', 'p7', $id) ?>"><br>

            <label for="p8">Period 8:</label>
            <input id="p8" name="p8" type="text"
                   value="<?php echo getAccountDetail('people', 'p8', $id) ?>"><br>
        </fieldset>
        <br>
        <br>

        <input name="update_student" type="submit" value="Update Student">
    </fieldset>

	<?php
	if (!is_null($updated_student)) {
		echo $updated_student ? "<p style=\"color: green;\">Successfully (student) updated account information (ID Updated = " . $_POST['id'] . ").</p>\n" :
			"<p style=\"color: red;\">Failed to (student) update account information (ID = " . $_POST['id'] . ").</p>\n";
	}
	?>
</form>


<form method="post" action="updateInfo.php?id=<?php echo $id ?>">
    <fieldset <?php if (getRank($_SESSION['id']) < 1) echo 'disabled'; ?>>
        <legend><b>Administrative Information</b></legend>

        <input name="id" type="hidden" value="<?php echo $id ?>">

        <label for="perms">Permissions:</label>
        <select id="perms" name="perms">
            <option value="1" <?php echo getAccountDetail('people', 'perms', $id) == 1 ? "selected" : "" ?>>
                Student
            </option>
            <option value="10" <?php echo getAccountDetail('people', 'perms', $id) == 10 ? "selected" : "" ?>>
                Officer
            </option>
            <option value="100" <?php echo getAccountDetail('people', 'perms', $id) == 100 ? "selected" : "" ?>>
                Admin
            </option>
        </select><br>
        <br>

        <label for="mu_student_id">Mu Student ID:</label>
        <input id="mu_student_id" name="mu_student_id" type="text" pattern="[0-9\s]{3}" size="3" required
               value="<?php echo getAccountDetail('people', 'mu_student_id', $id) ?>"><br>
        <br>

        <label for="member_famat">Is FAMAT Member:</label>
        <select id="member_famat" name="member_famat">
            <option value="0" <?php echo getAccountDetail('people', 'member_famat', $id) == 0 ? "selected" : "" ?>>
                Nope
            </option>
            <option value="1" <?php echo getAccountDetail('people', 'member_famat', $id) == 1 ? "selected" : "" ?>>
                Yup
            </option>
        </select><br>
        <br>

        <label for="member_nation">Is Nation Member:</label>
        <select id="member_nation" name="member_nation">
            <option value="0" <?php echo getAccountDetail('people', 'member_nation', $id) == 0 ? "selected" : "" ?>>
                Nope
            </option>
            <option value="1" <?php echo getAccountDetail('people', 'member_nation', $id) == 1 ? "selected" : "" ?>>
                Yup
            </option>
        </select><br>
        <br>

        <label for="medical">Medical?:</label>
        <select id="medical" name="medical">
            <option value="0" <?php echo getAccountDetail('people', 'medical', $id) == 0 ? "selected" : "" ?>>
                Nope
            </option>
            <option value="1" <?php echo getAccountDetail('people', 'medical', $id) == 1 ? "selected" : "" ?>>
                Yup
            </option>
        </select><br>
        <br>

        <label for="insurance">Insurance?:</label>
        <select id="insurance" name="insurance">
            <option value="0" <?php echo getAccountDetail('people', 'insurance', $id) == 0 ? "selected" : "" ?>>
                Nope
            </option>
            <option value="1" <?php echo getAccountDetail('people', 'insurance', $id) == 1 ? "selected" : "" ?>>
                Yup
            </option>
        </select><br>
        <br>

        <label for="school_insurance">School Insurance?:</label>
        <select id="school_insurance" name="school_insurance">
            <option value="0" <?php echo getAccountDetail('people', 'school_insurance', $id) == 0 ? "selected" : "" ?>>
                Nope
            </option>
            <option value="1" <?php echo getAccountDetail('people', 'school_insurance', $id) == 1 ? "selected" : "" ?>>
                Yup
            </option>
        </select><br>
        <br>

        <input name="update_admin" type="submit" value="Update Admin">
    </fieldset>

	<?php
	if (!is_null($updated_admin)) {
		echo $updated_admin ? "<p style=\"color: green;\">Successfully (admin) updated account information (ID Updated = " . $_POST['id'] . ").</p>\n" :
			"<p style=\"color: red;\">Failed to (admin) update account information (ID = " . $_POST['id'] . ").</p>\n";
	}
	?>
</form>
