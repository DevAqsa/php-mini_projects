<?php
require_once 'config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $title = trim($_POST['title']);
                $description = trim($_POST['description']);
                $due_date = $_POST['due_date'];
                $category = $_POST['category'];
                $priority = $_POST['priority'];
                
                $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, category, priority) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $due_date, $category, $priority]);
                break;
                
            case 'edit':
                $id = $_POST['task_id'];
                $title = trim($_POST['title']);
                $description = trim($_POST['description']);
                $due_date = $_POST['due_date'];
                $category = $_POST['category'];
                $priority = $_POST['priority'];
                
                $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, category = ?, priority = ? WHERE id = ?");
                $stmt->execute([$title, $description, $due_date, $category, $priority, $id]);
                break;
                
            case 'complete':
                $id = $_POST['task_id'];
                $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = ?");
                $stmt->execute([$id]);
                break;
                
            case 'delete':
                $id = $_POST['task_id'];
                $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
                $stmt->execute([$id]);
                break;
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch categories
$categories = $pdo->query("SELECT name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);

// Fetch task for editing
$editTask = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editTask = $stmt->fetch();
}

// Filter tasks
$category_filter = $_GET['category'] ?? 'all';
$priority_filter = $_GET['priority'] ?? 'all';

$where_clauses = [];
$params = [];

if ($category_filter !== 'all') {
    $where_clauses[] = "category = ?";
    $params[] = $category_filter;
}

if ($priority_filter !== 'all') {
    $where_clauses[] = "priority = ?";
    $params[] = $priority_filter;
}

$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Fetch all tasks with filters
$sql = "SELECT * FROM tasks $where_sql ORDER BY 
        CASE priority 
            WHEN 'high' THEN 1 
            WHEN 'medium' THEN 2 
            WHEN 'low' THEN 3 
        END, 
        due_date ASC, created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Todo List</title>
    <style>
        /* Previous CSS styles remain the same */
        .priority-high {
            border-left: 4px solid #dc3545;
        }
        .priority-medium {
            border-left: 4px solid #ffc107;
        }
        .priority-low {
            border-left: 4px solid #28a745;
        }
        .category-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            background-color: #e9ecef;
            margin-right: 10px;
        }
        .filters {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            gap: 20px;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .task-meta {
            display: flex;
            align-items: center;
            margin-top: 5px;
            color: #666;
            font-size: 0.9em;
        }
        .priority-indicator {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }
        .priority-high-bg {
            background-color: #ffd7d7;
            color: #dc3545;
        }
        .priority-medium-bg {
            background-color: #fff3cd;
            color: #856404;
        }
        .priority-low-bg {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $editTask ? 'Edit Task' : 'Advanced Todo List'; ?></h1>
        
        <!-- Task Filters -->
        <div class="filters">
            <form method="GET" class="filter-group">
                <label>Category:</label>
                <select name="category" onchange="this.form.submit()">
                    <option value="all">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>"
                                <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <form method="GET" class="filter-group">
                <label>Priority:</label>
                <select name="priority" onchange="this.form.submit()">
                    <option value="all">All Priorities</option>
                    <option value="high" <?php echo $priority_filter === 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="medium" <?php echo $priority_filter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="low" <?php echo $priority_filter === 'low' ? 'selected' : ''; ?>>Low</option>
                </select>
            </form>
        </div>
        
        <!-- Add/Edit Task Form -->
        <div class="task-form <?php echo $editTask ? 'edit-mode' : ''; ?>">
            <h2><?php echo $editTask ? 'Edit Task' : 'Add New Task'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $editTask ? 'edit' : 'add'; ?>">
                <?php if ($editTask): ?>
                    <input type="hidden" name="task_id" value="<?php echo $editTask['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" value="<?php echo $editTask ? htmlspecialchars($editTask['title']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="3"><?php echo $editTask ? htmlspecialchars($editTask['description']) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <select name="category" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>"
                                    <?php echo ($editTask && $editTask['category'] === $category) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Priority:</label>
                    <select name="priority" required>
                        <option value="high" <?php echo ($editTask && $editTask['priority'] === 'high') ? 'selected' : ''; ?>>High</option>
                        <option value="medium" <?php echo ($editTask && $editTask['priority'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="low" <?php echo ($editTask && $editTask['priority'] === 'low') ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Due Date:</label>
                    <input type="date" name="due_date" value="<?php echo $editTask ? $editTask['due_date'] : ''; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <?php echo $editTask ? 'Update Task' : 'Add Task'; ?>
                </button>
                <?php if ($editTask): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Task List -->
        <div class="task-list">
            <h2>Tasks</h2>
            <?php foreach ($tasks as $task): ?>
                <div class="task-item <?php echo $task['status'] === 'completed' ? 'completed' : ''; ?> priority-<?php echo $task['priority']; ?>">
                    <div class="task-content">
                        <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                        <p><?php echo htmlspecialchars($task['description']); ?></p>
                        <div class="task-meta">
                            <span class="category-badge"><?php echo htmlspecialchars($task['category']); ?></span>
                            <span class="priority-indicator priority-<?php echo $task['priority']; ?>-bg">
                                <?php echo ucfirst($task['priority']); ?> Priority
                            </span>
                            <span class="due-date">Due: <?php echo date('F j, Y', strtotime($task['due_date'])); ?></span>
                        </div>
                    </div>
                    <div class="task-actions">
                        <?php if ($task['status'] === 'pending'): ?>
                            <a href="?edit=<?php echo $task['id']; ?>" class="btn btn-warning">Edit</a>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="complete">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="btn btn-success">Complete</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($tasks)): ?>
                <p>No tasks found. Add your first task above!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>