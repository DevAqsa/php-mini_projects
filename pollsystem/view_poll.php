<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$pollId = $_GET['id'];
$userIp = $_SERVER['REMOTE_ADDR'];

// Get poll details
$stmt = $pdo->prepare("
    SELECT p.*, 
           (SELECT COUNT(*) FROM votes v WHERE v.poll_id = p.id AND v.user_ip = ?) as has_voted
    FROM polls p 
    WHERE p.id = ?
");
$stmt->execute([$userIp, $pollId]);
$poll = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$poll) {
    die("Poll not found");
}

// Get options and votes
$stmt = $pdo->prepare("
    SELECT o.*, COUNT(v.id) as vote_count,
           (SELECT COUNT(*) FROM votes WHERE poll_id = ?) as total_votes
    FROM options o
    LEFT JOIN votes v ON o.id = v.option_id
    WHERE o.poll_id = ?
    GROUP BY o.id
");
$stmt->execute([$pollId, $pollId]);
$options = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Poll</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($poll['question']); ?></h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <?php if (!$poll['has_voted']): ?>
            <form method="POST" action="vote.php">
                <input type="hidden" name="poll_id" value="<?php echo $pollId; ?>">
                <?php foreach ($options as $option): ?>
                    <div class="option">
                        <input type="radio" name="option_id" value="<?php echo $option['id']; ?>" required>
                        <label><?php echo htmlspecialchars($option['option_text']); ?></label>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="vote-btn">Vote</button>
            </form>
        <?php else: ?>
            <h3>Results:</h3>
            <?php foreach ($options as $option): ?>
                <?php 
                $percentage = $option['total_votes'] > 0 
                    ? ($option['vote_count'] / $option['total_votes']) * 100 
                    : 0;
                ?>
                <div class="option">
                    <div><?php echo htmlspecialchars($option['option_text']); ?></div>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                    <div><?php echo round($percentage, 1); ?>% (<?php echo $option['vote_count']; ?> votes)</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="index.php" class="back-btn">Back to Polls</a>
    </div>
</body>
</html>