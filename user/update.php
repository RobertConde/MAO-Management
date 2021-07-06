<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/require/htmlSnippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/require/checks.php";
checkPerms();

// Update process
$updated = null;
if (isset($_POST['update'])) {  // Process POST update
//	echo "SESSION<br>";
//	foreach ($_SESSION as $key => $value) {
//		echo "Key: $key; Value: $value<br>";
//	}
//	echo "<br>", "POST<br>";
//	foreach ($_POST as $key => $value) {
//		echo "Key: $key; Value: $value<br>";
//	}

    if ($_SESSION['id'] != $_POST['id'] && !checkCompareRank($_SESSION['id'], $_POST['id']))   // Confirm rank is higher (so that people can't update through POST requests without being logged into an account of higher rank)
	    die("<p style=\"color:red;\">You do not have the required permissions!</p>\n");

	require_once $_SERVER['DOCUMENT_ROOT'] . "/require/basicAccountManage.php";

	$updated =  updateAccount($_POST['id'] , $_POST['fname'], $_POST['lname'], $_POST['grade'], $_POST['email'], $_POST['phone'], $_POST['division'], $_SESSION['id']);
}

// View form (using correct ID)
$id = $_SESSION['id'];
if (isset($_GET['id']) && $_GET['id'] != $_SESSION['id']) {
	checkPerms(10);

    if (checkCompareRank($_SESSION['id'], $_GET['id']))
        $id = $_GET['id'];
    else
	    die("<p style=\"color:red;\">You do not have the required permissions!</p>\n");
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/require/sql.php";
?>

<html lang="en">
<h2><u>MAO Account Update</u></h2>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/require/sql.php";
if (getRank($_SESSION['id']) >= 1)
    echo "<form method=\"get\">\n",
         "<label for=\"id\"><i>Search ID:</i></label>\n",
         "<input id=\"id\" name=\"id\" type=\"search\" pattern=\"[0-9]{7}\" required>\n",
         "<input id=\"search\" type=\"submit\" value=\"Search\">\n",
         "</form>\n",
         "<hr>\n";
?>

<h3>Account Information</h3>
<?php
if ($id != $_SESSION['id'])
	echo "<p style=\"color:violet;\"><i><b>Note:</b> You are updating an account that isn't yours, and has a permission rank below you!</i></p>\n";
?>

<form method="post" action="update.php?id=<?php echo $id ?>">
    <fieldset>
        <label for="id"><i>ID</i>
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext"><i>Cannot</i> be updated!<br>Ask admin.</span>
            </div>:
        </label>
        <input id="id" name="id" type="text" pattern="[0-9]{7}" required
               value="<?php echo $id ?>"
               readonly
               style="background: #ccc;"><br>
        <br>
        <label for="fname">First Name:</label>
        <input id="fname" name="fname" type="text"
               value="<?php echo getDetail('people', 'fname', $id) ?>"><br>
        <br>
        <label for="lname">Last Name:</label>
        <input id="lname" name="lname" type="text" required
               value="<?php echo getDetail('people', 'lname', $id) ?>"><br>
        <br>
        <label for="grade">Grade:</label>
        <select id="grade" name="grade" required>
            <option disabled></option>
            <option value="6"  <?php echo getDetail('people', 'grade', $id) == 6 ? "selected" : "" ?>>6th Grade</option>
            <option value="7"  <?php echo getDetail('people', 'grade', $id) == 7 ? "selected" : "" ?>>7th Grade</option>
            <option value="8"  <?php echo getDetail('people', 'grade', $id) == 8 ? "selected" : "" ?>>8th Grade</option>
            <option value="9"  <?php echo getDetail('people', 'grade', $id) == 9 ? "selected" : "" ?>>9th Grade</option>
            <option value="10" <?php echo getDetail('people', 'grade', $id) == 10 ? "selected" : "" ?>>10th Grade</option>
            <option value="11" <?php echo getDetail('people', 'grade', $id) == 11 ? "selected" : "" ?>>11th Grade</option>
            <option value="12" <?php echo getDetail('people', 'grade', $id) == 12 ? "selected" : "" ?>>12th Grade</option>
            <option value="0"  <?php echo getDetail('people', 'grade', $id) == 0 ? "selected" : "" ?>>Not a Student</option>
        </select><br>
        <br>
        <label for="email">Email
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Will be used to send you <i>login codes</i> for your account!</span>
            </div>:
        </label>
        <input id="email" name="email" type="email" required
               value="<?php echo getDetail('people', 'email', $id) ?>"><br>
        <br>
        <label for="phone">Phone Number
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Must be a 10-digit US phone number. Only type the digits!</span>
            </div>:
        </label>
        <input id="phone" name="phone" type="tel" pattern="[0-9]{10}" required
               value="<?php echo getDetail('people', 'phone', $id) ?>"><br>
        <br>
        <label for="division">Division
            <div class="tooltip"><i class="fa fa-question-circle"></i>
                <span class="tooltiptext">Select the division you <i>will</i> compete the most this competition cycle.</span>
            </div>:
        </label>
        <select id="division" name="division" required>
            <option disabled></option>
            <option value="1" <?php echo getDetail('people', 'division', $id) == 1 ? "selected" : "" ?>>Algebra I</option>
            <option value="2" <?php echo getDetail('people', 'division', $id) == 2 ? "selected" : "" ?>>Geometry</option>
            <option value="3" <?php echo getDetail('people', 'division', $id) == 3 ? "selected" : "" ?>>Algebra II</option>
            <option value="4" <?php echo getDetail('people', 'division', $id) == 4 ? "selected" : "" ?>>Precalculus</option>
            <option value="5" <?php echo getDetail('people', 'division', $id) == 5 ? "selected" : "" ?>>Calculus</option>
            <option value="6" <?php echo getDetail('people', 'division', $id) == 6 ? "selected" : "" ?>>Statistics</option>
            <option value="0" <?php echo getDetail('people', 'division', $id) == 0 ? "selected" : "" ?>>Not a Student</option>
        </select>
    </fieldset><br>
    <br>
    <input id="update" name="update" type="submit"
           value="Update<?php
           if ($id != $_SESSION['id'])
               echo " (Someone Else's Account)";
           ?>">

</form>
</html>

<?php
if (isset($updated)) {
	echo $updated ?
		"<p style=\"color:green;\">Successfully updated account information (ID Updated = " . $_POST['id'] . ").</p>\n" :
		"<p style=\"color:red;\">Failed to update account information (ID = " . $_POST['id'] . ").</p>\n";
}
