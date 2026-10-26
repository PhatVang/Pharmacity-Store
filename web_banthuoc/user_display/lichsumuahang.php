<?php 
session_start(); 
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php'; 
// Chống bóng ma trình duyệt
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Bác bảo vệ: Khách chưa đăng nhập thì mời ra ngoài
if (!isset($_SESSION['ten_khach_hang'])) {
    header("Location: /web_banthuoc/public_auth/dangnhap.php");
    exit(); 
}

// Kết nối Database
$ten_dang_nhap = $_SESSION['ten_khach_hang'];

// Lôi danh sách đơn hàng của người này ra, cái nào mới mua (ngay_dat) thì đưa lên đầu (DESC)
$sql = "SELECT * FROM orders WHERE username = '$ten_dang_nhap' ORDER BY ngay_dat DESC";
$result = mysqli_query($conn, $sql);
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/header.php'; ?>

    <main class="container profile-main">
        <div class="profile-layout">
            
            <aside class="profile-card">
                <div class="profile-user-info">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($ten_dang_nhap, 0, 1)); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($ten_dang_nhap); ?></h3>
                    <p class="member-badge"><i class="fa-regular fa-circle-check"></i> Thành viên RaumaCity</p>
                </div>
                <nav class="profile-nav">
                    <a href="profile.php"><i class="fa-regular fa-user"></i> Hồ sơ của tôi</a>
                    <a href="lichsumuahang.php" class="active"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                    <a href="/web_banthuoc/public_auth/dangxuat.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
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
                        <a href="cuahang.php" class="btn btn-primary" style="margin-top: 15px;">Đi mua sắm ngay</a>
                    </div>
                <?php } ?>

            </div>
        </div>
    </main>

    <script src="/web_banthuoc/assets/script.js"></script>
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