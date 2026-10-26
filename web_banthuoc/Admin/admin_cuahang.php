<?php 
session_start(); 
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';
// 3 DÒNG NÀY ĐỂ CẤM TRÌNH DUYỆT LƯU CACHE (CHỐNG BẤM NÚT BACK)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Kiểm tra quyền Admin
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: /web_banthuoc/user_display/index.php");
    exit();
}

// Kết nối Database

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy thông tin Ông Chủ từ Database
$ten_dang_nhap = $_SESSION['ten_khach_hang'];
$sql = "SELECT * FROM users WHERE username = '$ten_dang_nhap'";
$result = mysqli_query($conn, $sql);
$userData = mysqli_fetch_assoc($result);

// ==========================================================
// THÊM ĐOẠN NÀY VÀO ĐỂ TÍNH TOÁN BIẾN $tong_tim CHO ADMIN
// ==========================================================
$tong_tim = 0;
if (isset($conn)) {
    $username_tim = $_SESSION['ten_khach_hang'];
    $sql_tim = "SELECT COUNT(pl.id) as total FROM user_favorites pl JOIN users u ON pl.user_id = u.id WHERE u.username = '$username_tim'";
    $res_tim = @mysqli_query($conn, $sql_tim);
    if ($res_tim && $row_tim = mysqli_fetch_assoc($res_tim)) {
        $tong_tim = $row_tim['total'];
    }
}
// ==========================================================

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}


// 2. LOGIC LỌC, TÌM KIẾM & PHÂN TRANG (PAGINATION)
// ==========================================
// BƯỚC 1: XÂY DỰNG BỘ LỌC ($where_clause)
// ==========================================
// Đã sửa lại thành WHERE trạng thái để lấy đúng hàng đang bán
$where_clause = "WHERE p.trang_thai = 1 AND c.trang_thai = 1"; 

if (isset($_GET['yeuthich']) && $_GET['yeuthich'] == '1') {
    $current_user_id = isset($userData['id']) ? $userData['id'] : 0; 
    $where_clause .= " AND p.id IN (SELECT product_id FROM user_favorites WHERE user_id = $current_user_id)";
} elseif (isset($_GET['timkiem']) && $_GET['timkiem'] != '') {
    $tukhoa = mysqli_real_escape_string($conn, $_GET['timkiem']);
    $where_clause .= " AND (p.name LIKE '%$tukhoa%' OR p.description LIKE '%$tukhoa%')";
} elseif (isset($_GET['danhmuc']) && $_GET['danhmuc'] != '') {
    $danhmuc_duoc_chon = (int)$_GET['danhmuc'];
    $where_clause .= " AND p.category_id = $danhmuc_duoc_chon";
}

// -- BƯỚC A: TÍNH TOÁN TỔNG SỐ TRANG --
$limit = 6; // CHỐT: Tối đa 6 sản phẩm 1 trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
if ($page < 1) $page = 1;

// ĐÃ SỬA LỖI: Xóa chữ WHERE cứng đi vì trong biến $where_clause đã có rồi!
$sql_count = "SELECT COUNT(*) AS total FROM products p INNER JOIN categories c ON p.category_id = c.id $where_clause";
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];

$total_pages = ceil($total_records / $limit);
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$offset = ($page - 1) * $limit; // Tính điểm bắt đầu cắt dữ liệu

