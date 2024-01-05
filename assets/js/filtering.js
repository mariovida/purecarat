document.addEventListener("DOMContentLoaded", function () {
  var filterButtons = document.querySelectorAll(".filter-button");
  var ringItems = document.querySelectorAll(".ring-item");

  filterButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      var filter = button.dataset.filter;

      ringItems.forEach(function (item) {
        if (filter === "all" || item.dataset.filter === filter) {
          item.style.display = "block";
        } else {
          item.style.display = "none";
        }
      });
    });
  });
});
