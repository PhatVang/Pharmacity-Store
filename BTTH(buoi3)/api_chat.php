<?php
// Báo cho web biết file này trả về dữ liệu chuẩn JSON
header('Content-Type: application/json');

// Lấy tin nhắn (triệu chứng) từ JavaScript gửi lên
$data = json_decode(file_get_contents('php://input'), true);
$user_message = $data['message'] ?? '';

if (empty($user_message)) {
    echo json_encode(['reply' => 'Vui lòng mô tả triệu chứng của bạn!']);
    exit;
}

// KHÓA API CỦA SẾP
$api_key = 'AIzaSyCaKic4UczAhUzO_ljgwp40b0fx_dLl_p0'; 

// Dùng model mới nhất và nhẹ nhất của Google hiện tại
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;

// BƠM NHÂN CÁCH CHO AI
$system_prompt = "Bạn là một Dược sĩ tư vấn chuyên nghiệp, tận tâm đang làm việc tại nhà thuốc PharmaCity. Khi khách hàng kể bệnh, hãy chẩn đoán ngắn gọn, khuyên họ cách nghỉ ngơi và GỢI Ý MỘT VÀI LOẠI THUỐC hoặc THỰC PHẨM CHỨC NĂNG phù hợp. Luôn kết thúc bằng câu: 'Bạn có thể tìm mua các sản phẩm này tại mục Nhà thuốc của PharmaCity nhé. Chúc bạn mau khỏe!'. Hãy trả lời thật tự nhiên, ngắn gọn dưới 150 chữ, không dùng các ký tự markdown phức tạp.";

$full_prompt = $system_prompt . "\n\nTriệu chứng của khách: " . $user_message;

// Đóng gói dữ liệu gửi đi
$postData = [
    "contents" => [
        [
            "parts" => [
                ["text" => $full_prompt]
            ]
        ]
    ]
];

// Dùng cURL để gọi điện cho Google
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// TẮT KIỂM TRA BẢO MẬT CỦA XAMPP (Chỉ viết 1 lần thôi)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Nhận kết quả
$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

// ==========================================
// HỆ THỐNG BẮT LỖI TỰ ĐỘNG ĐỈNH CAO
// ==========================================

// Lỗi 1: XAMPP chặn không cho gửi
if ($response === false) {
    echo json_encode(['reply' => "LỖI XAMPP: " . $curl_error]);
    exit;
}

$json_response = json_decode($response, true);

// Lỗi 2: Google trả về lỗi (Sai key, sai model, hết tiền...)
if (isset($json_response['error'])) {
    echo json_encode(['reply' => "LỖI TỪ GOOGLE: " . $json_response['error']['message']]);
    exit;
}

// THÀNH CÔNG: Lấy câu trả lời
if (isset($json_response['candidates'][0]['content']['parts'][0]['text'])) {
    $ai_reply = $json_response['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['reply' => $ai_reply]);
} else {
    // Lỗi 3: AI ngáo, không thèm trả lời
    echo json_encode(['reply' => "LỖI KHÔNG XÁC ĐỊNH: Không đọc được dữ liệu từ AI."]);
}
?>