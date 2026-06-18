<?php session_start(); ?>
<?php
// 1. KẾT NỐI DATABASE (Y chang trang đăng ký)
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "pharmacity";

$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);
$thongbao = "";

// 2. XỬ LÝ KHI BẤM NÚT ĐĂNG NHẬP
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password_nhap_vao = $_POST['password']; // Mật khẩu người dùng gõ vào (vd: 123456)

    // Bước A: Thần chú tìm xem CÓ AI TÊN NÀY TRONG KHO KHÔNG? (Chưa xét mật khẩu)
    $check_sql = "SELECT * FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_sql);

    // Nếu tìm thấy 1 người có tên này
    if (mysqli_num_rows($check_result) == 1) {
        
        // Lôi toàn bộ thông tin người đó ra (thành một cái mảng)
        $row = mysqli_fetch_assoc($check_result);
        $mat_khau_trong_kho = $row['password']; // Đây là chuỗi đã băm ngoằn ngoèo

        // Bước B: Yêu cầu PHP đối chiếu mật khẩu nhập vào với chuỗi băm trong kho
        if (password_verify($password_nhap_vao, $mat_khau_trong_kho)) {
            $thongbao = "<div class='alert alert-success'>Đăng nhập thành công!</div>";
            $_SESSION['ten_khach_hang'] = $row['username'];
            $_SESSION['quyen_han'] = $row['role'];
            if ($_SESSION['quyen_han'] == 1) {
                header("Location: admin_index.php"); 
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $thongbao = "<div class='alert alert-error'>Tên đăng nhập hoặc mật khẩu không chính xác</div>";
        }

    } else {
        $thongbao = "<div class='alert alert-error'>Tên đăng nhập hoặc mật khẩu không chính xác</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - PharmaCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="hehe.css">
</head>
<body>

    <header>
        <div class="container navbar">
            <div class="logo">
                <a href ="trangchu.php"><i class="fa-solid fa-heart-pulse"></i> PharmaCity </a>
            </div>
            <nav class="nav-links">
                <a href="trangchu.php"><i class="fa-solid fa-wave-square"></i> Trang chủ</a>
                <a href="muahang.php"><i class="fa-solid fa-bag-shopping"></i> Nhà thuốc</a>
            </nav>
            <div class="auth-buttons">
                <a href="dangky.php" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Đăng ký</a>
            </div>
        </div>
    </header>

    <main class="auth-wrapper">
        <div class="auth-box">
            <h2>Đăng nhập hệ thống</h2>
            
            <?php echo $thongbao; ?>

            <form action="dangnhap.php" method="POST">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>

                <button type="submit" class="btn btn-outline btn-submit">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                </button>
            </form>
            
            <div class="auth-link-text">
                Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a>
            </div>
        </div>
    </main>

</body>
</html>