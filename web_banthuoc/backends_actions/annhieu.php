<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';
// Kiểm tra quyền
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: /web_banthuoc/user_display/index.php"); exit();
}

// Nếu có nhận được danh sách ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['sp_ids'])) {
    
    
    // Nối mảng ID thành chuỗi (VD: "1,2,5")
    $danh_sach_id = implode(",", $_POST['sp_ids']); 
    
    // Đổi trạng thái = 0 cho tất cả ID đó
    $sql = "UPDATE products SET trang_thai = 0 WHERE id IN ($danh_sach_id)";
    mysqli_query($conn, $sql);
}

header("Location: /web_banthuoc/Admin/admin.php");
exit();
?>