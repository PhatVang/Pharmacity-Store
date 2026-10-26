<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['ten_khach_hang'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập!']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ!']);
    exit();
}

$ten_dang_nhap = mysqli_real_escape_string($conn, $_SESSION['ten_khach_hang']);
$sql_user = "SELECT id FROM users WHERE username='$ten_dang_nhap'";
$res_user = mysqli_query($conn, $sql_user);
$user_id = mysqli_fetch_assoc($res_user)['id'];

$check_sql = "SELECT * FROM user_favorites WHERE user_id = $user_id AND product_id = $product_id";
$check_res = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_res) > 0) {
    mysqli_query($conn, "DELETE FROM user_favorites WHERE user_id = $user_id AND product_id = $product_id");
    $action = false; 
} else {
    mysqli_query($conn, "INSERT INTO user_favorites (user_id, product_id) VALUES ($user_id, $product_id)");
    $action = true; 
}

// Đếm lại tổng số tim
$count_sql = "SELECT COUNT(*) as total FROM user_favorites WHERE product_id = $product_id";
$count_res = mysqli_query($conn, $count_sql);
$total_likes = mysqli_fetch_assoc($count_res)['total'];

// Trả về đúng tên biến 'success' và 'liked'
echo json_encode([
    'success' => true,
    'liked' => $action,
    'total_likes' => $total_likes
]);
?>