<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Kết nối database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pharmacity";
$conn = mysqli_connect($servername, $username, $password, $dbname);

// ==============================================================
// [KHU VỰC API ẨN] - XỬ LÝ THẢ TIM VÀ BÌNH LUẬN KHÔNG CẦN FILE MỚI
// ==============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (!isset($_SESSION['ten_khach_hang'])) {
        echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập!']);
        exit;
    }

    $ten_dang_nhap = mysqli_real_escape_string($conn, $_SESSION['ten_khach_hang']);
    $user_query = mysqli_query($conn, "SELECT id FROM users WHERE username = '$ten_dang_nhap'");
    $user_id = mysqli_fetch_assoc($user_query)['id'];

    $action = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($product_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ!']);
        exit;
    }

    // Xử lý Thả tim / Bỏ tim
    if ($action === 'toggle_favorite') {
        $fav_check = mysqli_query($conn, "SELECT id FROM user_favorites WHERE user_id = $user_id AND product_id = $product_id");
        if (mysqli_num_rows($fav_check) > 0) {
            mysqli_query($conn, "DELETE FROM user_favorites WHERE user_id = $user_id AND product_id = $product_id");
            echo json_encode(['status' => 'removed', 'message' => 'Đã bỏ thích sản phẩm!']);
        } else {
            mysqli_query($conn, "INSERT INTO user_favorites (user_id, product_id) VALUES ($user_id, $product_id)");
            echo json_encode(['status' => 'added', 'message' => 'Đã thêm vào mục yêu thích!']);
        }
        exit;
    }

    // Xử lý Lưu Bình Luận & Đánh Giá
    if ($action === 'submit_review') {
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = mysqli_real_escape_string($conn, trim($_POST['comment'] ?? ''));

        if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
            $sql_rev = "INSERT INTO product_reviews (product_id, user_id, rating, comment) VALUES ($product_id, $user_id, $rating, '$comment')";
            if (mysqli_query($conn, $sql_rev)) {
                echo json_encode(['status' => 'success', 'message' => 'Cảm ơn bạn đã đánh giá!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi lưu dữ liệu.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đủ thông tin.']);
        }
        exit;
    }
    exit;
}
// ==============================================================
// KẾT THÚC KHU VỰC API 
// ==============================================================

// 2. Kiểm tra và lấy ID từ URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_sanpham = (int)$_GET['id'];

    // Lấy thông tin sản phẩm
    $sql = "SELECT p.*, c.name AS category_name, c.trang_thai AS category_trang_thai
            FROM products p
            INNER JOIN categories c ON p.category_id = c.id
            WHERE p.id = $id_sanpham";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        // Kiểm tra trạng thái ẩn/hiện
        $is_admin = isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1;
        if (($product['trang_thai'] == 0 || $product['category_trang_thai'] == 0) && !$is_admin) {
            include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/header.php';
            echo "<div class='container my-5 text-center py-5'><div class='card shadow-sm border-0 p-5 rounded-4 mx-auto' style='max-width: 500px;'><i class='fa-solid fa-eye-slash text-secondary mb-3' style='font-size: 3rem;'></i><h3 class='fw-bold text-dark mb-2'>Sản phẩm không khả dụng!</h3><button onclick='history.back()' class='btn btn-success rounded-pill px-4'>Quay lại</button></div></div>";
            include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/footer.php';
            exit;
        }

        // Tính điểm đánh giá trung bình
        $sql_avg = "SELECT AVG(rating) as avg_rating, COUNT(*) as count_rev FROM product_reviews WHERE product_id = $id_sanpham";
        $res_avg = mysqli_query($conn, $sql_avg);
        $row_avg = mysqli_fetch_assoc($res_avg);
        $rating = $row_avg['avg_rating'] ? round($row_avg['avg_rating'], 1) : 0;
        $review_count = $row_avg['count_rev'] ?? 0;

        // Kiểm tra thả tim
        $is_favorited = false;
        if (isset($_SESSION['ten_khach_hang'])) {
            $ten_khach_hang = mysqli_real_escape_string($conn, $_SESSION['ten_khach_hang']);
            $user_query = mysqli_query($conn, "SELECT id FROM users WHERE username = '$ten_khach_hang'");
            if (mysqli_num_rows($user_query) > 0) {
                $user_id = mysqli_fetch_assoc($user_query)['id'];
                $fav_check = mysqli_query($conn, "SELECT id FROM user_favorites WHERE user_id = $user_id AND product_id = $id_sanpham");
                if (mysqli_num_rows($fav_check) > 0) $is_favorited = true;
            }
        }

        // Lấy bình luận
        $sql_reviews = "SELECT r.*, u.username FROM product_reviews r INNER JOIN users u ON r.user_id = u.id WHERE r.product_id = $id_sanpham ORDER BY r.created_at DESC";
        $result_reviews = mysqli_query($conn, $sql_reviews);
        $reviews = [];
        while($row_rev = mysqli_fetch_assoc($result_reviews)) {
            $reviews[] = $row_rev;
        }
    } else {
        echo "<div class='container mt-5 text-center'><h3>Không tìm thấy sản phẩm!</h3><a href='/web_banthuoc/user_display/index.php' class='btn btn-success'>Quay lại</a></div>";
        exit;
    }
} else {
    echo "<div class='container mt-5 text-center'><h3>ID sản phẩm không hợp lệ!</h3></div>";
    exit;
}
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/header.php'; ?>

