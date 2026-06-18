<?php 
session_start(); 
// 3 DÒNG NÀY ĐỂ CẤM TRÌNH DUYỆT LƯU CACHE (CHỐNG BẤM NÚT BACK)
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
<?php
// 1. KẾT NỐI DATABASE
$servername = "localhost";
$username = "root"; // Mặc định của XAMPP
$password = "";     // Mặc định của XAMPP không có mật khẩu
$dbname = "pharmacity";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}


// 2. LOGIC LỌC & TÌM KIẾM SẢN PHẨM Ở ĐÂY
    if (isset($_GET['timkiem']) && $_GET['timkiem'] != '') {
        // TRƯỜNG HỢP 1: TÌM KIẾM (Chỉ hiện hàng đang bán)
        $tukhoa = $_GET['timkiem'];
        // Nhớ bọc ngoặc đơn ( ) cho phần OR để logic đúng nhé Sếp
        $sql = "SELECT * FROM products WHERE (name LIKE '%$tukhoa%' OR description LIKE '%$tukhoa%') AND trang_thai = 1";
        
    } elseif (isset($_GET['danhmuc']) && $_GET['danhmuc'] != '') {
        // TRƯỜNG HỢP 2: LỌC DANH MỤC (Chỉ hiện hàng đang bán)
        $danhmuc_duoc_chon = $_GET['danhmuc'];
        $sql = "SELECT * FROM products WHERE category = '$danhmuc_duoc_chon' AND trang_thai = 1";
        
    } else {
        // TRƯỜNG HỢP 3: MẶC ĐỊNH (Chỉ hiện hàng đang bán)
        $sql = "SELECT * FROM products WHERE trang_thai = 1";
    }

    // Tiến hành chạy lệnh và lấy kết quả
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng thuốc - PharmaCity</title>
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
            <a href="admin_index.php"><i class="fa-solid fa-wave-square"></i> Trang chủ</a>         
            <a href="admin_haha.php" class="active"><i class="fa-solid fa-bag-shopping"></i> Nhà thuốc</a>
        </nav>           
            <div class="auth-buttons">
                <button id="cart-btn" class="btn btn-outline">
                    <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng
                    <span id="cart-count">0</span>
                </button>
                <div class="user-menu">
                    <button class="btn-user">
                        <i class="fa-solid fa-user-tie"></i> Xin chào ông chủ, <?php echo $ten_dang_nhap; ?> <i class="fa-solid fa-caret-down"></i>
                    </button>
                    
                    <div class="dropdown-content">
                        <a href="admin_account.php"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                        <a href="admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
                        <a href="admin_lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                        <a href="admin.php" class="active"><i class="fa-solid fa-boxes-stacked"></i> Quản lí sản phẩm</a>
                        <a href="admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>                        
                        <hr> 
                        <a href="dangxuat.php" class="logout-text"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                    </div>
                </div> 
            </div>            
        </div>
    </header>
    <main class="container">
        <div class="store-header">
            <div class="store-title">
                <h1>Cửa hàng thuốc</h1>
                <p>Duyệt qua danh mục các sản phẩm y tế đã được kiểm định của chúng tôi.</p>
            </div>
            <form action="admin_haha.php" method="GET" class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="timkiem" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['timkiem']) ? $_GET['timkiem'] : ''; ?>">
                <button type="submit" style="display: none;"></button>
            </form>
        </div>

        <div class="store-layout">
            <aside class="sidebar">
                <h3><i class="fa-solid fa-filter"></i> Danh mục</h3>
                <div class="category-list">
                    <?php
                    $dm_hientai = isset($_GET['danhmuc']) ? $_GET['danhmuc'] : '';
                    ?>
                    <a href="admin_haha.php" class="category-item <?php echo ($dm_hientai == '') ? 'active' : ''; ?>">Tất cả</a>
                    <a href="admin_haha.php?danhmuc=Thuốc" class="category-item <?php echo ($dm_hientai == 'Thuốc') ? 'active' : ''; ?>">Thuốc</a>
                    <a href="admin_haha.php?danhmuc=Thực phẩm chức năng" class="category-item <?php echo ($dm_hientai == 'Thực phẩm chức năng') ? 'active' : ''; ?>">Thực phẩm chức năng</a>
                    <a href="admin_haha.php?danhmuc=Sơ cứu" class="category-item <?php echo ($dm_hientai == 'Sơ cứu') ? 'active' : ''; ?>">Sơ cứu</a>
                    <a href="admin_haha.php?danhmuc=Chăm sóc cá nhân" class="category-item <?php echo ($dm_hientai == 'Chăm sóc cá nhân') ? 'active' : ''; ?>">Chăm sóc cá nhân</a>
                </div>
            </aside>
            <div class="product-grid">
                <?php
                // Nếu có sản phẩm trong kho thì bắt đầu vòng lặp   
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if($row['image_url'] != "") { ?>
                                    <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>">
                                <?php } else { ?>
                                    <span class="no-image-text">Không có ảnh</span>
                                <?php } ?>
                                <button class="btn-favorite"><i class="fa-regular fa-heart"></i></button>
                            </div>
                            <div class="product-info">
                                <span class="product-tag"><?php echo $row['category']; ?></span>
                                <h3 class="product-title"><?php echo $row['name']; ?></h3>
                                <p class="product-desc"><?php echo $row['description']; ?></p>
                                <div class="product-footer">
                                    <span class="product-price"><?php echo number_format($row['price'], 0, ',', '.'); ?> đ</span>
                                <button class="btn-add-cart" 
            data-name="<?php echo $row['name']; ?>" 
            data-price="<?php echo $row['price']; ?>">
        <i class="fa-solid fa-cart-plus"></i>
    </button>
                                </div>
                            </div>
                        </div>
                <?php
                    } // Kết thúc vòng lặp
                } else {
                    echo "<p>Hiện tại chưa có sản phẩm nào trong cửa hàng.</p>";
                }
                ?>
            </div>

        </div>
    </main>

   <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo">
                        <i class="fa-solid fa-heart-pulse"></i>
                        PharmaCity
                    </div>
                    <p>Nhà thuốc kỹ thuật số và người bạn đồng hành chăm sóc sức khỏe đáng tin cậy của bạn.</p>
                </div>
                
                <div class="footer-links">
                    <h4>Liên kết nhanh</h4>
                    <ul>
                        <li><a href="admin_haha.php">Cửa hàng thuốc</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Pháp lý</h4>
                    <ul>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Điều khoản dịch vụ</a></li>
                        <li><a href="#">Tuyên bố từ chối trách nhiệm y tế</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>© 2026 PharmaCity. Chỉ mang tính chất cung cấp thông tin.</p>
            </div>
        </div>
    </footer>
    <div id="cart-modal" class="cart-modal">
        <div class="cart-content">
            <div class="cart-header">
                <h2>Tóm tắt đơn hàng</h2>
                <span id="close-cart" class="close-btn">&times;</span>
            </div>
            
            <div id="cart-items" class="cart-items">
                </div>
            
            <div class="cart-footer">
                <div class="cart-total">
                    <span>Tổng cộng:</span>
                    <span id="cart-total-price">$0.00</span>
                </div>
                <button class="btn btn-primary btn-checkout">Đặt đơn</button>
            </div>
        </div>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const nutUser = document.querySelector(".btn-user");
    const menuXoXuong = document.querySelector(".dropdown-content");

    if (nutUser && menuXoXuong) {
        // 1. Khi bấm vào nút Tên người dùng
        nutUser.addEventListener("click", function(event) {
            event.stopPropagation(); // Ngăn chặn sự kiện click lan ra ngoài
            menuXoXuong.classList.toggle("show"); // Bật/tắt menu
        });

        // 2. Khi bấm ra chỗ khác trên màn hình thì tự đóng menu lại
        window.addEventListener("click", function(event) {
            if (!menuXoXuong.contains(event.target) && !nutUser.contains(event.target)) {
                menuXoXuong.classList.remove("show");
            }
        });
    }
});
</script>
<script>
        // Lấy tên khách hàng từ PHP gửi sang cho JavaScript
        const tenKhachHang = "<?php echo isset($_SESSION['ten_khach_hang']) ? $_SESSION['ten_khach_hang'] : 'Khach'; ?>";
        
        // Tạo ra cái tên kho riêng biệt. Ví dụ: PhongBeo đăng nhập thì kho tên là "gioHang_PhongBeo"
        const tenKhoGioHang = "gioHang_" + tenKhachHang; 
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
<script src="script.js?v=999"></script>
</body>
</html>