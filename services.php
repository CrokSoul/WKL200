<?php
include 'includes/db.php';

$services_sql = "SELECT * FROM services ORDER BY id ASC";
$services_result = mysqli_query($conn, $services_sql);

include 'includes/header.php';
?>

<main class="services-page">
    <section class="services-section">
        <h1>Our Services</h1>
        <div class="services-line"></div>

        <?php if ($services_result && mysqli_num_rows($services_result) > 0) : ?>
            <?php $first_item = true; ?>

            <?php while ($service = mysqli_fetch_assoc($services_result)) : ?>
                <?php if (!$first_item) : ?>
                    <div class="service-divider"></div>
                <?php endif; ?>

                <div class="service-item">
                    <div class="service-image">
                        <?php if ($service['service_name'] == 'Classic Manicure') : ?>
                            <img src="images/ClassicManicure.jpg" alt="Classic Manicure" class="service-img">
                        <?php elseif ($service['service_name'] == 'Gel Polish') : ?>
                            <img src="images/GelPolish.jpg" alt="Gel Polish" class="service-img">
                        <?php elseif ($service['service_name'] == 'Nail Art') : ?>
                            <img src="images/NailArt.jpg" alt="Nail Art" class="service-img">
                        <?php else : ?>
                            <span>Image</span>
                        <?php endif; ?>
                    </div>

                    <div class="service-content">
                        <div class="service-top">
                            <h2><?php echo htmlspecialchars($service['service_name']); ?></h2>

                            <details class="service-details">
                                <summary class="details-btn">View Details <span class="arrow">▼</span></summary>
                                <div class="details-content">
                                    <p><strong>Price:</strong> £<?php echo number_format($service['price'], 2); ?></p>
                                    <p><strong>Duration:</strong> <?php echo htmlspecialchars($service['duration']); ?></p>
                                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                                </div>
                            </details>
                        </div>

                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>
                </div>

                <?php $first_item = false; ?>
            <?php endwhile; ?>
        <?php else : ?>
            <p class="no-reviews-message">No services available yet.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>