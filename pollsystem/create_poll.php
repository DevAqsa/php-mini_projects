<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question'];
    $options = $_POST['options'];

    try {
        $pdo->beginTransaction();

        // Insert poll question
        $stmt = $pdo->prepare("INSERT INTO polls (question) VALUES (?)");
        $stmt->execute([$question]);
        $pollId = $pdo->lastInsertId();

        // Insert options
        $stmt = $pdo->prepare("INSERT INTO options (poll_id, option_text) VALUES (?, ?)");
        foreach ($options as $option) {
            if (!empty($option)) {
                $stmt->execute([$pollId, $option]);
            }
        }

        $pdo->commit();
        header("Location: view_poll.php?id=" . $pollId);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Poll</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Create New Poll</h2>
        <form method="POST" class="poll-form">
            <div class="form-group">
                <label>Question:</label>
                <input type="text" name="question" required>
            </div>
            <div class="form-group">
                <label>Options:</label>
                <div id="optionsContainer">
                    <input type="text" name="options[]" required>
                    <input type="text" name="options[]" required>
                </div>
                <button type="button" onclick="addOption()" class="add-btn">Add Option</button>
            </div>
            <button type="submit" class="submit-btn">Create Poll</button>
        </form>
    </div>

    <script>
        function addOption() {
            const container = document.getElementById('optionsContainer');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'options[]';
            input.required = true;
            container.appendChild(input);
        }
    </script>
</body>
</html>