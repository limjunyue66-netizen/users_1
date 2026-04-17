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
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($username === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $pdo = db_connect();
        $statement = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
        $statement->execute([
            ':username' => $username,
            ':email' => $email,
        ]);

        if ($statement->fetch()) {
            $error = 'That username or email is already registered.';
        } else {
            $insert = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)');
            $insert->execute([
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            auth_login_user([
                'id' => (int) $pdo->lastInsertId(),
                'username' => $username,
                'email' => $email,
            ]);

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
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <p>Fill out the form below to create your account.</p>

    <?php if ($error !== ''): ?>
        <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <p>
            <label for="username">Username</label><br>
            <input id="username" name="username" type="text" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="username" required>
        </p>
        <p>
            <label for="email">Email</label><br>
            <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" required>
        </p>
        <p>
            <label for="password">Password</label><br>
            <input id="password" name="password" type="password" autocomplete="new-password" required>
        </p>
        <p>
            <label for="confirm_password">Confirm password</label><br>
            <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required>
        </p>
        <button type="submit">Create account</button>
    </form>

    <p><a href="login.html">Login here</a></p>
</body>
</html>
