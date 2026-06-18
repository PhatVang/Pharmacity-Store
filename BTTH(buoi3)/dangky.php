
<?php
// 1. KẾT NỐI DATABASE
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "pharmacity";

$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

$thongbao = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // BƯỚC A: Kiểm tra xem tên đăng nhập đã có ai dùng chưa (Dùng Prepared Statement)
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    
    // Gắn biến $username vào dấu "?" (chữ 's' đại diện cho kiểu String)
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $thongbao = "<div class='alert alert-error'>Tên đăng nhập này đã có người sử dụng. Vui lòng chọn tên khác!</div>";
    } else {
        // BƯỚC B: Băm mật khẩu 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // BƯỚC C: Cất thông tin vào database (Dùng Prepared Statement)
        $insert_sql = "INSERT INTO users (fullname, username, password) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $insert_sql);
        
        // Gắn 3 biến vào 3 dấu "?" ('sss' nghĩa là cả 3 đều là String)
        mysqli_stmt_bind_param($stmt_insert, "sss", $fullname, $username, $hashed_password);
        
        if (mysqli_stmt_execute($stmt_insert)) {
            // Đăng ký xong là "bế" thẳng sang trang đăng nhập, gắn thêm cái mác "?dangky=thanhcong" trên URL để báo hiệu
            header("Location: dangnhap.php?dangky=thanhcong");
            exit();
        } else {
            // Ở môi trường thực tế, nên ghi lỗi vào file log thay vì in ra màn hình
            $thongbao = "<div class='alert alert-error'>Đã xảy ra lỗi hệ thống, vui lòng thử lại sau.</div>";
            // error_log("Lỗi đăng ký: " . mysqli_error($conn)); // Dùng cái này để lưu log ẩn
        }
        mysqli_stmt_close($stmt_insert);
    }
    mysqli_stmt_close($stmt_check);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - PharmaCity</title>
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
                <a href="dangnhap.php" class="btn btn-outline"><i class="fa-solid fa-right-to-bracket"></i> Đăng nhập</a>
            </div>
        </div>
    </header>

    <main class="auth-wrapper">
        <div class="auth-box">
            <h2>Tạo tài khoản mới</h2>
            
            <?php echo $thongbao; ?>

            <form action="dangky.php" method="POST">
                <div class="form-group">
                    <label for="fullname">Họ và tên</label>
                    <input type="text" id="fullname" name="fullname" placeholder="VD: Nguyễn Văn A" required>
                </div>

                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" maxlength="25" placeholder="Viết liền, không dấu (tối đa 25 kí tự)" required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password"  maxlength="10" placeholder="Nhập mật khẩu của bạn (8-10 kí tự)" required>
                </div>

                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="fa-solid fa-user-plus"></i> Đăng ký ngay
                </button>
            </form>
            
            <div class="auth-link-text">
                Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a>
            </div>
        </div>
    </main>

</body>
</html>