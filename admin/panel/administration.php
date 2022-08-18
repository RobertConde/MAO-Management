<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/accounts.php";
safelyStartSession();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/snippets.php";
navigationBarAndBootstrap();
stylesheet();

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/permissions.php";
checkPerms(ADMIN_PERMS);

require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";

if (isset($_POST['reset_not_graduated_confirmations'])) {
	resetNotGraduatedConfirmations();
    redirect(currentURL());
}
?>

<title>MAO | Administration</title>

<h2 style="text-align: center; margin: 6px;"><u>Administration Panel</u></h2>

<form method="post">
    <fieldset style="text-align: center;">
        <legend>Account Confirmations</legend>

        <div class="form-group">
            <input name="reset_not_graduated_confirmations" type="submit" value="Reset Not Graduated Confirmations"
                   class="btn">
        </div>

        <div class="form-group">
            <input type="button" value="View Confirmations" class="btn"
                   onclick="window.open('<?php echo relativeURL('/admin/reports/confirmations') ?>', '_blank')">
        </div>
    </fieldset>
</form>