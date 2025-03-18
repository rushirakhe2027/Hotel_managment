<?php
require_once 'includes/functions.php';
require_once 'db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$success = $error = '';

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    
    // Verify the booking belongs to the user
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        // Only allow cancellation of pending or confirmed bookings
        if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed') {
            $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
            $stmt->bind_param("i", $booking_id);
            
            if ($stmt->execute()) {
                $success = "Booking cancelled successfully!";
            } else {
                $error = "Error cancelling booking: " . $conn->error;
            }
        } else {
            $error = "This booking cannot be cancelled.";
        }
    } else {
        $error = "Invalid booking or unauthorized access.";
    }
    $stmt->close();
}

// Get user's bookings with room details
$stmt = $conn->prepare("
    SELECT b.*, r.name as room_name, r.price as room_price, r.image as room_image
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Luxury Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <div class="container mt-5 pt-4">
        <h2 class="mb-4">My Bookings</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($bookings->num_rows > 0): ?>
            <div class="row">
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <?php if ($booking['room_image']): ?>
                                <img src="<?php echo htmlspecialchars($booking['room_image']); ?>" class="card-img-top" alt="Room Image" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($booking['room_name']); ?></h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Check-in:</strong></p>
                                        <p class="text-muted"><?php echo formatDate($booking['check_in']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Check-out:</strong></p>
                                        <p class="text-muted"><?php echo formatDate($booking['check_out']); ?></p>
                                    </div>
                                </div>
                                <p class="mb-2">
                                    <strong>Status:</strong>
                                    <span class="badge <?php
                                        echo match($booking['status']) {
                                            'confirmed' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </p>
                                <p class="mb-2">
                                    <strong>Total Price:</strong>
                                    $<?php 
                                        $nights = calculateNights($booking['check_in'], $booking['check_out']);
                                        echo number_format($booking['room_price'] * $nights, 2);
                                    ?>
                                </p>
                                <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                    <form method="POST" class="mt-3" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" name="cancel_booking" class="btn btn-danger">
                                            Cancel Booking
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                You don't have any bookings yet. 
                <a href="rooms.php" class="alert-link">Browse our rooms</a> to make a booking.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 