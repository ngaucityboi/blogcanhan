<?php
$BASE_PATH = $BASE_PATH ?? '';
?>

<style>
  .modern-footer {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-top: 80px;
    padding: 60px 0 20px;
  }
  
  .footer-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px;
  }
  
  .footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    margin-bottom: 40px;
  }
  
  .footer-section h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .footer-section p {
    line-height: 1.8;
    color: rgba(255,255,255,0.9);
    margin-bottom: 16px;
  }
  
  .footer-links {
    list-style: none;
    padding: 0;
  }
  
  .footer-links li {
    margin-bottom: 12px;
  }
  
  .footer-links a {
    color: rgba(255,255,255,0.9);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  
  .footer-links a:hover {
    color: white;
    transform: translateX(5px);
  }
  
  .social-links {
    display: flex;
    gap: 12px;
    margin-top: 16px;
  }
  
  .social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 18px;
  }
  
  .social-link:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-3px);
  }
  
  .footer-divider {
    height: 1px;
    background: rgba(255,255,255,0.2);
    margin: 30px 0;
  }
  
  .footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    padding: 20px 0;
  }
  
  .footer-copyright {
    color: rgba(255,255,255,0.8);
    font-size: 14px;
  }
  
  .footer-logo {
    font-size: 20px;
    font-weight: 700;
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .footer-logo i {
    font-size: 24px;
  }
  
  @media (max-width: 768px) {
    .modern-footer { padding: 40px 0 20px; margin-top: 60px; }
    .footer-content { gap: 30px; }
    .footer-bottom { flex-direction: column; text-align: center; }
  }
</style>

<footer class="modern-footer">
  <div class="footer-container">
    <div class="footer-content">
      <div class="footer-section">
        <h3>
          <i class="fa-solid fa-book-open"></i>
          MyBook
        </h3>
        <p>
          Nền tảng chia sẻ kiến thức và câu chuyện. Nơi mọi người có thể viết, đọc và kết nối với nhau thông qua những bài viết chất lượng.
        </p>
        <div class="social-links">
          <a href="#" class="social-link" title="Facebook">
            <i class="fa-brands fa-facebook-f"></i>
          </a>
          <a href="#" class="social-link" title="Twitter">
            <i class="fa-brands fa-twitter"></i>
          </a>
          <a href="#" class="social-link" title="Instagram">
            <i class="fa-brands fa-instagram"></i>
          </a>
          <a href="#" class="social-link" title="LinkedIn">
            <i class="fa-brands fa-linkedin-in"></i>
          </a>
          <a href="#" class="social-link" title="YouTube">
            <i class="fa-brands fa-youtube"></i>
          </a>
        </div>
      </div>
      
      <div class="footer-section">
        <h3>
          <i class="fa-solid fa-compass"></i>
          Khám phá
        </h3>
        <ul class="footer-links">
          <li>
            <a href="<?= $BASE_PATH ?>/">
              <i class="fa-solid fa-chevron-right"></i>
              Trang chủ
            </a>
          </li>
          <li>
            <a href="<?= $BASE_PATH ?>/posts/index.php">
              <i class="fa-solid fa-chevron-right"></i>
              Bài viết mới nhất
            </a>
          </li>
          <li>
            <a href="<?= $BASE_PATH ?>/posts/create.php">
              <i class="fa-solid fa-chevron-right"></i>
              Viết bài mới
            </a>
          </li>
          <li>
            <a href="<?= $BASE_PATH ?>/search/search.php">
              <i class="fa-solid fa-chevron-right"></i>
              Tìm kiếm
            </a>
          </li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h3>
          <i class="fa-solid fa-circle-info"></i>
          Hỗ trợ
        </h3>
        <ul class="footer-links">
          <li>
            <a href="#">
              <i class="fa-solid fa-chevron-right"></i>
              Về chúng tôi
            </a>
          </li>
          <li>
            <a href="#">
              <i class="fa-solid fa-chevron-right"></i>
              Điều khoản sử dụng
            </a>
          </li>
          <li>
            <a href="#">
              <i class="fa-solid fa-chevron-right"></i>
              Chính sách bảo mật
            </a>
          </li>
          <li>
            <a href="#">
              <i class="fa-solid fa-chevron-right"></i>
              Liên hệ
            </a>
          </li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h3>
          <i class="fa-solid fa-envelope"></i>
          Liên hệ
        </h3>
        <p>
          <i class="fa-solid fa-location-dot"></i>
          Hà Nội, Việt Nam
        </p>
        <p>
          <i class="fa-solid fa-phone"></i>
          +84 123 456 789
        </p>
        <p>
          <i class="fa-solid fa-envelope"></i>
          contact@mybook.vn
        </p>
      </div>
    </div>
    
    <div class="footer-divider"></div>
    
    <div class="footer-bottom">
      <a href="<?= $BASE_PATH ?>/" class="footer-logo">
        <i class="fa-solid fa-book-open"></i>
        MyBook
      </a>
      <div class="footer-copyright">
        © <?= date('Y') ?> MyBook. Tất cả quyền được bảo lưu. Made with <i class="fa-solid fa-heart" style="color: #ff6b6b;"></i> in Vietnam
      </div>
    </div>
  </div>
</footer>

</body>
</html>
