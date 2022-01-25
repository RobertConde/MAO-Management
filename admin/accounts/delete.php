<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(OFFICER_PERMS);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

$deleted = null;
$post_id = getSelectID('POST');
if (isset($_POST['delete']) && !is_null(getDetail('people', 'id', 'id', $post_id))) { // If form is POSTed
	require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/accounts/UD.php";

	$deleted = deleteAccount($post_id); // Delete account
	checkPerms(OFFICER_PERMS);
}

$id = null;
if (isset($_GET['select-id'])) {
	$select_id = getSelectID();

	$rankComp = compareRank($_SESSION['id'], $select_id, true);
	if (!is_null($rankComp)) {
		if ($rankComp)
			$id = $select_id;
		else
			die("<p style='color:red;'>You do not have the required permissions!</p>");
	}
}
?>

    <title>MAO | Delete Account</title>

    <h2 style="margin: 6px;"><u>Delete Account</u></h2>

<?php
personSelectForm();
personSelect();
?><br>

    <!--    TODO: fix styling   -->
    <form method="post" style="margin: 6px;">
        <fieldset class="filled border">
            <legend>Account Information</legend>

            <label for="id">ID:</label>
            <input id="id" type="text" pattern="[0-9]{7}" size="7" required
                   value="<?php echo $id; ?>"
                   disabled>
            <input name="select-id" type="hidden" value="<?php echo $id; ?>"><br>

            <label for="first_name">First Name:</label>
            <input id="first_name" name="first_name" type="text" size="15"
                   value="<?php echo getAccountDetail('people', 'first_name', $id); ?>"
                   disabled><br>

            <label for="last_name">Last Name:</label>
            <input id="last_name" name="last_name" type="text" size="15" required
                   value="<?php echo getAccountDetail('people', 'last_name', $id); ?>"
                   disabled><br>

            <label for="grade">Grade:</label>
            <select id="grade" name="grade" required
                    disabled>
                <option value="6" <?php echo getGrade($id) == 6 ? "selected" : ""; ?>>6th
                    Grade
                </option>
                <option value="7" <?php echo getGrade($id) == 7 ? "selected" : ""; ?>>7th
                    Grade
                </option>
                <option value="8" <?php echo getGrade($id) == 8 ? "selected" : ""; ?>>8th
                    Grade
                </option>
                <option value="9" <?php echo getGrade($id) == 9 ? "selected" : ""; ?>>9th
                    Grade
                </option>
                <option value="10" <?php echo getGrade($id) == 10 ? "selected" : ""; ?>>10th
                    Grade
                </option>
                <option value="11" <?php echo getGrade($id) == 11 ? "selected" : ""; ?>>11th
                    Grade
                </option>
                <option value="12" <?php echo getGrade($id) == 12 ? "selected" : ""; ?>>12th
                    Grade
                </option>
                <option value="0" <?php echo getGrade($id) == 0 ? "selected" : ""; ?>>Not a
                    Student
                </option>
            </select>

			<?php
			if (!is_null($id))
				echo "<br><br><input id='delete' name='delete' type='submit' value='Delete Account'>";
			?>
        </fieldset>
    </form>

    <h3 style="margin-bottom: 6px;"><u><i>Read</i>, Before You Click (Delete):</u></h3>
    <ul style="display: inline-block; margin: 6px; text-align: left;">
        <li>Account Information will be Deleted</li>
        <li>Account Login Code will be Deleted</li>
        <li>Account Transactions will be Deleted</li>
        <li>Competition Selections will be Deleted</li>
        <li>Competition Forms will be Deleted</li>
    </ul><br>

<?php
if (!is_null($deleted)) {
	echo $deleted ? "<p style='color:green;'>Successfully deleted.</p>\n" :
		"<p style='color:red;'>Failed to delete!</p>\n";
}

if (is_null($id) && isset($_GET['id']))
	echo "<i class='rainbow'>Account not found!</i>\n";
