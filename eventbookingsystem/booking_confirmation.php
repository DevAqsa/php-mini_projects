<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header('Location: list_events.php');
    exit();
}

$booking_id = $_GET['id'];

$sql = "SELECT b.*, e.title, e.event_date, e.start_time 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE b.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: list_events.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Booking Confirmation</h2>
                <p class="card-text">Thank you for your booking!</p>
                
                <h4>Booking Details:</h4>
                <ul class="list-unstyled">
                    <li>Event: <?php echo htmlspecialchars($booking['title']); ?></li>
                    <li>Date: <?php echo $booking['event_date']; ?></li>
                    <li>Time: <?php echo $booking['start_time']; ?></li>
                    <li>Number of Tickets: <?php echo $booking['num_tickets']; ?></li>
                    <li>Booking Reference: <?php echo $booking['id']; ?></li>
                </ul>
                
                <a href="list_events.php" class="btn btn-primary">Back to Events</a>
            </div>
        </div>
    </div>
</body>
</html>