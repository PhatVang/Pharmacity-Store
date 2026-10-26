<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php'; 
// 3 DÒNG NÀY ĐỂ CHỐNG "BÓNG MA" (BẤM NÚT BACK CỦA TRÌNH DUYỆT)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Kết nối Database 

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Bắt buộc phải đăng nhập mới được thanh toán
if (!isset($_SESSION['ten_khach_hang'])) {
    header("Location: /web_banthuoc/public_auth/dangnhap.php");
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
    
    // CHỈ lấy danh sách mặt hàng, KHÔNG LẤY biến $tong_tien_hidden từ Frontend nữa
    $cart_data = json_decode($_POST['cart_data_hidden'], true);

    // ==========================================
    // BƯỚC 1: BACKEND TỰ TÍNH TIỀN TỪ DATABASE (Bảo mật 100%)
    // ==========================================
    $tong_tien_that = 0;
    $danh_sach_sp_chuan = []; 

    foreach ($cart_data as $item) {
        // Lọc tên sản phẩm cho an toàn
        $ten_sp = mysqli_real_escape_string($conn, $item['name']);
        $so_luong = (int)$item['quantity'];

        // Truy vấn lại giá gốc của sản phẩm này trong kho
        $sql_check_price = "SELECT price FROM products WHERE name = '$ten_sp'";
        $res_price = mysqli_query($conn, $sql_check_price);
        
        if (mysqli_num_rows($res_price) > 0) {
            $row_price = mysqli_fetch_assoc($res_price);
            $gia_that = (int)$row_price['price'];
            
            // Tự động nhân tiền: Giá thật x Số lượng
            $tong_tien_that += ($gia_that * $so_luong);

            // Cất vào một mảng "sạch" để lát nữa lưu chi tiết
            $danh_sach_sp_chuan[] = [
                'name' => $ten_sp,
                'quantity' => $so_luong,
                'price' => $gia_that
            ];
        }
    }

    // ==========================================
    // BƯỚC 2: LƯU ĐƠN HÀNG VỚI TỔNG TIỀN "SẠCH"
    // ==========================================
    $sql_order = "INSERT INTO orders (username, fullname, diachi, sodt, tong_tien, ghi_chu, phuong_thuc, trang_thai) 
                  VALUES ('$ten_dang_nhap', '$fullname', '$diachi', '$sodt', '$tong_tien_that', '$ghi_chu', '$phuong_thuc', 'Chờ duyệt')";
    
    if (mysqli_query($conn, $sql_order)) {
        $order_id = mysqli_insert_id($conn); 
        
        // BƯỚC 3: LƯU CHI TIẾT ĐƠN HÀNG 
        foreach ($danh_sach_sp_chuan as $sp) {
            $sp_name = $sp['name'];
            $sp_qty = $sp['quantity'];
            $sp_price = $sp['price'];
            
            $sql_detail = "INSERT INTO order_details (order_id, product_name, quantity, price) 
                           VALUES ('$order_id', '$sp_name', '$sp_qty', '$sp_price')";
            mysqli_query($conn, $sql_detail);
        }

        // BƯỚC 4: Rẽ nhánh chuyển trang
        $trang_dich = (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1) ? '/web_banthuoc/Admin/admin_lichsumuahang.php' : '/web_banthuoc/user_display/lichsumuahang.php';
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
    <title>Thanh toán - RaumaCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/web_banthuoc/assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <?php
            // Đặt biến đường dẫn thông minh: Nếu có session (đã đăng nhập) thì đi đường VIP, chưa thì đi đường Khách
            $link_trangchu = isset($_SESSION['ten_khach_hang']) ? '/web_banthuoc/user_display/index.php' : '/web_banthuoc/trangchu.php';
            $link_cuahang  = isset($_SESSION['ten_khach_hang']) ? '/web_banthuoc/user_display/cuahang.php' : '/web_banthuoc/user_display/muahang.php';
        ?>
        <div class="container navbar">
            <div class="logo fs-4 fw-bold">
            <a href="<?php echo $link_trangchu; ?>" class="text-decoration-none text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-heart-pulse text-success"></i> RaumaCity
            </a>
        </div>
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
                        <img id="img-qr-thanh-toan" src="/web_banthuoc/uploads/z7645924431972_8200ad1bef4fdcc0c1a21d1eb7de1a44.jpg" alt="Mã QR Vietcombank" class="qr-image">
                    </div>
                </div>
                <div class="checkout-note-box">
                <h3 class="checkout-note-title">
                    <i class="fa-solid fa-comment-dots"></i> 4. Lời nhắn cho RaumaCity (Tùy chọn)
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
                window.location.href = "/web_banthuoc/Admin/admin_cuahang.php"; // Bế Admin về đúng chỗ
            } else {
                window.location.href = "/web_banthuoc/user_display/cuahang.php";       // Bế Khách về chỗ cũ
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
                window.location.href = laAdmin ? "/web_banthuoc/Admin/admin_account.php" : "/web_banthuoc/user_display/profile.php";
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