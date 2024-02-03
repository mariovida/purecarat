document.addEventListener("DOMContentLoaded", function () {
  var nav = document.getElementById("mainNav");

  window.addEventListener("scroll", function () {
    if (window.scrollY > 1) {
      nav.classList.add("scrolled");
    } else {
      nav.classList.remove("scrolled");
    }
  });

  /* CART */
  const cartContainer = document.getElementById("cartContainer");
  const closeCartBtn = document.getElementById("closeCartBtn");
  const overlay = document.getElementById("overlay");

  // Toggle cart visibility
  document
    .querySelector(".cart-btn")
    .addEventListener("click", function (event) {
      event.preventDefault();
      cartContainer.style.right = "0";
      overlay.style.display = "block";
      overlay.style.opacity = 1;
    });

  closeCartBtn.addEventListener("click", function () {
    closeCart();
  });

  overlay.addEventListener("click", function () {
    closeCart();
  });

  function closeCart() {
    cartContainer.style.right = "-500px";
    overlay.style.opacity = 0;
    setTimeout(() => {
      overlay.style.display = "none";
    }, 300);
  }
});
