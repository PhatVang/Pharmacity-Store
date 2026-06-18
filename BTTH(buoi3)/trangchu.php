<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="hehe.css" />
  </head>
  <body>
    <header>
      <div class="container navbar">
        <div class="logo">
          <a href ="trangchu.php"><i class="fa-solid fa-heart-pulse"></i> PharmaCity </a>
        </div>

        <nav class="nav-links">
                <a href="trangchu.php"><i class="fa-solid fa-wave-square"></i> Trang chủ</a>
                <a href="muahang.php"><i class="fa-solid fa-bag-shopping"></i> Nhà thuốc</a>
            </nav>
            <div class="auth-buttons">
                            <a href="dangnhap.php" class="btn btn-outline">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                </a>
                <a href="dangky.php" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Đăng ký
                </a>
</div>
    </header>
    <section class="container">
      <div class="hero-section">
        <div class="hero-content">
          <div class="badge">Y tế thế hệ mới</div>
          <h1 class="hero-title">
            Sức khỏe của bạn,<br /><span>Ưu tiên của chúng tôi.</span>
          </h1>
          <p class="hero-desc">
            Trải nghiệm tương lai của ngành y tế. Nhận phân tích triệu chứng tức
            thì, mua sắm vật tư y tế cao cấp tại cùng một nơi.
          </p>
          <nav class="hero-buttons">
            <a href="#" class="btn btn-primary">Kiểm tra triệu chứng ngay</a>
            <a href="muahang.php" class="btn btn-outline">Mua thuốc</a>
          </nav>
        </div>
        <div class="hero-visual">
          <div class="art-circle-2"></div>
          <div class="art-circle-1"></div>
        </div>
      </div>
    </section>

    <section class="features-section">
      <div class="container">
        <div class="section-header">
          <h2>Tại sao chọn PharmaCity</h2>
          <p>
            Chúng tôi kết hợp công nghệ tiên tiến với chuỗi cung ứng y tế đáng
            tin cậy để mang đến cho bạn sự chăm sóc tốt nhất có thể.
          </p>
        </div>

        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-solid fa-stethoscope"></i>
            </div>
            <h3>Kiểm tra triệu chứng</h3>
            <p>
              Nhận chuẩn đoán của bác sĩ qua kên chat.
            </p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-regular fa-clock"></i>
            </div>
            <h3>Phục vụ 24/7</h3>
            <p>
              Truy cập nhà thuốc kỹ thuật số và các công cụ sức khỏe của chúng
              tôi bất cứ lúc nào, ngày hay đêm.
            </p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3>Chất lượng đáng tin cậy</h3>
            <p>
              Tất cả các loại thuốc của chúng tôi có nguồn gốc trực tiếp từ các
              nhà sản xuất đã được xác minh.
            </p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-solid fa-bolt"></i>
            </div>
            <h3>Giao hàng nhanh chóng</h3>
            <p>
              Nhận các loại thuốc thiết yếu được giao đến tận cửa nhà bạn một
              cách nhanh chóng.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-section">
      <div class="container">
        <div class="cta-banner">
          <h2>Cảm thấy không khỏe?</h2>
          <p>
            Mô tả các triệu chứng của bạn cho chúng tôi
            và nhận các đề xuất tức thì về các bước tiếp theo cũng như các biện
            pháp khắc phục không kê đơn.
          </p>
          <a href="#" class="btn btn-white"
            >Bắt đầu chẩn đoán miễn phí
            <i class="fa-solid fa-arrow-right" style="margin-left: 8px"></i
          ></a>
        </div>
      </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo">
                        <i class="fa-solid fa-heart-pulse"></i>
                        PharmaCity
                    </div>
                    <p>Nhà thuốc kỹ thuật số và người bạn đồng hành chăm sóc sức khỏe đáng tin cậy của bạn.</p>
                </div>
                
                <div class="footer-links">
                    <h4>Liên kết nhanh</h4>
                    <ul>
                        <li><a href="muahang.php">Cửa hàng thuốc</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Pháp lý</h4>
                    <ul>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Điều khoản dịch vụ</a></li>
                        <li><a href="#">Tuyên bố từ chối trách nhiệm y tế</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>© 2026 PharmaCity. Chỉ mang tính chất cung cấp thông tin.</p>
            </div>
        </div>
    </footer>
    
  </body>
</html>
