document.addEventListener("DOMContentLoaded", function () {
  // Lắng nghe sự kiện click vào các nút thả tim
  document.querySelectorAll(".btn-toggle-like").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      let productId = this.getAttribute("data-product-id");
      let icon = this.querySelector("i");

      // ĐÃ SỬA ĐƯỜNG DẪN TRỎ VỀ ĐÚNG USER_DISPLAY
      fetch("/web_banthuoc/user_display/toggle_like.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ product_id: productId }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            // Update icon trái tim trên sản phẩm
            if (data.action === "liked") {
              icon.classList.remove("fa-regular", "text-secondary");
              icon.classList.add("fa-solid", "text-danger");
            } else {
              icon.classList.remove("fa-solid", "text-danger");
              icon.classList.add("fa-regular", "text-secondary");
            }

            // Update số lượng đếm trên Header
            let heartCountBadge = document.getElementById("header-heart-count");
            if (heartCountBadge) {
              heartCountBadge.textContent = data.total_likes;
            }
          } else {
            alert(data.message); // Báo lỗi nếu chưa đăng nhập
          }
        })
        .catch((error) => console.error("Lỗi API Tim:", error));
    });
  });
});
