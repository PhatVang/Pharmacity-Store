<?php
session_start();

// Kiểm tra quyền Admin
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kết nối Database
    $conn = mysqli_connect("localhost", "root", "", "pharmacity");

    // Hứng dữ liệu chữ từ form 
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = (int)$_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $trang_thai = 1;
    
    // --- XỬ LÝ UPLOAD ẢNH ---
    $image_url = ""; // Mặc định là rỗng nếu sếp không chọn ảnh
    
    // Kiểm tra xem sếp có chọn file không và file có bị lỗi không
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        // Tên thư mục sẽ chứa ảnh (Sẽ nằm cùng chỗ với các file php)
        $thu_muc_luu = "uploads/"; 
        
        // Nếu thư mục này chưa có thật trong máy, PHP sẽ tự động tạo ra nó!
        if (!file_exists($thu_muc_luu)) {
            mkdir($thu_muc_luu, 0777, true);
        }
        
        // Tạo một cái tên file mới (ghép thời gian hiện tại vào để 2 ảnh trùng tên không bị đè lên nhau)
        $ten_file = time() . "_" . basename($_FILES["image_file"]["name"]);
        $duong_dan_file = $thu_muc_luu . $ten_file;
        
        // Dời file từ bộ nhớ tạm của máy tính vào thư mục uploads/
        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $duong_dan_file)) {
            $image_url = $duong_dan_file; // Lấy cái đường dẫn này lưu vào Database
        }
    }

    // Nhét tất cả vào Database
    $sql = "INSERT INTO products (name, category, price, image_url, description, trang_thai) 
            VALUES ('$name', '$category', '$price', '$image_url', '$description', '$trang_thai')";
    
    if (mysqli_query($conn, $sql)) {
        // Lưu thành công thì quay lại trang Admin
        header("Location: admin.php");
        exit();
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>