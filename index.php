<?php include 'includes/header.php'; ?>

<main>
    <section class="hero">
        <div class="hero-text">
            <h1>Welcome to our<br>Nail Studio</h1>
            <div class="hero-line"></div>

            <div class="hero-buttons">
                <a href="booking.php" class="primary-btn">Book an Appointment</a>
                <a href="about.php" class="secondary-btn">Learn More</a>
            </div>
        </div>

        <div class="hero-image">
            <img src="images/MainPicture.jpg" alt="Nail Studio" class="hero-img">
        </div>
    </section>

    <section class="home-cards">
        <a href="reviews.php" class="home-card-link">
            <div class="home-card">
                <div class="card-image">
                    <img src="images/Reviews.jpg" alt="Reviews" class="card-img">
                </div>
                <div class="card-title">
                    <span>Reviews</span>
                </div>
            </div>
        </a>

        <a href="services.php" class="home-card-link">
            <div class="home-card">
                <div class="card-image">
                    <img src="images/Services.jpg" alt="Services" class="card-img">
                </div>
                <div class="card-title">
                    <span>Services</span>
                </div>
            </div>
        </a>

        <a href="gallery.php" class="home-card-link">
            <div class="home-card">
                <div class="card-image">
                    <img src="images/CompletedWorks.jpg" alt="Completed Works" class="card-img">
                </div>
                <div class="card-title">
                    <span>Completed Works</span>
                </div>
            </div>
        </a>
    </section>
</main>

<?php include 'includes/footer.php'; ?>