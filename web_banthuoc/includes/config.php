<?php
// Thông tin đăng nhập Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharmacity";

// Tạo kết nối
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối Database thất bại: " . mysqli_connect_error());
}

// Thiết lập font chữ tiếng Việt cho Database
mysqli_set_charset($conn, "utf8mb4");
// KHÓA API CỦA SẾP - Giấu ở đây cho an toàn
$gemini_api_key = 'AQ.Ab8RN6LjW4K1q7mdrAy2Ti3QRHm1DIGcXF_WGwx6YPtuyMgyjQ';
?>
