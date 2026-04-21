<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$rating = "";
$review_text = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $review_text = trim($_POST['review_text']);

    if ($rating < 1 || $rating > 5) {
        $message = "Please select a rating from 1 to 5 stars.";
    } elseif (empty($review_text)) {
        $message = "Please write your review.";
    } else {
        $sql = "INSERT INTO reviews (user_id, review_text, rating, status) VALUES (?, ?, ?, 'visible')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isi", $user_id, $review_text, $rating);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: reviews.php");
            exit();
        } else {
            $message = "Something went wrong. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<main class="write-review-page">
    <section class="write-review-section">
        <h1>Write a review</h1>
        <div class="reviews-line"></div>

        <?php if (!empty($message)) : ?>
            <p class="form-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="" method="POST" class="write-review-form">
            <div class="rating-row">
                <label>Rate your recent experience:</label>

                <div class="rating-stars">
                    <input type="radio" id="star5" name="rating" value="5" <?php if ($rating == 5) echo 'checked'; ?>>
                    <label for="star5">★</label>

                    <input type="radio" id="star4" name="rating" value="4" <?php if ($rating == 4) echo 'checked'; ?>>
                    <label for="star4">★</label>

                    <input type="radio" id="star3" name="rating" value="3" <?php if ($rating == 3) echo 'checked'; ?>>
                    <label for="star3">★</label>

                    <input type="radio" id="star2" name="rating" value="2" <?php if ($rating == 2) echo 'checked'; ?>>
                    <label for="star2">★</label>

                    <input type="radio" id="star1" name="rating" value="1" <?php if ($rating == 1) echo 'checked'; ?>>
                    <label for="star1">★</label>
                </div>
            </div>

            <label for="review_text" class="review-text-label">Write your review here:</label>
            <textarea id="review_text" name="review_text" required><?php echo htmlspecialchars($review_text); ?></textarea>

            <button type="submit" class="post-review-btn">Post a review</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>