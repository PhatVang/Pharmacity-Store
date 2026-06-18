<?php
// 1. Khởi động máy đọc thẻ
session_start();

// 2. Xóa sạch mọi dữ liệu bên trong (Tên, quyền hạn...)
session_unset();

// 3. Tiêu hủy luôn 
session_destroy();

// 4. Bắt xe ôm chở khách về lại trang chủ (dành cho người chưa đăng nhập)
header("Location: trangchu.php");
exit();
?>