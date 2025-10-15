<?php
/**
 * Debug Log Viewer
 * File để xem log debug
 */

// Security check - chỉ cho phép truy cập từ localhost
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', 'localhost'])) {
    die('Access denied');
}

$logFile = __DIR__ . '/../includes/logs/debug.log';
$action = $_GET['action'] ?? '';

// Clear log if requested
if ($action === 'clear') {
    file_put_contents($logFile, '');
    header('Location: view_log.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Log Viewer</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .log-content { 
            background: #fff; 
            padding: 20px; 
            border: 1px solid #ddd; 
            height: 500px; 
            overflow-y: auto; 
            white-space: pre-wrap;
            font-size: 12px;
        }
        .controls { margin-bottom: 20px; }
        .controls a { 
            background: #007cba; 
            color: white; 
            padding: 8px 16px; 
            text-decoration: none; 
            margin-right: 10px; 
            border-radius: 4px;
        }
        .controls a:hover { background: #005a87; }
        .info { background: #e7f3ff; padding: 10px; margin-bottom: 20px; border-left: 4px solid #007cba; }
        .error { color: #d63384; }
        .warning { color: #fd7e14; }
        .debug { color: #6c757d; }
        .info-line { color: #0d6efd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐛 Debug Log Viewer</h1>
        
        <div class="info">
            <strong>File:</strong> <?= htmlspecialchars($logFile) ?><br>
            <strong>Size:</strong> <?= file_exists($logFile) ? number_format(filesize($logFile)) . ' bytes' : 'File không tồn tại' ?><br>
            <strong>Last Modified:</strong> <?= file_exists($logFile) ? date('d/m/Y H:i:s', filemtime($logFile)) : 'N/A' ?>
        </div>
        
        <div class="controls">
            <a href="view_log.php">🔄 Refresh</a>
            <a href="view_log.php?action=clear" onclick="return confirm('Bạn có chắc muốn xóa toàn bộ log?')">🗑️ Clear Log</a>
        </div>
        
        <div class="log-content" id="logContent">
            <?php
            if (file_exists($logFile)) {
                $content = file_get_contents($logFile);
                if (empty($content)) {
                    echo "Log file is empty.";
                } else {
                    // Highlight different log levels
                    $content = htmlspecialchars($content);
                    $content = preg_replace('/\[ERROR\].*$/m', '<span class="error">$0</span>', $content);
                    $content = preg_replace('/\[WARNING\].*$/m', '<span class="warning">$0</span>', $content);
                    $content = preg_replace('/\[DEBUG\].*$/m', '<span class="debug">$0</span>', $content);
                    $content = preg_replace('/\[INFO\].*$/m', '<span class="info-line">$0</span>', $content);
                    echo $content;
                }
            } else {
                echo "Log file does not exist yet.";
            }
            ?>
        </div>
        
        <script>
            // Auto scroll to bottom
            const logContent = document.getElementById('logContent');
            logContent.scrollTop = logContent.scrollHeight;
            
            // Auto refresh every 5 seconds
            setInterval(function() {
                location.reload();
            }, 5000);
        </script>
    </div>
</body>
</html>