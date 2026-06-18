<?php 
session_start(); 

// Chống bóng ma trình duyệt
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
$ten_dang_nhap = $_SESSION['ten_khach_hang'];

// Lôi danh sách đơn hàng của người này ra, cái nào mới mua (ngay_dat) thì đưa lên đầu (DESC)
$sql = "SELECT * FROM orders WHERE username = '$ten_dang_nhap' ORDER BY ngay_dat DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử mua hàng - PharmaCity</title>
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
                    <a href="profile.php"><i class="fa-regular fa-user"></i> Hồ sơ của tôi</a>
                    <a href="lichsumuahang.php" class="active"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                    <a href="dangxuat.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                </nav>
            </aside>

            <div class="profile-card">
                <div class="profile-header-box">
                    <h2>Lịch sử mua hàng</h2>
                </div>

                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <div style="overflow-x: auto;">
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
                                            <?php echo htmlspecialchars($row['fullname']); ?><br>
                                            <span style="font-size: 0.85rem; color: #6b7280;"><?php echo htmlspecialchars($row['diachi']); ?></span>
                                        </td>
                                        <td style="color: #059669; font-weight: bold;">
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
                    <div style="text-align: center; padding: 50px 0; color: #6b7280;">
                        <i class="fa-solid fa-box-open" style="font-size: 3rem; margin-bottom: 15px; color: #d1d5db;"></i>
                        <p>Bạn chưa có đơn hàng nào.</p>
                        <a href="haha.php" class="btn btn-primary" style="margin-top: 15px;">Đi mua sắm ngay</a>
                    </div>
                <?php } ?>

            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        // JS cho Menu xổ xuống
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

        // Chống bóng ma
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) window.location.reload(); 
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['dat_hang']) && $_GET['dat_hang'] == 'thanhcong'): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Lấy tên kho giỏ hàng của khách này
        const tenKhachHang = "<?php echo $_SESSION['ten_khach_hang']; ?>";
        const tenKhoGioHang = "gioHang_" + tenKhachHang;

        // 2. XÓA SẠCH GIỎ HÀNG sau khi đặt thành công
        localStorage.removeItem(tenKhoGioHang);

        // 3. Hiện thông báo chúc mừng mượt mà
        Swal.fire({
            title: "Tuyệt vời!",
            text: "Đơn hàng của Sếp đã được ghi nhận và đang chờ Admin duyệt nhé!",
            icon: "success",
            confirmButtonColor: "#059669",
            confirmButtonText: "Xem lịch sử đơn"
        }).then(() => {
            // Xóa cái chữ ?dat_hang=thanhcong trên thanh URL cho nó sạch đẹp
            window.history.replaceState(null, null, window.location.pathname);
            
            // Ép tải lại trang để số đếm giỏ hàng trên Header về 0
            window.location.reload();
        });
    });
</script>
<?php endif; ?>
</body>
</html>