// ==========================================
// BƯỚC 2: RÁP BỘ LỌC VÀO CÂU LỆNH SQL LẤY DỮ LIỆU
// ==========================================
$current_user_id = isset($userData['id']) ? $userData['id'] : 0;
$sql = "SELECT p.*, c.name AS category_name,
        (SELECT COUNT(*) FROM user_favorites pl WHERE pl.product_id = p.id) AS total_likes,
        (SELECT COUNT(*) FROM user_favorites pl WHERE pl.product_id = p.id AND pl.user_id = $current_user_id) AS is_user_liked
        FROM products p 
        INNER JOIN categories c ON p.category_id = c.id 
        $where_clause 
        LIMIT $limit OFFSET $offset"; // ĐÃ THÊM LẠI PHÂN TRANG (LIMIT, OFFSET)

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng thuốc - Admin RaumaCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/web_banthuoc/assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body>

    <header class="py-3 sticky-top bg-white shadow-sm" style="z-index: 1000;">
        <div class="container d-flex justify-content-between align-items-center">
            
            <div class="logo fs-4 fw-bold" style="min-width: 280px;">
                <a href="/web_banthuoc/Admin/admin_index.php" class="text-decoration-none text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-heart-pulse text-success"></i> RaumaCity
                </a>
            </div>
            
            <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle fw-semibold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #111827; transition: color 0.3s;">
        <i class="fa-solid fa-layer-group me-1" style="color: #059669;"></i> Danh mục sản phẩm
    </a>
    <ul class="dropdown-menu shadow-sm rounded-3 border-0 py-2" aria-labelledby="navbarDropdown" style="background-color: #ffffff;">
        
        <li>
            <a class="dropdown-item py-2 px-3 fw-medium" href="/web_banthuoc/Admin/admin_cuahang.php">
                <i class="fa-solid fa-border-all me-2 text-secondary"></i> Tất cả sản phẩm
            </a>
        </li>
        <?php if(isset($_SESSION['ten_khach_hang'])): ?>
            <li>
        <a class="dropdown-item py-2 px-3 fw-medium" href="/web_banthuoc/Admin/admin_cuahang.php?yeuthich=1">
            <i class="fa-solid fa-heart me-2 text-danger"></i> Sản phẩm yêu thích
        </a>
    </li>
        <?php else: ?>
           <li>
        <a class="dropdown-item py-2 px-3 fw-medium btn-yeucau-dangnhap" href="#">
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
                echo '<a class="dropdown-item py-2 px-3 dynamic-xml-item" href="/web_banthuoc/Admin/admin_cuahang.php?danhmuc=' . $id . '">';
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
            <nav class="d-flex gap-4">
                <a href="/web_banthuoc/Admin/admin_index.php" class="text-decoration-none text-secondary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-wave-square"></i> Trang chủ
                </a>
                <a href="/web_banthuoc/Admin/admin_cuahang.php" class="text-decoration-none text-success fw-bold d-flex align-items-center gap-2">
                    <i class="fa-solid fa-bag-shopping"></i> Nhà thuốc
                </a>
            </nav>

            <div class="d-flex gap-3 align-items-center justify-content-end" style="min-width: 280px;">
                <button id="cart-btn" class="btn btn-light rounded-pill position-relative border border-light-subtle shadow-sm d-flex align-items-center gap-2 px-3 py-2">
                        <i class="fa-solid fa-cart-shopping text-dark"></i>
                        <span class="fw-bold text-dark d-none d-sm-block">Giỏ hàng</span>
                        <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">0</span>
                    </button>

                <div class="dropdown">
                    <button class="btn btn-outline-success rounded-pill px-3 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-user-tie"></i> Xin chào ông chủ, <?php echo $ten_dang_nhap; ?>
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
            </div>
        </div>
    </header>

    <main class="container my-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h1 class="fw-bold fs-2 mb-2">Cửa hàng thuốc</h1>
                <p class="text-secondary mb-0">Duyệt qua danh mục các sản phẩm y tế đã được kiểm định của chúng tôi.</p>
            </div>
            <form action="/web_banthuoc/Admin/admin_cuahang.php" method="GET" class="position-relative" style="min-width: 320px;">
                <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
                <input type="text" id="o-tim-kiem" name="q" class="form-control rounded-pill ps-5 py-2 border-secondary-subtle focus-ring focus-ring-success" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <div id="khung-goi-y-admin" class="d-none position-absolute bg-white border rounded shadow-sm w-100" style="top: 100%; z-index: 1050; max-height: 300px; overflow-y: auto;"></div>
                <button type="submit" class="d-none"></button>
            </form>
        </div>

        <div class="row g-4">
            
            <aside class="col-lg-3">
                <div class="card border-light-subtle shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h3 class="fs-5 fw-bold mb-3"><i class="fa-solid fa-filter text-success me-2"></i> Danh mục</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-2 category-list">
                            <?php $dm_hientai = isset($_GET['danhmuc']) ? $_GET['danhmuc'] : ''; ?>
                            <a href="/web_banthuoc/Admin/admin_cuahang.php" class="category-item text-decoration-none px-3 py-2 rounded-3 <?php echo ($dm_hientai == '' && !isset($_GET['yeuthich'])) ? 'active' : ''; ?>">Tất cả</a>
                            <a href="/web_banthuoc/Admin/admin_cuahang.php?yeuthich=1" class="category-item text-decoration-none px-3 py-2 rounded-3 <?php echo (isset($_GET['yeuthich']) && $_GET['yeuthich'] == '1') ? 'active' : ''; ?>">Sản phẩm yêu thích</a>
                            <?php
                            $sql_danhmuc = "SELECT * FROM categories";
                            $result_danhmuc = mysqli_query($conn, $sql_danhmuc);
                            if (mysqli_num_rows($result_danhmuc) > 0) {
                                while($row_dm = mysqli_fetch_assoc($result_danhmuc)) {
                                    if (isset($row_dm['trang_thai']) && $row_dm['trang_thai'] == 0) {
                                        continue;
                                    }
                                    $id_dm = $row_dm['id'];
                                    $ten_dm = htmlspecialchars($row_dm['name']);
                                    $active_class = ($dm_hientai == $id_dm) ? 'active' : '';
                                    echo '<a href="/web_banthuoc/Admin/admin_cuahang.php?danhmuc=' . $id_dm . '" class="category-item text-decoration-none px-3 py-2 rounded-3 ' . $active_class . '">' . $ten_dm . '</a>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="col-lg-9">
                <div id="ds-san-pham-grid" data-mode="member" class="row g-4">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <div class="col-sm-6 col-md-4">
                                <div class="card h-100 border-light-subtle shadow-sm rounded-4 feature-card-hover overflow-hidden position-relative">
                                    <!-- Nút Thả Tim + Lượt thích CSDL desu~ -->
<button class="btn btn-light rounded-pill position-absolute top-0 end-0 m-2 shadow-sm text-secondary z-1 btn-heart px-2 py-1 d-flex align-items-center gap-1" data-product-id="<?php echo $row['id']; ?>" style="height: 35px;">
    <i class="<?php echo ($row['is_user_liked'] > 0) ? 'fa-solid' : 'fa-regular'; ?> fa-heart text-danger fs-5"></i>
    <span class="like-count fw-bold text-dark fs-6" id="like-count-<?php echo $row['id']; ?>"><?php echo $row['total_likes']; ?></span>
</button>

                                    <a href="/web_banthuoc/user_display/chitiet.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <?php if($row['image_url'] != "") { ?>
                                                <img src="/web_banthuoc/<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="w-100 h-100 object-fit-cover">
                                            <?php } else { ?>
                                                <span class="text-secondary fst-italic fs-6">Không có ảnh</span>
                                            <?php } ?>
                                        </div>
                                    </a>

                                    <div class="card-body d-flex flex-column p-4">
                                        <span class="text-success fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            <?php echo $row['category_name']; ?>
                                        </span>
                                        <a href="/web_banthuoc/user_display/chitiet.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark">
                                            <h3 class="card-title fw-bold fs-5 mb-2">
                                                <?php echo htmlspecialchars($row['name']); ?>
                                            </h3>
                                        </a>
                                        <p class="card-text text-secondary mb-4" style="font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?php echo $row['description']; ?>
                                        </p>
                                        
                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            <span class="fs-5 fw-bold text-dark">
                                                <?php echo number_format($row['price'], 0, ',', '.'); ?> đ
                                            </span>
                                            <button class="btn btn-success rounded-circle d-flex align-items-center justify-content-center btn-add-cart" 
                                                    style="width: 45px; height: 45px;"
                                                    data-name="<?php echo $row['name']; ?>" 
                                                    data-price="<?php echo $row['price']; ?>">
                                                <i class="fa-solid fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<div class='col-12 text-center py-5 text-secondary'>Hiện tại chưa có sản phẩm nào trong cửa hàng.</div>";
                    }
                    ?>
                </div>
                <?php if ($total_pages > 1): ?>
