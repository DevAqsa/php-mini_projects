<?php
require_once 'config.php';

// Get all active polls
$stmt = $pdo->query("SELECT * FROM polls WHERE status = 'active' ORDER BY created_at DESC");
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Poll System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Online Poll System</h1>
        <a href="create_poll.php" class="create-btn">Create New Poll</a>
        
        <div class="poll-list">
            <h2>Active Polls</h2>
            <?php if ($polls): ?>
                <?php foreach ($polls as $poll): ?>
                    <div class="poll-item">
                        <h3><?php echo htmlspecialchars($poll['question']); ?></h3>
                        <p>Created: <?php echo $poll['created_at']; ?></p>
                        <a href="view_poll.php?id=<?php echo $poll['id']; ?>" class="view-btn">View Poll</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No active polls found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>