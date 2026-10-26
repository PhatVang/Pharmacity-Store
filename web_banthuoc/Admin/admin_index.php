<?php 
session_start(); 
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';
// 3 DÒNG NÀY ĐỂ CẤM TRÌNH DUYỆT LƯU CACHE (CHỐNG BẤM NÚT BACK)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Kiểm tra quyền Admin
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: /web_banthuoc/user_display/index.php");
    exit();
}

// Kết nối Database

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy thông tin Ông Chủ từ Database
$ten_dang_nhap = $_SESSION['ten_khach_hang'];
$sql = "SELECT * FROM users WHERE username = '$ten_dang_nhap'";
$result = mysqli_query($conn, $sql);
$userData = mysqli_fetch_assoc($result);

// ==========================================================
// THÊM ĐOẠN NÀY VÀO ĐỂ TÍNH TOÁN BIẾN $tong_tim CHO ADMIN
// ==========================================================
$tong_tim = 0;
if (isset($conn)) {
    $username_tim = $_SESSION['ten_khach_hang'];
    $sql_tim = "SELECT COUNT(pl.id) as total FROM user_favorites pl JOIN users u ON pl.user_id = u.id WHERE u.username = '$username_tim'";
    $res_tim = @mysqli_query($conn, $sql_tim);
    if ($res_tim && $row_tim = mysqli_fetch_assoc($res_tim)) {
        $tong_tim = $row_tim['total'];
    }
}
// ==========================================================
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ Quản trị - RaumaCity</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="/web_banthuoc/assets/style.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body>

    <header class="py-3 sticky-top bg-white shadow-sm" style="z-index: 1000;">
        <div class="container d-flex justify-content-between align-items-center">
            
            <div class="logo fs-4 fw-bold" style="min-width: 280px;">
                <a href="/web_banthuoc/Admin/admin_index.php" class="text-decoration-none text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-heart-pulse text-success"></i> RaumaCity
                </a>
            </div>
            
            <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle fw-semibold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #111827; transition: color 0.3s;">
        <i class="fa-solid fa-layer-group me-1" style="color: #059669;"></i> Danh mục sản phẩm
    </a>
    <ul class="dropdown-menu shadow-sm rounded-3 border-0 py-2" aria-labelledby="navbarDropdown" style="background-color: #ffffff;">
        
        <li>
            <a class="dropdown-item py-2 px-3 fw-medium" href="/web_banthuoc/Admin/admin_cuahang.php">
                <i class="fa-solid fa-border-all me-2 text-secondary"></i> Tất cả sản phẩm
            </a>
        </li>
        <?php if(isset($_SESSION['ten_khach_hang'])): ?>
            <li>
        <a class="dropdown-item py-2 px-3 fw-medium" href="/web_banthuoc/Admin/admin_cuahang.php?yeuthich=1">
            <i class="fa-solid fa-heart me-2 text-danger"></i> Sản phẩm yêu thích
        </a>
    </li>
        <?php else: ?>
            <li>
        <a class="dropdown-item py-2 px-3 fw-medium btn-yeucau-dangnhap" href="#">
            <i class="fa-solid fa-heart me-2 text-secondary"></i> Sản phẩm yêu thích
        </a>
    </li>
        <?php endif; ?>
        <li><hr class="dropdown-divider border-light-subtle"></li>

        <?php
        $xml_path = $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/danhmuc.xml';

        if (file_exists($xml_path)) {
            $xml = simplexml_load_file($xml_path);
            
            foreach ($xml->category as $cat) {
                $id = (int)$cat->id;
                $name = htmlspecialchars($cat->name);
                
                echo '<li>';
                // Thẻ <a> dưới đây đã được gắn class "dynamic-xml-item" để ăn theo file style.css
                echo '<a class="dropdown-item py-2 px-3 dynamic-xml-item" href="/web_banthuoc/Admin/admin_cuahang.php?danhmuc=' . $id . '">';
                echo '<i class="fa-solid fa-chevron-right me-2 text-muted" style="font-size: 0.8rem;"></i> ' . $name;
                echo '</a>';
                echo '</li>';
            }
        } else {
            echo '<li><a class="dropdown-item text-danger">Không tìm thấy file cấu trúc XML!</a></li>';
        }
        ?>
    </ul>
