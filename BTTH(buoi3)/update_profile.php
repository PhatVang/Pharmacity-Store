<?php
session_start();

// 1. Nếu chưa đăng nhập thì đuổi về
if (!isset($_SESSION['ten_khach_hang'])) {
    header("Location: dangnhap.php");
    exit();
}

// 2. Nếu có người bấm nút gửi form (phương thức POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kết nối Database
    $conn = mysqli_connect("localhost", "root", "", "pharmacity");
    
    // Lấy tên người đang đăng nhập
    $ten_dang_nhap = $_SESSION['ten_khach_hang'];

    // Hứng dữ liệu từ form gửi sang
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $sodt = $_POST['sodt'];
    $ngaysinh = $_POST['ngaysinh'];
    $gioitinh = $_POST['gioitinh'];
    $diachi = $_POST['diachi'];

    // 3. Ra lệnh UPDATE (Cập nhật) vào đúng dòng của người đó
    $sql = "UPDATE users SET 
            fullname = '$fullname',
            email = '$email',
            sodt = '$sodt',
            ngaysinh = '$ngaysinh',
            gioitinh = '$gioitinh',
            diachi = '$diachi'
            WHERE username = '$ten_dang_nhap'";

    // Bắt đầu thực thi câu lệnh 
    if (mysqli_query($conn, $sql)) {
        // --- ĐOẠN CODE RẼ NHÁNH THÔNG MINH CỦA SẾP NẰM Ở ĐÂY ---
        // Nếu là Ông Chủ (Admin)
        if (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1) {
            header("Location: admin_account.php");
        } 
        // Nếu là Khách hàng bình thường (quyen_han = 0)
        else {
            header("Location: profile.php");
        }
        exit();
    } else {
        echo "Lỗi cập nhật: " . mysqli_error($conn);
    }
}
?>