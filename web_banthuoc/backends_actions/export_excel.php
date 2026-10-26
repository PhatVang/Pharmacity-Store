<?php
// Bắt buộc gọi file config để kết nối Database
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';

// 1. GỬI LỆNH CHO TRÌNH DUYỆT BIẾT ĐÂY LÀ FILE EXCEL (CSV)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=DanhSachSanPham.csv');

// 2. MỞ LUỒNG XUẤT DỮ LIỆU
$output = fopen('php://output', 'w');

// 3. XUẤT MÃ BOM (CỰC KỲ QUAN TRỌNG)
// Thêm dòng này để Excel hiểu đây là file UTF-8, giúp tiếng Việt có dấu không bị lỗi font
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// 4. IN RA DÒNG TIÊU ĐỀ CỘT
fputcsv($output, array('ID Sản Phẩm', 'Tên Sản Phẩm', 'Danh Mục', 'Giá (VNĐ)', 'Trạng Thái'));

// 5. TRUY VẤN DỮ LIỆU (Dùng INNER JOIN anh em mình vừa học)
$sql = "SELECT p.id, p.name, c.name AS category_name, p.price, p.trang_thai 
        FROM products p 
        INNER JOIN categories c ON p.category_id = c.id";
$result = mysqli_query($conn, $sql);

// 6. ĐỔ DỮ LIỆU VÀO FILE TỪNG DÒNG MỘT
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Chuyển số 1/0 thành chữ cho dễ đọc trên báo cáo
        $trang_thai_chu = ($row['trang_thai'] == 1) ? 'Đang bán' : 'Ngừng bán';
        
        // Đưa từng hàng dữ liệu vào file
        fputcsv($output, array(
            $row['id'], 
            $row['name'], 
            $row['category_name'], 
            $row['price'], 
            $trang_thai_chu
        ));
    }
}

// 7. ĐÓNG LUỒNG VÀ KẾT THÚC
fclose($output);
exit();
?>