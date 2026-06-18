<?php
session_start();
// 3 DÒNG NÀY ĐỂ CHỐNG "BÓNG MA" (BẤM NÚT BACK CỦA TRÌNH DUYỆT)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Kết nối Database 
$conn = mysqli_connect("localhost", "root", "", "pharmacity");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Bắt buộc phải đăng nhập mới được thanh toán
if (!isset($_SESSION['ten_khach_hang'])) {
    header("Location: dangnhap.php");
    exit();
}

$ten_dang_nhap = $_SESSION['ten_khach_hang'];

// Xử lý khi khách bấm nút "Hoàn tất đặt hàng"
if (isset($_POST['btn_dat_hang'])) {
    $fullname = $_POST['fullname'];
    $ghi_chu = isset($_POST['ghi_chu']) ? mysqli_real_escape_string($conn, $_POST['ghi_chu']) : '';
    $diachi = $_POST['diachi'];
    $sodt = $_POST['sodt'];
    $phuong_thuc = $_POST['phuong_thuc'];
    $tong_tien = $_POST['tong_tien_hidden'];
    $cart_data = json_decode($_POST['cart_data_hidden'], true);

    // Lưu vào bảng orders với trạng thái "Chờ duyệt"
    $sql_order = "INSERT INTO orders (username, fullname, diachi, sodt, tong_tien, ghi_chu, phuong_thuc, trang_thai) 
                  VALUES ('$ten_dang_nhap', '$fullname', '$diachi', '$sodt', '$tong_tien', '$ghi_chu', '$phuong_thuc', 'Chờ duyệt')";
    
    if (mysqli_query($conn, $sql_order)) {
        $order_id = mysqli_insert_id($conn); 
        
        // Lưu từng sản phẩm vào bảng order_details
        foreach ($cart_data as $item) {
            $ten_sp = $item['name'];
            $so_luong = $item['quantity'];
            $gia = $item['price'];
            $sql_detail = "INSERT INTO order_details (order_id, product_name, quantity, price) 
                           VALUES ('$order_id', '$ten_sp', '$so_luong', '$gia')";
            mysqli_query($conn, $sql_detail);
        }

        // Chuyển hướng sang Lịch sử mua hàng
        // Phân luồng: Admin về nhà Admin, Khách về nhà Khách
        $trang_dich = (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1) ? 'admin_lichsumuahang.php' : 'lichsumuahang.php';
        header("Location: " . $trang_dich . "?dat_hang=thanhcong");
        exit();
    }
}

