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
        // TRƯỜNG HỢP 1: NGƯỜI DÙNG GÕ TỪ KHÓA TÌM KIẾM
        $tukhoa = $_GET['timkiem'];
        $sql = "SELECT * FROM products WHERE name LIKE '%$tukhoa%' OR description LIKE '%$tukhoa%'";
        
    } elseif (isset($_GET['danhmuc']) && $_GET['danhmuc'] != '') {
        // TRƯỜNG HỢP 2: NGƯỜI DÙNG BẤM LỌC THEO DANH MỤC
        $danhmuc_duoc_chon = $_GET['danhmuc'];
        $sql = "SELECT * FROM products WHERE category = '$danhmuc_duoc_chon'";
        
    } else {
        // TRƯỜNG HỢP 3: KHÔNG TÌM, CŨNG KHÔNG LỌC -> LÔI HẾT RA
        $sql = "SELECT * FROM products";
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
                <a href ="trangchu.php"><i class="fa-solid fa-heart-pulse"></i> PharmaCity </a>
            </div>
            
            <nav class="nav-links">
                <a href="trangchu.php"><i class="fa-solid fa-wave-square"></i> Trang chủ</a>
                <a href="muahang.php"><i class="fa-solid fa-bag-shopping"></i> Nhà thuốc</a>
            </nav>
                       <div class="auth-buttons">

                <a href="dangnhap.php" class="btn btn-outline">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                </a>
                <a href="dangky.php" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Đăng ký
                </a>
            </div>
    </header>

    <main class="container">
        <div class="store-header">
            <div class="store-title">
                <h1>Cửa hàng thuốc</h1>
                <p>Duyệt qua danh mục các sản phẩm y tế đã được kiểm định của chúng tôi.</p>
            </div>
            <form action="muahang.php" method="GET" class="search-bar">
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
                    // Lấy danh mục hiện tại từ thanh URL
                    $dm_hientai = isset($_GET['danhmuc']) ? $_GET['danhmuc'] : '';
                    ?>
                    <a href="muahang.php" class="category-item <?php echo ($dm_hientai == '') ? 'active' : ''; ?>">Tất cả</a>
                    <a href="muahang.php?danhmuc=Thuốc" class="category-item <?php echo ($dm_hientai == 'Thuốc') ? 'active' : ''; ?>">Thuốc</a>
                    <a href="muahang.php?danhmuc=Thực phẩm chức năng" class="category-item <?php echo ($dm_hientai == 'Thực phẩm chức năng') ? 'active' : ''; ?>">Thực phẩm chức năng</a>
                    <a href="muahang.php?danhmuc=Sơ cứu" class="category-item <?php echo ($dm_hientai == 'Sơ cứu') ? 'active' : ''; ?>">Sơ cứu</a>
                    <a href="muahang.php?danhmuc=Chăm sóc cá nhân" class="category-item <?php echo ($dm_hientai == 'Chăm sóc cá nhân') ? 'active' : ''; ?>">Chăm sóc cá nhân</a>
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
            <button class="btn-add-cart btn-yeucau-dangnhap"><i class="fa-solid fa-cart-plus"></i></button>
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
                        <li><a href="muahang.php">Cửa hàng thuốc</a></li>
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

<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Tìm tất cả các nút mua hàng của khách
            const nutKhach = document.querySelectorAll(".btn-yeucau-dangnhap");
            
            nutKhach.forEach(button => {
                button.addEventListener("click", function() {
                    // Hiện bảng thông báo của SweetAlert2
                    Swal.fire({
                        title: "Bạn chưa đăng nhập!",
                        text: "Vui lòng đăng nhập hệ thống để có thể thêm sản phẩm vào giỏ hàng nhé.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#19a695", 
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Đến trang Đăng nhập",
                        cancelButtonText: "Để sau"
                    }).then((result) => {
                        // Nếu khách bấm nút Đăng nhập
                        if (result.isConfirmed) {
                            window.location.href = "dangnhap.php"; // Chở khách sang trang đăng nhập
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>