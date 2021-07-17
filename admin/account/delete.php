<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
stylesheet();
navigationBar();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/checks.php";
checkPerms(OFFICER);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/SQL.php";

$deleted = null;
if (isset($_POST['delete']) && !is_null(getDetail('people', 'id', 'id', $_POST['id']))) { // If form is POSTed
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/account/UD.php";

	$deleted = deleteAccount($_POST['id']); // Delete account
}

$id = null;
if (isset($_GET['id'])) {
	$rankComp = checkCompareRank($_SESSION['id'], $_GET['id'], true);

	if (!is_null($rankComp)) {
		if ($rankComp)
			$id = $_GET['id'];
		else
			die("<p style=\"color:red;\">You do not have the required permissions!</p>\n");
	}
}
?>

<html lang="en">
<h2><u>Delete Account</u></h2>

<form method="get">
    <label for="id"><i>Search ID:</i></label>
    <input id="id" name="id" type="search">
    <input id="search" type="submit" value="Search">
</form>

<hr>

<form method="post" action="delete">
    <fieldset>
        <legend>Account Information</legend>

        <label for="id">ID</label>
        <input id="id" type="text" pattern="[0-9]{7}" required
               value="<?php echo $id ?>"
               disabled>
        <input name="id" type="hidden" value="<?php echo $id ?>"><br>
        <br>
        <label for="fname">First Name:</label>
        <input id="fname" name="fname" type="text"
               value="<?php echo getAccountDetail('people', 'fname', $id) ?>"
               disabled><br>
        <br>
        <label for="lname">Last Name:</label>
        <input id="lname" name="lname" type="text" required
               value="<?php echo getAccountDetail('people', 'lname', $id) ?>"
               disabled><br>
        <br>
        <label for="grade">Grade:</label>
        <select id="grade" name="grade" required
                disabled>
            <option value="6"  <?php echo getAccountDetail('people', 'grade', $id) == 6 ? "selected" : "" ?>>6th Grade</option>
            <option value="7"  <?php echo getAccountDetail('people', 'grade', $id) == 7 ? "selected" : "" ?>>7th Grade</option>
            <option value="8"  <?php echo getAccountDetail('people', 'grade', $id) == 8 ? "selected" : "" ?>>8th Grade</option>
            <option value="9"  <?php echo getAccountDetail('people', 'grade', $id) == 9 ? "selected" : "" ?>>9th Grade</option>
            <option value="10" <?php echo getAccountDetail('people', 'grade', $id) == 10 ? "selected" : "" ?>>10th Grade</option>
            <option value="11" <?php echo getAccountDetail('people', 'grade', $id) == 11 ? "selected" : "" ?>>11th Grade</option>
            <option value="12" <?php echo getAccountDetail('people', 'grade', $id) == 12 ? "selected" : "" ?>>12th Grade</option>
            <option value="0"  <?php echo getAccountDetail('people', 'grade', $id) == 0 ? "selected" : "" ?>>Not a Student</option>
        </select><br>
        <br>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required
               value="<?php echo getAccountDetail('people', 'email', $id) ?>"
               disabled><br>
        <br>
        <label for="phone">Phone Number</label>
        <input id="phone" name="phone" type="tel" pattern="[0-9]{10}" required
               value="<?php echo getAccountDetail('people', 'phone', $id) ?>"
               disabled><br>
        <br>
        <label for="division">Division</label>
        <select id="division" name="division" required
                disabled>
            <option value="1" <?php echo getAccountDetail('people', 'division', $id) == 1 ? "selected" : "" ?>>Algebra I</option>
            <option value="2" <?php echo getAccountDetail('people', 'division', $id) == 2 ? "selected" : "" ?>>Geometry</option>
            <option value="3" <?php echo getAccountDetail('people', 'division', $id) == 3 ? "selected" : "" ?>>Algebra II</option>
            <option value="4" <?php echo getAccountDetail('people', 'division', $id) == 4 ? "selected" : "" ?>>Precalculus</option>
            <option value="5" <?php echo getAccountDetail('people', 'division', $id) == 5 ? "selected" : "" ?>>Calculus</option>
            <option value="6" <?php echo getAccountDetail('people', 'division', $id) == 6 ? "selected" : "" ?>>Statistics</option>
            <option value="0" <?php echo getAccountDetail('people', 'division', $id) == 0 ? "selected" : "" ?>>Not a Student</option>
        </select>
    </fieldset><br>

    <h3><u><i>Read</i>, Before You Click (Delete):</u></h3>
    <ul>
        <li>Account Information will be Deleted</li>
        <li>Account Login Code will be Deleted</li>
        <li>Account Transactions will be Deleted</li>
    </ul>
    <?php
    if (!is_null($id))
        echo "\n<input id='delete' name='delete' type='submit' value='Delete Account'>\n";
    ?>
</form>
</html>

<?php
if (!is_null($deleted)) {
	echo $deleted ? "<p style=\"color:green;\">Successfully deleted.</p>\n" :
		"<p style=\"color:red;\">Failed to delete!</p>\n";
}

if (is_null($id) && isset($_GET['id']))
	echo "<i class='rainbow'>Account not found!</i>\n";
