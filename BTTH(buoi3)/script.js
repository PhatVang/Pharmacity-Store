document.addEventListener("DOMContentLoaded", function () {
  // 1. Lấy giỏ hàng từ bộ nhớ với tên chuẩn
  let gioHang = JSON.parse(localStorage.getItem(tenKhoGioHang)) || [];

  // 2. BƯỚC DỌN RÁC THẦN THÁNH
  if (gioHang.length > 0 && gioHang[0].quantity === undefined) {
    gioHang = [];
    localStorage.setItem(tenKhoGioHang, JSON.stringify(gioHang));
  }

  // --- CÁC HÀM XỬ LÝ CHÍNH ---
  function capNhatGioHang() {
    localStorage.setItem(tenKhoGioHang, JSON.stringify(gioHang));
    veGioHangRaManHinh();
  }

  function veGioHangRaManHinh() {
    const divChuaHang = document.getElementById("cart-items");
    const theHienTongTien = document.getElementById("cart-total-price");
    const cucDoBaoSoLuong = document.getElementById("cart-count");

    if (divChuaHang) divChuaHang.innerHTML = "";
    let tongTien = 0;
    let tongSoLuong = 0;

    if (gioHang.length === 0) {
      if (divChuaHang)
        divChuaHang.innerHTML =
          '<p style="text-align:center; color:#888; margin-top: 50px;">Chưa có sản phẩm nào trong giỏ.</p>';
      if (theHienTongTien) theHienTongTien.innerText = "0 đ";
      if (cucDoBaoSoLuong) cucDoBaoSoLuong.style.display = "none";
    } else {
      gioHang.forEach((monHang, viTri) => {
        tongTien += monHang.price * monHang.quantity;
        tongSoLuong += monHang.quantity;

        if (divChuaHang) {
          divChuaHang.innerHTML += `
                        <div class="cart-item">
                            <div class="cart-item-info">
                                <h4>${monHang.name}</h4>
                                <span class="cart-item-price">${monHang.price.toLocaleString("vi-VN")} đ</span>
                            </div>
                            <div class="qty-controls">
                                <button class="qty-btn" onclick="thayDoiSoLuong(${viTri}, -1)">-</button>
                                <span>${monHang.quantity}</span>
                                <button class="qty-btn" onclick="thayDoiSoLuong(${viTri}, 1)">+</button>
                            </div>
                        </div>
                    `;
        }
      });
      //Tính tổng
      if (theHienTongTien) {
        theHienTongTien.innerText = tongTien.toLocaleString("vi-VN") + " đ";
      }
      if (cucDoBaoSoLuong) {
        cucDoBaoSoLuong.style.display = "block";
        cucDoBaoSoLuong.innerText = tongSoLuong;
      }
    }

    // --- CHỨC NĂNG ĐẶT HÀNG (THANH TOÁN) ---
    const nutDatHang = document.querySelector(".btn-checkout");
    if (nutDatHang) {
      const nutMoi = nutDatHang.cloneNode(true);
      nutDatHang.parentNode.replaceChild(nutMoi, nutDatHang);

      nutMoi.addEventListener("click", function () {
        // 1. Kiểm tra giỏ trống
        if (gioHang.length === 0) {
          alert("Giỏ hàng của Sếp đang trống không à, mua gì đi chứ!");
          return;
        }

        // 2. Kiểm tra thiếu thông tin (Biến isMissingInfo lấy từ PHP)
        if (typeof isMissingInfo !== "undefined" && isMissingInfo === true) {
          Swal.fire({
            title: "Opps!",
            text: "Vui lòng cập nhật đầy đủ Số điện thoại và Địa chỉ trong phần Hồ sơ để đặt hàng!",
            icon: "error",
            confirmButtonText: "Đi cập nhật ngay",
            confirmButtonColor: "#059669",
          }).then((result) => {
            // Chuyển trang tùy theo quyền Khách hay Admin
            if (window.location.pathname.includes("admin_")) {
              window.location.href = "admin_account.php";
            } else {
              window.location.href = "profile.php";
            }
          });
          return; // Chặn lại, không cho đi tiếp
        }

        // 3. Nếu mọi thứ ngon lành -> Chuyển sang trang Thanh toán hiển thị QR
        window.location.href = "thanhtoan.php";
      });
    }
  } // <-- KẾT THÚC HÀM VẼ GIỎ HÀNG

  //  Hàm tăng giảm số lượng
  window.thayDoiSoLuong = function (viTri, soLuongThayDoi) {
    gioHang[viTri].quantity += soLuongThayDoi;
    if (gioHang[viTri].quantity <= 0) {
      gioHang.splice(viTri, 1);
    }
    capNhatGioHang();
  };

  //  BẮT SỰ KIỆN NÚT BẤM VÀO GIỎ
  const nutThemVaoGio = document.querySelectorAll(".btn-add-cart");
  nutThemVaoGio.forEach((button) => {
    button.addEventListener("click", function () {
      const ten = this.getAttribute("data-name");
      const gia = parseFloat(this.getAttribute("data-price"));

      let monDaCo = gioHang.find((item) => item.name === ten);

      if (monDaCo) {
        monDaCo.quantity += 1;
      } else {
        gioHang.push({ name: ten, price: gia, quantity: 1 });
      }

      capNhatGioHang();

      // Hiện thông báo
      alert("🛒 Đã thêm thành công: " + ten + " vào giỏ hàng!");
    });
  });

  // Mở giỏ hàng khi bấm vào nút "Giỏ hàng" trên Header
  const cartBtn = document.getElementById("cart-btn");
  if (cartBtn) {
    cartBtn.addEventListener("click", () => {
      document.getElementById("cart-modal").style.display = "block";
    });
  }

  // Đóng giỏ hàng khi bấm dấu X
  const closeCartBtn = document.getElementById("close-cart");
  if (closeCartBtn) {
    closeCartBtn.addEventListener("click", () => {
      document.getElementById("cart-modal").style.display = "none";
    });
  }

  // Chạy lần đầu tiên khi vừa vào web để hiển thị giỏ cũ
  veGioHangRaManHinh();
});
