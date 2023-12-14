document.addEventListener("DOMContentLoaded", function () {
  $(".filters button").on("click", function () {
    var selectedFilter = $(this).text().trim();

    if (selectedFilter === "All") {
      $(".ring-item").show();
    } else {
      $(".ring-item").hide();
      $(".ring-item[data-filter='" + selectedFilter + "']").show();
    }
  });
});
