class TableManager {
  constructor(tableId, ajaxUrl, options = {}) {
    this.tableId = tableId;
    this.ajaxUrl = ajaxUrl;
    this.options = Object.assign(
      {
        searchable: true,
        sortable: true,
        filterable: false,
      },
      options
    );

    this.table = document.getElementById(tableId);
    this.tbody = document.getElementById(`${tableId}-body`);
    this.searchInput = document.getElementById("table-search");
    this.filters = document.querySelectorAll(".table-filter");
    this.resetBtn = document.getElementById("reset-filters");
    this.tableInfo = document.getElementById("table-info");

    this.currentSort = { column: null, direction: "asc" };
    this.allRows = [];
    this.filteredRows = [];
    this.teamCellContent = new Map();
    this.isTeamTable = false;

    this.init();
  }

  init() {
    this.detectTableType();
    this.cacheRows();

    if (this.options.searchable && this.searchInput) {
      this.searchInput.addEventListener(
        "input",
        this.debounce(() => this.filterRows(), 300)
      );
    }

    if (this.options.sortable) {
      this.initSort();
    }

    if (this.options.filterable && this.filters.length > 0) {
      this.filters.forEach((filter) => {
        filter.addEventListener("change", () => this.filterRows());
      });
    }

    if (this.resetBtn) {
      this.resetBtn.addEventListener("click", () => this.resetFilters());
    }

    this.updateInfo();
  }

  detectTableType() {
    const firstRow = this.tbody.querySelector("tr");
    if (firstRow && firstRow.querySelector(".team-cell")) {
      this.isTeamTable = true;
    }
  }

  cacheRows() {
    const rows = Array.from(this.tbody.querySelectorAll("tr"));

    if (this.isTeamTable) {
      this.cacheTeamRows(rows);
    } else {
      this.cacheSimpleRows(rows);
    }

    this.filteredRows = [...this.allRows];
  }

  cacheTeamRows(rows) {
    let currentTeamCell = null;
    let currentTeamId = null;

    rows.forEach((row) => {
      const teamCell = row.querySelector(".team-cell");

      if (teamCell) {
        currentTeamCell = teamCell.cloneNode(true);
        currentTeamId = row.dataset.teamId;
        if (!this.teamCellContent.has(currentTeamId)) {
          this.teamCellContent.set(currentTeamId, currentTeamCell);
        }
      }

      const memberCells = Array.from(row.querySelectorAll("td")).map((cell) =>
        cell.cloneNode(true)
      );

      this.allRows.push({
        teamId: row.dataset.teamId || "",
        team: row.dataset.team || "",
        grade: row.dataset.grade || "",
        poste: row.dataset.poste || "",
        memberName: row.dataset.memberName || "",
        memberCells: memberCells,
        text: Array.from(row.querySelectorAll("td, th"))
          .map((cell) => cell.textContent.trim().toLowerCase())
          .join(" "),
      });
    });
  }

  cacheSimpleRows(rows) {
    rows.forEach((row) => {
      const cells = Array.from(row.querySelectorAll("td")).map((cell) => ({
        element: cell.cloneNode(true),
        sortValue: cell.dataset.sort || cell.textContent.trim(),
      }));

      const rowData = {
        cells: cells,
        text: cells
          .map((c) => c.element.textContent.trim().toLowerCase())
          .join(" "),
      };

      // Copier tous les data-* attributes
      Array.from(row.attributes).forEach((attr) => {
        if (attr.name.startsWith("data-")) {
          const key = attr.name.replace("data-", "");
          rowData[key] = attr.value;
        }
      });

      this.allRows.push(rowData);
    });
  }

  initSort() {
    const headers = this.table.querySelectorAll("thead th[data-column]");
    headers.forEach((header) => {
      header.addEventListener("click", () => {
        const column = parseInt(header.dataset.column);
        this.sortByColumn(column);
      });
    });
  }

  sortByColumn(column) {
    const direction =
      this.currentSort.column === column && this.currentSort.direction === "asc"
        ? "desc"
        : "asc";

    if (this.isTeamTable) {
      this.sortTeamTable(column, direction);
    } else {
      this.sortSimpleTable(column, direction);
    }

    this.currentSort = { column, direction };
    //this.updateSortIcons(column, direction);
    this.renderRows();
  }

  sortTeamTable(column, direction) {
    const teamGroups = this.groupByTeam(this.filteredRows);

    if (column === 0) {
      teamGroups.sort((a, b) => {
        const comparison = a.teamName.localeCompare(b.teamName, "fr");
        return direction === "asc" ? comparison : -comparison;
      });
    } else {
      teamGroups.forEach((group) => {
        group.rows.sort((a, b) => {
          let aValue, bValue;

          switch (column) {
            case 1:
              aValue = a.memberName;
              bValue = b.memberName;
              break;
            case 2:
              aValue = a.grade;
              bValue = b.grade;
              break;
            case 3:
              aValue = a.poste;
              bValue = b.poste;
              break;
            default:
              return 0;
          }

          const comparison = aValue.localeCompare(bValue, "fr");
          return direction === "asc" ? comparison : -comparison;
        });
      });
    }

    this.filteredRows = [];
    teamGroups.forEach((group) => {
      this.filteredRows.push(...group.rows);
    });
  }

