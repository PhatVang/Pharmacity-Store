<?php 
session_start(); 
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';
// Chống bóng ma
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Nếu chưa có thẻ nhưng quyền hạn = 0 (là khách)
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    // Đuổi cổ ra ngoài trang chủ ngay lập tức!
    header("Location: /web_banthuoc/user_display/index.php");
    exit();
}

$ten_dang_nhap = $_SESSION['ten_khach_hang'];
// --- LOGIC LỌC VÀ TÌM KIẾM CỦA ADMIN ---
// 1. Khởi tạo câu lệnh gốc (Lấy hết, không phân biệt ẩn hiện)
$sql_products = "SELECT p.*, c.name AS category_name FROM products p INNER JOIN categories c ON p.category_id = c.id WHERE 1=1";

// 2. Nếu Sếp có gõ chữ vào ô tìm kiếm
if (isset($_GET['q']) && $_GET['q'] != '') {
    $tukhoa = mysqli_real_escape_string($conn, $_GET['q']);
    $sql_products .= " AND (p.name LIKE '%$tukhoa%' OR p.description LIKE '%$tukhoa%')";
}

// 3. Nếu Sếp có chọn danh mục
if (isset($_GET['danhmuc']) && $_GET['danhmuc'] != '') {
    $danhmuc_id = (int)$_GET['danhmuc'];
    $sql_products .= " AND p.category_id = $danhmuc_id";
}

