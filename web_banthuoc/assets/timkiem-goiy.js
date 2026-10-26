// ===================== GỢI Ý TÌM KIẾM SẢN PHẨM (DÙNG CHUNG CHO CẢ 3 TRANG) =====================
// File này thay thế cho 3 file cũ:
//   - timkiem-goiy.js              (trang chủ khách hàng / thành viên: muahang.php, cuahang.php)
//   - timkiem-goiy-admin.js        (trang quản lý "Kho sản phẩm" của admin - dạng bảng)
//   - timkiem-goiy-admin-cuahang.js(trang "Cửa hàng" của admin - dạng lưới thẻ)
//
// Cách hoạt động: script tự "nhận diện" đang chạy ở trang nào dựa vào các phần tử
// có sẵn trong HTML của từng trang (id khung gợi ý, id khung chứa danh sách sản phẩm),
// từ đó tự chọn đúng API để gọi và đúng cách vẽ lại danh sách cho phù hợp.
// Sếp chỉ cần nhúng DUY NHẤT file này vào cả 3 trang, không cần sửa gì thêm ở HTML/PHP.
document.addEventListener("DOMContentLoaded", function () {
  const oTimKiem = document.getElementById("o-tim-kiem");
  if (!oTimKiem) return; // Trang này không có ô tìm kiếm thì thôi, khỏi chạy

  // ---- Nhận diện khung gợi ý (khác id giữa trang khách hàng và trang admin) ----
  const khungGoiY =
    document.getElementById("khung-goi-y") ||
    document.getElementById("khung-goi-y-admin");
  if (!khungGoiY) return;

  const laTrangAdmin = khungGoiY.id === "khung-goi-y-admin";

  // ---- Nhận diện khung chứa danh sách sản phẩm cần vẽ lại (3 kiểu khác nhau) ----
  const khungSanPham = document.getElementById("ds-san-pham"); // Lưới thẻ - trang khách hàng
  const thanBang = document.getElementById("ds-san-pham-body"); // <tbody> - trang admin "Kho sản phẩm"
  const luoiSanPham = document.getElementById("ds-san-pham-grid"); // Lưới thẻ - trang admin "Cửa hàng"

  // Cả 3 trang giờ dùng CHUNG 1 file API (backends_actions/timkiem_goiy.php).
  // Trang admin gửi kèm &pham_vi=admin để API trả thêm cả hàng đã ẩn; API sẽ tự
  // kiểm tra lại quyền admin bằng session trước khi cho xem, không tin vào tham số này.
  // (Trang admin cố tình KHÔNG kèm tham số "danhmuc": gõ từ khoá là quét xuyên suốt toàn bộ danh mục)
  const urlApi = laTrangAdmin
    ? "/web_banthuoc/backends_actions/timkiem_goiy.php?pham_vi=admin&q="
    : "/web_banthuoc/backends_actions/timkiem_goiy.php?q=";

  let boDemThoiGian = null; // Debounce, tránh gọi API dồn dập khi gõ nhanh
  let yeuCauDangChay = null; // Đánh dấu yêu cầu AJAX mới nhất, để bỏ qua kết quả trả về muộn

  // Lưu lại nội dung gốc (lúc mới tải trang) của khung đang được dùng, để khi xoá hết ô tìm kiếm thì trả lại y như cũ
  const htmlGoc = {
    khungSanPham: khungSanPham ? khungSanPham.innerHTML : "",
    thanBang: thanBang ? thanBang.innerHTML : "",
    luoiSanPham: luoiSanPham ? luoiSanPham.innerHTML : "",
  };

  function duongDanAnh(anh) {
    if (!anh) return "";
    return "/web_banthuoc/" + anh;
  }

  function thoatHtml(chuoi) {
    return String(chuoi == null ? "" : chuoi).replace(
      /[&<>"']/g,
      function (ky) {
        return {
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;",
        }[ky];
      },
    );
  }

  // ===================== VẼ THẺ SẢN PHẨM (dùng chung cho lưới #ds-san-pham và #ds-san-pham-grid) =====================
  function veTheSanPham(khung, danhSach) {
    if (!khung) return;

    if (!danhSach || danhSach.length === 0) {
      khung.innerHTML =
        '<div class="col-12 text-center py-5 text-secondary">Không tìm thấy sản phẩm nào phù hợp.</div>';
      return;
    }

    // data-mode chỉ có ở trang khách hàng (member / guest); trang admin "Cửa hàng" không có -> luôn coi như member
    const cheDoTrang = khung.getAttribute("data-mode") || "member";

    khung.innerHTML = danhSach
      .map(function (sp) {
        const anhHtml = sp.image_url
          ? '<img src="' +
            duongDanAnh(sp.image_url) +
            '" alt="' +
            thoatHtml(sp.name) +
            '" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.outerHTML=\'<span class=\\\'text-secondary fst-italic fs-6\\\'>Không có ảnh</span>\';">'
          : '<span class="text-secondary fst-italic fs-6">Không có ảnh</span>';

        // Nhãn "Đã ẩn": chỉ áp dụng khi API có trả về trang_thai (trang admin)
        const nhanAn =
          sp.trang_thai !== undefined && sp.trang_thai == 0
            ? '<span class="badge bg-secondary position-absolute top-0 start-0 m-2 z-1">Đã ẩn</span>'
            : "";

        // guest -> nút nhắc đăng nhập; còn lại (member hoặc admin) -> nút thêm giỏ hàng thật
        const nutGioHang =
          cheDoTrang === "guest"
            ? '<button class="btn btn-success rounded-circle d-flex align-items-center justify-content-center btn-yeucau-dangnhap" data-id="' +
              sp.id +
              '" style="width: 45px; height: 45px;"><i class="fa-solid fa-cart-plus"></i></button>'
            : '<button class="btn btn-success rounded-circle d-flex align-items-center justify-content-center btn-add-cart-goiy" data-name="' +
              thoatHtml(sp.name) +
              '" data-price="' +
              sp.price +
              '" style="width: 45px; height: 45px;"><i class="fa-solid fa-cart-plus"></i></button>';

        return (
          '<div class="col-sm-6 col-md-4">' +
          '<div class="card h-100 border-light-subtle shadow-sm rounded-4 feature-card-hover overflow-hidden position-relative">' +
          nhanAn +
          '<button class="btn btn-light rounded-circle position-absolute top-0 end-0 m-2 shadow-sm text-secondary z-1" style="width: 35px; height: 35px; padding: 0;">' +
          '<i class="fa-regular fa-heart"></i>' +
          "</button>" +
          '<div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">' +
          anhHtml +
          "</div>" +
          '<div class="card-body d-flex flex-column p-4">' +
          '<span class="text-success fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">' +
          thoatHtml(sp.category_name) +
          "</span>" +
          '<h3 class="card-title fw-bold fs-5 text-dark mb-2">' +
          thoatHtml(sp.name) +
          "</h3>" +
          '<p class="card-text text-secondary mb-4" style="font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">' +
          thoatHtml(sp.description) +
          "</p>" +
          '<div class="mt-auto d-flex justify-content-between align-items-center">' +
          '<span class="fs-5 fw-bold text-dark">' +
          sp.price_format +
          "</span>" +
          nutGioHang +
          "</div>" +
          "</div>" +
          "</div>" +
          "</div>"
        );
      })
      .join("");

    // Gắn sự kiện thêm giỏ hàng cho các nút vừa vẽ (chỉ áp dụng nút thật, không áp dụng nút nhắc đăng nhập
    // vì nút đó đã được bắt bằng uỷ quyền sự kiện có sẵn trong PHP của trang)
    khung.querySelectorAll(".btn-add-cart-goiy").forEach(function (nut) {
      nut.addEventListener("click", function () {
        const ten = this.getAttribute("data-name");
        const gia = parseFloat(this.getAttribute("data-price"));
        if (typeof window.themVaoGioHangTuNgoai === "function") {
          window.themVaoGioHangTuNgoai(ten, gia);
        }
      });
    });
  }

  // ===================== VẼ BẢNG "KHO SẢN PHẨM" (chỉ dùng cho trang admin - #ds-san-pham-body) =====================
  function veDongBang(danhSach) {
    if (!thanBang) return;

    if (!danhSach || danhSach.length === 0) {
      thanBang.innerHTML =
        "<tr><td colspan='7' class='text-center'>Không tìm thấy sản phẩm nào phù hợp.</td></tr>";
      return;
    }

    thanBang.innerHTML = danhSach
      .map(function (sp, idx) {
        const anhHtml = sp.image_url
          ? '<img src="' +
            duongDanAnh(sp.image_url) +
            '" class="thumb-img" onerror="this.onerror=null; this.outerHTML=\'<i class=\\\'fa-solid fa-image thumb-icon\\\'></i>\';">'
          : '<i class="fa-solid fa-image thumb-icon"></i>';

        const trangThaiHtml =
          sp.trang_thai == 1
            ? '<span class="status-badge status-success"><i class="fa-solid fa-eye"></i> Đang bán</span>'
            : '<span class="status-badge status-hidden"><i class="fa-solid fa-eye-slash"></i> Đã ẩn</span>';

        const nutToggle =
          sp.trang_thai == 1
            ? '<button type="button" class="btn-action btn-hide btn-toggle" data-id="' +
              sp.id +
              '" title="Ẩn sản phẩm này"><i class="fa-solid fa-eye-slash"></i></button>'
            : '<button type="button" class="btn-action btn-show btn-toggle" data-id="' +
              sp.id +
              '" title="Hiển thị lại"><i class="fa-solid fa-eye"></i></button>';

        return (
          '<tr class="' +
          (sp.trang_thai == 0 ? "row-hidden" : "") +
          '">' +
          '<td><input type="checkbox" class="check-item" name="sp_ids[]" value="' +
          sp.id +
          '"></td>' +
          "<td>" +
          (idx + 1) +
          "</td>" +
          '<td class="product-cell">' +
          '<div class="product-thumb">' +
          anhHtml +
          "</div>" +
          "<strong>" +
          thoatHtml(sp.name) +
          "</strong>" +
          "</td>" +
          "<td>" +
          thoatHtml(sp.category_name) +
          "</td>" +
          '<td class="price-cell">' +
          sp.price_format +
          "</td>" +
          "<td>" +
          trangThaiHtml +
          "</td>" +
          "<td>" +
          '<div class="action-buttons">' +
          '<button type="button" class="btn-action btn-edit" ' +
          'data-id="' +
          sp.id +
          '" ' +
          'data-name="' +
          thoatHtml(sp.name) +
          '" ' +
          'data-category="' +
          sp.category_id +
          '" ' +
          'data-price="' +
          sp.price +
          '" ' +
          'data-desc="' +
          thoatHtml(sp.description) +
          '" ' +
          'title="Sửa"><i class="fa-solid fa-pen-to-square"></i></button>' +
          nutToggle +
          "</div>" +
          "</td>" +
          "</tr>"
        );
      })
      .join("");
  }

  // Vẽ lại đúng khung đang có mặt trên trang (chỉ khung nào tồn tại mới được vẽ)
  function capNhatDanhSachChinh(danhSach) {
    if (khungSanPham) veTheSanPham(khungSanPham, danhSach);
    if (luoiSanPham) veTheSanPham(luoiSanPham, danhSach);
    if (thanBang) veDongBang(danhSach);
  }

  // ===================== VẼ KHUNG GỢI Ý THẢ XUỐNG (dùng chung cho cả 3 trang) =====================
  function veKhungGoiY(danhSach, tuKhoa) {
    if (!danhSach || danhSach.length === 0) {
      khungGoiY.innerHTML =
        '<div class="search-suggest-empty">Không tìm thấy sản phẩm nào khớp với "' +
        thoatHtml(tuKhoa) +
        '"</div>';
      khungGoiY.classList.remove("d-none");
      return;
    }

    khungGoiY.innerHTML = danhSach
      .map(function (sp) {
        const anhHtml = sp.image_url
          ? '<img src="' +
            duongDanAnh(sp.image_url) +
            "\" alt=\"\" onerror=\"this.onerror=null; this.outerHTML='<div class=\\'search-suggest-noimg\\'><i class=\\'fa-solid fa-pills\\'></i></div>';\">"
          : '<div class="search-suggest-noimg"><i class="fa-solid fa-pills"></i></div>';

        // Nhãn trạng thái chỉ hiện ở trang admin (khi API có trả về trang_thai)
        const chuTrangThai =
          laTrangAdmin && sp.trang_thai !== undefined
            ? " &middot; " +
              (sp.trang_thai == 1
                ? '<span class="status-badge status-success" style="font-size:0.7rem;"><i class="fa-solid fa-eye"></i> Đang bán</span>'
                : '<span class="status-badge status-hidden" style="font-size:0.7rem;"><i class="fa-solid fa-eye-slash"></i> Đã ẩn</span>')
            : "";

        return (
          '<button type="button" class="search-suggest-item" data-name="' +
          encodeURIComponent(sp.name) +
          '">' +
          '<div class="search-suggest-thumb">' +
          anhHtml +
          "</div>" +
          '<div class="search-suggest-info">' +
          '<span class="search-suggest-name">' +
          thoatHtml(sp.name) +
          "</span>" +
          '<span class="search-suggest-cat">' +
          thoatHtml(sp.category_name) +
          chuTrangThai +
          "</span>" +
          "</div>" +
          '<span class="search-suggest-price">' +
          sp.price_format +
          "</span>" +
          "</button>"
        );
      })
      .join("");

    khungGoiY.classList.remove("d-none");

    // Bấm vào 1 gợi ý -> điền tên vào ô và submit tìm kiếm luôn
    khungGoiY.querySelectorAll(".search-suggest-item").forEach(function (item) {
      item.addEventListener("click", function () {
        const ten = decodeURIComponent(this.getAttribute("data-name"));
        oTimKiem.value = ten;
        khungGoiY.classList.add("d-none");
        oTimKiem.form.submit();
      });
    });
  }

  // ===================== GỌI API LẤY GỢI Ý =====================
  function goiYTuKhoa(tuKhoa) {
    const yeuCauNay = Symbol();
    yeuCauDangChay = yeuCauNay;

    fetch(urlApi + encodeURIComponent(tuKhoa))
      .then(function (res) {
        return res.json();
      })
      .then(function (json) {
        // Nếu trong lúc chờ mà Sếp gõ thêm từ khác rồi thì bỏ qua kết quả này
        if (yeuCauDangChay !== yeuCauNay) return;

        if (json.ok) {
          veKhungGoiY(json.data, tuKhoa);
          capNhatDanhSachChinh(json.data); // Đồng thời cập nhật luôn danh sách/bảng/lưới chính
        }
      })
      .catch(function () {
        khungGoiY.classList.add("d-none");
      });
  }

  // ===================== CÁC SỰ KIỆN CHUNG =====================
  oTimKiem.addEventListener("input", function () {
    const tuKhoa = this.value.trim();
    clearTimeout(boDemThoiGian);

    if (tuKhoa.length === 0) {
      khungGoiY.classList.add("d-none");
      khungGoiY.innerHTML = "";
      // Trả lại đúng khung gốc tương ứng với trang đang chạy
      if (khungSanPham) khungSanPham.innerHTML = htmlGoc.khungSanPham;
      if (thanBang) thanBang.innerHTML = htmlGoc.thanBang;
      if (luoiSanPham) luoiSanPham.innerHTML = htmlGoc.luoiSanPham;
      return;
    }

    boDemThoiGian = setTimeout(function () {
      goiYTuKhoa(tuKhoa);
    }, 300);
  });

  oTimKiem.addEventListener("focus", function () {
    if (this.value.trim().length > 0 && khungGoiY.innerHTML.trim() !== "") {
      khungGoiY.classList.remove("d-none");
    }
  });

  document.addEventListener("click", function (e) {
    if (!oTimKiem.form.contains(e.target)) {
      khungGoiY.classList.add("d-none");
    }
  });

  oTimKiem.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      khungGoiY.classList.add("d-none");
    }
  });
});
