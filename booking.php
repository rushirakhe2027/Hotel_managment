<?php
require_once 'includes/functions.php';
require_once 'db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    redirect('login.php');
}

// Check if room_id is provided
if (!isset($_GET['room_id'])) {
    redirect('index.php');
}

$room_id = intval($_GET['room_id']);
$error = $success = '';

// Get room details
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
$stmt->close();

if (!$room) {
    redirect('index.php');
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    
    if (empty($check_in) || empty($check_out)) {
        $error = "Please select both check-in and check-out dates.";
    } else if (!areValidDates($check_in, $check_out)) {
        $error = "Invalid dates selected. Check-in date must be today or later, and check-out date must be after check-in date.";
    } else {
        // Check if room is available for these dates
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM bookings 
            WHERE room_id = ? 
            AND status != 'cancelled'
            AND (
                (check_in <= ? AND check_out >= ?) OR
                (check_in <= ? AND check_out >= ?) OR
                (check_in >= ? AND check_out <= ?)
            )
        ");
        $stmt->bind_param("issssss", $room_id, $check_out, $check_in, $check_in, $check_in, $check_in, $check_out);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing_bookings = $result->fetch_assoc()['count'];
        $stmt->close();

        if ($existing_bookings > 0) {
            $error = "Sorry, this room is not available for the selected dates.";
        } else {
            // Create the booking
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("
                INSERT INTO bookings (user_id, room_id, check_in, check_out, status) 
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $stmt->bind_param("iiss", $user_id, $room_id, $check_in, $check_out);
            
            if ($stmt->execute()) {
                $success = "Booking request submitted successfully! Please wait for confirmation.";
            } else {
                $error = "Error creating booking: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room - <?php echo htmlspecialchars($room['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <div class="container mt-5 pt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title mb-0">Book Room: <?php echo htmlspecialchars($room['name']); ?></h2>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                            <div class="text-center mt-3">
                                <a href="my_bookings.php" class="btn btn-primary">View My Bookings</a>
                                <a href="index.php" class="btn btn-secondary">Back to Home</a>
                            </div>
                        <?php else: ?>
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="check_in" class="form-label">Check-in Date</label>
                                        <input type="date" class="form-control" id="check_in" name="check_in" required
                                               min="<?php echo date('Y-m-d'); ?>">
                                        <div class="invalid-feedback">Please select a check-in date.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="check_out" class="form-label">Check-out Date</label>
                                        <input type="date" class="form-control" id="check_out" name="check_out" required>
                                        <div class="invalid-feedback">Please select a check-out date.</div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Price per Night:</strong></p>
                                        <p class="text-muted">$<?php echo number_format($room['price'], 2); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Total Price:</strong></p>
                                        <p class="text-muted" id="total_price">Select dates to see total</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Book Now</button>
                                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Calculate total price
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');
        const totalPriceElement = document.getElementById('total_price');
        const pricePerNight = <?php echo $room['price']; ?>;

        function updateTotalPrice() {
            if (checkInInput.value && checkOutInput.value) {
                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);
                const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    const total = nights * pricePerNight;
                    totalPriceElement.textContent = '$' + total.toFixed(2);
                } else {
                    totalPriceElement.textContent = 'Invalid dates selected';
                }
            }
        }

        checkInInput.addEventListener('change', function() {
            checkOutInput.min = this.value;
            updateTotalPrice();
        });

        checkOutInput.addEventListener('change', updateTotalPrice);
    </script>
</body>
</html> 