<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: /web_banthuoc/user_display/index.php");
    exit();
}

// Hàm tự động xuất dữ liệu ra file XML
function dong_bo_xml($conn) {
    $xml_path = $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/danhmuc.xml';
    // CHỈ LẤY CÁC DANH MỤC ĐANG HIỆN (trang_thai = 1)
    $sql_xml = "SELECT id, name FROM categories WHERE trang_thai = 1"; 
    $result_xml = mysqli_query($conn, $sql_xml);

    $xml_content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml_content .= "<!DOCTYPE categories SYSTEM \"danhmuc.dtd\">\n";
    $xml_content .= "<categories>\n";
    
    while ($row = mysqli_fetch_assoc($result_xml)) {
        $xml_content .= "    <category>\n";
        $xml_content .= "        <id>" . $row['id'] . "</id>\n";
        $xml_content .= "        <name>" . htmlspecialchars($row['name']) . "</name>\n";
        $xml_content .= "    </category>\n";
    }
    $xml_content .= "</categories>";
    file_put_contents($xml_path, $xml_content); // Ghi đè file XML
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    if ($name == '') {
        header("Location: /web_banthuoc/Admin/categories_manager.php");
        exit();
    }
    
    $name_escaped = mysqli_real_escape_string($conn, $name);
    $sql_check = "SELECT id FROM categories WHERE LOWER(name) = LOWER('$name_escaped')";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        header("Location: /web_banthuoc/Admin/categories_manager.php?tb=trung_ten");
        exit();
    }
    
    // Mặc định thêm mới là trang_thai = 1 (Hiện)
    $sql = "INSERT INTO categories (name, trang_thai) VALUES ('$name_escaped', 1)";
    if (mysqli_query($conn, $sql)) {
        dong_bo_xml($conn); // ĐỒNG BỘ XML NGAY LẬP TỨC
        header("Location: /web_banthuoc/Admin/categories_manager.php?tb=them_thanhcong");
        exit();
    }
}
?>