<?php
require_once 'includes/functions.php';
require_once 'db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get available rooms
$sql = "SELECT * FROM rooms WHERE status = 'available'";
$result = $conn->query($sql);
$rooms = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms - Luxury Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="rooms.php">Rooms</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <a class="nav-link" href="admin/dashboard.php">Admin Dashboard</a>
                        <?php else: ?>
                            <a class="nav-link" href="user/dashboard.php">My Dashboard</a>
                        <?php endif; ?>
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        <a class="nav-link" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="nav-link" href="login.php">Login</a>
                        <a class="nav-link" href="register.php">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-4">
        <h2 class="mb-4">Available Rooms</h2>
        <div class="row">
            <?php foreach ($rooms as $room): ?>
                <div class="col-md-4 mb-4">
                    <div class="card room-card h-100">
                        <?php if ($room['image']): ?>
                            <img src="<?php echo htmlspecialchars($room['image']); ?>" class="card-img-top" alt="Room Image">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x200?text=Room+Image" class="card-img-top" alt="Room Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($room['description']); ?></p>
                            <p class="card-text">
                                <strong>Price:</strong> $<?php echo number_format($room['price'], 2); ?>/night
                            </p>
                            <a href="booking.php?room_id=<?php echo $room['id']; ?>" class="btn btn-primary w-100">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 