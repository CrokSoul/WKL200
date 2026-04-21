<?php
include 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $surname = trim($_POST['surname']);
    $date_of_birth = $_POST['date_of_birth'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $reenter_password = $_POST['reenter_password'];

    if (empty($first_name) || empty($surname) || empty($date_of_birth) || empty($email) || empty($password) || empty($reenter_password)) {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif ($password !== $reenter_password) {
        $message = "Passwords do not match.";
    } else {
        $check_email = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $check_email);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $message = "This email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_user = "INSERT INTO users (first_name, surname, date_of_birth, email, password, role) VALUES (?, ?, ?, ?, ?, 'user')";
            $stmt = mysqli_prepare($conn, $insert_user);
            mysqli_stmt_bind_param($stmt, "sssss", $first_name, $surname, $date_of_birth, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Registration successful. You can now login.";
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<main class="auth-page">
    <div class="auth-box register-box">
        <?php if (!empty($message)) : ?>
            <p class="form-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="" method="POST" class="auth-form register-form">
            <div class="register-grid">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="surname">Surname</label>
                    <input type="text" id="surname" name="surname" required>
                </div>

                <div class="form-group">
                    <label for="reenter_password">Re-enter Password</label>
                    <input type="password" id="reenter_password" name="reenter_password" required>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>

            <button type="submit" class="auth-btn">Register</button>

            <p class="auth-switch">
                Already have an account?
                <a href="login.php">Login here!</a>
            </p>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>