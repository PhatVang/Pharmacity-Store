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

// --- LOGIC LẤY DANH MỤC ---
$sql_categories = "SELECT * FROM categories ORDER BY id DESC";
$result_categories = mysqli_query($conn, $sql_categories);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <i class="fa-solid fa-user-tie"></i> Xin chào ông chủ, <?php echo htmlspecialchars($ten_dang_nhap); ?> <i class="fa-solid fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="/web_banthuoc/Admin/admin_account.php"><i class="fa-solid fa-id-card"></i> Hồ sơ của tôi</a>
                        <a href="/web_banthuoc/Admin/admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Bảng Thống Kê</a>
                        <a href="/web_banthuoc/Admin/admin_lichsumuahang.php"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử mua hàng</a>
                        <a href="/web_banthuoc/Admin/admin.php"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                        <a href="/web_banthuoc/Admin/admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>
                        <a href="/web_banthuoc/Admin/categories_manager.php" class="active"><i class="fa-solid fa-list"></i> Quản lý danh mục</a>
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
                    <a href="/web_banthuoc/Admin/admin.php"><i class="fa-solid fa-boxes-stacked"></i> Quản lý sản phẩm</a>
                    <a href="/web_banthuoc/Admin/admin_xacnhangiaodich.php"><i class="fa-solid fa-file-invoice-dollar"></i> Xác nhận giao dịch</a>
                    <a href="/web_banthuoc/Admin/categories_manager.php" class="active"><i class="fa-solid fa-list"></i> Quản lý danh mục</a>
                    <hr>
                    <a href="/web_banthuoc/public_auth/dangxuat.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                </nav>
            </aside>

            <div class="profile-card">
                <div class="profile-header-box">
                    <h2>Danh mục sản phẩm</h2>
                    <button id="btn-add-category" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm danh mục</button>
                </div>

                <div class="table-responsive mt-4">
                    <table class="order-table admin-product-table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên danh mục</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stt = 1;
                            if (mysqli_num_rows($result_categories) > 0) {
                                while($row = mysqli_fetch_assoc($result_categories)) { 
                            ?>
                                <tr class="<?php echo ($row['trang_thai'] == 0) ? 'row-hidden' : ''; ?>">
                                    <td><?php echo $stt++; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                    </td>
                                    <td>
                                        <?php if($row['trang_thai'] == 1) { ?>
                                            <span class="status-badge status-success"><i class="fa-solid fa-eye"></i> Hiện</span>
                                        <?php } else { ?>
                                            <span class="status-badge status-hidden"><i class="fa-solid fa-eye-slash"></i> Đã ẩn</span>
                                        <?php } ?>          
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn-action btn-edit" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                    title="Sửa"><i class="fa-solid fa-pen-to-square"></i></button>
                                            
                                            <button type="button" class="btn-action <?php echo ($row['trang_thai'] == 1) ? 'btn-hide' : 'btn-show'; ?>" 
                                                    onclick="window.location.href='/web_banthuoc/backends_actions/andanhmuc.php?id=<?php echo $row['id']; ?>'"
                                                    title="<?php echo ($row['trang_thai'] == 1) ? 'Ẩn danh mục này' : 'Hiển thị lại'; ?>">
                                                <i class="fa-solid <?php echo ($row['trang_thai'] == 1) ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                } 
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Chưa có danh mục nào!</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
            
        </div>
    </main>
    
    <!-- Modal Thêm Danh mục -->
    <div id="add-category-modal" class="cart-modal">         
        <div class="cart-content modal-add-content" style="max-width: 400px;">
            <div class="cart-header">
                <h2><i class="fa-solid fa-plus-circle"></i> Thêm danh mục</h2>
                <span id="close-add-modal" class="close-btn">&times;</span>
            </div>
            
            <form action="/web_banthuoc/backends_actions/themdanhmuc.php" method="POST" class="modal-add-form">
                <div class="form-group">
                    <label>Tên danh mục *</label>
                    <input type="text" name="name" required class="modal-input" placeholder="VD: Thuốc tây">
                </div>
                <button type="submit" class="btn btn-primary btn-full-width">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu danh mục
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Sửa Danh mục -->
    <div id="edit-category-modal" class="cart-modal"> 
        <div class="cart-content modal-add-content" style="max-width: 400px;">
            <div class="cart-header">
                <h2><i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa danh mục</h2>
                <span id="close-edit-modal" class="close-btn">&times;</span>
            </div>
            
            <form action="/web_banthuoc/backends_actions/suadanhmuc.php" method="POST" class="modal-add-form">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-group">
                    <label>Tên danh mục *</label>
                    <input type="text" name="name" id="edit-name" required class="modal-input">
                </div>
                <button type="submit" class="btn btn-update btn-full-width">
                    <i class="fa-solid fa-floppy-disk"></i> Cập nhật thay đổi
                </button>
            </form>
        </div>
    </div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Dropdown menu user
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

    // Modal logic
    const addModal = document.getElementById("add-category-modal");
    const editModal = document.getElementById("edit-category-modal");
    
    // Add logic
    const btnAdd = document.getElementById("btn-add-category");
    if (btnAdd) {
        btnAdd.addEventListener("click", function() {
            addModal.style.display = "flex";
        });
    }
    
    // Close Add Modal
    document.getElementById("close-add-modal").addEventListener("click", function() {
        addModal.style.display = "none";
    });
    
    // Edit logic
    const editButtons = document.querySelectorAll(".btn-edit");
    editButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            const id = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");
            
            document.getElementById("edit-id").value = id;
            document.getElementById("edit-name").value = name;
            
            editModal.style.display = "flex";
        });
    });
    
    // Close Edit Modal
    document.getElementById("close-edit-modal").addEventListener("click", function() {
        editModal.style.display = "none";
    });
    
    // Click outside to close
    window.addEventListener("click", function(event) {
        if (event.target === addModal) addModal.style.display = "none";
        if (event.target === editModal) editModal.style.display = "none";
    });
});
</script>

<?php if (isset($_GET['tb'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php if ($_GET['tb'] == "them_thanhcong"): ?>
            Swal.fire({ title: "Thành công!", text: "Đã thêm danh mục mới!", icon: "success", confirmButtonColor: "#059669", timer: 2000 });
        <?php elseif ($_GET['tb'] == "sua_thanhcong"): ?>
            Swal.fire({ title: "Thành công!", text: "Đã cập nhật danh mục!", icon: "success", confirmButtonColor: "#059669", timer: 2000 });
        <?php elseif ($_GET['tb'] == "capnhat_thanhcong"): ?>
            Swal.fire({ title: "Thành công!", text: "Trạng thái danh mục đã được cập nhật!", icon: "success", confirmButtonColor: "#059669", timer: 2000 });
        <?php elseif ($_GET['tb'] == "trung_ten"): ?>
            Swal.fire({ title: "Lỗi!", text: "Tên danh mục đã tồn tại!", icon: "error", confirmButtonColor: "#ef4444" });
        <?php endif; ?>
    });
</script>
<?php endif; ?>

</body>
</html>
