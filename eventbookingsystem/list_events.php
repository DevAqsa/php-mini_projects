<?php
require_once 'config.php';

$sql = "SELECT * FROM events ORDER BY event_date";
$stmt = $pdo->query($sql);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Available Events</h2>
        <a href="create_event.php" class="btn btn-primary mb-3">Create New Event</a>
        
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                            <p>Date: <?php echo $event['event_date']; ?></p>
                            <p>Time: <?php echo $event['start_time']; ?> - <?php echo $event['end_time']; ?></p>
                            <a href="book_event.php?id=<?php echo $event['id']; ?>" class="btn btn-success">Book Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>