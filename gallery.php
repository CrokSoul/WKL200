<?php
include 'includes/db.php';

$gallery_sql = "SELECT * FROM gallery ORDER BY uploaded_at DESC";
$gallery_result = mysqli_query($conn, $gallery_sql);

include 'includes/header.php';
?>

<main class="gallery-page">
    <section class="gallery-section">
        <h1>Completed Works</h1>
        <div class="gallery-line"></div>

        <div class="gallery-grid">
            <?php if ($gallery_result && mysqli_num_rows($gallery_result) > 0) : ?>
                <?php while ($image = mysqli_fetch_assoc($gallery_result)) : ?>
                    <div class="gallery-item">
                        <img 
                            src="uploads/<?php echo htmlspecialchars($image['image_name']); ?>" 
                            alt="<?php echo htmlspecialchars($image['title']); ?>" 
                            class="gallery-img"
                        >

                        <div class="gallery-caption">
                            <?php echo !empty($image['title']) ? htmlspecialchars($image['title']) : 'Untitled Image'; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="no-reviews-message">No completed works uploaded yet.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>