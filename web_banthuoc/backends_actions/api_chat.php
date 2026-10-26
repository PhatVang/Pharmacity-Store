<?php
header('Content-Type: application/json');

// 1. GỌI KẾT NỐI DATABASE VÀ API KEY
require_once '../includes/config.php'; 

$data = json_decode(file_get_contents('php://input'), true);
$user_message = $data['message'] ?? '';

if (empty($user_message)) {
    echo json_encode(['reply' => 'Vui lòng mô tả triệu chứng của bạn!']);
    exit;
}

// Bắt lỗi 1: Không tìm thấy biến $conn
if (!isset($conn)) {
    echo json_encode(['reply' => 'LỖI CODE PHP: Không tìm thấy biến $conn. Hãy mở file config.php ra xem biến kết nối tên là gì rồi sửa lại!']);
    exit;
}

$api_key = $gemini_api_key; 
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;

// ==========================================
// 2. LẤY DANH SÁCH THUỐC TỪ DATABASE 
// ==========================================
$danh_sach_thuoc = "";

// Sửa lại đúng tên bảng products, cột name và description theo DB của đại ca
// Kèm theo điều kiện trang_thai = 1 để chỉ lấy thuốc đang bán
$sql = "SELECT name, description FROM products WHERE trang_thai = 1 LIMIT 50"; 

$result = @mysqli_query($conn, $sql); 

if (!$result) {
    $db_error = mysqli_error($conn);
    echo json_encode(['reply' => 'LỖI DATABASE: ' . $db_error]);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        // Nối chuỗi dùng đúng tên key: name và description
        $danh_sach_thuoc .= "- " . $row['name'] . " (Công dụng: " . $row['description'] . ")\n";
    }
} else {
    $danh_sach_thuoc = "Hiện tại nhà thuốc đang tạm hết hàng.";
}

// ==========================================
// 3. CẬP NHẬT LẠI PROMPT: ÉP AI DÙNG DATABASE
// ==========================================
$system_prompt = "Bạn là một Dược sĩ tư vấn chuyên nghiệp tại nhà thuốc RaumaCity. 
DƯỚI ĐÂY LÀ DANH SÁCH CÁC LOẠI THUỐC HIỆN CÓ TRONG KHO:
" . $danh_sach_thuoc . "

YÊU CẦU BẮT BUỘC: 
1. Chẩn đoán ngắn gọn triệu chứng.
2. NẾU GỢI Ý THUỐC, BẠN CHỈ ĐƯỢC PHÉP CHỌN CÁC LOẠI THUỐC TRONG DANH SÁCH TRÊN. Tuyệt đối không tự bịa ra thuốc ngoài danh sách. 
3. Nếu triệu chứng không phù hợp với bất kỳ thuốc nào trong kho, hãy khuyên họ đi khám bác sĩ.
4. Luôn kết thúc bằng câu: 'Bạn có thể tìm mua các sản phẩm này tại mục Nhà thuốc của RaumaCity nhé. Chúc bạn mau khỏe!'. 
Trả lời ngắn gọn dưới 150 chữ, không dùng markdown. Bạn hãy luôn nói rằng 'Tôi chỉ là mô hình AI, bạn hãy trao đổi với Bác sĩ thật qua sđt: 0865798772'.";

$full_prompt = $system_prompt . "\n\nTriệu chứng của khách: " . $user_message;

// 4. GỬI LÊN GOOGLE
$postData = [
    "contents" => [
        [
            "parts" => [
                ["text" => $full_prompt]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //Tắt SSL ở XAMPP

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo json_encode(['reply' => "LỖI: " . $curl_error]);
    exit;
}

$json_response = json_decode($response, true);

if (isset($json_response['candidates'][0]['content']['parts'][0]['text'])) {
    $ai_reply = $json_response['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['reply' => $ai_reply]);
} else {
    echo json_encode(['reply' => "LỖI KHÔNG XÁC ĐỊNH từ AI."]);
}
?>