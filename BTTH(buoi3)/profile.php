<?php 
session_start(); 
// 3 DÒNG NÀY ĐỂ CẤM TRÌNH DUYỆT LƯU CACHE (CHỐNG BẤM NÚT BACK)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Bác bảo vệ: Khách chưa đăng nhập thì mời ra ngoài
if (!isset($_SESSION['ten_khach_hang'])) {
    header("Location: dangnhap.php");
    exit(); 
}
// Kết nối Database
$conn = mysqli_connect("localhost", "root", "", "pharmacity");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy tên đăng nhập từ Session
$ten_dang_nhap = $_SESSION['ten_khach_hang'];

// Lôi ĐÚNG thông tin của người đang đăng nhập ra
$sql = "SELECT * FROM users WHERE username = '$ten_dang_nhap'";
$result = mysqli_query($conn, $sql);
$userData = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ của tôi - PharmaCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="hehe.css">
</head>
<body>

    <header>
        <div class="container navbar">
            <div class="logo">
                <a href ="index.php"><i class="fa-solid fa-heart-pulse"></i> PharmaCity </a>
            </div>
            
            <nav class="nav-links">
                <a href="index.php"><i class="fa-solid fa-wave-square"></i> Trang chủ</a>
                <a href="haha.php"><i class="fa-solid fa-bag-shopping"></i> Nhà thuốc</a>
            </nav>
            
            <div class="auth-buttons">
                

                <div class="user-menu">
                    <button class="btn-user">
                        <i class="fa-solid fa-user-circle"></i> Xin chào, <?php echo $ten_dang_nhap; ?> <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="profile.php"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                        <a href="lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                        <hr>
                        <a href="dangxuat.php" class="logout-text"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container profile-main">
        <div class="profile-layout">
            
            <aside class="profile-card">
                <div class="profile-user-info">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($ten_dang_nhap, 0, 1)); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($ten_dang_nhap); ?></h3>
                    <p class="member-badge"><i class="fa-regular fa-circle-check"></i> Thành viên PharmaCity</p>
                </div>
                <nav class="profile-nav">
                    <a href="profile.php" class="active"><i class="fa-regular fa-user"></i> Hồ sơ của tôi</a>
                    <a href="lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                    <a href="trangchu.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
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
                        <button type="submit" id="btn-save" class="btn btn-primary" style="display: none;">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>

        </div>
    </main>

    <script src="script.js"></script>
    <script>
        // Copy lại JS mở menu xổ xuống ở đây cho gọn
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
    </script>
    <script>
        // Mở khóa form khi bấm nút Chỉnh sửa
        document.querySelector('.btn-edit-profile').addEventListener('click', function(e) {
            e.preventDefault(); 
            
            // 1. Tìm TẤT CẢ các ô input VÀ select
            const elements = document.querySelectorAll('#form-profile input, #form-profile select');
            
            elements.forEach(el => {
                // Nếu là hộp chọn (select) thì gỡ 'disabled', nếu là input thì gỡ 'readonly'
                if (el.tagName === 'SELECT') {
                    el.removeAttribute('disabled');
                } else {
                    el.removeAttribute('readonly');
                }
                
                // Đổi màu nền trắng và viền xanh báo hiệu đang được sửa
                el.style.backgroundColor = '#fff'; 
                el.style.border = '1px solid #19a695'; 
            });

            // 2. Hiện nút Lưu thay đổi, giấu nút Chỉnh sửa
            document.getElementById('btn-save').style.display = 'inline-block';
            this.style.display = 'none';
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
</body>
</html>