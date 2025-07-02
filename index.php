<?php
require 'functions.php';
session_start();

$step = $_SESSION['step'] ?? 1;
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit-email'])) {
        $email = $_POST['email'];
        $code = generateVerificationCode();
        $_SESSION['email'] = $email;
        $_SESSION['reg_code'] = $code;
        $_SESSION['step'] = 2;
        sendVerificationEmail($email, $code); // Email subject/body format handled inside
    } elseif (isset($_POST['submit-verification'])) {
        if ($_POST['verification_code'] === $_SESSION['reg_code']) {
            registerEmail($_SESSION['email']);
            $_SESSION['step'] = 3;
            unset($_SESSION['reg_code']);
        } else {
            $err = 'Invalid code';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body>
<?php if ($step === 1): ?>
    <form method="POST">
        <!-- 📧 Email Input & Submission Button -->
        <input type="email" name="email" required>
        <button id="submit-email" name="submit-email">Submit</button>
    </form>
<?php elseif ($step === 2): ?>
    <p>Check your email for the code.</p>
    <form method="POST">
        <!-- 🔢 Verification Code Input & Submission Button -->
        <input type="text" name="verification_code" maxlength="6" required>
        <button id="submit-verification" name="submit-verification">Verify</button>
    </form>
    <?php if ($err): ?>
        <p style="color:red;"><?= htmlspecialchars($err) ?></p>
    <?php endif; ?>
<?php else: ?>
    <p>Registration successful! You’ll get updates soon.</p>
<?php endif; ?>
</body>
</html>
