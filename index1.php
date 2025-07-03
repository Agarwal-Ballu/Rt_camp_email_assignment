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
        sendVerificationEmail($email, $code);
    } elseif (isset($_POST['submit-verification'])) {
        if ($_POST['verification_code'] === $_SESSION['reg_code']) {
            registerEmail($_SESSION['email']);
            $_SESSION['step'] = 3;
            unset($_SESSION['reg_code']);
        } else {
            $err = 'Invalid verification code.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Email Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="content">
        <h1>Email <br><span>Verification</span> <br>System</h1>
        <p class="par">Subscribe with your email to receive updates. You'll get a verification code to confirm registration.</p>
       
    </div>

    <div class="form" id="form">
        <?php if ($step === 1): ?>
            <h2>Register your Email</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button class="btn" id="submit-email" name="submit-email">Submit</button>
            </form>
        <?php elseif ($step === 2): ?>
            <h2>Enter Verification Code</h2>
            <form method="POST">
                <input type="text" name="verification_code" placeholder="6-digit code" maxlength="6" required>
                <button class="btn" id="submit-verification" name="submit-verification">Verify</button>
            </form>
            <?php if ($err): ?>
                <p style="color: red; text-align:center;"><?= htmlspecialchars($err) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <h2 style="color: lightgreen;">✅ Registered Successfully!</h2>
            <p style="text-align: center;">You'll receive updates in your inbox.</p>
        <?php endif; ?>
        <div style="text-align: center; margin-top: 15px;">
            <a href="unsubscribe.php" style="color: #ff7200; text-decoration: underline;">Unsubscribe?</a>
        </div>
    </div>
</div>
</body>
</html>
