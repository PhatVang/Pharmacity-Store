<?php 
session_start(); 

// Chống bóng ma
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Kiểm tra quyền Admin (Cấm khách thường vào đây)
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

// Lôi danh sách đơn hàng CỦA ÔNG CHỦ ra, cái nào mới mua thì đưa lên đầu
$sql = "SELECT * FROM orders WHERE username = '$ten_dang_nhap' ORDER BY ngay_dat DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử mua hàng Admin - PharmaCity</title>
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
                        <a href="admin_lichsumuahang.php" class="active"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
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
                    <a href="admin_account.php"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                    <a href="admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
                    <a href="admin_lichsumuahang.php" class="active"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                    <a href="admin.php"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                    <a href="admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>                  
                    <hr><a href="dangxuat.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                </nav>
            </aside>

            <div class="profile-card">
                <div class="profile-header-box">
                    <h2>Lịch sử mua hàng của Sếp</h2>
                </div>

                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <div class="table-responsive">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Giao đến</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><strong>#<?php echo $row['id']; ?></strong></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['ngay_dat'])); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($row['fullname']); ?>
                                            <span class="order-address-detail"><?php echo htmlspecialchars($row['diachi']); ?></span>
                                        </td>
                                        <td class="order-price-highlight">
                                            <?php echo number_format($row['tong_tien'], 0, ',', '.'); ?> đ
                                        </td>
                                        <td>
                                            <span class="status-badge status-pending">
                                                <?php echo $row['trang_thai']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-box-open empty-state-icon"></i>
                        <p>Sếp chưa đặt đơn hàng nào để test hệ thống cả.</p>
                        <a href="admin_haha.php" class="btn btn-primary empty-state-btn">Đi đặt hàng ngay</a>
                    </div>
                <?php } ?>

            </div>
        </div>
    </main>

    <script src="script.js?v=999"></script>
    <script>
        // Kịch bản cho menu xổ xuống trên Header
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

        // Chống bóng ma khi xài nút Back của trình duyệt
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) window.location.reload(); 
        });
    </script>
</body>
</html>