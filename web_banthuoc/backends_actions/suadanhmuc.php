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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    
    if ($name == '' || $id <= 0) {
        header("Location: /web_banthuoc/Admin/categories_manager.php");
        exit();
    }
    
    $name_escaped = mysqli_real_escape_string($conn, $name);
    $sql_check = "SELECT id FROM categories WHERE LOWER(name) = LOWER('$name_escaped') AND id != $id";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        header("Location: /web_banthuoc/Admin/categories_manager.php?tb=trung_ten");
        exit();
    }
    
    $sql = "UPDATE categories SET name = '$name_escaped' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        dong_bo_xml($conn); // ĐỒNG BỘ XML NGAY LẬP TỨC
        header("Location: /web_banthuoc/Admin/categories_manager.php?tb=sua_thanhcong");
        exit();
    }
}
?>