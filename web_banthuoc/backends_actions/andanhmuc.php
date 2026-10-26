<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';

if (!isset($_SESSION['ten_khach_hang']) || $_SESSION['quyen_han'] == 0) {
    header("Location: /web_banthuoc/user_display/index.php");
    exit();
}

function dong_bo_xml($conn) {
    $xml_path = $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/danhmuc.xml';
    $sql_xml = "SELECT id, name FROM categories WHERE trang_thai = 1";
    $result_xml = mysqli_query($conn, $sql_xml);

    $xml_content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE categories SYSTEM \"danhmuc.dtd\">\n<categories>\n";
    while ($row = mysqli_fetch_assoc($result_xml)) {
        $xml_content .= "    <category>\n        <id>" . $row['id'] . "</id>\n        <name>" . htmlspecialchars($row['name']) . "</name>\n    </category>\n";
    }
    $xml_content .= "</categories>";
    file_put_contents($xml_path, $xml_content);
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // 1. Lấy trạng thái hiện tại
    $sql_get = "SELECT trang_thai FROM categories WHERE id = $id";
    $res = mysqli_query($conn, $sql_get);
    
    if ($row = mysqli_fetch_assoc($res)) {
        // Đảo ngược trạng thái: Nếu đang 1 (hiển thị) thì gán thành 0 (ẩn), và ngược lại
        $new_status = ($row['trang_thai'] == 1) ? 0 : 1;
        
        // 2. Chạy lệnh Update thay vì Delete
        $sql = "UPDATE categories SET trang_thai = $new_status WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            dong_bo_xml($conn); // ĐỒNG BỘ XML NGAY LẬP TỨC
            header("Location: /web_banthuoc/Admin/categories_manager.php?tb=capnhat_thanhcong");
            exit();
        }
    }
}

header("Location: /web_banthuoc/Admin/categories_manager.php");
exit();
?>