<?php 
session_start(); 
// Chống bóng ma
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Nếu chưa có thẻ nhưng quyền hạn = 0 (là khách)
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    // Đuổi cổ ra ngoài trang chủ ngay lập tức!
    header("Location: index.php");
    exit();
}
$conn = mysqli_connect("localhost", "root", "", "pharmacity");
$ten_dang_nhap = $_SESSION['ten_khach_hang'];
// --- LOGIC LỌC VÀ TÌM KIẾM CỦA ADMIN ---
// 1. Khởi tạo câu lệnh gốc (Lấy hết, không phân biệt ẩn hiện)
$sql_products = "SELECT * FROM products WHERE 1=1";

// 2. Nếu Sếp có gõ chữ vào ô tìm kiếm
if (isset($_GET['timkiem']) && $_GET['timkiem'] != '') {
    $tukhoa = mysqli_real_escape_string($conn, $_GET['timkiem']);
    $sql_products .= " AND (name LIKE '%$tukhoa%' OR description LIKE '%$tukhoa%')";
}

// 3. Nếu Sếp có chọn danh mục
if (isset($_GET['danhmuc']) && $_GET['danhmuc'] != '') {
    $danhmuc = mysqli_real_escape_string($conn, $_GET['danhmuc']);
    $sql_products .= " AND category = '$danhmuc'";
}

