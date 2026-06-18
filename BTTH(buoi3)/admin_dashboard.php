<?php 
session_start(); 
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: index.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "pharmacity");
$ten_dang_nhap = $_SESSION['ten_khach_hang'];

// ==========================================
// 1. LẤY SỐ LIỆU TỔNG (Cho 4 cái Thẻ)
// ==========================================
$q_doanhthu = mysqli_query($conn, "SELECT SUM(tong_tien) AS total FROM orders WHERE trang_thai = 'Đang giao hàng'");
$doanhthu = mysqli_fetch_assoc($q_doanhthu)['total'] ?? 0;

$q_donhang = mysqli_query($conn, "SELECT COUNT(id) AS total FROM orders");
$donhang = mysqli_fetch_assoc($q_donhang)['total'] ?? 0;

$q_sanpham = mysqli_query($conn, "SELECT COUNT(id) AS total FROM products WHERE trang_thai = 1");
$sanpham = mysqli_fetch_assoc($q_sanpham)['total'] ?? 0;

$q_khachhang = mysqli_query($conn, "SELECT COUNT(id) AS total FROM users WHERE role = 0");
$khachhang = mysqli_fetch_assoc($q_khachhang)['total'] ?? 0;

// ==========================================
// 2. LẤY DỮ LIỆU VẼ BIỂU ĐỒ (Cho từng mục)
// ==========================================
// --- Biểu đồ 1: Doanh thu theo ngày (Line Chart) ---
$q_chart_dt = mysqli_query($conn, "SELECT DATE(ngay_dat) as date, SUM(tong_tien) as total FROM orders WHERE trang_thai != 'Đã hủy' GROUP BY DATE(ngay_dat) ORDER BY date ASC LIMIT 7");
$dt_labels = []; $dt_data = [];
while($row = mysqli_fetch_assoc($q_chart_dt)) {
    $dt_labels[] = date('d/m', strtotime($row['date']));
    $dt_data[] = $row['total'];
}

// --- Biểu đồ 2: Đơn hàng theo trạng thái (Doughnut Chart) ---
$q_chart_dh = mysqli_query($conn, "SELECT trang_thai, COUNT(id) as count FROM orders GROUP BY trang_thai");
$dh_labels = []; $dh_data = [];
while($row = mysqli_fetch_assoc($q_chart_dh)) {
    $dh_labels[] = $row['trang_thai'];
    $dh_data[] = $row['count'];
}

// --- Biểu đồ 3: Sản phẩm theo danh mục (Bar Chart) ---
$q_chart_sp = mysqli_query($conn, "SELECT category, COUNT(id) as count FROM products WHERE trang_thai = 1 GROUP BY category");
$sp_labels = []; $sp_data = [];
while($row = mysqli_fetch_assoc($q_chart_sp)) {
    $sp_labels[] = $row['category'];
    $sp_data[] = $row['count'];
}

