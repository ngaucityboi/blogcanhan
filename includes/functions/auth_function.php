<?php
/**
 * Authentication Functions
 * Chứa các function liên quan đến đăng nhập, đăng ký, đăng xuất
 */

/**
 * Function đăng nhập người dùng
 * @param string $identity - username hoặc email
 * @param string $password - mật khẩu
 * @param PDO $pdo - kết nối database
 * @return array - kết quả đăng nhập
 */
function loginUser($identity, $password, $pdo) {
    logInfo("LOGIN ATTEMPT: Starting login for identity: " . $identity);
    
    $result = ['success' => false, 'error' => '', 'user' => null];
    
    if (empty($identity) || empty($password)) {
        logWarning("LOGIN FAILED: Empty identity or password");
        $result['error'] = 'Vui lòng nhập đủ tài khoản (username/email) và mật khẩu.';
        return $result;
    }
    
    try {
        logDebug("LOGIN: Checking PDO connection");
        if (!$pdo || !($pdo instanceof PDO)) {
            logError("LOGIN ERROR: PDO connection is null or invalid");
            $result['error'] = 'Lỗi kết nối database.';
            return $result;
        }
        
        $sql = "SELECT id, username, email, password, role, status
                FROM users
                WHERE username = :id OR email = :id
                LIMIT 1";
        logDebug("LOGIN: Executing SQL query for user lookup");
        
        $st = $pdo->prepare($sql);
        $st->execute([':id' => $identity]);
        $user = $st->fetch(PDO::FETCH_ASSOC);
        
        logDebug("LOGIN: Query executed, user found: " . ($user ? 'YES' : 'NO'));
        
        if (!$user) {
            logWarning("LOGIN FAILED: User not found for identity: " . $identity);
            $result['error'] = 'Tài khoản hoặc mật khẩu không đúng.';
        } elseif (!password_verify($password, $user['password'])) {
            logWarning("LOGIN FAILED: Password verification failed for user: " . $user['username']);
            $result['error'] = 'Tài khoản hoặc mật khẩu không đúng.';
        } elseif ($user['status'] !== 'normal') {
            logWarning("LOGIN FAILED: User account locked: " . $user['username'] . ", status: " . $user['status']);
            $result['error'] = 'Tài khoản của bạn đang bị khóa.';
        } else {
            // Đăng nhập thành công
            logInfo("LOGIN SUCCESS: User logged in successfully: " . $user['username']);
            
            $_SESSION['user_id']  = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = (int)$user['role'];
            
            logDebug("LOGIN: Session data set - user_id: " . $user['id'] . ", username: " . $user['username'] . ", role: " . $user['role']);
            
            $result['success'] = true;
            $result['user'] = $user;
        }
    } catch (PDOException $e) {
        logError("LOGIN ERROR: PDO Exception - " . $e->getMessage());
        logException($e);
        $result['error'] = 'Lỗi hệ thống. Vui lòng thử lại.';
    } catch (Exception $e) {
        logError("LOGIN ERROR: General Exception - " . $e->getMessage());
        logException($e);
        $result['error'] = 'Lỗi không xác định. Vui lòng thử lại.';
    }
    
    logDebug("LOGIN: Final result - success: " . ($result['success'] ? 'true' : 'false') . ", error: " . $result['error']);
    return $result;
}

/**
 * Function đăng ký người dùng mới
 * @param string $username - tên đăng nhập
 * @param string $email - email
 * @param string $password - mật khẩu
 * @param PDO $pdo - kết nối database
 * @return array - kết quả đăng ký
 */
