<?php
session_start();

// Kiểm tra quyền Admin
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: index.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "pharmacity");

    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = (int)$_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Kiểm tra xem Sếp có up ảnh mới không?
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $thu_muc_luu = "uploads/"; 
        if (!file_exists($thu_muc_luu)) mkdir($thu_muc_luu, 0777, true);
        
        $ten_file = time() . "_" . basename($_FILES["image_file"]["name"]);
        $duong_dan_file = $thu_muc_luu . $ten_file;
        
        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $duong_dan_file)) {
            // Lệnh UPDATE CÓ cập nhật đường dẫn ảnh mới
            $sql = "UPDATE products SET name='$name', category='$category', price=$price, description='$description', image_url='$duong_dan_file' WHERE id=$id";
        }
    } else {
        // Lệnh UPDATE KHÔNG đụng chạm gì tới ảnh cũ
        $sql = "UPDATE products SET name='$name', category='$category', price=$price, description='$description' WHERE id=$id";
    }

    mysqli_query($conn, $sql);
    header("Location: admin.php");
    exit();
}
?>