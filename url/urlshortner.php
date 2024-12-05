<?php
$host = 'localhost';
$dbname = 'url_shortener';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create URLs table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_url TEXT NOT NULL,
    short_code VARCHAR(10) UNIQUE NOT NULL,
    clicks INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Function to generate random short code
function generateShortCode($length = 6) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $code;
}

// Function to validate URL
function isValidURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

$message = '';
$shortUrl = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $originalUrl = trim($_POST['url']);
    
    if (isValidURL($originalUrl)) {
        // Generate unique short code
        do {
            $shortCode = generateShortCode();
            $stmt = $pdo->prepare("SELECT id FROM urls WHERE short_code = ?");
            $stmt->execute([$shortCode]);
        } while ($stmt->fetch());
        
        // Insert URL into database
        $stmt = $pdo->prepare("INSERT INTO urls (original_url, short_code) VALUES (?, ?)");
        $stmt->execute([$originalUrl, $shortCode]);
        
        $shortUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
            . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/$shortCode";
        $message = "URL shortened successfully!";
    } else {
        $message = "Please enter a valid URL!";
    }
}

// Handle redirections
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));
$shortCode = end($segments);

if (strlen($shortCode) === 6) {
    $stmt = $pdo->prepare("SELECT original_url FROM urls WHERE short_code = ?");
    $stmt->execute([$shortCode]);
    $url = $stmt->fetchColumn();
    
    if ($url) {
        // Update click count
        $stmt = $pdo->prepare("UPDATE urls SET clicks = clicks + 1 WHERE short_code = ?");
        $stmt->execute([$shortCode]);
        
        // Redirect to original URL
        header("Location: " . $url);
        exit;
    }
}

// Get statistics
$stmt = $pdo->query("SELECT * FROM urls ORDER BY created_at DESC LIMIT 10");
$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        input[type="url"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        .stats {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>URL Shortener</h1>
        
        <form method="POST" class="form-group">
            <input type="url" name="url" placeholder="Enter URL to shorten" required>
            <button type="submit">Shorten URL</button>
        </form>
        
        <?php if ($message): ?>
            <div class="message <?php echo isValidURL($_POST['url'] ?? '') ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($shortUrl): ?>
            <div class="form-group">
                <strong>Your shortened URL:</strong>
                <br>
                <a href="<?php echo htmlspecialchars($shortUrl); ?>" target="_blank">
                    <?php echo htmlspecialchars($shortUrl); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <h2>Recent URLs</h2>
            <table>
                <tr>
                    <th>Original URL</th>
                    <th>Short URL</th>
                    <th>Clicks</th>
                    <th>Created</th>
                </tr>
                <?php foreach ($urls as $url): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(substr($url['original_url'], 0, 50)) . '...'; ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($url['short_code']); ?>" target="_blank">
                                <?php echo htmlspecialchars($url['short_code']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($url['clicks']); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($url['created_at']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>