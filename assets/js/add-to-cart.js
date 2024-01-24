$(document).ready(function () {
  $(".add-to-cart").on("click", function (e) {
    e.preventDefault();
    var ringId = $(this).data("id");

    $.ajax({
      url: baseUrl + "/helpers/addToCart.php",
      type: "POST",
      data: { ringId: ringId },
      success: function (response) {
        $(".cart-bag").html(response.cartHtml);
        $(".cart-total p:last-child").text(response.total);
      },
      error: function (error) {
        console.error("Error adding to cart:", error);
      },
    });
  });
});
