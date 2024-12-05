<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header('Location: list_events.php');
    exit();
}

$event_id = $_GET['id'];

// Get event details
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('Location: list_events.php');
    exit();
}

// Check available capacity
$stmt = $pdo->prepare("SELECT SUM(num_tickets) as booked FROM bookings WHERE event_id = ?");
$stmt->execute([$event_id]);
$booking_info = $stmt->fetch(PDO::FETCH_ASSOC);
$available_tickets = $event['capacity'] - ($booking_info['booked'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $num_tickets = $_POST['num_tickets'];

    if ($num_tickets <= $available_tickets) {
        $sql = "INSERT INTO bookings (event_id, user_name, user_email, num_tickets) 
                VALUES (:event_id, :name, :email, :num_tickets)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':event_id' => $event_id,
                ':name' => $name,
                ':email' => $email,
                ':num_tickets' => $num_tickets
            ]);
            header('Location: booking_confirmation.php?id=' . $pdo->lastInsertId());
            exit();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Book Event: <?php echo htmlspecialchars($event['title']); ?></h2>
        <p>Available Tickets: <?php echo $available_tickets; ?></p>
        
        <?php if ($available_tickets > 0): ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Number of Tickets</label>
                    <input type="number" name="num_tickets" class="form-control" min="1" max="<?php echo $available_tickets; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Book Now</button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                Sorry, this event is fully booked!
            </div>
        <?php endif; ?>
    </div>
</body>
</html>