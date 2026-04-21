<?php
include 'includes/db.php';

$sql = "SELECT reviews.review_text, reviews.rating, reviews.created_at, users.first_name, users.surname
        FROM reviews
        INNER JOIN users ON reviews.user_id = users.id
        WHERE reviews.status = 'visible'
        ORDER BY reviews.created_at DESC";

$result = mysqli_query($conn, $sql);

include 'includes/header.php';
?>

<main class="reviews-page">
    <section class="reviews-section">
        <div class="reviews-header">
            <div>
                <h1>Reviews</h1>
            </div>

            <a href="write_review.php" class="write-review-btn">Write a review</a>
        </div>

        <div class="reviews-line"></div>

        <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php $first_review = true; ?>
            <?php while ($review = mysqli_fetch_assoc($result)) : ?>
                <?php
                    $display_name = htmlspecialchars($review['first_name']) . ' ' . strtoupper(substr($review['surname'], 0, 1)) . '.';
                    $rating = (int)$review['rating'];
                ?>

                <?php if (!$first_review) : ?>
                    <div class="review-divider"></div>
                <?php endif; ?>

                <div class="review-item">
                    <h2><?php echo $display_name; ?></h2>

                    <div class="review-stars">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <?php if ($i <= $rating) : ?>
                                <span class="star filled">★</span>
                            <?php else : ?>
                                <span class="star empty">★</span>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                    <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                </div>

                <?php $first_review = false; ?>
            <?php endwhile; ?>
        <?php else : ?>
            <p class="no-reviews-message">No reviews yet. Be the first to leave one.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>