<?php
$link_back = '/web_banthuoc/user_display/muahang.php';
$link_home = '/web_banthuoc/trangchu.php'; // Mặc định khách vãng lai

if (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1) {
    $link_back = '/web_banthuoc/Admin/admin_cuahang.php';
    $link_home = '/web_banthuoc/Admin/admin_index.php';
} elseif (isset($_SESSION['ten_khach_hang'])) {
    $link_back = '/web_banthuoc/user_display/cuahang.php';
    $link_home = '/web_banthuoc/user_display/index.php';
}
?>

<main class="container my-5" style="max-width: 1000px; margin: 0 auto;">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $link_home; ?>" class="text-success text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo $link_back; ?>" class="text-success text-decoration-none">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <div class="product-detail-container">
        <div class="row">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="product-img-wrapper">
                    <button class="favorite-btn <?php echo $is_favorited ? 'active' : ''; ?>" onclick="toggleFavorite(<?php echo $id_sanpham; ?>)" id="fav-btn-<?php echo $id_sanpham; ?>" title="Thêm vào yêu thích">
                        <i class="fa-solid fa-heart"></i>
                    </button>
                    <?php $img_path = !empty($product['image_url']) ? '/web_banthuoc/' . $product['image_url'] : '/web_banthuoc/assets/images/default.jpg'; ?>
                    <img src="<?php echo $img_path; ?>" class="product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            </div>

            <div class="col-lg-7 ps-lg-5 d-flex flex-column justify-content-center">
                <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="d-flex align-items-center mb-3">
                    <div class="rating-stars">
                        <?php
                        $full_stars = floor($rating);
                        $half_star = $rating - $full_stars >= 0.5;
                        for($i = 1; $i <= 5; $i++) {
                            if($i <= $full_stars) echo '<i class="fa-solid fa-star"></i>';
                            elseif($half_star && $i == $full_stars + 1) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                            else echo '<i class="fa-regular fa-star"></i>';
                        }
                        ?>
                    </div>
                    <span class="rating-text"><?php echo number_format($rating, 1); ?> (<?php echo $review_count; ?> đánh giá)</span>
                </div>

                <div class="info-pills">
                    <div class="info-pill"><i class="fa-solid fa-check-circle"></i> Chính hãng 100%</div>
                    <div class="info-pill"><i class="fa-solid fa-truck-fast"></i> Giao hàng tận nơi</div>
                    <div class="info-pill"><i class="fa-solid fa-box-open"></i> Đổi trả 7 ngày</div>
                </div>

                <div class="product-price">
                    <?php echo number_format($product['price'], 0, ',', '.'); ?> ₫
                    <span class="price-badge">Giá tốt</span>
                </div>

                <div class="product-description">
                    <p class="fw-bold mb-1">Mô tả sản phẩm:</p>
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>

                <div class="action-buttons d-flex gap-3 mt-auto">
                    <?php if (isset($_SESSION['ten_khach_hang']) && $_SESSION['ten_khach_hang'] != 'KhachVangLai'): ?>
                        <button type="button" class="btn btn-add-cart w-100" data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" data-price="<?php echo $product['price']; ?>">
                            <i class="fa-solid fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-yeucau-dangnhap btn-success text-white w-100 fs-5 fw-bold rounded-pill" style="padding: 12px;">
                            <i class="fa-solid fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="reviews-section">
        <h3 class="fw-bold mb-4">Đánh giá sản phẩm</h3>
        <div class="review-form-container p-4 rounded-4 mb-5" style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-pen-to-square text-success me-2"></i>Viết đánh giá của bạn</h5>
            <form id="review-form">
                <input type="hidden" id="review-product-id" value="<?php echo $id_sanpham; ?>">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Chọn số sao:</label>
                    <div class="star-rating-input">
                        <input type="radio" id="star5" name="rating" value="5"><label for="star5"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star4" name="rating" value="4"><label for="star4"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star3" name="rating" value="3"><label for="star3"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star2" name="rating" value="2"><label for="star2"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star1" name="rating" value="1"><label for="star1"><i class="fa-solid fa-star"></i></label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="review-comment" class="form-label fw-semibold text-secondary">Bình luận của bạn:</label>
                    <textarea class="form-control border-light-subtle focus-ring focus-ring-success" id="review-comment" rows="3" placeholder="Chia sẻ cảm nhận của bạn về chất lượng sản phẩm này..." style="resize: none;"></textarea>
                </div>
                <button type="button" class="btn btn-success px-4 py-2 rounded-pill fw-bold" onclick="submitReview()">
                    <i class="fa-solid fa-paper-plane me-2"></i> Gửi đánh giá
                </button>
            </form>
        </div>

        <div class="reviews-list">
            <h5 class="fw-bold border-bottom pb-3 mb-0">Tất cả đánh giá (<?php echo count($reviews); ?>)</h5>
            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $rev): ?>
                    <div class="review-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="reviewer-name">
                                    <i class="fa-solid fa-circle-user text-success fs-5 me-2 align-middle"></i>
                                    <?php echo htmlspecialchars($rev['username']); ?>
                                </div>
                                <div class="rating-stars small mt-1" style="margin-left: 32px;">
                                    <?php
                                    for($i=1; $i<=5; $i++) {
                                        echo $i <= $rev['rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="review-date"><?php echo date('d/m/Y H:i', strtotime($rev['created_at'])); ?></div>
                        </div>
                        <p class="mb-0 text-secondary" style="margin-left: 32px; font-size: 0.95rem; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($rev['comment'])); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa-regular fa-comments text-secondary opacity-50 mb-3" style="font-size: 3rem;"></i>
                    <p class="text-secondary fs-5">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
const isLoggedIn = <?php echo isset($_SESSION['ten_khach_hang']) ? 'true' : 'false'; ?>;

// HÀM GỌI THẢ TIM (TỰ GỌI CHÍNH FILE CHITIET.PHP)
function toggleFavorite(productId) {
    if (!isLoggedIn) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Yêu cầu đăng nhập', 
            text: 'Bạn cần đăng nhập để thêm sản phẩm vào danh sách yêu thích!', 
            confirmButtonColor: '#059669', 
            confirmButtonText: 'Đăng nhập ngay',
            showCancelButton: true,
            cancelButtonText: 'Để sau',
            cancelButtonColor: '#6c757d'
        }).then((result) => { 
            if (result.isConfirmed) window.location.href = '/web_banthuoc/public_auth/dangnhap.php'; 
        });
        return;
    }
    const btn = document.getElementById('fav-btn-' + productId);

    // BẮN DATA VỀ ĐÚNG FILE CHITIET.PHP 
    fetch('/web_banthuoc/user_display/chitiet.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=toggle_favorite&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'added') {
            btn.classList.add('active');
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
        } else if (data.status === 'removed') {
            btn.classList.remove('active');
            Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: data.message, showConfirmButton: false, timer: 1500 });
        } else {
            Swal.fire('Lỗi!', data.message || 'Có lỗi xảy ra', 'error');
        }
    }).catch(error => console.error('Error:', error));
}

