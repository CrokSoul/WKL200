<?php
session_start();
include 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $sql = "SELECT id, first_name, surname, email, password, role FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['surname'] = $user['surname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'admin') {
                    header("Location: admin.php");
                    exit();
                } else {
                    header("Location: index.php");
                    exit();
                }
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "No account found with this email.";
        }
    }
}

include 'includes/header.php';
?>

<main class="auth-page">
    <div class="auth-box">
        <?php if (!empty($message)) : ?>
            <p class="form-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="" method="POST" class="auth-form">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="auth-btn">Login</button>
        </form>

        <p class="auth-switch">
            Don’t have an account?
            <a href="register.php">Register here!</a>
        </p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>