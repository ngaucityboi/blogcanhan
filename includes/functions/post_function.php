<?php
/**
 * Post Functions
 * Chứa các function liên quan đến bài viết, đã được cập nhật
 * để khớp với schema và sử dụng bảng 'users'.
 */

/**
 * Hàm tiện ích: Tạo slug (chuỗi thân thiện với URL) từ một chuỗi bất kỳ.
 * Ví dụ: 'Bài viết mới về PHP 8' -> 'bai-viet-moi-ve-php-8'
 * @param string $string Chuỗi đầu vào
 * @return string Slug đã được xử lý
 */
function createSlug($string) {
    $search = [
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#', '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
        '#(ì|í|ị|ỉ|ĩ)#', '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#', '#(ỳ|ý|ỵ|ỷ|ỹ)#', '#(đ)#',
        '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#', '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
        '#(Ì|Í|Ị|Ỉ|Ĩ)#', '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
        '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#', '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#', '#(Đ)#',
        "/[^a-zA-Z0-9\-\_]/",
    ];
    $replace = [
        'a', 'e', 'i', 'o', 'u', 'y', 'd',
        'A', 'E', 'I', 'O', 'U', 'Y', 'D',
        '-',
    ];
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    $string = strtolower($string);
    return trim($string, '-');
}

/**
 * Tạo bài viết mới
 * @param PDO $pdo
 * @param array $data Dữ liệu bài viết ['title', 'content', 'status', 'author_id']
 * @return int|false Trả về ID của bài viết mới hoặc false nếu thất bại
 */
function createPost($pdo, $data) {
    // 1. Tạo slug từ title. Nếu title trống, tạo slug ngẫu nhiên.
    $slugBase = !empty($data['title']) ? createSlug($data['title']) : 'bai-viet-' . time();
    $slug = $slugBase;
    
    // 2. Đảm bảo slug là duy nhất bằng cách thêm số vào cuối nếu cần
    $i = 1;
    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() == 0) {
            break; // Slug này chưa có, có thể dùng
        }
        $slug = $slugBase . '-' . $i++; // Thêm số và thử lại
    }

    try {
        // 3. Chuẩn bị câu lệnh SQL
        $sql = "INSERT INTO posts (title, slug, content, status, author_id, published_at) 
                VALUES (:title, :slug, :content, :status, :author_id, :published_at)";
        
        $stmt = $pdo->prepare($sql);
        
        // 4. Thực thi câu lệnh
        $stmt->execute([
            ':title'        => $data['title'],
            ':slug'         => $slug,
            ':content'      => $data['content'],
            ':status'       => $data['status'],
            ':author_id'    => $data['author_id'],
            // Chỉ đặt ngày publish khi trạng thái là 'published'
            ':published_at' => ($data['status'] === 'published') ? date('Y-m-d H:i:s') : null
        ]);
        
        return $pdo->lastInsertId(); // Trả về ID của bài viết vừa tạo
    } catch (PDOException $e) {
        // Trong môi trường production, bạn nên ghi lỗi ra file log thay vì hiển thị
        // error_log($e->getMessage()); 
        return false;
    }
}

/**
 * Lấy danh sách bài viết cho trang chủ
 * @param PDO $pdo
 * @param int $limit Số lượng bài viết tối đa
 * @return array
 */
function getPostsForHomepage($pdo, $limit = 15) {
    try {
        // Sửa lại: JOIN với bảng 'users' qua 'author_id'
        $sql = "SELECT p.id, p.title, p.content, p.published_at, u.username 
                FROM posts p
                JOIN users u ON p.author_id = u.id
                WHERE p.status = 'published'
                ORDER BY p.published_at DESC 
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

/**
 * Lấy chi tiết một bài viết qua ID
 * @param PDO $pdo
 * @param int $postId
 * @return array|false
 */
function getPostById($pdo, $postId) {
    try {
        // Sửa lại: JOIN với bảng 'users' qua 'author_id'
        $sql = "SELECT p.*, u.username 
                FROM posts p 
                JOIN users u ON p.author_id = u.id 
                WHERE p.id = :id"; // Có thể thêm điều kiện status nếu cần
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $postId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Cập nhật bài viết
 * @param PDO $pdo
 * @param int $postId
 * @param array $data
 * @return bool
 */
function updatePost($pdo, $postId, $data) {
    try {
        // Logic tương tự createPost, có thể cần cập nhật slug và published_at
        $sql = "UPDATE posts 
                SET title = :title, content = :content, status = :status, updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id'      => $postId,
            ':title'   => $data['title'],
            ':content' => $data['content'],
            ':status'  => $data['status'] ?? 'draft'
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Xóa bài viết
 * @param PDO $pdo
 * @param int $postId
 * @return bool
 */
function deletePost($pdo, $postId) {
    try {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id' => $postId]);
    } catch (PDOException $e) {
        return false;
    }
}