<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eva Nail Studio</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<header class="navbar">
    <div class="logo">
        <img src="images/logo.png" alt="Logo">
    </div>

    <nav class="nav-links">
        <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
        <a href="booking.php" class="<?php echo ($current_page == 'booking.php') ? 'active' : ''; ?>">Booking</a>
        <a href="services.php" class="<?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">Services</a>
        <a href="reviews.php" class="<?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">Reviews</a>
        <a href="gallery.php" class="<?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">Completed Works</a>
        <a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
            <a href="admin.php" class="<?php echo ($current_page == 'admin.php' || $current_page == 'manage_appointments.php' || $current_page == 'manage_reviews.php' || $current_page == 'manage_gallery.php') ? 'active' : ''; ?>">Admin</a>
        <?php endif; ?>
    </nav>

    <div class="nav-button">
        <?php if (isset($_SESSION['user_id'])) : ?>
            <a href="logout.php" class="login-btn">Logout</a>
        <?php else : ?>
            <a href="login.php" class="login-btn <?php echo ($current_page == 'login.php') ? 'active-login' : ''; ?>">Login</a>
        <?php endif; ?>
    </div>
</header>