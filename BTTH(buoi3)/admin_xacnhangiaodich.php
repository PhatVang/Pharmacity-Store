<?php 
session_start(); 

// Chống bóng ma trình duyệt
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Bắt buộc đăng nhập
if (!isset($_SESSION['ten_khach_hang'])) {
    header("Location: dangnhap.php");
    exit(); 
}

$conn = mysqli_connect("localhost", "root", "", "pharmacity");
$ten_dang_nhap = $_SESSION['ten_khach_hang'];

// 1. XỬ LÝ KHI ADMIN BẤM NÚT DUYỆT HOẶC HỦY
$thong_bao = "";
if (isset($_POST['action']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    if ($action == 'duyet') {
        mysqli_query($conn, "UPDATE orders SET trang_thai = 'Đang giao hàng' WHERE id = '$order_id'");
        $thong_bao = "duyet_thanhcong";
    } elseif ($action == 'huy') {
        mysqli_query($conn, "UPDATE orders SET trang_thai = 'Đã hủy' WHERE id = '$order_id'");
        $thong_bao = "huy_thanhcong";
    }
}

// 2. Lấy TOÀN BỘ đơn hàng của tất cả khách hàng
$sql = "SELECT * FROM orders ORDER BY ngay_dat DESC, id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận giao dịch - Admin PharmaCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="hehe.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a href="admin.php"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                    <a href="admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>                 
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
                    <div class="profile-avatar avatar-admin"> 
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($ten_dang_nhap); ?></h3>
                    <p class="member-badge badge-admin"><i class="fa-solid fa-star"></i> Chủ PharmaCity</p>
                </div>
                <nav class="profile-nav">
                    <a href="admin_account.php"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                    <a href="admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
                    <a href="admin_lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                    <a href="admin.php"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                    <a href="admin_xacnhangiaodich.php" class="active"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>
                    <hr><a href="dangxuat.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                </nav>
            </aside>

            <div class="profile-card admin-content-card">
                <div class="profile-header-box">
                    <h2>Xác nhận giao dịch</h2>
                </div>

                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <div class="table-responsive">
                        <table class="order-table admin-transaction-table">
                            <thead>
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Giao đến</th>
                                    <th>Thanh toán</th>
                                    <th>Tổng tiền</th>
                                    <th>Ghi chú của khách</th>
                                    <th>Trạng thái</th>
                                    <th class="action-cell-min">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($result)) { 
                                    $badge_class = "status-pending";
                                    if ($row['trang_thai'] == 'Đang giao hàng') $badge_class = "status-approved";
                                    if ($row['trang_thai'] == 'Đã hủy') $badge_class = "status-canceled";
                                ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo $row['id']; ?></strong><br>
                                            <span class="admin-date-text"><?php echo date('d/m/Y H:i', strtotime($row['ngay_dat'])); ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['fullname']); ?></strong><br>
                                            <span class="admin-sub-text">SĐT: <?php echo htmlspecialchars($row['sodt']); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($row['phuong_thuc'] == 'Chuyển khoản'): ?>
                                                <span class="method-qr"><i class="fa-solid fa-qrcode"></i> CK</span>
                                            <?php else: ?>
                                                <span class="method-cod"><i class="fa-solid fa-money-bill"></i> Tiền mặt</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="price-text">
                                            <?php echo number_format($row['tong_tien'], 0, ',', '.'); ?> đ
                                        </td>
                                        <td>
                                        <?php 
                                        // Nếu khách có để lại ghi chú thì gọi class màu xanh nổi bật
                                        if (!empty($row['ghi_chu'])) {
                                            echo "<div class='admin-note-text'>";
                                            echo "<i class='fa-solid fa-comment-dots'></i> " . htmlspecialchars($row['ghi_chu']);
                                            echo "</div>";
                                        } else {
                                        // Nếu không có ghi chú thì gọi class chữ mờ
                                            echo "<span class='admin-note-empty'>Không có</span>";
                                        }
                                        ?>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $badge_class; ?>">
                                                <?php echo $row['trang_thai']; ?>
                                            </span>
                                        </td>
                                        <td class="action-cell-min">
                                            <?php if ($row['trang_thai'] == 'Chờ duyệt'): ?>
                                                <form method="POST" class="form-inline">
                                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="action" value="duyet">
                                                    <button type="submit" class="btn-sm btn-duyet" title="Duyệt đơn này"><i class="fa-solid fa-check"></i></button>
                                                </form>
                                                <form method="POST" class="form-inline">
                                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="action" value="huy">
                                                    <button type="submit" class="btn-sm btn-huy" title="Hủy đơn này" onclick="return confirm('Hủy đơn #<?php echo $row['id']; ?> thật không Sếp?');"><i class="fa-solid fa-xmark"></i></button>
                                                </form>
                                            <?php else: ?>
                                                <span class="locked-text"><i class="fa-solid fa-lock"></i> Đã chốt</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="empty-state-box">
                        <i class="fa-solid fa-file-invoice-dollar empty-state-icon"></i>
                        <p>Chưa có giao dịch nào cần xác nhận.</p>
                    </div>
                <?php } ?>

            </div>
        </div>
    </main>

    <script>
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

    <?php if ($thong_bao == "duyet_thanhcong"): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Thành công!",
                text: "Đã duyệt giao dịch. Khách hàng đang đợi shipper giao tới!",
                icon: "success",
                confirmButtonColor: "#059669",
                timer: 2000
            });
            window.history.replaceState(null, null, window.location.pathname);
        });
    </script>
    <?php elseif ($thong_bao == "huy_thanhcong"): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Đã hủy!",
                text: "Giao dịch đã bị hủy.",
                icon: "info",
                confirmButtonColor: "#dc2626",
                timer: 2000
            });
            window.history.replaceState(null, null, window.location.pathname);
        });
    </script>
    <?php endif; ?>

</body>
</html>