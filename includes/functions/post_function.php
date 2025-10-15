<?php
/**
 * Post Functions
 * Chứa các function liên quan đến bài viết
 */

/**
 * Lấy danh sách bài viết
 * @param PDO $pdo
 * @param int $limit
 * @param int $offset
 * @return array
 */
function getPosts($pdo, $limit = 10, $offset = 0) {
    try {
        $sql = "SELECT p.*, u.username 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'published' 
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Lấy chi tiết một bài viết
 * @param PDO $pdo
 * @param int $postId
 * @return array|null
 */
function getPostById($pdo, $postId) {
    try {
        $sql = "SELECT p.*, u.username 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.id = :id AND p.status = 'published'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $postId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Tạo bài viết mới
 * @param PDO $pdo
 * @param array $data
 * @return bool
 */
function createPost($pdo, $data) {
    try {
        $sql = "INSERT INTO posts (title, content, excerpt, user_id, status, created_at) 
                VALUES (:title, :content, :excerpt, :user_id, :status, NOW())";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':excerpt' => $data['excerpt'] ?? '',
            ':user_id' => $data['user_id'],
            ':status' => $data['status'] ?? 'draft'
        ]);
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
        $sql = "UPDATE posts 
                SET title = :title, content = :content, excerpt = :excerpt, 
                    status = :status, updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $postId,
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':excerpt' => $data['excerpt'] ?? '',
            ':status' => $data['status'] ?? 'draft'
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

/**
 * Tìm kiếm bài viết
 * @param PDO $pdo
 * @param string $keyword
 * @param int $limit
 * @return array
 */
function searchPosts($pdo, $keyword, $limit = 20) {
    try {
        $keyword = '%' . $keyword . '%';
        $sql = "SELECT p.*, u.username 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE (p.title LIKE :keyword OR p.content LIKE :keyword) 
                AND p.status = 'published' 
                ORDER BY p.created_at DESC 
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Đếm tổng số bài viết
 * @param PDO $pdo
 * @return int
 */
function countPosts($pdo) {
    try {
        $sql = "SELECT COUNT(*) FROM posts WHERE status = 'published'";
        $stmt = $pdo->query($sql);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Lấy danh sách bài viết cho trang chủ
 * @param PDO $pdo
 * @param int $limit - số lượng bài viết tối đa
 * @return array
 */
function getPostsForHomepage($pdo, $limit = 15) {
    try {
        $sql = "SELECT id, title, content, published_at 
                FROM posts 
                ORDER BY published_at DESC 
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}