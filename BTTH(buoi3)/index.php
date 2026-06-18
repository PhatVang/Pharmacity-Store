<?php 
session_start(); 
// 3 DÒNG NÀY ĐỂ CẤM TRÌNH DUYỆT LƯU CACHE (CHỐNG BẤM NÚT BACK)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (!isset($_SESSION['ten_khach_hang'])) {
    header("Location: dangnhap.php");
    exit(); 
}
 
?>
<!DOCTYPE html>
<html lang="vi">
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
          <a href ="index.php"><i class="fa-solid fa-heart-pulse"></i> PharmaCity </a>
        </div>

        <nav class="nav-links">
    <a href="index.php" class="active"><i class="fa-solid fa-wave-square"></i> Trang chủ</a>
    <a href="haha.php"><i class="fa-solid fa-bag-shopping"></i> Nhà thuốc</a>
        </nav>
    <div class="auth-buttons">
                <div class="user-menu">
                    <button class="btn-user">
                        <i class="fa-solid fa-user-circle"></i> Xin chào, <?php echo $_SESSION['ten_khach_hang']; ?> <i class="fa-solid fa-caret-down"></i>
                    </button>
                    
                    <div class="dropdown-content">
                        <a href="profile.php"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                        <a href="lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                        <hr> <a href="dangxuat.php" class="logout-text"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                    </div>
                </div>
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
            <a href="haha.php" class="btn btn-outline">Mua thuốc</a>
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
                        <li><a href="haha.php">Cửa hàng thuốc</a></li>
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
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const nutUser = document.querySelector(".btn-user");
    const menuXoXuong = document.querySelector(".dropdown-content");

    if (nutUser && menuXoXuong) {
        // 1. Khi bấm vào nút Tên người dùng
        nutUser.addEventListener("click", function(event) {
            event.stopPropagation(); // Ngăn chặn sự kiện click lan ra ngoài
            menuXoXuong.classList.toggle("show"); // Bật/tắt menu
        });

        // 2. Khi bấm ra chỗ khác trên màn hình thì tự đóng menu lại
        window.addEventListener("click", function(event) {
            if (!menuXoXuong.contains(event.target) && !nutUser.contains(event.target)) {
                menuXoXuong.classList.remove("show");
            }
        });
    }
});
</script>
<script>
        // Tuyệt chiêu chống "Bóng ma" (BFCache) của trình duyệt
        window.addEventListener('pageshow', function(event) {
            // Nếu trình duyệt lôi trang web từ trong bộ nhớ đệm (cache) ra
            if (event.persisted) {
                // Ép nó phải F5 (tải lại) từ server ngay lập tức
                window.location.reload(); 
            }
        });
    </script>
    <div id="ai-chatbox" class="ai-chatbox-container">
        <div class="chatbox-header">
            <div class="chatbox-title">
                <i class="fa-solid fa-user-doctor"></i> Bác sĩ AI PharmaCity
            </div>
            <button id="close-chat" class="chatbox-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="chatbox-body" class="chatbox-body">
            <div class="message bot-message">
                <div class="msg-avatar"><i class="fa-solid fa-robot"></i></div>
                <div class="msg-text">Chào bạn! Tôi là Dược sĩ AI của PharmaCity. Bạn đang gặp triệu chứng gì hãy kể cho tôi nghe nhé!</div>
            </div>
        </div>
        <div class="chatbox-footer">
            <input type="text" id="chat-input" placeholder="Nhập triệu chứng của bạn..." autocomplete="off">
            <button id="btn-send-chat"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>

    <button id="chatbot-toggle-btn" class="chatbot-toggle-btn">
        <i class="fa-solid fa-comment-medical"></i>
    </button>
    <script>
        const chatbox = document.getElementById('ai-chatbox');
        const chatBody = document.getElementById('chatbox-body');
        const chatInput = document.getElementById('chat-input');
        const btnSend = document.getElementById('btn-send-chat');

        // Mở / Đóng Chatbox
        document.getElementById('chatbot-toggle-btn').addEventListener('click', () => chatbox.style.display = 'flex');
        document.getElementById('close-chat').addEventListener('click', () => chatbox.style.display = 'none');

        // Hàm tự động cuộn xuống tin nhắn mới nhất
        function scrollToBottom() {
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        // Hàm in tin nhắn (Của Khách hoặc Của AI) lên màn hình
        function appendMessage(sender, text) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `message ${sender}-message`;
            
            // Nếu là khách thì hiện icon User, AI thì hiện icon Robot
            let avatar = sender === 'user' ? '<i class="fa-solid fa-user"></i>' : '<i class="fa-solid fa-user-doctor"></i>';
            
            // Xử lý xuống dòng và in đậm cho đẹp
            let formattedText = text.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            msgDiv.innerHTML = `
                <div class="msg-avatar">${avatar}</div>
                <div class="msg-text">${formattedText}</div>
            `;
            chatBody.appendChild(msgDiv);
            scrollToBottom();
        }

        // Kịch bản gửi tin nhắn đi
        async function sendMessage() {
            const text = chatInput.value.trim();
            if (!text) return; // Không gõ gì thì không gửi

            // 1. In tin nhắn của khách lên
            appendMessage('user', text);
            chatInput.value = ''; // Xóa trắng ô nhập

            // 2. In chữ "Đang suy nghĩ..." để khách chờ
            const loadingId = 'loading-' + Date.now();
            const loadingDiv = document.createElement('div');
            loadingDiv.className = `message bot-message`;
            loadingDiv.id = loadingId;
            loadingDiv.innerHTML = `
                <div class="msg-avatar"><i class="fa-solid fa-user-doctor"></i></div>
                <div class="msg-text">Bác sĩ đang chẩn đoán... <i class="fa-solid fa-spinner fa-spin"></i></div>
            `;
            chatBody.appendChild(loadingDiv);
            scrollToBottom();

            // 3. Gọi sang file api_chat.php để lấy đơn thuốc
            try {
                const response = await fetch('api_chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                });
                
                const data = await response.json();
                
                // Xóa chữ "Đang chẩn đoán..." đi
                document.getElementById(loadingId).remove();
                
                // In câu trả lời thật của AI ra
                appendMessage('bot', data.reply);

            } catch (error) {
                // Xử lý khi rớt mạng hoặc lỗi server
                document.getElementById(loadingId).remove();
                appendMessage('bot', "Đường truyền đang gặp sự cố, bạn vui lòng thử lại sau nhé!");
            }
        }

        // Bấm nút Gửi
        btnSend.addEventListener('click', sendMessage);

        // Hoặc gõ xong bấm phím Enter
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
  </body>
</html>
