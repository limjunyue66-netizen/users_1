<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function auth_login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'username' => (string) $user['username'],
        'email' => (string) $user['email'],
    ];
}

$error = '';
$loginId = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginId = trim((string) ($_POST['login_id'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($loginId === '' || $password === '') {
        $error = 'Please enter your username/email and password.';
    } else {
        $statement = db_connect()->prepare('SELECT id, username, email, password_hash FROM users WHERE username = :value OR email = :value LIMIT 1');
        $statement->execute([':value' => $loginId]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Invalid login details.';
        } else {
            auth_login_user($user);
            header('Location: text.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <p>Use your username or email address.</p>

    <?php if ($error !== ''): ?>
        <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <p>
            <label for="login_id">Username or email</label><br>
            <input id="login_id" name="login_id" type="text" value="<?php echo htmlspecialchars($loginId, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="username" required>
        </p>
        <p>
            <label for="password">Password</label><br>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </p>
        <button type="submit">Sign in</button>
    </form>

    <p><a href="register.html">Create account</a></p>
</body>
</html>
