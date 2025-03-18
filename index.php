<?php
require_once 'includes/functions.php';
require_once 'db.php';

// Redirect logged-in users to their respective dashboards
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('user/dashboard.php');
    }
}

// Get featured rooms
$sql = "SELECT * FROM rooms WHERE status = 'available' ORDER BY price DESC LIMIT 3";
$featured_rooms = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Luxury Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/images/hotel-hero.jpg');
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
        }
        .feature-card {
            border: none;
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-building"></i> Luxury Hotel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php">Rooms</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <a class="nav-link" href="login.php">Guest Login</a>
                    <a class="nav-link" href="register.php">Register</a>
                    <a class="nav-link" href="admin/login.php">Admin Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-3 mb-4">Welcome to Luxury Hotel</h1>
            <p class="lead mb-4">Experience unparalleled luxury and comfort in the heart of the city</p>
            <a href="rooms.php" class="btn btn-primary btn-lg">View Our Rooms</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Us</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-star feature-icon mb-3"></i>
                            <h4 class="card-title">Luxury Experience</h4>
                            <p class="card-text">Indulge in world-class amenities and personalized service.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-geo-alt feature-icon mb-3"></i>
                            <h4 class="card-title">Prime Location</h4>
                            <p class="card-text">Located in the heart of the city with easy access to attractions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-shield-check feature-icon mb-3"></i>
                            <h4 class="card-title">Safe & Secure</h4>
                            <p class="card-text">Your safety and comfort are our top priorities.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Rooms Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Featured Rooms</h2>
            <div class="row">
                <?php foreach ($featured_rooms as $room): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if ($room['image']): ?>
                                <img src="<?php echo htmlspecialchars($room['image']); ?>" class="card-img-top" alt="Room Image">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x200?text=Luxury+Room" class="card-img-top" alt="Room Image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(substr($room['description'], 0, 100)) . '...'; ?></p>
                                <p class="card-text">
                                    <strong>Price:</strong> $<?php echo number_format($room['price'], 2); ?>/night
                                </p>
                                <a href="rooms.php" class="btn btn-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="rooms.php" class="btn btn-outline-primary btn-lg">View All Rooms</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Luxury Hotel</h5>
                    <p>Experience the extraordinary in every stay.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="rooms.php" class="text-light">Our Rooms</a></li>
                        <li><a href="login.php" class="text-light">Guest Login</a></li>
                        <li><a href="register.php" class="text-light">Register</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p>
                        <i class="bi bi-geo-alt"></i> 123 Luxury Street<br>
                        <i class="bi bi-telephone"></i> +1 234 567 890<br>
                        <i class="bi bi-envelope"></i> info@luxuryhotel.com
                    </p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Luxury Hotel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 