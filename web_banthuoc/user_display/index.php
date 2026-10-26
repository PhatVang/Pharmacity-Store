<?php 
session_start(); 
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php'; 
// 3 DÒNG NÀY ĐỂ CẤM TRÌNH DUYỆT LƯU CACHE (CHỐNG BẤM NÚT BACK)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (!isset($_SESSION['ten_khach_hang'])) {
    header("Location: /web_banthuoc/public_auth/dangnhap.php");
    exit(); 
}
 
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/header.php'; ?>

    <section class="container my-5">
        <div class="p-5 rounded-4 row align-items-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #f0fbfa 0%, #f8fafc 100%) !important;">
            
            <div class="col-lg-6 pr-lg-5" style="z-index: 2;">
                <div class="badge bg-success-subtle text-success rounded-pill px-3 py-2 mb-3 fw-bold border border-success-subtle">
                    Y tế thế hệ mới
                </div>
                <h1 class="fw-bold mb-3 text-dark" style="font-size: 3.2rem; line-height: 1.1;">
                    Sức khỏe của bạn,<br><span class="text-success">Ưu tiên của chúng tôi.</span>
                </h1>
                <p class="fs-5 text-secondary mb-4">
                    Trải nghiệm tương lai của ngành y tế. Nhận phân tích triệu chứng tức thì, mua sắm vật tư y tế cao cấp tại cùng một nơi.
                </p>
                <nav class="d-flex gap-3">
                    <button onclick="document.getElementById('chatbot-toggle-btn').click()" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow-sm">Kiểm tra triệu chứng ngay</button>
                    <!-- Đã đổi link sang cuahang.php cho khách đã đăng nhập -->
                    <a href="/web_banthuoc/user_display/cuahang.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">Mua thuốc</a>
                </nav>
            </div>
            
            <div class="col-lg-6 position-relative d-flex justify-content-center align-items-center" style="height: 400px;">
                <div class="art-circle-2"></div>
                <div class="art-circle-1"></div>
            </div>

        </div>
    </section>

    <section class="py-5 bg-white">
      <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 600px;">
          <h2 class="fs-2 fw-bold mb-3">Tại sao chọn RaumaCity</h2>
          <p class="text-secondary fs-5">
            Chúng tôi kết hợp công nghệ tiên tiến với chuỗi cung ứng y tế đáng tin cậy để mang đến cho bạn sự chăm sóc tốt nhất có thể.
          </p>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border border-light-subtle shadow-sm rounded-4 text-center p-4 feature-card-hover">
              <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-3 mb-4 mx-auto" style="width: 60px; height: 60px; font-size: 1.8rem;">
                <i class="fa-solid fa-stethoscope"></i>
              </div>
              <h3 class="fs-5 fw-bold mb-3">Kiểm tra triệu chứng</h3>
              <p class="text-secondary mb-0">Nhận chuẩn đoán của bác sĩ qua kênh chat.</p>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border border-light-subtle shadow-sm rounded-4 text-center p-4 feature-card-hover">
              <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-3 mb-4 mx-auto" style="width: 60px; height: 60px; font-size: 1.8rem;">
                <i class="fa-regular fa-clock"></i>
              </div>
              <h3 class="fs-5 fw-bold mb-3">Phục vụ 24/7</h3>
              <p class="text-secondary mb-0">Truy cập nhà thuốc kỹ thuật số và các công cụ sức khỏe của chúng tôi bất cứ lúc nào, ngày hay đêm.</p>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border border-light-subtle shadow-sm rounded-4 text-center p-4 feature-card-hover">
              <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-3 mb-4 mx-auto" style="width: 60px; height: 60px; font-size: 1.8rem;">
                <i class="fa-solid fa-shield-halved"></i>
              </div>
              <h3 class="fs-5 fw-bold mb-3">Chất lượng đáng tin cậy</h3>
              <p class="text-secondary mb-0">Tất cả các loại thuốc của chúng tôi có nguồn gốc trực tiếp từ các nhà sản xuất đã được xác minh.</p>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border border-light-subtle shadow-sm rounded-4 text-center p-4 feature-card-hover">
              <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-3 mb-4 mx-auto" style="width: 60px; height: 60px; font-size: 1.8rem;">
                <i class="fa-solid fa-bolt"></i>
              </div>
              <h3 class="fs-5 fw-bold mb-3">Giao hàng nhanh chóng</h3>
              <p class="text-secondary mb-0">Nhận các loại thuốc thiết yếu được giao đến tận cửa nhà bạn một cách nhanh chóng.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-5 my-5">
      <div class="container">
        <div class="p-5 rounded-4 text-center text-white shadow-lg" style="background-color: #059669;">
          <h2 class="fs-1 fw-bold mb-3">Cảm thấy không khỏe?</h2>
          <p class="fs-5 mb-4 mx-auto" style="max-width: 700px; opacity: 0.9;">
            Mô tả các triệu chứng của bạn cho chúng tôi và nhận các đề xuất tức thì về các bước tiếp theo cũng như các biện pháp khắc phục không kê đơn.
          </p>
          <button onclick="document.getElementById('chatbot-toggle-btn').click()" class="btn btn-light fw-bold rounded-pill px-4 py-3 fs-5 shadow-sm" style="color: #059669;">
            Bắt đầu chẩn đoán miễn phí <i class="fa-solid fa-arrow-right ms-2"></i>
          </button>
        </div>
      </div>
    </section>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/footer.php'; ?>
    <div id="ai-chatbox" class="ai-chatbox-container">
        <div class="chatbox-header">
            <div class="chatbox-title">
                <i class="fa-solid fa-user-doctor"></i> Bác sĩ AI RaumaCity
            </div>
            <button id="close-chat" class="chatbox-close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="chatbox-body" class="chatbox-body">
            <div class="message bot-message">
                <div class="msg-avatar"><i class="fa-solid fa-robot"></i></div>
                <div class="msg-text">Chào bạn! Tôi là Dược sĩ AI của RaumaCity. Bạn đang gặp triệu chứng gì hãy kể cho tôi nghe nhé!</div>
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
                const response = await fetch('/web_banthuoc/backens_actions/api_chat.php', {
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