  sortSimpleTable(column, direction) {
    this.filteredRows.sort((a, b) => {
      const aValue = a.cells[column]?.sortValue || "";
      const bValue = b.cells[column]?.sortValue || "";

      let comparison;
      if (!isNaN(aValue) && !isNaN(bValue)) {
        comparison = parseFloat(aValue) - parseFloat(bValue);
      } else {
        comparison = aValue.toString().localeCompare(bValue.toString(), "fr");
      }

      return direction === "asc" ? comparison : -comparison;
    });
  }

  /*updateSortIcons(activeColumn, direction) {
    const headers = this.table.querySelectorAll("thead th[data-column]");
    headers.forEach((header) => {
      const icon = header.querySelector(".sort-icon svg");
      if (!icon) return;

      if (parseInt(header.dataset.column) === activeColumn) {
        icon.style.transform =
          direction === "desc" ? "rotate(180deg)" : "rotate(0deg)";
        icon.style.opacity = "1";
      } else {
        icon.style.transform = "rotate(0deg)";
        icon.style.opacity = "0.5";
      }
    });
  }*/

  filterRows() {
    const searchTerm = this.searchInput
      ? this.searchInput.value.toLowerCase()
      : "";
    const activeFilters = {};

    if (this.filters.length > 0) {
      this.filters.forEach((filter) => {
        const column = filter.dataset.filter;
        const value = filter.value;
        if (value) {
          activeFilters[column] = value;
        }
      });
    }

    this.filteredRows = this.allRows.filter((row) => {
      if (searchTerm && !row.text.includes(searchTerm)) {
        return false;
      }

      for (const [column, value] of Object.entries(activeFilters)) {
        if (row[column] !== value) {
          return false;
        }
      }

      return true;
    });

    this.renderRows();
    this.updateInfo();
  }

  groupByTeam(rows) {
    const groups = new Map();

    rows.forEach((row) => {
      const teamId = row.teamId;
      const teamName = row.team;

      if (!groups.has(teamId)) {
        groups.set(teamId, {
          teamId: teamId,
          teamName: teamName,
          rows: [],
        });
      }

      groups.get(teamId).rows.push(row);
    });

    return Array.from(groups.values());
  }

  renderRows() {
    this.tbody.innerHTML = "";

    if (this.filteredRows.length === 0) {
      const colspan = this.table.querySelectorAll("thead th").length;
      this.tbody.innerHTML = `
        <tr>
          <td colspan="${colspan}" class="px-6 py-12 text-center text-gray-500">
            Aucun résultat ne correspond à vos critères de recherche.
          </td>
        </tr>
      `;
      return;
    }

    if (this.isTeamTable) {
      this.renderTeamRows();
    } else {
      this.renderSimpleRows();
    }
  }

  renderTeamRows() {
    const teamGroups = this.groupByTeam(this.filteredRows);

    teamGroups.forEach((group) => {
      const rows = group.rows;
      const rowCount = rows.length;

      rows.forEach((rowData, index) => {
        const tr = document.createElement("tr");
        tr.className = "border-b border-gray-200 hover:bg-blue-50 transition";
        tr.dataset.team = rowData.team;
        tr.dataset.grade = rowData.grade;
        tr.dataset.poste = rowData.poste;
        tr.dataset.teamId = rowData.teamId;
        tr.dataset.memberName = rowData.memberName;

        if (index === 0) {
          const teamCell = this.teamCellContent.get(rowData.teamId);
          if (teamCell) {
            const clonedTeamCell = teamCell.cloneNode(true);
            clonedTeamCell.setAttribute("rowspan", rowCount);
            tr.appendChild(clonedTeamCell);
          }
        }

        rowData.memberCells.forEach((cell) => {
          tr.appendChild(cell.cloneNode(true));
        });

        this.tbody.appendChild(tr);
      });
    });
  }

  renderSimpleRows() {
    this.filteredRows.forEach((rowData) => {
      const tr = document.createElement("tr");
      tr.className = "border-b border-gray-200 hover:bg-gray-50 transition";

      // Restaurer les data-* attributes
      Object.keys(rowData).forEach((key) => {
        if (key !== "cells" && key !== "text") {
          tr.dataset[key] = rowData[key];
        }
      });

      rowData.cells.forEach((cellData) => {
        tr.appendChild(cellData.element.cloneNode(true));
      });

      this.tbody.appendChild(tr);
    });
  }

  resetFilters() {
    if (this.searchInput) {
      this.searchInput.value = "";
    }

    this.filters.forEach((filter) => {
      filter.value = "";
    });

    this.currentSort = { column: null, direction: "asc" };
    this.filteredRows = [...this.allRows];

    const headers = this.table.querySelectorAll("thead th .sort-icon svg");
    headers.forEach((icon) => {
      icon.style.transform = "rotate(0deg)";
      icon.style.opacity = "0.5";
    });

    this.renderRows();
    this.updateInfo();
  }

  updateInfo() {
    if (this.tableInfo) {
      this.tableInfo.textContent = "";
    }
  }

  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }
}

document.addEventListener("DOMContentLoaded", function () {
  // L'initialisation sera faite par chaque vue
});
