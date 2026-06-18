<?php
session_start();

// BẢO VỆ: Khóa cửa ngõ sau, cấm khách thường vào đây bấm bậy!
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: index.php");
    exit();
}

// Nếu nhận được ID truyền sang từ JavaScript
if (isset($_GET['id'])) {
    $conn = mysqli_connect("localhost", "root", "", "pharmacity");
    $id = (int)$_GET['id']; // Ép kiểu về số nguyên cho an toàn

    // 1. Kiểm tra xem sản phẩm này đang ở trạng thái nào (1 là Hiện, 0 là Ẩn)
    $sql_check = "SELECT trang_thai FROM products WHERE id = $id";
    $result = mysqli_query($conn, $sql_check);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // 2. Đảo ngược trạng thái (Nếu đang 1 thì thành 0, đang 0 thì thành 1)
        $trang_thai_moi = ($row['trang_thai'] == 1) ? 0 : 1;
        
        // 3. Cập nhật vào Database
        $sql_update = "UPDATE products SET trang_thai = $trang_thai_moi WHERE id = $id";
        mysqli_query($conn, $sql_update);
    }
}

// Xong việc thì chở Sếp quay lại bảng điều khiển Admin
header("Location: admin.php");
exit();
?>