document.addEventListener("DOMContentLoaded", function () {
  $(".filters button").on("click", function () {
    var selectedFilter = $(this).data("filter");

    if (selectedFilter === "all") {
      $(".ring-item").show();
    } else {
      $(".ring-item").hide();
      $(".ring-item[data-filter='" + selectedFilter + "']").show();
    }
  });
});
