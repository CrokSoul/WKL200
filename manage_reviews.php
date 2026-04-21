<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['review_id'], $_POST['action'])) {
        $review_id = (int)$_POST['review_id'];
        $action = $_POST['action'];

        if ($action === 'visible' || $action === 'hidden') {
            $update_sql = "UPDATE reviews SET status = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($stmt, "si", $action, $review_id);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Review status updated successfully.";
            } else {
                $message = "Something went wrong while updating the review.";
            }
        } elseif ($action === 'delete') {
            $delete_sql = "DELETE FROM reviews WHERE id = ?";
            $stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($stmt, "i", $review_id);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Review deleted successfully.";
            } else {
                $message = "Something went wrong while deleting the review.";
            }
        } else {
            $message = "Invalid action.";
        }
    }
}

$reviews_sql = "SELECT 
                    reviews.id,
                    reviews.review_text,
                    reviews.rating,
                    reviews.status,
                    reviews.created_at,
                    users.first_name,
                    users.surname
                FROM reviews
                INNER JOIN users ON reviews.user_id = users.id
                ORDER BY reviews.created_at DESC";

$reviews_result = mysqli_query($conn, $reviews_sql);

include 'includes/header.php';
?>

<main class="manage-page">
    <section class="manage-section">
        <div class="manage-header">
            <h1>Manage Reviews</h1>
            <a href="admin.php" class="back-admin-btn">Back to Dashboard</a>
        </div>

        <div class="manage-line"></div>

        <?php if (!empty($message)) : ?>
            <p class="form-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <div class="manage-table-wrapper">
            <table class="manage-table">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reviews_result && mysqli_num_rows($reviews_result) > 0) : ?>
                        <?php while ($review = mysqli_fetch_assoc($reviews_result)) : ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($review['first_name'] . ' ' . $review['surname']); ?>
                                </td>
                                <td>
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <?php if ($i <= (int)$review['rating']) : ?>
                                            <span class="star filled">★</span>
                                        <?php else : ?>
                                            <span class="star empty">★</span>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </td>
                                <td class="review-text-cell">
                                    <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                                </td>
                                <td class="status-<?php echo strtolower($review['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($review['status'])); ?>
                                </td>
                                <td>
                                    <?php echo date("d.m.Y", strtotime($review['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="review-actions">
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <select name="action" required>
                                                <option value="">Change status</option>
                                                <option value="visible">Visible</option>
                                                <option value="hidden">Hidden</option>
                                            </select>
                                            <button type="submit" class="small-action-btn">Update</button>
                                        </form>

                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="delete-btn">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">No reviews found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>