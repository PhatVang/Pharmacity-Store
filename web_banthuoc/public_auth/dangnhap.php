<?php session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';
?>
<?php
// 1. KẾT NỐI DATABASE (Y chang trang đăng ký)
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';
$thongbao = "";

// 2. XỬ LÝ KHI BẤM NÚT ĐĂNG NHẬP
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password_nhap_vao = $_POST['password']; // Mật khẩu người dùng gõ vào (vd: 123456)

    // Dùng Prepared Statement để ép $username chỉ là chuỗi thuần túy
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);
    // Nếu tìm thấy 1 người có tên này
    if (mysqli_num_rows($check_result) == 1) {
        
        // Lôi toàn bộ thông tin người đó ra (thành một cái mảng)
        $row = mysqli_fetch_assoc($check_result);
        $mat_khau_trong_kho = $row['password']; // Đây là chuỗi đã băm ngoằn ngoèo

        // Bước B: Yêu cầu PHP đối chiếu mật khẩu nhập vào với chuỗi băm trong kho
        if (password_verify($password_nhap_vao, $mat_khau_trong_kho)) {
            $thongbao = "<div class='alert alert-success'>Đăng nhập thành công!</div>";
            $_SESSION['ten_khach_hang'] = $row['username'];
            $_SESSION['quyen_han'] = $row['quyen_han'];
            if ($_SESSION['quyen_han'] == 1) {
                header("Location: /web_banthuoc/Admin/admin_index.php");;
            } else {
                header("Location: /web_banthuoc/user_display/index.php");
            }
            exit();
        } else {
            $thongbao = "<div class='alert alert-error'>Tên đăng nhập hoặc mật khẩu không chính xác</div>";
        }

    } else {
        $thongbao = "<div class='alert alert-error'>Tên đăng nhập hoặc mật khẩu không chính xác</div>";
    }
    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - RaumaCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/web_banthuoc/assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
</head>
<body>

    <header class="py-3 sticky-top bg-white shadow-sm" style="z-index: 1000;">
        <div class="container d-flex justify-content-between align-items-center">
            
            <div class="logo fs-4 fw-bold">
                <a href="/web_banthuoc/trangchu.php" class="text-decoration-none text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-heart-pulse text-success"></i> RaumaCity
                </a>
            </div>

            <nav class="nav-links d-flex gap-4">
                <a href="/web_banthuoc/trangchu.php" class="text-decoration-none text-secondary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-wave-square"></i> Trang chủ
                </a>
                <a href="/web_banthuoc/user_display/muahang.php" class="text-decoration-none text-secondary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-bag-shopping"></i> Nhà thuốc
                </a>
            </nav>

            <div class="auth-buttons d-flex gap-2">
                <a href="/web_banthuoc/public_auth/dangky.php" class="btn btn-success rounded-pill px-4">
                    <i class="fa-solid fa-user-plus"></i> Đăng ký
                </a>
            </div>
        </div>
    </header>

    <main class="auth-wrapper">
        <div class="auth-box">
            <h2>Đăng nhập hệ thống</h2>
            
            <?php echo $thongbao; ?>

            <form action="/web_banthuoc/public_auth/dangnhap.php" method="POST">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>

                <button type="submit" class="btn btn-success w-100 rounded-pill py-2 mt-3 fw-bold fs-5">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                </button>
            </form>
            
            <div class="auth-link-text">
                Chưa có tài khoản? <a href="/web_banthuoc/public_auth/dangky.php">Đăng ký ngay</a>
            </div>
        </div>
    </main>

</body>
</html>