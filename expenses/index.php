
<?php
require_once 'config.php';

// Add new expense
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $category_id = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $pdo->prepare("INSERT INTO expenses (category_id, amount, description, date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$category_id, $amount, $description, $date]);
    
    header('Location: index.php');
    exit();
}

// Delete expense
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: index.php');
    exit();
}

// Update expense
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $category_id = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    $stmt = $pdo->prepare("UPDATE expenses SET category_id = ?, amount = ?, description = ?, date = ? WHERE id = ?");
    $stmt->execute([$category_id, $amount, $description, $date, $id]);
    
    header('Location: index.php');
    exit();
}

// Get all expenses with category names
$stmt = $pdo->query("
    SELECT e.*, c.name as category_name 
    FROM expenses e 
    JOIN categories c ON e.category_id = c.id 
    ORDER BY date DESC
");
$expenses = $stmt->fetchAll();

// Calculate total expenses
$stmt = $pdo->query("SELECT SUM(amount) as total FROM expenses");
$total = $stmt->fetch()['total'];

// Get categories for dropdown
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Expense Tracker</h1>
        
        <!-- Add Expense Form -->
        <div class="form-container">
            <h2>Add New Expense</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <select name="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                
                <input type="number" name="amount" step="0.01" placeholder="Amount" required>
                <input type="text" name="description" placeholder="Description" required>
                <input type="date" name="date" required value="<?= date('Y-m-d') ?>">
                
                <button type="submit">Add Expense</button>
            </form>
        </div>

        <!-- Total Expenses -->
        <div class="total">
            <h2>Total Expenses: $<?= number_format($total, 2) ?></h2>
        </div>

        <!-- Expense List -->
        <div class="expenses-list">
            <h2>Recent Expenses</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $expense): ?>
                        <tr id="row-<?= $expense['id'] ?>">
                            <td><?= date('Y-m-d', strtotime($expense['date'])) ?></td>
                            <td><?= $expense['category_name'] ?></td>
                            <td><?= $expense['description'] ?></td>
                            <td>$<?= number_format($expense['amount'], 2) ?></td>
                            <td class="actions">
                                <button onclick="editExpense(<?= htmlspecialchars(json_encode($expense)) ?>)" class="edit-btn">Edit</button>
                                <a href="?delete=<?= $expense['id'] ?>" onclick="return confirm('Are you sure?')" class="delete-btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Expense</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit-id">
                <select name="category" id="edit-category" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="amount" id="edit-amount" step="0.01" required>
                <input type="text" name="description" id="edit-description" required>
                <input type="date" name="date" id="edit-date" required>
                <button type="submit">Update Expense</button>
            </form>
        </div>
    </div>

    <script>
        // Get modal elements
        const modal = document.getElementById('editModal');
        const span = document.getElementsByClassName('close')[0];

        // Function to edit expense
        function editExpense(expense) {
            document.getElementById('edit-id').value = expense.id;
            document.getElementById('edit-category').value = expense.category_id;
            document.getElementById('edit-amount').value = expense.amount;
            document.getElementById('edit-description').value = expense.description;
            document.getElementById('edit-date').value = expense.date;
            modal.style.display = 'block';
        }

        // Close modal when clicking (x)
        span.onclick = function() {
            modal.style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>