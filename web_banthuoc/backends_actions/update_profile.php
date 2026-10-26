<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';
// 1. Nếu chưa đăng nhập thì đuổi về
if (!isset($_SESSION['ten_khach_hang'])) {
    header("Location: /web_banthuoc/public_auth/dangnhap.php");
    exit();
}

// 2. Nếu có người bấm nút gửi form (phương thức POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kết nối Database
    
    // Lấy tên người đang đăng nhập
    $ten_dang_nhap = $_SESSION['ten_khach_hang'];

    // Hứng dữ liệu từ form gửi sang
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $sodt = $_POST['sodt'];
    $ngaysinh = $_POST['ngaysinh'];
    $gioitinh = $_POST['gioitinh'];
    $diachi = $_POST['diachi'];

    // 3. Dùng Prepared Statement cho lệnh UPDATE (Khóa cửa tiêm mã độc)
    $sql = "UPDATE users SET fullname=?, email=?, sodt=?, ngaysinh=?, gioitinh=?, diachi=? WHERE username=?";
    $stmt = mysqli_prepare($conn, $sql);
    // Gắn 7 biến vào 7 dấu "?" ('sssssss' = 7 chuỗi String)
    mysqli_stmt_bind_param($stmt, "sssssss", $fullname, $email, $sodt, $ngaysinh, $gioitinh, $diachi, $ten_dang_nhap);
    // Bắt đầu thực thi câu lệnh 
    if (mysqli_stmt_execute($stmt)) {
        if (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1) {
            header("Location: /web_banthuoc/Admin/admin_account.php");
        } else {
            header("Location: /web_banthuoc/user_display/profile.php");
        }
        exit();
    } else {
        echo "Lỗi hệ thống! Không thể cập nhật.";
    }
    mysqli_stmt_close($stmt);
}
?>