// HÀM GỌI BÌNH LUẬN (TỰ GỌI CHÍNH FILE CHITIET.PHP)
function submitReview() {
    if (!isLoggedIn) {
        Swal.fire({ 
            icon: 'warning', 
            title: 'Yêu cầu đăng nhập', 
            text: 'Bạn cần đăng nhập để đánh giá!', 
            confirmButtonColor: '#059669', 
            confirmButtonText: 'Đăng nhập ngay',
            showCancelButton: true,
            cancelButtonText: 'Để sau',
            cancelButtonColor: '#6c757d'
        }).then((result) => { 
            if (result.isConfirmed) window.location.href = '/web_banthuoc/public_auth/dangnhap.php'; 
        });
        return;
    }
    const productId = document.getElementById('review-product-id').value;
    const comment = document.getElementById('review-comment').value;
    const ratingElement = document.querySelector('input[name="rating"]:checked');

    if (!ratingElement || comment.trim() === '') {
        Swal.fire('Opps!', 'Vui lòng chọn số sao và nhập bình luận.', 'warning');
        return;
    }
    
    // BẮN DATA VỀ ĐÚNG FILE CHITIET.PHP 
    fetch('/web_banthuoc/user_display/chitiet.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=submit_review&product_id=${productId}&rating=${ratingElement.value}&comment=${encodeURIComponent(comment)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({ icon: 'success', title: 'Thành công!', text: data.message, showConfirmButton: false, timer: 2000 }).then(() => { window.location.reload(); });
        } else {
            Swal.fire('Lỗi!', data.message, 'error');
        }
    }).catch(error => console.error('Error:', error));
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/footer.php'; ?>