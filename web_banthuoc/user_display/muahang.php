<?php
include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/config.php';

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$current_user_id = 0;

// LOGIC LỌC & TÌM KIẾM
$where_clause = "WHERE p.trang_thai = 1";

if (isset($_GET['yeuthich']) && $_GET['yeuthich'] == '1') {
    $where_clause .= " AND 1=0";
} elseif (isset($_GET['timkiem']) && $_GET['timkiem'] != '') {
    $tukhoa = mysqli_real_escape_string($conn, $_GET['timkiem']);
    $where_clause .= " AND (p.name LIKE '%$tukhoa%' OR p.description LIKE '%$tukhoa%')";
} elseif (isset($_GET['danhmuc']) && $_GET['danhmuc'] != '') {
    $danhmuc_duoc_chon = (int)$_GET['danhmuc']; 
    $where_clause .= " AND p.category_id = $danhmuc_duoc_chon";
}

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
if ($page < 1) $page = 1;

$sql_count = "SELECT COUNT(*) AS total FROM products p INNER JOIN categories c ON p.category_id = c.id $where_clause";
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];

$total_pages = ceil($total_records / $limit);
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$offset = ($page - 1) * $limit;

$sql = "SELECT p.*, c.name AS category_name,
        (SELECT COUNT(*) FROM user_favorites pl WHERE pl.product_id = p.id) AS total_likes,
        0 AS is_user_liked
        FROM products p 
        INNER JOIN categories c ON p.category_id = c.id 
        $where_clause
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/web_banthuoc/includes/header.php'; ?>

    <main class="container my-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h1 class="fw-bold fs-2 mb-2">Cửa hàng thuốc</h1>
                <p class="text-secondary mb-0">Duyệt qua danh mục các sản phẩm y tế đã được kiểm định của chúng tôi.</p>
            </div>
            
            <!-- SỬA TRỎ FORM VỀ ĐÚNG MUAHANG.PHP -->
            <form action="/web_banthuoc/user_display/muahang.php" method="GET" class="position-relative" style="min-width: 320px;">
                <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
                <input type="text" id="o-tim-kiem" name="timkiem" class="form-control rounded-pill ps-5 py-2 border-secondary-subtle focus-ring focus-ring-success" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['timkiem']) ? htmlspecialchars($_GET['timkiem']) : ''; ?>" autocomplete="off">
                <div id="khung-goi-y" class="d-none position-absolute bg-white border rounded shadow-sm w-100" style="top: 100%; z-index: 1050; max-height: 300px; overflow-y: auto;"></div>
                <button type="submit" class="d-none"></button>
            </form>
        </div>

        <div class="row g-4">
            <aside class="col-lg-3">
                <div class="card border-light-subtle shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h3 class="fs-5 fw-bold mb-3"><i class="fa-solid fa-filter text-success me-2"></i> Danh mục</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-column gap-2 category-list">
                            <?php $dm_hientai = isset($_GET['danhmuc']) ? $_GET['danhmuc'] : ''; ?>
                            <a href="/web_banthuoc/user_display/muahang.php" class="category-item text-decoration-none px-3 py-2 rounded-3 <?php echo ($dm_hientai == '' && !isset($_GET['yeuthich'])) ? 'active' : ''; ?>">Tất cả</a>
                            <?php
                            $sql_danhmuc = "SELECT * FROM categories";
                            $result_danhmuc = mysqli_query($conn, $sql_danhmuc);
                            if (mysqli_num_rows($result_danhmuc) > 0) {
                                while($row_dm = mysqli_fetch_assoc($result_danhmuc)) {
                                    if (isset($row_dm['trang_thai']) && $row_dm['trang_thai'] == 0) continue;
                                    $id_dm = $row_dm['id'];
                                    $ten_dm = htmlspecialchars($row_dm['name']);
                                    $active_class = ($dm_hientai == $id_dm) ? 'active' : '';
                                    echo '<a href="/web_banthuoc/user_display/muahang.php?danhmuc=' . $id_dm . '" class="category-item text-decoration-none px-3 py-2 rounded-3 ' . $active_class . '">' . $ten_dm . '</a>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="col-lg-9">
                <!-- KHUNG CHỨA SẢN PHẨM DÀNH CHO KHÁCH -->
                <div id="ds-san-pham" data-mode="guest" class="row g-4">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <div class="col-sm-6 col-md-4">
                                <div class="card h-100 border-light-subtle shadow-sm rounded-4 feature-card-hover overflow-hidden position-relative">
                                    <button class="btn btn-light rounded-pill position-absolute top-0 end-0 m-2 shadow-sm text-secondary z-1 btn-heart-dangnhap px-2 py-1 d-flex align-items-center gap-1" data-product-id="<?php echo $row['id']; ?>" style="height: 35px;">
                                        <i class="fa-regular fa-heart text-danger fs-5"></i>
                                        <span class="like-count fw-bold text-dark fs-6"><?php echo $row['total_likes']; ?></span>
                                    </button>

                                    <a href="/web_banthuoc/user_display/chitiet.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <?php if($row['image_url'] != "") { ?>
                                                <img src="/web_banthuoc/<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="w-100 h-100 object-fit-cover">
                                            <?php } else { ?>
                                                <span class="text-secondary fst-italic fs-6">Không có ảnh</span>
                                            <?php } ?>
                                        </div>
                                    </a>

                                    <div class="card-body d-flex flex-column p-4">
                                        <span class="text-success fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            <?php echo htmlspecialchars($row['category_name']); ?>
                                        </span>
                                        <a href="/web_banthuoc/user_display/chitiet.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark">
                                            <h3 class="card-title fw-bold fs-5 mb-2">
                                                <?php echo htmlspecialchars($row['name']); ?>
                                            </h3>
                                        </a>
                                        <p class="card-text text-secondary mb-4" style="font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </p>
                                        
                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            <span class="fs-5 fw-bold text-dark">
                                                <?php echo number_format($row['price'], 0, ',', '.'); ?> đ
                                            </span>
                                            <button type="button" 
                                                    class="btn btn-success rounded-circle d-flex align-items-center justify-content-center btn-yeucau-dangnhap" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                    data-price="<?php echo $row['price']; ?>"
                                                    style="width: 45px; height: 45px;">
                                                <i class="fa-solid fa-cart-plus" style="pointer-events: none;"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<div class='col-12 text-center py-5 text-secondary'>Hiện tại chưa có sản phẩm nào trong mục này.</div>";
                    }
                    ?>
                </div>

                <!-- PHÂN TRANG -->
                <?php if (isset($total_pages) && $total_pages > 1): ?>
                <div class="d-flex justify-content-center mt-5">
                    <nav aria-label="Page navigation">
                        <ul class="pagination shadow-sm">
                        <?php
                        $url_params = "";
                        if(isset($_GET['timkiem'])) $url_params .= "&timkiem=".$_GET['timkiem'];
                        if(isset($_GET['danhmuc'])) $url_params .= "&danhmuc=".$_GET['danhmuc'];
                        if(isset($_GET['yeuthich'])) $url_params .= "&yeuthich=".$_GET['yeuthich'];

                        if($page > 1) {
                            echo '<li class="page-item"><a class="page-link text-success" href="?page='.($page - 1).$url_params.'"><i class="fa-solid fa-chevron-left"></i></a></li>';
                        }

                        for($i = 1; $i <= $total_pages; $i++) {
                            if ($i == $page) {
                                echo '<li class="page-item active"><a class="page-link bg-success border-success text-white" href="#">'.$i.'</a></li>';
                            } else {
                                echo '<li class="page-item"><a class="page-link text-success" href="?page='.$i.$url_params.'">'.$i.'</a></li>';
                            }
                        }

                        if($page < $total_pages) {
                            echo '<li class="page-item"><a class="page-link text-success" href="?page='.($page + 1).$url_params.'"><i class="fa-solid fa-chevron-right"></i></a></li>';
                        }
                        ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

  <script>
    var isLoggedIn = false;
    var tenKhachHang = "";
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- GIỮ NGUYÊN SCRIPT POPUP ĐĂNG NHẬP CHO KHÁCH -->
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const heartButtons = document.querySelectorAll('.btn-heart-dangnhap');
          heartButtons.forEach(function(btn) {
              btn.addEventListener('click', function(e) {
                  e.preventDefault();
                  if (typeof Swal !== 'undefined') {
                      Swal.fire({
                          title: 'Oops...!',
                          text: 'Bạn cần đăng nhập tài khoản trước mới được thả tim sản phẩm nhé!',
                          icon: 'warning',
                          showCancelButton: true,
                          confirmButtonColor: '#059669',
                          cancelButtonColor: '#6c757d',
                          confirmButtonText: 'Đi tới Đăng nhập',
                          cancelButtonText: 'Để sau'
                      }).then((result) => {
                          if (result.isConfirmed) window.location.href = '/web_banthuoc/public_auth/dangnhap.php';
                      });
                  }
              });
          });

          const nutYeuCau = document.querySelectorAll('.btn-yeucau-dangnhap');
          nutYeuCau.forEach(function(nut) {
              nut.addEventListener('click', function(e) {
                  e.preventDefault();
                  if (typeof Swal !== 'undefined') {
                      Swal.fire({
                          title: 'Oops...!',
                          text: 'Bạn cần đăng nhập tài khoản trước mới được bỏ đồ vào giỏ hàng nhé!',
                          icon: 'warning',
                          showCancelButton: true,
                          confirmButtonColor: '#059669',
                          cancelButtonColor: '#6c757d', 
                          confirmButtonText: 'Đi tới Đăng nhập',
                          cancelButtonText: 'Để sau'
                      }).then((result) => {
                          if (result.isConfirmed) window.location.href = '/web_banthuoc/public_auth/dangnhap.php';
                      });
                  }
              });
          });
      });
  </script>
  <script src="/web_banthuoc/assets/timkiem-goiy.js"></script>
</body>