// 4. Chốt lại bằng việc sắp xếp mới nhất lên đầu
$sql_products .= " ORDER BY p.id DESC";
$result_products = mysqli_query($conn, $sql_products);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/web_banthuoc/assets/style.css">
</head>
<body>

    <header>
        <div class="container navbar">
            <div class="logo">
                <a href="/web_banthuoc/Admin/admin_index.php"><i class="fa-solid fa-heart-pulse"></i> RaumaCity </a>
            </div>
            <nav class="nav-links">
                <a href="/web_banthuoc/Admin/admin_index.php"><i class="fa-solid fa-globe"></i> Xem trang web</a>
            </nav>
            <div class="auth-buttons">
                <div class="user-menu">
                    <button class="btn-user">
                        <i class="fa-solid fa-user-tie"></i> Xin chào ông chủ, <?php echo $ten_dang_nhap; ?> <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="/web_banthuoc/Admin/admin_account.php" class="active"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                        <a href="/web_banthuoc/Admin/admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
                        <a href="/web_banthuoc/Admin/admin_lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                        <a href="/web_banthuoc/Admin/admin.php"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                        <a href="/web_banthuoc/Admin/admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>
                        <a href="/web_banthuoc/Admin/categories_manager.php"><i class="fa-solid fa-list"></i> Quản lý danh mục</a>
                        <hr>
                        <a href="/web_banthuoc/public_auth/dangxuat.php" class="logout-text"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
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
                    <p class="member-badge badge-admin"><i class="fa-solid fa-star"></i> Chủ RaumaCity</p>
                </div>
                <nav class="profile-nav">
                    <a href="/web_banthuoc/Admin/admin_account.php"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                    <a href="/web_banthuoc/Admin/admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
                        <a href="/web_banthuoc/Admin/admin_lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                        <a href="/web_banthuoc/Admin/admin.php" class="active"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                        <a href="/web_banthuoc/Admin/admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>
                        <a href="/web_banthuoc/Admin/categories_manager.php"><i class="fa-solid fa-list"></i> Quản lý danh mục</a>
                        <hr>
                        <a href="/web_banthuoc/public_auth/dangxuat.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                </nav>
            </aside>

            <div class="profile-card">
                <div class="profile-header-box">
                    <h2>Kho sản phẩm</h2>
                    <button id="btn-add-product" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm sản phẩm</button>
                </div>

                <form action="/web_banthuoc/Admin/admin.php" method="GET" class="admin-toolbar position-relative">
                    <input type="text" id="o-tim-kiem" name="q" class="toolbar-input" placeholder="Tên sản phẩm..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <div id="khung-goi-y-admin" class="d-none position-absolute bg-white border rounded shadow-sm w-100" style="top: 100%; z-index: 1050; max-height: 300px; overflow-y: auto;"></div>
                    
                    <select name="danhmuc" class="toolbar-select" onchange="this.form.submit()">
                        <option value="">Chọn danh mục</option>
                        <?php
                        $dm_hientai_filter = isset($_GET['danhmuc']) ? $_GET['danhmuc'] : '';
                        $sql_dm_filter = "SELECT * FROM categories";
                        $kq_dm_filter = mysqli_query($conn, $sql_dm_filter);
                        if (mysqli_num_rows($kq_dm_filter) > 0) {
                            while($row_dm = mysqli_fetch_assoc($kq_dm_filter)) {
                                if (isset($row_dm['trang_thai']) && $row_dm['trang_thai'] == 0) continue;
                                $selected = ($dm_hientai_filter == $row_dm['id']) ? 'selected' : '';
                                echo '<option value="' . $row_dm['id'] . '" ' . $selected . '>' . htmlspecialchars($row_dm['name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    
                    <button type="submit" class="btn btn-search-admin"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <button type="button" class="btn btn-outline" onclick="window.location.href='admin.php'" title="Tải lại danh sách"><i class="fa-solid fa-rotate"></i></button>
                </form>

                <form action="/web_banthuoc/backends_actions/annhieu.php" method="POST">
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
                            <tbody id="ds-san-pham-body">
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
                                                    <img src="/web_banthuoc/<?php echo $row['image_url']; ?>" class="thumb-img">
                                                <?php } else { ?>
                                                    <i class="fa-solid fa-image thumb-icon"></i>
                                                <?php } ?>
                                            </div>
                                            <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
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
                                                        data-category="<?php echo htmlspecialchars($row['category_id']); ?>"
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

                    <div class="d-flex justify-content-end align-items-center gap-3 mt-4 mb-2">
    
    <button type="submit" class="btn btn-danger px-4 rounded-pill shadow-sm">
        <i class="fa-solid fa-eye-slash"></i> Ẩn các mục đã chọn
    </button>

    <a href="/web_banthuoc/backends_actions/export_excel.php" class="btn btn-success px-4 rounded-pill shadow-sm">
        <i class="fa-solid fa-file-excel"></i> Xuất Excel
    </a>

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
            
            <form action="/web_banthuoc/backends_actions/themsanpham.php" method="POST" class="modal-add-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Tên sản phẩm *</label>
                    <input type="text" name="name" required class="modal-input" placeholder="VD: Nước súc miệng" oninput="this.value = this.value.replace(/^-/, '')">
                </div>
                <div class="form-group">
                    <label>Danh mục *</label>
                    <select name="category_id" required class="modal-input">
                        <?php
                        $sql_dm_modal = "SELECT * FROM categories";
                        $kq_dm_modal = mysqli_query($conn, $sql_dm_modal);
                        while($row_dm = mysqli_fetch_assoc($kq_dm_modal)) {
                            if (isset($row_dm['trang_thai']) && $row_dm['trang_thai'] == 0) continue;
                            echo '<option value="' . $row_dm['id'] . '">' . htmlspecialchars($row_dm['name']) . '</option>';
                        }
                        ?>
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
                    <textarea name="description" class="modal-input" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3 rounded-pill py-2">Thêm ngay</button>
            </form>
        </div>
    </div>

    <!-- KHUNG SỬA SẢN PHẨM -->
    <div id="edit-product-modal" class="cart-modal">
        <div class="cart-content modal-add-content">
            <div class="cart-header">
                <h2><i class="fa-solid fa-pen-to-square"></i> Sửa sản phẩm</h2>
                <span id="close-edit-modal" class="close-btn">&times;</span>
            </div>
            <form action="/web_banthuoc/backends_actions/suasanpham.php" method="POST" class="modal-add-form" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-group">
                    <label>Tên sản phẩm *</label>
                    <input type="text" name="name" id="edit-name" required class="modal-input" oninput="this.value = this.value.replace(/^-/, '')">
                </div>
                <div class="form-group">
                    <label>Danh mục *</label>
                    <select name="category_id" id="edit-category" required class="modal-input">
                        <?php
                        $sql_dm_modal2 = "SELECT * FROM categories";
                        $kq_dm_modal2 = mysqli_query($conn, $sql_dm_modal2);
                        while($row_dm2 = mysqli_fetch_assoc($kq_dm_modal2)) {
                            if (isset($row_dm2['trang_thai']) && $row_dm2['trang_thai'] == 0) continue;
                            echo '<option value="' . $row_dm2['id'] . '">' . htmlspecialchars($row_dm2['name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Giá bán (VNĐ) *</label>
                    <input type="number" name="price" id="edit-price" required min="0" class="modal-input" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <div class="form-group">
                    <label>Ảnh sản phẩm mới (Để trống nếu không muốn đổi)</label>
                    <input type="file" name="image_file" accept="image/*" class="modal-input file-input-padded">
                </div>
                <div class="form-group">
                    <label>Mô tả sản phẩm</label>
                    <textarea name="description" id="edit-desc" class="modal-input" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3 rounded-pill py-2">Lưu cập nhật</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const btnAdd = document.getElementById('btn-add-product');
        const modalAdd = document.getElementById('add-product-modal');
        const closeAdd = document.getElementById('close-add-modal');

        if (btnAdd && modalAdd) {
            btnAdd.addEventListener('click', () => { modalAdd.style.display = 'block'; });
            closeAdd.addEventListener('click', () => { modalAdd.style.display = 'none'; });
            window.addEventListener('click', (e) => {
                if (e.target == modalAdd) modalAdd.style.display = 'none';
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

        document.getElementById('check-all').addEventListener('change', function() {
            document.querySelectorAll('.check-item').forEach(cb => cb.checked = this.checked);
        });

        const modalEdit = document.getElementById('edit-product-modal');
        const closeEdit = document.getElementById('close-edit-modal');

        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit-id').value = this.getAttribute('data-id');
                document.getElementById('edit-name').value = this.getAttribute('data-name');
                document.getElementById('edit-category').value = this.getAttribute('data-category');
                document.getElementById('edit-price').value = this.getAttribute('data-price');
                document.getElementById('edit-desc').value = this.getAttribute('data-desc');
                modalEdit.style.display = 'block';
            });
        });

        if (closeEdit) closeEdit.addEventListener('click', () => modalEdit.style.display = 'none');
        window.addEventListener('click', (e) => { if (e.target == modalEdit) modalEdit.style.display = 'none'; });
    </script>

    <script>
        document.querySelectorAll('.btn-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                if(confirm("Sếp có chắc chắn muốn thay đổi trạng thái hiển thị của sản phẩm này không?")) {
                    window.location.href = "/web_banthuoc/backends_actions/toggle_sanpham.php?id=" + productId;
                }
            });
        });
    </script>
    <script src="/web_banthuoc/assets/timkiem-goiy.js"></script>
</body>
</html>