// Lấy thông tin khách hàng hiện tại
$sql_user = "SELECT * FROM users WHERE username='$ten_dang_nhap'";
$result_user = mysqli_query($conn, $sql_user);
$user = mysqli_fetch_assoc($result_user);
// KIỂM TRA THIẾU THÔNG TIN (Chỉ đặt cờ, không cúp cầu dao)
$thieu_thong_tin = false;
if (empty($user['sodt']) || empty($user['diachi'])) {
    $thieu_thong_tin = true;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - PharmaCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="hehe.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <div class="container navbar">
            <a href="<?php echo (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1) ? 'admin_index.php' : 'index.php'; ?>">
                <i class="fa-solid fa-heart-pulse"></i> PharmaCity 
            </a>
            <nav class="nav-links">
                <a href="javascript:history.back()"><i class="fa-solid fa-arrow-left"></i> Quay lại cửa hàng</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="checkout-container">
            <form action="" method="POST" id="form-thanh-toan">
                
                <div class="checkout-section">
                    <h3><i class="fa-solid fa-location-dot"></i> 1. Thông tin nhận hàng</h3>
                    <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($user['fullname'] ?? 'Chưa cập nhật'); ?></p>
                    <p><strong>Số điện thoại:</strong> <span style="color: red;"><?php echo htmlspecialchars($user['sodt'] ?? 'Chưa cập nhật'); ?></span></p>
                    <p><strong>Địa chỉ:</strong> <span style="color: red;"><?php echo htmlspecialchars($user['diachi'] ?? 'Chưa cập nhật'); ?></span></p>
                    
                    <input type="hidden" name="fullname" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>">
                    <input type="hidden" name="sodt" value="<?php echo htmlspecialchars($user['sodt'] ?? ''); ?>">
                    <input type="hidden" name="diachi" value="<?php echo htmlspecialchars($user['diachi'] ?? ''); ?>">
                </div>

                <div class="checkout-section">
                    <h3><i class="fa-solid fa-box"></i> 2. Sản phẩm đã đặt</h3>
                    <div id="danh-sach-sp-thanh-toan">
                        </div>
                    <h3 class="checkout-total-price">
                        Tổng tiền: <span id="tong-tien-thanh-toan">0 đ</span>
                    </h3>
                    
                    <input type="hidden" name="cart_data_hidden" id="cart_data_hidden">
                    <input type="hidden" name="tong_tien_hidden" id="tong_tien_hidden">
                </div>

                <div class="checkout-section">
                    <h3><i class="fa-solid fa-money-bill-wave"></i> 3. Phương thức thanh toán</h3>
                    <div class="payment-method-box">
                        <label>
                            <input type="radio" name="phuong_thuc" value="Tiền mặt" checked onclick="hienThiQR(false)"> 
                            Thanh toán trực tiếp khi nhận hàng (COD)
                        </label>
                        <label>
                            <input type="radio" name="phuong_thuc" value="Chuyển khoản" onclick="hienThiQR(true)"> 
                            Chuyển khoản gián tiếp qua mã QR
                        </label>
                    </div>

                    <div id="qr-code-section" class="qr-code-wrapper" style="display: none;">
                        <p class="qr-instruction">Vui lòng quét mã QR dưới đây. Đơn hàng sẽ được duyệt khi có chuông "Ting Ting"!</p>
                        <img id="img-qr-thanh-toan" src="uploads/z7645924431972_8200ad1bef4fdcc0c1a21d1eb7de1a44.jpg" alt="Mã QR Vietcombank" class="qr-image">
                    </div>
                </div>
                <div class="checkout-note-box">
                <h3 class="checkout-note-title">
                    <i class="fa-solid fa-comment-dots"></i> 4. Lời nhắn cho PharmaCity (Tùy chọn)
                </h3>
                <p class="checkout-note-desc">
                    Ghi chú về sản phẩm, thời gian nhận hàng hoặc bất kỳ yêu cầu nào khác để Dược sĩ tư vấn thêm cho bạn.
                </p>
                <textarea id="ghi_chu_don_hang" name="ghi_chu" class="checkout-note-textarea" placeholder="Ví dụ: Lấy cho em Panadol loại vỉ xanh, giao giờ hành chính nhé shop..." rows="3" maxlength="250"></textarea>
            </div>
            <br>
                <button type="submit" name="btn_dat_hang" class="btn btn-primary btn-full-width btn-checkout-submit">
                    Hoàn tất đặt hàng
                </button>
            </form>
        </div>
    </main>

    <script>
        const tenKhachHang = "<?php echo $ten_dang_nhap; ?>";
        const tenKhoGioHang = "gioHang_" + tenKhachHang;

        document.addEventListener('DOMContentLoaded', () => {
            let cart = JSON.parse(localStorage.getItem(tenKhoGioHang)) || [];
            
            if (cart.length === 0) {
            alert("Giỏ hàng trống! Đang quay lại cửa hàng...");
            if (document.referrer.includes("admin_")) {
                window.location.href = "admin_haha.php"; // Bế Admin về đúng chỗ
            } else {
                window.location.href = "haha.php";       // Bế Khách về chỗ cũ
            }
            return;
        }

            let htmlSanPham = "";
            let tongTien = 0;

            cart.forEach(item => {
                let thanhTien = item.price * item.quantity;
                tongTien += thanhTien;
                htmlSanPham += `
                    <div class="checkout-item-row">
                        <span><strong>${item.quantity}x</strong> ${item.name}</span>
                        <span>${thanhTien.toLocaleString('vi-VN')} đ</span>
                    </div>
                `;
            });

            document.getElementById('danh-sach-sp-thanh-toan').innerHTML = htmlSanPham;
            document.getElementById('tong-tien-thanh-toan').innerText = tongTien.toLocaleString('vi-VN') + ' đ';

            document.getElementById('cart_data_hidden').value = JSON.stringify(cart);
            document.getElementById('tong_tien_hidden').value = tongTien;
            // TẠO MÃ VIETQR ĐỘNG THEO TỔNG TIỀN
            const bankID = "vietcombank"; 
            const soTaiKhoan = "1049335106"; 
            const tenChuTaiKhoan = "TRINH HUNG PHAT";           
            // Tạo nội dung chuyển khoản không dấu (Ví dụ: Thanh toan don hang admin)
            const noiDung = "Thanh toan don hang " + tenKhachHang; 
            // Lắp ráp đường link gọi API VietQR
            const linkVietQR = `https://img.vietqr.io/image/${bankID}-${soTaiKhoan}-compact2.jpg?amount=${tongTien}&addInfo=${encodeURIComponent(noiDung)}&accountName=${encodeURIComponent(tenChuTaiKhoan)}`;            
            // Tráo ảnh tĩnh thành ảnh động
            document.getElementById('img-qr-thanh-toan').src = linkVietQR;
        });
        function hienThiQR(hien) {
            const qrSection = document.getElementById('qr-code-section');
            if (hien) {
                qrSection.style.display = 'block';
            } else {
                qrSection.style.display = 'none';
            }
        }
    </script>

    <?php if ($thieu_thong_tin): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Dò xem trang trước đó khách đứng có chữ "admin" không
            let laAdmin = <?php echo (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1) ? 'true' : 'false'; ?>;

            Swal.fire({
                title: "Opps!",
                text: "Vui lòng cập nhật đầy đủ Số điện thoại và Địa chỉ trong phần Hồ sơ để đặt hàng!",
                icon: "error",
                confirmButtonColor: "#059669", 
                confirmButtonText: "Đi cập nhật ngay",
                allowOutsideClick: false, 
                allowEscapeKey: false
            }).then((result) => {
                // Bế về đúng chuồng
                window.location.href = laAdmin ? "admin_account.php" : "profile.php";
            });
        });
    </script>
    <?php endif; ?>

    <script>
        // TRỊ TẬN GỐC LỖI BẤM NÚT BACK CỦA TRÌNH DUYỆT
        window.addEventListener('pageshow', function(event) {
            // Nếu trình duyệt moi trang web từ trong Cache ra
            if (event.persisted) {
                // Ép nó F5 tải lại dữ liệu mới nhất từ Server
                window.location.reload(); 
            }
        });
    </script>
</body>
</html>