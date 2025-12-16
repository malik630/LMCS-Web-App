$(document).ready(function () {
  let sortDirection = {};

  // Fonction de recherche et filtrage
  function filterMembers() {
    const searchTerm = $("#search-member").val().toLowerCase();
    const roleFilter = $("#filter-role").val().toLowerCase();
    const gradeFilter = $("#filter-grade").val().toLowerCase();

    let visibleCount = 0;

    $(".member-row").each(function () {
      const $row = $(this);
      const nom = $row.data("nom");
      const prenom = $row.data("prenom");
      const grade = $row.data("grade");
      const poste = $row.data("poste");
      const role = $row.data("role");
      const teams = $row.data("teams");

      const matchSearch =
        !searchTerm ||
        nom.includes(searchTerm) ||
        prenom.includes(searchTerm) ||
        poste.includes(searchTerm) ||
        teams.includes(searchTerm);

      const matchRole = !roleFilter || role === roleFilter;
      const matchGrade =
        !gradeFilter || grade.includes(gradeFilter.toLowerCase());

      if (matchSearch && matchRole && matchGrade) {
        $row.show();
        visibleCount++;
      } else {
        $row.hide();
      }
    });

    $("#no-results").toggle(visibleCount === 0);
    $("#members-tbody").toggle(visibleCount > 0);
  }

  // Fonction de tri
  function sortTable(column) {
    const $tbody = $("#members-tbody");
    const rows = $tbody.find("tr").toArray();

    const direction = sortDirection[column] === "asc" ? "desc" : "asc";
    sortDirection[column] = direction;

    // Mise à jour des icônes
    $(".sort-icon").text("↕");
    $(`th[data-sort="${column}"] .sort-icon`).text(
      direction === "asc" ? "↑" : "↓"
    );

    rows.sort((a, b) => {
      let aVal = $(a).data(column) || "";
      let bVal = $(b).data(column) || "";

      aVal = String(aVal).toLowerCase();
      bVal = String(bVal).toLowerCase();

      if (direction === "asc") {
        return aVal.localeCompare(bVal);
      } else {
        return bVal.localeCompare(aVal);
      }
    });

    $tbody.empty().append(rows);
  }

  // Event listeners
  $("#search-member, #filter-role, #filter-grade").on(
    "input change",
    filterMembers
  );

  $("#reset-filters").on("click", function () {
    $("#search-member").val("");
    $("#filter-role").val("");
    $("#filter-grade").val("");
    filterMembers();
  });

  $("th[data-sort]").on("click", function () {
    const column = $(this).data("sort");
    if (column !== "photo") {
      sortTable(column);
    }
  });
});
