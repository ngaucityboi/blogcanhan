<?php
/**
 * Simple Logging System
 * Ghi log để debug lỗi
 */

/**
 * Write log to file
 * @param string $message
 * @param string $level
 * @param string $file
 */
function writeLog($message, $level = 'INFO', $file = 'debug.log') {
    $logDir = __DIR__ . '/../logs/';
    
    // Tạo folder logs nếu chưa có
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . $file;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

/**
 * Log info message
 * @param string $message
 */
function logInfo($message) {
    writeLog($message, 'INFO');
}

/**
 * Log error message
 * @param string $message
 */
function logError($message) {
    writeLog($message, 'ERROR');
}

/**
 * Log debug message
 * @param string $message
 */
function logDebug($message) {
    writeLog($message, 'DEBUG');
}

/**
 * Log warning message
 * @param string $message
 */
function logWarning($message) {
    writeLog($message, 'WARNING');
}

/**
 * Log exception
 * @param Exception $e
 */
function logException($e) {
    $message = "Exception: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine();
    writeLog($message, 'ERROR');
}

/**
 * Log array/object data
 * @param mixed $data
 * @param string $label
 */
function logData($data, $label = 'DATA') {
    $message = $label . ": " . print_r($data, true);
    writeLog($message, 'DEBUG');
}

/**
 * Clear log file
 * @param string $file
 */
function clearLog($file = 'debug.log') {
    $logFile = __DIR__ . '/../logs/' . $file;
    if (file_exists($logFile)) {
        file_put_contents($logFile, '');
    }
}