<div class="col-12 mt-5 d-flex justify-content-center">
    <nav aria-label="Page navigation">
        <ul class="pagination shadow-sm">
            <?php
            // Khúc này cực kỳ quan trọng: Giữ lại đuôi ?timkiem=... hoặc ?danhmuc=... khi khách bấm sang trang 2, trang 3
            $url_params = "";
            if(isset($_GET['q'])) $url_params .= "&q=".$_GET['q'];
            if(isset($_GET['danhmuc'])) $url_params .= "&danhmuc=".$_GET['danhmuc'];
            
            // Nút "Trang trước" (Ẩn đi nếu đang ở trang 1)
            if($page > 1) {
                echo '<li class="page-item"><a class="page-link text-success" href="?page='.($page - 1).$url_params.'"><i class="fa-solid fa-chevron-left"></i></a></li>';
            }

            // In ra các nút số trang 1, 2, 3, 4...
            for($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    // Trang hiện tại thì bôi nền xanh lá
                    echo '<li class="page-item active"><a class="page-link bg-success border-success text-white" href="#">'.$i.'</a></li>';
                } else {
                    // Trang khác thì để màu trắng chữ xanh
                    echo '<li class="page-item"><a class="page-link text-success" href="?page='.$i.$url_params.'">'.$i.'</a></li>';
                }
            }

            // Nút "Trang sau" (Ẩn đi nếu đang ở trang cuối cùng)
            if($page < $total_pages) {
                echo '<li class="page-item"><a class="page-link text-success" href="?page='.($page + 1).$url_params.'"><i class="fa-solid fa-chevron-right"></i></a></li>';
            }
            ?>
        </ul>
    </nav>
