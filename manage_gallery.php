<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_id'])) {
        $delete_id = (int)$_POST['delete_id'];

        $get_image_sql = "SELECT image_name FROM gallery WHERE id = ?";
        $stmt = mysqli_prepare($conn, $get_image_sql);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $file_path = "uploads/" . $row['image_name'];

            $delete_sql = "DELETE FROM gallery WHERE id = ?";
            $stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($stmt, "i", $delete_id);

            if (mysqli_stmt_execute($stmt)) {
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                $message = "Image deleted successfully.";
            } else {
                $message = "Something went wrong while deleting the image.";
            }
        }
    } elseif (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] === 0) {
        $title = trim($_POST['title']);
        $file_name = $_FILES['gallery_image']['name'];
        $tmp_name = $_FILES['gallery_image']['tmp_name'];
        $file_size = $_FILES['gallery_image']['size'];

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($file_ext, $allowed_ext)) {
            $message = "Only JPG, JPEG, PNG, and WEBP files are allowed.";
        } elseif ($file_size > 5 * 1024 * 1024) {
            $message = "File size must be less than 5MB.";
        } else {
            $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
            $destination = "uploads/" . $new_file_name;

            if (move_uploaded_file($tmp_name, $destination)) {
                $insert_sql = "INSERT INTO gallery (image_name, title) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($stmt, "ss", $new_file_name, $title);

                if (mysqli_stmt_execute($stmt)) {
                    $message = "Image uploaded successfully.";
                } else {
                    $message = "Image uploaded, but database insert failed.";
                }
            } else {
                $message = "Failed to upload image.";
            }
        }
    } else {
        if (isset($_POST['upload_image'])) {
            $message = "Please choose an image to upload.";
        }
    }
}

$gallery_sql = "SELECT * FROM gallery ORDER BY uploaded_at DESC";
$gallery_result = mysqli_query($conn, $gallery_sql);

include 'includes/header.php';
?>

<main class="manage-page">
    <section class="manage-section">
        <div class="manage-header">
            <h1>Manage Gallery</h1>
            <a href="admin.php" class="back-admin-btn">Back to Dashboard</a>
        </div>

        <div class="manage-line"></div>

        <?php if (!empty($message)) : ?>
            <p class="form-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="gallery-upload-form">
            <div class="gallery-upload-grid">
                <input type="text" name="title" placeholder="Enter image title (optional)">
                <input type="file" name="gallery_image" accept=".jpg,.jpeg,.png,.webp" required>
            </div>
            <button type="submit" name="upload_image" class="small-action-btn">Upload Image</button>
        </form>

        <div class="admin-gallery-grid">
            <?php if ($gallery_result && mysqli_num_rows($gallery_result) > 0) : ?>
                <?php while ($image = mysqli_fetch_assoc($gallery_result)) : ?>
                    <div class="admin-gallery-card">
                        <img src="uploads/<?php echo htmlspecialchars($image['image_name']); ?>" alt="Gallery image">
                        
                        <div class="admin-gallery-card-body">
                            <h3>
                                <?php echo !empty($image['title']) ? htmlspecialchars($image['title']) : 'Untitled Image'; ?>
                            </h3>

                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                <input type="hidden" name="delete_id" value="<?php echo $image['id']; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="no-reviews-message">No gallery images found.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>