function registerUser($username, $email, $password, $pdo) {
    logInfo("REGISTER ATTEMPT: Starting registration for username: " . $username . ", email: " . $email);
    
    $result = ['success' => false, 'error' => ''];
    
    // Kiểm tra input
    if (empty($username) || empty($email) || empty($password)) {
        logWarning("REGISTER FAILED: Empty fields - username: " . ($username ? 'OK' : 'EMPTY') . ", email: " . ($email ? 'OK' : 'EMPTY') . ", password: " . ($password ? 'OK' : 'EMPTY'));
        $result['error'] = "Tên đăng nhập, email và mật khẩu không được để trống.";
        return $result;
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logWarning("REGISTER FAILED: Invalid email format: " . $email);
        $result['error'] = "Email không hợp lệ.";
        return $result;
    }
    
    // Kiểm tra độ dài password
    if (strlen($password) < 6) {
        logWarning("REGISTER FAILED: Password too short for user: " . $username);
        $result['error'] = "Mật khẩu phải có ít nhất 6 ký tự.";
        return $result;
    }
    
    try {
        logDebug("REGISTER: Checking PDO connection");
        if (!$pdo || !($pdo instanceof PDO)) {
            logError("REGISTER ERROR: PDO connection is null or invalid");
            $result['error'] = 'Lỗi kết nối database.';
            return $result;
        }
        
        // Kiểm tra username hoặc email đã tồn tại
        logDebug("REGISTER: Checking for existing username/email");
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            logWarning("REGISTER FAILED: Username or email already exists - username: " . $username . ", email: " . $email);
            $result['error'] = "Tên đăng nhập hoặc email đã tồn tại. Vui lòng chọn tên khác.";
            return $result;
        }
        
        // Mã hóa mật khẩu
        logDebug("REGISTER: Hashing password");
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Giá trị mặc định
        $role = 2;  // User thường
        $status = 'normal';
        
        logDebug("REGISTER: Inserting new user into database");
        // Thêm user mới
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password, $role, $status])) {
            logInfo("REGISTER SUCCESS: User registered successfully - username: " . $username . ", email: " . $email);
            $result['success'] = true;
        } else {
            logError("REGISTER ERROR: Failed to insert user into database");
            logData($stmt->errorInfo(), "PDO Error Info");
            $result['error'] = "Đã xảy ra lỗi khi tạo tài khoản. Vui lòng thử lại.";
        }
        
    } catch (PDOException $e) {
        logError("REGISTER ERROR: PDO Exception - " . $e->getMessage());
        logException($e);
        $result['error'] = "Lỗi hệ thống. Vui lòng thử lại.";
    } catch (Exception $e) {
        logError("REGISTER ERROR: General Exception - " . $e->getMessage());
        logException($e);
        $result['error'] = "Lỗi không xác định. Vui lòng thử lại.";
    }
    
    logDebug("REGISTER: Final result - success: " . ($result['success'] ? 'true' : 'false') . ", error: " . $result['error']);
    return $result;
}

/**
 * Function đăng xuất người dùng
 */
function logoutUser() {
    // Khởi tạo session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Xóa toàn bộ session
    $_SESSION = [];
    
    // Xóa cookie phiên nếu có
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, 
                 $params['path'], $params['domain'], 
                 $params['secure'], $params['httponly']);
    }
    
    // Hủy phiên
    session_destroy();
}

/**
 * Kiểm tra user đã đăng nhập chưa
 * @return bool
 */
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Lấy thông tin user hiện tại
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['role'] ?? 2
    ];
}

/**
 * Kiểm tra quyền admin
 * @return bool
 */
function isAdmin() {
    $user = getCurrentUser();
    return $user && (int)$user['role'] === 1;
}

/**
 * Chuyển hướng nếu chưa đăng nhập
 * @param string $redirectTo - trang chuyển hướng
 */
function requireLogin($redirectTo = '/user/login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectTo");
        exit;
    }
}

/**
 * Chuyển hướng nếu không có quyền admin
 * @param string $redirectTo - trang chuyển hướng
 */
function requireAdmin($redirectTo = '/index.php') {
    if (!isAdmin()) {
        header("Location: $redirectTo");
        exit;
    }
}