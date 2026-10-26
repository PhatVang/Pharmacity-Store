<footer class="pt-5 pb-3 mt-auto">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-6 mb-4">
                    <div class="footer-brand">
                        <div class="logo fs-4 fw-bold mb-3"><i class="fa-solid fa-heart-pulse text-success"></i> RaumaCity</div>
                        <p class="text-secondary">Nhà thuốc kỹ thuật số và người bạn đồng hành chăm sóc sức khỏe đáng tin cậy của bạn.</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="footer-links">
                        <h4 class="fs-5 fw-bold mb-3">Liên kết nhanh</h4>
                        <ul class="list-unstyled">
                            <?php if (isset($_SESSION['quyen_han']) && $_SESSION['quyen_han'] == 1): ?>
                                <li class="mb-2"><a href="/web_banthuoc/Admin/admin_cuahang.php" class="text-decoration-none text-secondary">Cửa hàng thuốc</a></li>
                            <?php elseif (isset($_SESSION['ten_khach_hang'])): ?>
                                <li class="mb-2"><a href="/web_banthuoc/user_display/cuahang.php" class="text-decoration-none text-secondary">Cửa hàng thuốc</a></li>
                            <?php else: ?>
                                <li class="mb-2"><a href="/web_banthuoc/user_display/muahang.php" class="text-decoration-none text-secondary">Cửa hàng thuốc</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="footer-links">
                        <h4 class="fs-5 fw-bold mb-3">Pháp lý</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Chính sách bảo mật</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Điều khoản dịch vụ</a></li>
                            <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Tuyên bố từ chối trách nhiệm y tế</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="text-center pt-3 border-top text-secondary" style="font-size: 0.85rem;">
                <p>© 2026 RaumaCity. Chỉ mang tính chất cung cấp thông tin.</p>
            </div>
        </div>
    </footer>

    <div id="cart-modal" class="cart-modal">
        <div class="cart-content">
            <div class="cart-header">
                <h2>Tóm tắt đơn hàng</h2>
                <span id="close-cart" class="close-btn">&times;</span>
            </div>
            <div id="cart-items" class="cart-items"></div>
            <div class="cart-footer">
                <div class="cart-total">
                    <span>Tổng cộng:</span>
                    <span id="cart-total-price">0 đ</span>
                </div>
                <button class="btn btn-primary btn-checkout">Đặt đơn</button>
            </div>
        </div>
    </div>

    <script>
        // Mở menu Avatar khi click
        document.addEventListener("DOMContentLoaded", function() {
            const nutUser = document.querySelector(".btn-user");
            const menuXoXuong = document.querySelector(".dropdown-content");
            if (nutUser && menuXoXuong) {
                nutUser.addEventListener("click", function(event) {
                    event.stopPropagation();
                    menuXoXuong.classList.toggle("show");
                });
                window.addEventListener("click", function(event) {
                    if (!menuXoXuong.contains(event.target) && !nutUser.contains(event.target)) {
                        menuXoXuong.classList.remove("show");
                    }
                });
            }
        });
    </script>

    <script>
        // Khởi tạo tên giỏ hàng chuẩn bị cho script.js
        const tenKhachHang = "<?php echo isset($_SESSION['ten_khach_hang']) ? $_SESSION['ten_khach_hang'] : 'KhachVangLai'; ?>";
        const tenKhoGioHang = "gioHang_" + tenKhachHang; 
    </script>

    <script>
        // Chống bóng ma quay lại trang (BFCache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) { window.location.reload(); }
        });
    </script>
    
    <script src="/web_banthuoc/assets/script.js?v=<?php echo time(); ?>"></script>
    
    <script>
        // THÔNG BÁO CHO KHÁCH VÃNG LAI KHI BẤM VÀO NÚT YÊU CẦU ĐĂNG NHẬP (THÊM GIỎ HÀNG)
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

        // THÔNG BÁO CHO KHÁCH VÃNG LAI KHI BẤM VÀO THẢ TIM (HOẶC MENU YÊU THÍCH)
        const heartButtonsGuest = document.querySelectorAll('.btn-heart-dangnhap');
        heartButtonsGuest.forEach(function(btn) {
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
    </script>

</body>
</html>