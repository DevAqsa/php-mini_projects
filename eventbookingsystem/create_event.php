<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $capacity = $_POST['capacity'];

    $sql = "INSERT INTO events (title, description, event_date, start_time, end_time, capacity) 
            VALUES (:title, :description, :event_date, :start_time, :end_time, :capacity)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':event_date' => $event_date,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':capacity' => $capacity
        ]);
        header('Location: list_events.php');
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Create New Event</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Event Date</label>
                <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Capacity</label>
                <input type="number" name="capacity" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
</body>
</html>