</li>
            <nav class="d-flex gap-4">
                <a href="/web_banthuoc/Admin/admin_index.php" class="text-decoration-none text-success fw-bold d-flex align-items-center gap-2">
                    <i class="fa-solid fa-wave-square"></i> Trang chủ
                </a>
                <a href="/web_banthuoc/Admin/admin_cuahang.php" class="text-decoration-none text-secondary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-bag-shopping"></i> Nhà thuốc
                </a>
            </nav>

            <div class="d-flex gap-3 align-items-center justify-content-end" style="min-width: 280px;">
                <div class="dropdown">
                    <button class="btn btn-outline-success rounded-pill px-3 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user-tie"></i> Xin chào ông chủ, <?php echo $ten_dang_nhap; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><a class="dropdown-item py-2" href="/web_banthuoc/Admin/admin_account.php"><i class="fa-solid fa-id-card me-2 text-secondary"></i> Hồ sơ của tôi</a></li>
                        <li><a class="dropdown-item py-2" href="/web_banthuoc/Admin/admin_dashboard.php"><i class="fa-solid fa-chart-pie me-2 text-secondary"></i> Bảng Thống Kê</a></li>
                        <li><a class="dropdown-item py-2" href="/web_banthuoc/Admin/admin_lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> Lịch sử mua hàng</a></li>
                        <li><a class="dropdown-item py-2" href="/web_banthuoc/Admin/admin.php"><i class="fa-solid fa-boxes-stacked me-2 text-secondary"></i> Quản lý sản phẩm</a></li>
                        <li><a class="dropdown-item py-2" href="/web_banthuoc/Admin/admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar me-2 text-secondary"></i> Xác nhận giao dịch</a></li>
                        <li><a class="dropdown-item py-2" href="/web_banthuoc/Admin/categories_manager.php"><i class="fa-solid fa-list"></i> Quản lí danh mục</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger fw-bold" href="/web_banthuoc/public_auth/dangxuat.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main class="container-fluid p-0">
        
        <section class="container my-5">
            <div class="p-5 rounded-4 row align-items-center position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #f0fbfa 0%, #f8fafc 100%) !important;">
                
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
                        <a href="/web_banthuoc/Admin/admin_cuahang.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">Mua thuốc</a>
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
    </main>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Tuyệt chiêu chống "Bóng ma" (BFCache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload(); 
            }
        });

        // ================= KỊCH BẢN CHO AI CHATBOT =================
        const chatbox = document.getElementById('ai-chatbox');
        const chatBody = document.getElementById('chatbox-body');
        const chatInput = document.getElementById('chat-input');
        const btnSend = document.getElementById('btn-send-chat');

        document.getElementById('chatbot-toggle-btn').addEventListener('click', () => chatbox.style.display = 'flex');
        document.getElementById('close-chat').addEventListener('click', () => chatbox.style.display = 'none');

        function scrollToBottom() {
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function appendMessage(sender, text) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `message ${sender}-message`;
            
            let avatar = sender === 'user' ? '<i class="fa-solid fa-user"></i>' : '<i class="fa-solid fa-user-doctor"></i>';
            let formattedText = text.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            msgDiv.innerHTML = `
                <div class="msg-avatar">${avatar}</div>
                <div class="msg-text">${formattedText}</div>
            `;
            chatBody.appendChild(msgDiv);
            scrollToBottom();
        }

        async function sendMessage() {
            const text = chatInput.value.trim();
            if (!text) return; 

            appendMessage('user', text);
            chatInput.value = ''; 

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

            try {
                const response = await fetch('/web_banthuoc/backends_actions/api_chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                });
                
                const data = await response.json();
                
                document.getElementById(loadingId).remove();
                appendMessage('bot', data.reply);

            } catch (error) {
                document.getElementById(loadingId).remove();
                appendMessage('bot', "Đường truyền đang gặp sự cố, bạn vui lòng thử lại sau nhé!");
            }
        }

        btnSend.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</body>
</html>