<?php
require 'functions.php';
session_start();
$step = $_SESSION['step'] ?? 1;
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit-unsubscribe'])) {
        $email = $_POST['unsubscribe_email'];
        $code = generateVerificationCode();
        $_SESSION['unsub_email'] = $email;
        $_SESSION['unsub_code'] = $code;
        sendVerificationEmail($email, $code);
        $_SESSION['step'] = 2;
    } elseif (isset($_POST['verify-unsubscribe'])) {
        if ($_POST['unsubscribe_verification_code'] === $_SESSION['unsub_code']) {
            unsubscribeEmail($_SESSION['unsub_email']);
            $_SESSION['step'] = 3;
        } else $err = 'Invalid code';
    }
}
?>
<!DOCTYPE html><html><body>
<?php if ($step === 1): ?>
  <form method="POST">
    <input type="email" name="unsubscribe_email" required>
    <button id="submit-unsubscribe" name="submit-unsubscribe">Unsubscribe</button>
  </form>
<?php elseif ($step === 2): ?>
  <form method="POST">
    <input type="text" name="unsubscribe_verification_code" maxlength="6" required>
    <button id="verify-unsubscribe" name="verify-unsubscribe">Verify</button>
  </form>
  <p style="color:red;"><?= $err ?></p>
<?php else: ?>
  <p>You have been unsubscribed.</p>
<?php endif; ?>
</body></html>