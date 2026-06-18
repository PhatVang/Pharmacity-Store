<?php 
session_start(); 

// Chống bóng ma
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Kiểm tra quyền Admin
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: index.php");
    exit();
}

// Kết nối Database
$conn = mysqli_connect("localhost", "root", "", "pharmacity");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy thông tin Ông Chủ từ Database
$ten_dang_nhap = $_SESSION['ten_khach_hang'];
$sql = "SELECT * FROM users WHERE username = '$ten_dang_nhap'";
$result = mysqli_query($conn, $sql);
$userData = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ Ông chủ - PharmaCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="hehe.css">
</head>
<body>

    <header>
        <div class="container navbar">
            <div class="logo">
                <a href ="admin_index.php"><i class="fa-solid fa-heart-pulse"></i> PharmaCity </a>
            </div>
            <nav class="nav-links">
                <a href="admin_index.php"><i class="fa-solid fa-globe"></i> Xem trang web</a>
            </nav>
            <div class="auth-buttons">
                <div class="user-menu">
                    <button class="btn-user">
                        <i class="fa-solid fa-user-tie"></i> Xin chào ông chủ, <?php echo $ten_dang_nhap; ?> <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="admin_account.php"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                        <a href="admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
                        <a href="admin_lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                        <a href="admin.php"><i class="fa-solid fa-boxes-stacked"></i> Quản lí sản phẩm</a>
                        <a href="admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>                       
                        <hr> <a href="dangxuat.php" class="logout-text"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container profile-main">
        <div class="profile-layout">
            
            <aside class="profile-card">
                <div class="profile-user-info">
                    <div class="profile-avatar avatar-admin"> 
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($ten_dang_nhap); ?></h3>
                    <p class="member-badge badge-admin"><i class="fa-solid fa-star"></i> Chủ PharmaCity</p>
                </div>
                <nav class="profile-nav">
                    <a href="admin_account.php" class="active"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                    <a href="admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
                    <a href="admin_lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                    <a href="admin.php"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                    <a href="admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>                  
                    <hr><a href="dangxuat.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                </nav>
            </aside>

            <div class="profile-card">
                <div class="profile-header-box">
                    <h2>Thông tin cá nhân</h2>
                    <button class="btn btn-outline btn-edit-profile"><i class="fa-solid fa-pen"></i> Chỉnh sửa</button>
                </div>

               <form action="update_profile.php" method="POST" id="form-profile" class="profile-form">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($userData['fullname'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="sodt" value="<?php echo htmlspecialchars($userData['sodt'] ?? ''); ?>" readonly maxlength="10" pattern="0[1-9][0-9]{8}" title="Số điện thoại phải có đúng 10 số, bắt đầu bằng 0, số thứ hai từ 1-9" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^[^0]/, '').replace(/^00/, '0')">
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="date" name="ngaysinh" value="<?php echo htmlspecialchars($userData['ngaysinh'] ?? ''); ?>" max="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Giới tính</label>
                            <select name="gioitinh" disabled>
                                <option value="" <?php echo (empty($userData['gioitinh'])) ? 'selected' : ''; ?>>Chưa cập nhật</option>
                                <option value="Nam" <?php echo (isset($userData['gioitinh']) && $userData['gioitinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                                <option value="Nữ" <?php echo (isset($userData['gioitinh']) && $userData['gioitinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                                <option value="Khác" <?php echo (isset($userData['gioitinh']) && $userData['gioitinh'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <input type="text" name="diachi" maxlength="50" value="<?php echo htmlspecialchars($userData['diachi'] ?? ''); ?>" readonly>
                        </div>
                    </div>

                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" id="btn-save" class="btn btn-update" style="display: none;">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        // Kịch bản cho menu xổ xuống
        document.addEventListener("DOMContentLoaded", function() {
            const nutUser = document.querySelector(".btn-user");
            const menuXoXuong = document.querySelector(".dropdown-content");
            if (nutUser && menuXoXuong) {
                nutUser.addEventListener("click", function(event) {
                    event.stopPropagation();
                    menuXoXuong.classList.toggle("show");
                });
                window.addEventListener("click", function(event) {
                    if (!menuXoXuong.contains(event.target) && !nutUser.contains(event.target)) {
                        menuXoXuong.classList.remove("show");
                    }
                });
            }
        });

        // Kịch bản mở khóa form khi bấm nút Chỉnh sửa
        document.querySelector('.btn-edit-profile').addEventListener('click', function(e) {
            e.preventDefault(); 
            const elements = document.querySelectorAll('#form-profile input, #form-profile select');
            elements.forEach(el => {
                if (el.tagName === 'SELECT') { el.removeAttribute('disabled'); } 
                else { el.removeAttribute('readonly'); }
                el.style.backgroundColor = '#fff'; 
                el.style.border = '1px solid #19a695'; 
            });
            document.getElementById('btn-save').style.display = 'inline-block';
            this.style.display = 'none';
        });

        // Chống bóng ma
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) window.location.reload(); 
        });
    </script>
</body>
</html>