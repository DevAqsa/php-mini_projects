<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poll_id']) && isset($_POST['option_id'])) {
    $pollId = $_POST['poll_id'];
    $optionId = $_POST['option_id'];
    $userIp = $_SERVER['REMOTE_ADDR'];

    // Check if user has already voted
    $stmt = $pdo->prepare("SELECT id FROM votes WHERE poll_id = ? AND user_ip = ?");
    $stmt->execute([$pollId, $userIp]);
    
    if ($stmt->rowCount() === 0) {
        // Insert vote
        $stmt = $pdo->prepare("INSERT INTO votes (poll_id, option_id, user_ip) VALUES (?, ?, ?)");
        $stmt->execute([$pollId, $optionId, $userIp]);
        $_SESSION['message'] = "Vote recorded successfully!";
    } else {
        $_SESSION['message'] = "You have already voted on this poll!";
    }
    
    header("Location: view_poll.php?id=" . $pollId);
    exit();
}
?>