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
<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="main">
    <div class="form">
        <?php if ($step === 1): ?>
            <h2>Unsubscribe Email</h2>
            <form method="POST">
                <input type="email" name="unsubscribe_email" placeholder="Enter your email" required>
                <button class="btn" id="submit-unsubscribe" name="submit-unsubscribe">Unsubscribe</button>
            </form>
        <?php elseif ($step === 2): ?>
            <h2>Verify Unsubscribe</h2>
            <form method="POST">
                <input type="text" name="unsubscribe_verification_code" placeholder="Enter verification code" maxlength="6" required>
                <button class="btn" id="verify-unsubscribe" name="verify-unsubscribe">Verify</button>
            </form>
            <p style="color: red; text-align:center;"><?= htmlspecialchars($err) ?></p>
        <?php else: ?>
            <h2 style="color: lightgreen;">You’ve been unsubscribed.</h2>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
