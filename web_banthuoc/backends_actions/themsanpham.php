<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';
// Kiểm tra quyền Admin
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: /web_banthuoc/user_display/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kết nối Database
    

    // Hứng dữ liệu từ form (Cột danh mục bây giờ nhận ID số)
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = (int)$_POST['category_id']; // Ép kiểu về số nguyên cho chắc chắn
    $price = (int)$_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $trang_thai = 1;
    
    // --- XỬ LÝ UPLOAD ẢNH (ĐÃ BẢO MẬT CHỐNG SHELL) ---
    $image_url = "";
    
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $file_name = $_FILES["image_file"]["name"];
        
        // 1. Lấy đuôi file và chuyển thành chữ thường (VD: Jpg -> jpg)
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // 2. Danh sách các đuôi file được phép (White-list)
        $allowed_exts = array("jpg", "jpeg", "png", "gif", "webp");
        
        // 3. Kiểm tra xem đuôi file có nằm trong danh sách an toàn không?
        if (in_array($file_ext, $allowed_exts)) {
            $thu_muc_luu_thuc_te = "../uploads/";
            $thu_muc_luu_db = "uploads/";
            
            if (!file_exists($thu_muc_luu_thuc_te)) {
                mkdir($thu_muc_luu_thuc_te, 0777, true);
            }
            
            $ten_file = time() . "_" . basename($file_name);
            $duong_dan_thuc_te = $thu_muc_luu_thuc_te . $ten_file;
            $duong_dan_db = $thu_muc_luu_db . $ten_file;
            
            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $duong_dan_thuc_te)) {
                $image_url = $duong_dan_db;
            }
        } else {
            // Nếu cố tình up file .php, .exe... thì chặn lại ngay!
            die("LỖI BẢO MẬT: Chỉ được phép tải lên hình ảnh (.jpg, .png, .jpeg)!");
        }
    }

    // BƯỚC THAY THẾ: Câu lệnh INSERT vào cấu trúc bảng mới
    $sql = "INSERT INTO products (name, category_id, price, description, image_url, trang_thai) 
            VALUES ('$name', $category_id, $price, '$description', '$image_url', $trang_thai)";
            
    if (mysqli_query($conn, $sql)) {
        header("Location: /web_banthuoc/Admin/admin.php?them=thanhcong");
        exit();
    } else {
        echo "Lỗi hệ thống: " . mysqli_error($conn);
    }
}
?>