// --- Biểu đồ 4: Khách hàng theo giới tính (Pie Chart) ---
$q_chart_kh = mysqli_query($conn, "SELECT gioitinh, COUNT(id) as count FROM users WHERE role = 0 GROUP BY gioitinh");
$kh_labels = []; $kh_data = [];
while($row = mysqli_fetch_assoc($q_chart_kh)) {
    $kh_labels[] = empty($row['gioitinh']) ? 'Chưa cập nhật' : $row['gioitinh'];
    $kh_data[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng thống kê - Admin PharmaCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="hehe.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <a href="admin_dashboard.php" class="active"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
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
                    <a href="admin_account.php"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                    <a href="admin_dashboard.php" class="active"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
                    <a href="admin_lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                    <a href="admin.php"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                    <a href="admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>
                    <hr><a href="dangxuat.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                </nav>
            </aside>

            <div class="admin-content-card">
                
                <div class="profile-header-box dash-header-spacing">
                    <h2>Tổng quan hệ thống</h2>
                    <p class="admin-sub-text">Cập nhật lúc: <?php echo date('H:i d/m/Y'); ?></p>
                </div>

                <div class="dashboard-grid">
                    <div class="dash-card active-card" id="card-dt" onclick="renderChart('dt')">
                        <div class="dash-icon icon-revenue"><i class="fa-solid fa-money-bill-trend-up"></i></div>
                        <div class="dash-info">
                            <h4>Doanh thu</h4>
                            <p><?php echo number_format($doanhthu, 0, ',', '.'); ?>đ</p>
                        </div>
                    </div>
                    
                    <div class="dash-card" id="card-dh" onclick="renderChart('dh')">
                        <div class="dash-icon icon-orders"><i class="fa-solid fa-cart-flatbed"></i></div>
                        <div class="dash-info">
                            <h4>Đơn hàng</h4>
                            <p><?php echo $donhang; ?> đơn</p>
                        </div>
                    </div>

                    <div class="dash-card" id="card-sp" onclick="renderChart('sp')">
                        <div class="dash-icon icon-products"><i class="fa-solid fa-pills"></i></div>
                        <div class="dash-info">
                            <h4>Sản phẩm bán</h4>
                            <p><?php echo $sanpham; ?> loại</p>
                        </div>
                    </div>

                    <div class="dash-card" id="card-kh" onclick="renderChart('kh')">
                        <div class="dash-icon icon-users"><i class="fa-solid fa-users"></i></div>
                        <div class="dash-info">
                            <h4>Khách hàng</h4>
                            <p><?php echo $khachhang; ?> người</p>
                        </div>
                    </div>
                </div>

                <div class="chart-container">
                    <h3 class="chart-title" id="main-chart-title"><i class="fa-solid fa-chart-column"></i> Thống kê Doanh thu 7 ngày gần nhất</h3>
                    <div style="height: 350px;">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>

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

    <script>
        const ctx = document.getElementById('myChart').getContext('2d');
        let myChartInstance = null; // Biến lưu trữ biểu đồ hiện tại

        // Đổ dữ liệu từ PHP sang JS
        const chartData = {
            'dt': {
                title: '<i class="fa-solid fa-money-bill-trend-up"></i> Thống kê Doanh thu những ngày có đơn',
                type: 'line',
                labels: <?php echo json_encode($dt_labels); ?>,
                data: <?php echo json_encode($dt_data); ?>,
                colors: ['rgba(21, 128, 61, 0.8)'], // Xanh lá đậm
                border: 'rgb(21, 128, 61)'
            },
            'dh': {
                title: '<i class="fa-solid fa-cart-flatbed"></i> Phân bổ Trạng thái Đơn hàng',
                type: 'doughnut', // Bánh vòng
                labels: <?php echo json_encode($dh_labels); ?>,
                data: <?php echo json_encode($dh_data); ?>,
                colors: ['#fef08a', '#dcfce3', '#fecaca', '#bfdbfe'], // Vàng, Xanh lá, Đỏ, Xanh dương
                border: '#ffffff'
            },
            'sp': {
                title: '<i class="fa-solid fa-pills"></i> Thống kê Sản phẩm theo Danh mục',
                type: 'bar', // Cột
                labels: <?php echo json_encode($sp_labels); ?>,
                data: <?php echo json_encode($sp_data); ?>,
                colors: ['rgba(25, 166, 149, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(139, 92, 246, 0.8)'],
                border: ['rgb(25, 166, 149)', 'rgb(245, 158, 11)', 'rgb(59, 130, 246)', 'rgb(139, 92, 246)']
            },
            'kh': {
                title: '<i class="fa-solid fa-users"></i> Phân loại Khách hàng theo Giới tính',
                type: 'pie', // Tròn
                labels: <?php echo json_encode($kh_labels); ?>,
                data: <?php echo json_encode($kh_data); ?>,
                colors: ['#f472b6', '#60a5fa', '#a78bfa', '#9ca3af'], // Hồng, Xanh bơ, Tím, Xám
                border: '#ffffff'
            }
        };

        // Hàm vẽ lại biểu đồ
        function renderChart(typeKey) {
            const config = chartData[typeKey];

            // 1. Đổi màu hiệu ứng cho cái Thẻ (Card) đang bấm
            document.querySelectorAll('.dash-card').forEach(card => card.classList.remove('active-card'));
            document.getElementById('card-' + typeKey).classList.add('active-card');

            // 2. Đổi tiêu đề biểu đồ
            document.getElementById('main-chart-title').innerHTML = config.title;

            // 3. Xóa biểu đồ cũ (nếu có) để nhường chỗ cho cái mới
            if (myChartInstance != null) {
                myChartInstance.destroy();
            }

            // 4. Vẽ biểu đồ mới
            myChartInstance = new Chart(ctx, {
                type: config.type,
                data: {
                    labels: config.labels,
                    datasets: [{
                        label: (typeKey === 'dt') ? 'Doanh thu (VNĐ)' : 'Số lượng',
                        data: config.data,
                        backgroundColor: config.colors,
                        borderColor: config.border,
                        borderWidth: 2,
                        borderRadius: (config.type === 'bar') ? 6 : 0, // Bo góc cho cột
                        tension: 0.3, // Làm cong đường line cho mượt
                        fill: (config.type === 'line') ? true : false, // Tô nền cho line chart
                        backgroundColor: (config.type === 'line') ? 'rgba(21, 128, 61, 0.1)' : config.colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        // Chỉ hiện chú thích màu sắc cho biểu đồ tròn/bánh
                        legend: { display: (config.type === 'pie' || config.type === 'doughnut') }
                    },
                    scales: (config.type === 'bar' || config.type === 'line') ? {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    } : {} // Tắt trục X Y nếu là biểu đồ tròn
                }
            });
        }

        // Vừa vào trang là tự động gọi vẽ biểu đồ Doanh thu (dt) làm mặc định
        window.onload = function() {
            renderChart('dt');
        };
    </script>

</body>
</html>