// 4. Chốt lại bằng việc sắp xếp mới nhất lên đầu
$sql_products .= " ORDER BY id DESC";
$result_products = mysqli_query($conn, $sql_products);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <a href="admin_account.php" class="active"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
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
                        <a href="admin.php"class="active"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                        <a href="admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>
                        <hr><a href="dangxuat.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                </nav>
            </aside>

            <div class="profile-card">
                <div class="profile-header-box">
                    <h2>Kho sản phẩm</h2>
                    <button id="btn-add-product" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm sản phẩm</button>
                </div>

                <form action="admin.php" method="GET" class="admin-toolbar">
                    <input type="text" name="timkiem" class="toolbar-input" placeholder="Tên sản phẩm..." value="<?php echo isset($_GET['timkiem']) ? htmlspecialchars($_GET['timkiem']) : ''; ?>">
                    
                    <select name="danhmuc" class="toolbar-select" onchange="this.form.submit()">
                        <option value="">Chọn danh mục</option>
                        <option value="Thuốc" <?php echo (isset($_GET['danhmuc']) && $_GET['danhmuc'] == 'Thuốc') ? 'selected' : ''; ?>>Thuốc</option>
                        <option value="Thực phẩm chức năng" <?php echo (isset($_GET['danhmuc']) && $_GET['danhmuc'] == 'Thực phẩm chức năng') ? 'selected' : ''; ?>>Thực phẩm chức năng</option>
                        <option value="Sơ cứu" <?php echo (isset($_GET['danhmuc']) && $_GET['danhmuc'] == 'Sơ cứu') ? 'selected' : ''; ?>>Sơ cứu</option>
                        <option value="Chăm sóc cá nhân" <?php echo (isset($_GET['danhmuc']) && $_GET['danhmuc'] == 'Chăm sóc cá nhân') ? 'selected' : ''; ?>>Chăm sóc cá nhân</option>
                    </select>
                    
                    <button type="submit" class="btn btn-search-admin"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <button type="button" class="btn btn-outline" onclick="window.location.href='admin.php'" title="Tải lại danh sách"><i class="fa-solid fa-rotate"></i></button>
                </form>
                <form action="annhieu.php" method="POST">
                    <div class="table-responsive">
                        <table class="order-table admin-product-table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="check-all"></th>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Giá bán</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stt = 1;
                                if (mysqli_num_rows($result_products) > 0) {
                                    while($row = mysqli_fetch_assoc($result_products)) { 
                                ?>
                                    <tr class="<?php echo ($row['trang_thai'] == 0) ? 'row-hidden' : ''; ?>">
                                        <td><input type="checkbox" class="check-item" name="sp_ids[]" value="<?php echo $row['id']; ?>"></td>
                                        
                                        <td><?php echo $stt++; ?></td>
                                        <td class="product-cell">
                                            <div class="product-thumb">
                                                <?php if($row['image_url'] != '') { ?>
                                                    <img src="<?php echo $row['image_url']; ?>" class="thumb-img">
                                                <?php } else { ?>
                                                    <i class="fa-solid fa-image thumb-icon"></i>
                                                <?php } ?>
                                            </div>
                                            <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                                        <td class="price-cell"><?php echo number_format($row['price'], 0, ',', '.'); ?> đ</td>
                                        <td>
                                            <?php if($row['trang_thai'] == 1) { ?>
                                                <span class="status-badge status-success"><i class="fa-solid fa-eye"></i> Đang bán</span>
                                            <?php } else { ?>
                                                <span class="status-badge status-hidden"><i class="fa-solid fa-eye-slash"></i> Đã ẩn</span>
                                            <?php } ?>          
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button" class="btn-action btn-edit" 
                                                        data-id="<?php echo $row['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                        data-category="<?php echo htmlspecialchars($row['category']); ?>"
                                                        data-price="<?php echo $row['price']; ?>"
                                                        data-desc="<?php echo htmlspecialchars($row['description']); ?>"
                                                        title="Sửa"><i class="fa-solid fa-pen-to-square"></i></button>
                                                
                                                <?php if($row['trang_thai'] == 1) { ?>
                                                    <button type="button" class="btn-action btn-hide btn-toggle" data-id="<?php echo $row['id']; ?>" title="Ẩn sản phẩm này"><i class="fa-solid fa-eye-slash"></i></button>
                                                <?php } else { ?>
                                                    <button type="button" class="btn-action btn-show btn-toggle" data-id="<?php echo $row['id']; ?>" title="Hiển thị lại"><i class="fa-solid fa-eye"></i></button>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                    } 
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>Kho hàng đang trống!</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="admin-footer-actions">
                        <button type="submit" class="btn btn-danger-action" onclick="return confirm('Sếp có chắc chắn muốn ẩn tất cả các mục đã chọn?');">
                            <i class="fa-solid fa-trash-can"></i> Ẩn các mục đã chọn
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </main>
     <div id="add-product-modal" class="cart-modal">         
        <div class="cart-content modal-add-content">
            <div class="cart-header">
                <h2><i class="fa-solid fa-plus-circle"></i> Thêm sản phẩm mới</h2>
                <span id="close-add-modal" class="close-btn">&times;</span>
            </div>
            
            <form action="themsanpham.php" method="POST" class="modal-add-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Tên sản phẩm *</label>
                    <input type="text" name="name" required class="modal-input" placeholder="VD: Nước súc miệng" oninput="this.value = this.value.replace(/^-/, '')">
                </div>
                <div class="form-group">
                    <label>Danh mục *</label>
                    <select name="category" required class="modal-input">
                        <option value="Thuốc">Thuốc</option>
                        <option value="Thực phẩm chức năng">Thực phẩm chức năng</option>
                        <option value="Sơ cứu">Sơ cứu</option>
                        <option value="Chăm sóc cá nhân">Chăm sóc cá nhân</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá bán (VNĐ) *</label>
                    <input type="number" name="price" required min="0" placeholder="VD: 35000" class="modal-input" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <div class="form-group">
                    <label>Ảnh sản phẩm</label>
                    <input type="file" name="image_file" accept="image/*" class="modal-input file-input-padded">
                </div>
                <div class="form-group">
                    <label>Mô tả sản phẩm</label>   
                    <textarea name="description" rows="3" class="modal-input"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-full-width">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu sản phẩm
                </button>
            </form>
        </div>
    </div>

    <div id="edit-product-modal" class="cart-modal"> 
        <div class="cart-content modal-add-content">
            <div class="cart-header">
                <h2><i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa sản phẩm</h2>
                <span id="close-edit-modal" class="close-btn">&times;</span>
            </div>
            
            <form action="suasanpham.php" method="POST" class="modal-add-form" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit-id">

                <div class="form-group">
                    <label>Tên sản phẩm *</label>
                    <input type="text" name="name" id="edit-name" required class="modal-input" oninput="this.value = this.value.replace(/^-/, '')">
                </div>
                <div class="form-group">
                    <label>Danh mục *</label>
                    <select name="category" id="edit-category" required class="modal-input">
                        <option value="Thuốc">Thuốc</option>
                        <option value="Thực phẩm chức năng">Thực phẩm chức năng</option>
                        <option value="Sơ cứu">Sơ cứu</option>
                        <option value="Chăm sóc cá nhân">Chăm sóc cá nhân</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá bán (VNĐ) *</label>
                    <input type="number" name="price" id="edit-price" required min="0" class="modal-input" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <div class="form-group">
                    <label>Ảnh sản phẩm (Để trống nếu muốn giữ nguyên ảnh cũ)</label>
                    <input type="file" name="image_file" accept="image/*" class="modal-input file-input-padded">
                </div>
                <div class="form-group">
                    <label>Mô tả sản phẩm</label>
                    <textarea name="description" id="edit-desc" rows="3" class="modal-input"></textarea>
                </div>
                <button type="submit" class="btn btn-update btn-full-width">
                    <i class="fa-solid fa-floppy-disk"></i> Cập nhật thay đổi
                </button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
   

    <script>
        // Kịch bản mở/đóng Popup Thêm Sản Phẩm
        const modalAdd = document.getElementById('add-product-modal');
        const btnAdd = document.getElementById('btn-add-product');
        const closeAdd = document.getElementById('close-add-modal');

        if (btnAdd && modalAdd) {
            // Bấm nút thì mở bảng
            btnAdd.addEventListener('click', () => {
                modalAdd.style.display = 'block';
            });
            // Bấm dấu X thì đóng bảng
            closeAdd.addEventListener('click', () => {
                modalAdd.style.display = 'none';
            });
            // Bấm ra ngoài rìa đen thì cũng đóng bảng
            window.addEventListener('click', (e) => {
                if (e.target == modalAdd) {
                    modalAdd.style.display = 'none';
                }
            });
        }
    </script>
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
        // JS cho nút Chọn Tất Cả
        document.getElementById('check-all').addEventListener('change', function() {
            document.querySelectorAll('.check-item').forEach(cb => cb.checked = this.checked);
        });
        // Kịch bản cho Popup Sửa Sản Phẩm
        const modalEdit = document.getElementById('edit-product-modal');
        const closeEdit = document.getElementById('close-edit-modal');

        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                // Đẩy dữ liệu từ nút bấm vào các ô input của Form Sửa
                document.getElementById('edit-id').value = this.getAttribute('data-id');
                document.getElementById('edit-name').value = this.getAttribute('data-name');
                document.getElementById('edit-category').value = this.getAttribute('data-category');
                document.getElementById('edit-price').value = this.getAttribute('data-price');
                document.getElementById('edit-desc').value = this.getAttribute('data-desc');

                modalEdit.style.display = 'block'; // Mở bảng
            });
        });

        if (closeEdit) closeEdit.addEventListener('click', () => modalEdit.style.display = 'none');
        window.addEventListener('click', (e) => { if (e.target == modalEdit) modalEdit.style.display = 'none'; });
    </script>
    <script>
        // Kịch bản cho nút Ẩn / Hiện 1 sản phẩm (Mắt nhắm/mở)
        document.querySelectorAll('.btn-toggle').forEach(button => {
            button.addEventListener('click', function() {
                // Lấy ID của sản phẩm đang bị bấm
                const productId = this.getAttribute('data-id');
                
                // Hỏi lại cho chắc ăn
                if(confirm("Sếp có chắc chắn muốn thay đổi trạng thái hiển thị của sản phẩm này không?")) {
                    // Chở ID sang file xử lý
                    window.location.href = "toggle_sanpham.php?id=" + productId;
                }
            });
        });
    </script>
</body>
</html>