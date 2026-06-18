<?php
session_start();
// Kiểm tra quyền
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: index.php"); exit();
}

// Nếu có nhận được danh sách ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['sp_ids'])) {
    $conn = mysqli_connect("localhost", "root", "", "pharmacity");
    
    // Nối mảng ID thành chuỗi (VD: "1,2,5")
    $danh_sach_id = implode(",", $_POST['sp_ids']); 
    
    // Đổi trạng thái = 0 cho tất cả ID đó
    $sql = "UPDATE products SET trang_thai = 0 WHERE id IN ($danh_sach_id)";
    mysqli_query($conn, $sql);
}

header("Location: admin.php");
exit();
?>