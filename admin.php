<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pending_count = 0;
$reviews_count = 0;
$gallery_count = 0;

$pending_query = "SELECT COUNT(*) AS total FROM appointments WHERE status = 'pending'";
$pending_result = mysqli_query($conn, $pending_query);
if ($pending_result && $row = mysqli_fetch_assoc($pending_result)) {
    $pending_count = $row['total'];
}

$reviews_query = "SELECT COUNT(*) AS total FROM reviews WHERE status = 'visible'";
$reviews_result = mysqli_query($conn, $reviews_query);
if ($reviews_result && $row = mysqli_fetch_assoc($reviews_result)) {
    $reviews_count = $row['total'];
}

$gallery_query = "SELECT COUNT(*) AS total FROM gallery";
$gallery_result = mysqli_query($conn, $gallery_query);
if ($gallery_result && $row = mysqli_fetch_assoc($gallery_result)) {
    $gallery_count = $row['total'];
}

include 'includes/header.php';
?>

<main class="admin-page">
    <section class="admin-section">
        <div class="admin-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Manage appointments, reviews, and completed works from one place.</p>
            </div>
        </div>

        <div class="admin-line"></div>

        <div class="admin-stats">
            <div class="admin-stat-card">
                <h3>Pending Appointments</h3>
                <p><?php echo $pending_count; ?></p>
            </div>

            <div class="admin-stat-card">
                <h3>Visible Reviews</h3>
                <p><?php echo $reviews_count; ?></p>
            </div>

            <div class="admin-stat-card">
                <h3>Gallery Images</h3>
                <p><?php echo $gallery_count; ?></p>
            </div>
        </div>

        <div class="admin-grid">
            <a href="manage_appointments.php" class="admin-card">
                <div class="admin-card-icon">📅</div>
                <h2>Manage Appointments</h2>
                <p>
                    View all customer bookings and approve, decline, or complete appointments.
                </p>
            </a>

            <a href="manage_reviews.php" class="admin-card">
                <div class="admin-card-icon">★</div>
                <h2>Manage Reviews</h2>
                <p>
                    Review customer feedback and hide or remove reviews when needed.
                </p>
            </a>

            <a href="manage_gallery.php" class="admin-card">
                <div class="admin-card-icon">🖼️</div>
                <h2>Manage Gallery</h2>
                <p>
                    Upload new work photos and keep the completed works page up to date.
                </p>
            </a>

            <a href="manage_services.php" class="admin-card">
                <div class="admin-card-icon">💅</div>
                <h2>Manage Services</h2>
                <p>
                    Add new services, edit prices and descriptions, or remove services from the website.
                </p>
            </a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>