<?php
// ===================== API GỢI Ý TÌM KIẾM SẢN PHẨM (DÙNG CHUNG) =====================
// File này thay thế cho 2 file cũ: timkiem_goiy.php (khách hàng) + timkiem_goiy_admin.php (admin).
//
// Cách hoạt động: mặc định API chạy ở chế độ "khách hàng" (chỉ trả sản phẩm đang bán,
// giới hạn 24 kết quả). Nếu gọi kèm tham số &pham_vi=admin, API sẽ chuyển sang chế độ
// "admin" (trả cả sản phẩm đã ẩn, không lọc theo danh mục đang xem, giới hạn 30 kết quả).
//
// QUAN TRỌNG VỀ BẢO MẬT: chế độ admin CHỈ được bật khi phiên đăng nhập hiện tại thực sự
// là admin (session), tuyệt đối không tin vào tham số &pham_vi=admin để tự ý cấp quyền
// - nếu không, ai đó chỉ cần gõ thẳng URL kèm tham số này là xem được cả hàng đã ẩn dù
// chưa đăng nhập. Vì vậy dù client có gửi &pham_vi=admin mà session không phải admin,
// API vẫn trả lời bằng chế độ khách hàng như bình thường (không báo lỗi, không rò rỉ gì thêm).
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

if (!$conn) {
    echo json_encode(['ok' => false, 'message' => 'Kết nối Database thất bại']);
    exit();
}

// Chỉ công nhận chế độ admin khi ĐÃ đăng nhập VÀ có quyền hạn (quyen_han != 0)
$muon_che_do_admin = isset($_GET['pham_vi']) && $_GET['pham_vi'] === 'admin';
$la_admin_that_su = isset($_SESSION['ten_khach_hang']) && isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] != 0;
$che_do_admin = $muon_che_do_admin && $la_admin_that_su;

$tukhoa = isset($_GET['q']) ? trim($_GET['q']) : '';

// Từ khoá quá ngắn thì khỏi truy vấn cho đỡ tốn, trả về mảng rỗng luôn
if (mb_strlen($tukhoa) < 1) {
    echo json_encode(['ok' => true, 'data' => []]);
    exit();
}

// Chuẩn hoá khoảng trắng thừa (gõ nhiều dấu cách liên tiếp) rồi tách thành từng từ
$tukhoa_chuan = preg_replace('/\s+/u', ' ', $tukhoa);
$tu_list = explode(' ', $tukhoa_chuan);
$tu_list = array_values(array_filter($tu_list, function ($t) {
    return mb_strlen($t) > 0;
}));

if (empty($tu_list)) {
    echo json_encode(['ok' => true, 'data' => []]);
    exit();
}

// Giới hạn số từ xét để tránh câu quá dài làm nặng truy vấn
$tu_list = array_slice($tu_list, 0, 8);

// Với MỖI từ trong từ khoá, TÊN sản phẩm phải khớp (chỉ tìm theo p.name, không xét mô tả)
// => nhiều từ thì bắt buộc khớp ĐỦ (AND giữa các từ), nhờ vậy gõ sai thứ tự từ vẫn tìm trúng,
// chính xác hơn hẳn so với so khớp nguyên cụm.
$dieu_kien = [];
$params = [];
$types = '';

foreach ($tu_list as $tu) {
    $dieu_kien[] = "p.name LIKE CONCAT('%', ?, '%')";
    $params[] = $tu;
    $types .= 's';
}

$where_tukhoa = implode(' AND ', $dieu_kien);

// Chế độ khách hàng: chỉ lấy sản phẩm đang bán (trang_thai = 1)
// Chế độ admin: cố tình KHÔNG lọc trang_thai (để thấy cả hàng đã ẩn),
// và cũng KHÔNG lọc theo danh mục đang chọn trên bộ lọc - gõ từ khoá là quét xuyên suốt TOÀN BỘ danh mục.
$dieu_kien_trang_thai = $che_do_admin ? '' : ' AND p.trang_thai = 1';

// Cụm từ khoá đầy đủ (không tách) dùng để tính điểm liên quan (relevance)
$tukhoa_full = $tukhoa_chuan;

// Điểm liên quan: khớp chính xác tên > tên bắt đầu bằng từ khoá > từ khoá nằm trong tên > chỉ khớp ở mô tả
// Admin cần thêm trang_thai + category_id để hiển thị nhãn "Đã ẩn" và các nút thao tác;
// khách hàng thì không cần nên khỏi lấy cho gọn.
$cot_du_lieu = $che_do_admin
    ? "p.id, p.name, p.price, p.image_url, p.description, p.trang_thai, p.category_id, c.name AS category_name"
    : "p.id, p.name, p.price, p.image_url, p.description, c.name AS category_name";

$thu_tu_sap_xep = $che_do_admin
    ? "diem_lien_quan ASC, p.id DESC"
    : "diem_lien_quan ASC, p.name ASC";

$gioi_han = $che_do_admin ? 30 : 24;

$sql = "SELECT $cot_du_lieu,
        (CASE
            WHEN p.name = ? THEN 1
            WHEN p.name LIKE CONCAT(?, '%') THEN 2
            WHEN p.name LIKE CONCAT('%', ?, '%') THEN 3
            ELSE 4
        END) AS diem_lien_quan
        FROM products p
        INNER JOIN categories c ON p.category_id = c.id
        WHERE ($where_tukhoa)$dieu_kien_trang_thai
        ORDER BY $thu_tu_sap_xep
        LIMIT $gioi_han";

$stmt = mysqli_prepare($conn, $sql);

$danh_sach = [];
if ($stmt) {
    // 3 tham số đầu cho phần tính điểm liên quan, sau đó tới các tham số theo từng từ
    $all_types = 'sss' . $types;
    $all_params = array_merge([$tukhoa_full, $tukhoa_full, $tukhoa_full], $params);

    $refs = [];
    $refs[] = &$all_types;
    foreach ($all_params as $key => $value) {
        $refs[] = &$all_params[$key];
    }
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $refs));

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sp = [
                'id'            => (int)$row['id'],
                'name'          => $row['name'],
                'price'         => (float)$row['price'],
                'price_format'  => number_format($row['price'], 0, ',', '.') . ' đ',
                'image_url'     => $row['image_url'],
                'description'   => $row['description'],
                'category_name' => $row['category_name'],
            ];

            if ($che_do_admin) {
                $sp['category_id'] = (int)$row['category_id'];
                $sp['trang_thai']  = (int)$row['trang_thai'];
            }

            $danh_sach[] = $sp;
        }
    }
    mysqli_stmt_close($stmt);
}

echo json_encode(['ok' => true, 'data' => $danh_sach]);
exit();
?>