</div>
<?php endif; ?>
            </div>

        </div>
    </main>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/web_banthuoc/assets/timkiem-goiy.js"></script>

<!-- SCRIPT XỬ LÝ THẢ TIM REALTIME VỚI DATABASE -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const heartButtons = document.querySelectorAll('.btn-heart');
        heartButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                let productId = btn.getAttribute('data-product-id');
                let heartIcon = btn.querySelector('i');
                let countSpan = btn.querySelector('.like-count');

                fetch('/web_banthuoc/user_display/toggle_like.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(res => res.json())
                .then(data => {
                    // DÙNG ĐÚNG CHUẨN DATA.SUCCESS TỪ API CỦA BẠN ĐẠI CA
                    if (data.success) { 
                        // 1. Đổi màu trái tim tại chỗ
                        if (data.liked) {
                            heartIcon.classList.remove('fa-regular');
                            heartIcon.classList.add('fa-solid');
                        } else {
                            heartIcon.classList.remove('fa-solid');
                            heartIcon.classList.add('fa-regular');
                        }
                        
                        // 2. Cập nhật con số bên cạnh trái tim
                        if (countSpan) {
                            countSpan.textContent = data.total_likes;
                        }
                        
                        // 3. Cập nhật số đếm trên thanh Header (Bong bóng đỏ)
                        let headerHeart = document.getElementById('header-heart-count');
                        if (headerHeart) {
                            let currentCount = parseInt(headerHeart.textContent) || 0;
                            if (data.liked) {
                                headerHeart.textContent = currentCount + 1;
                            } else {
                                headerHeart.textContent = currentCount - 1 > 0 ? currentCount - 1 : 0;
                            }
                        }
                    } else {
                        // Nếu có lỗi thật từ server (ví dụ chưa đăng nhập)
                        Swal.fire('Thất bại', data.message || 'Lỗi không xác định', 'error');
                    }
                })
                .catch(err => console.error("Lỗi:", err));
            });
        });
    });
</script>
</body>
</html>