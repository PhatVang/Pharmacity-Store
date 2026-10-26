<?php
$link_cuahang  = isset($_SESSION['ten_khach_hang']) ? '/web_banthuoc/user_display/cuahang.php' : '/web_banthuoc/user_display/muahang.php';
// Tự động nhận diện đang ở trang nào để bôi màu xanh menu và ẩn/hiện nút Đăng nhập
$trang_hien_tai = basename($_SERVER['PHP_SELF']);

// ==========================================================
// 1. CODE MỚI THÊM: ĐẾM SỐ LƯỢNG SẢN PHẨM Sản phẩm yêu thích CỦA USER
// ==========================================================
$tong_tim = 0;
if (isset($_SESSION['ten_khach_hang']) && isset($conn)) {
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
    <title>RaumaCity</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/web_banthuoc/assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if(isset($thieu_thong_tin)): ?>
        <script> const isMissingInfo = <?php echo $thieu_thong_tin; ?>; </script>
    <?php endif; ?>
</head>
<body>

<header class="py-3 sticky-top bg-white shadow-sm" style="z-index: 1000;">
    <div class="container d-flex justify-content-between align-items-center">
        
        <?php
            // Đặt biến đường dẫn thông minh
            if (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1) {
                $link_trangchu = '/web_banthuoc/Admin/admin_index.php';
                $link_cuahang  = '/web_banthuoc/Admin/admin_cuahang.php';
            } else {
                $link_trangchu = isset($_SESSION['ten_khach_hang']) ? '/web_banthuoc/user_display/index.php' : '/web_banthuoc/trangchu.php';
                $link_cuahang  = isset($_SESSION['ten_khach_hang']) ? '/web_banthuoc/user_display/cuahang.php' : '/web_banthuoc/user_display/muahang.php';
            }
        ?>

        <div class="logo fs-4 fw-bold" style="min-width: 280px;">
            <a href="<?php echo $link_trangchu; ?>" class="text-decoration-none text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-heart-pulse text-success"></i> RaumaCity
            </a>    
        </div>
        <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle fw-semibold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #111827; transition: color 0.3s;">
        <i class="fa-solid fa-layer-group me-1" style="color: #059669;"></i> Danh mục sản phẩm
    </a>
    <ul class="dropdown-menu shadow-sm rounded-3 border-0 py-2" aria-labelledby="navbarDropdown" style="background-color: #ffffff;">
        
        <li>
            <a class="dropdown-item py-2 px-3 fw-medium" href="<?php echo $link_cuahang; ?>">
                <i class="fa-solid fa-border-all me-2 text-secondary"></i> Tất cả sản phẩm
            </a>
        </li>

        <?php if(isset($_SESSION['ten_khach_hang'])): ?>
            <li>
                <a class="dropdown-item py-2 px-3 fw-medium" href="<?php echo $link_cuahang; ?>?yeuthich=1">
                    <i class="fa-solid fa-heart me-2 text-danger"></i> Sản phẩm yêu thích
                </a>
            </li>
        <?php else: ?>
            <li>
                <a class="dropdown-item py-2 px-3 fw-medium btn-heart-dangnhap" href="#">
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
                echo '<a class="dropdown-item py-2 px-3 dynamic-xml-item" href="' . $link_cuahang . '?danhmuc=' . $id . '">';
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
        <nav class="nav-links d-flex gap-4">
            <a href="<?php echo $link_trangchu; ?>" class="text-decoration-none <?php echo ($trang_hien_tai == 'trangchu.php' || $trang_hien_tai == 'index.php') ? 'text-success fw-bold' : 'text-secondary'; ?> d-flex align-items-center gap-2">
                <i class="fa-solid fa-wave-square"></i> Trang chủ
            </a>
            <a href="<?php echo $link_cuahang; ?>" class="text-decoration-none <?php echo ($trang_hien_tai == 'muahang.php' || $trang_hien_tai == 'cuahang.php') ? 'text-success fw-bold' : 'text-secondary'; ?> d-flex align-items-center gap-2">
                <i class="fa-solid fa-bag-shopping"></i> Nhà thuốc
            </a>
        </nav>

        <div class="d-flex align-items-center justify-content-end gap-3" style="min-width: 280px;">
            <?php if(isset($_SESSION['ten_khach_hang'])): ?>
                

                <?php if($trang_hien_tai == 'cuahang.php' || $trang_hien_tai == 'muahang.php'): ?>
                    <button id="cart-btn" class="btn btn-light rounded-pill position-relative border border-light-subtle shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                        <i class="fa-solid fa-cart-shopping text-dark"></i>
                        <span class="fw-bold text-dark d-none d-sm-block">Giỏ hàng</span>
                        <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">0</span>
                    </button>
                <?php endif; ?>


                <div class="user-menu position-relative">
                    <?php if (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-success rounded-pill px-3 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-user-tie"></i> Xin chào ông chủ, <?php echo $_SESSION['ten_khach_hang']; ?>
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
                    <?php else: ?>
                        <button class="btn-user d-flex align-items-center gap-2 border-0 bg-transparent p-0">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px;">
                                <?php echo strtoupper(substr($_SESSION['ten_khach_hang'], 0, 1)); ?>
                            </div>
                            <span class="fw-bold d-none d-md-block text-dark"><?php echo $_SESSION['ten_khach_hang']; ?></span>
                            <i class="fa-solid fa-chevron-down text-secondary ms-1" style="font-size: 0.8rem;"></i>
                        </button>
                        
                        <div class="dropdown-content position-absolute end-0 mt-3 bg-white border border-light-subtle shadow-lg rounded-3 py-2" style="width: 220px; right: 0;">
                            <a href="/web_banthuoc/user_display/profile.php" class="d-block px-4 py-2 text-decoration-none text-dark"><i class="fa-solid fa-id-card me-2 text-secondary"></i> Hồ sơ của tôi</a>
                            <a href="/web_banthuoc/user_display/lichsumuahang.php" class="d-block px-4 py-2 text-decoration-none text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> Lịch sử mua hàng</a>
                            <hr class="my-2 border-light-subtle">
                            <a href="/web_banthuoc/public_auth/dangxuat.php" class="d-block px-4 py-2 text-decoration-none text-danger logout-text"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="auth-buttons d-flex gap-2">
                    <?php if ($trang_hien_tai != 'dangnhap.php'): ?>
                        <a href="/web_banthuoc/public_auth/dangnhap.php" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                        </a>
                    <?php endif; ?>

                    <?php if ($trang_hien_tai != 'dangky.php'): ?>
                        <a href="/web_banthuoc/public_auth/dangky.php" class="btn btn-success rounded-pill px-4">
                            <i class="fa-solid fa-user-plus"></i> Đăng ký
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div> 
</header>