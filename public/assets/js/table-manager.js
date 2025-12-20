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

    this.init();
  }

  init() {
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

  cacheRows() {
    const rows = Array.from(this.tbody.querySelectorAll("tr"));
    let currentTeamCell = null;
    let currentTeamId = null;

    rows.forEach((row) => {
      const teamCell = row.querySelector(".team-cell");

      // Si cette ligne a une cellule d'équipe, on la sauvegarde
      if (teamCell) {
        currentTeamCell = teamCell.cloneNode(true);
        currentTeamId = row.dataset.teamId;
        // Sauvegarder le contenu de la cellule d'équipe par teamId
        if (!this.teamCellContent.has(currentTeamId)) {
          this.teamCellContent.set(currentTeamId, currentTeamCell);
        }
      }

      // Extraire seulement les cellules qui ne sont pas .team-cell
      const memberCells = Array.from(row.querySelectorAll("td")).map((cell) =>
        cell.cloneNode(true)
      );

      this.allRows.push({
        teamId: row.dataset.teamId || "",
        team: row.dataset.team || "",
        grade: row.dataset.grade || "",
        poste: row.dataset.poste || "",
        memberName: row.dataset.memberName || "",
        memberCells: memberCells, // Les 4 cellules : Membre, Grade, Poste, Action
        text: Array.from(row.querySelectorAll("td, th"))
          .map((cell) => cell.textContent.trim().toLowerCase())
          .join(" "),
      });
    });

    this.filteredRows = [...this.allRows];
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

    // Grouper par équipe
    const teamGroups = this.groupByTeam(this.filteredRows);

    // Trier les groupes d'équipes si on trie par la colonne équipe (0)
    if (column === 0) {
      teamGroups.sort((a, b) => {
        const comparison = a.teamName.localeCompare(b.teamName, "fr");
        return direction === "asc" ? comparison : -comparison;
      });
    } else {
      // Sinon, trier les membres dans chaque groupe
      teamGroups.forEach((group) => {
        group.rows.sort((a, b) => {
          let aValue, bValue;

          switch (column) {
            case 1: // Membre
              aValue = a.memberName;
              bValue = b.memberName;
              break;
            case 2: // Grade
              aValue = a.grade;
              bValue = b.grade;
              break;
            case 3: // Poste
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

    // Reconstituer filteredRows
    this.filteredRows = [];
    teamGroups.forEach((group) => {
      this.filteredRows.push(...group.rows);
    });

    this.currentSort = { column, direction };
    this.updateSortIcons(column, direction);
    this.renderRows();
  }

  updateSortIcons(activeColumn, direction) {
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
  }

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
      // Recherche textuelle
      if (searchTerm && !row.text.includes(searchTerm)) {
        return false;
      }

      // Filtres
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
    // Vider le tbody
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

    // Grouper par équipe
    const teamGroups = this.groupByTeam(this.filteredRows);

    // Reconstruire les lignes avec les bons rowspans
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

        // Pour la première ligne du groupe, ajouter la cellule d'équipe avec rowspan
        if (index === 0) {
          const teamCell = this.teamCellContent.get(rowData.teamId);
          if (teamCell) {
            const clonedTeamCell = teamCell.cloneNode(true);
            clonedTeamCell.setAttribute("rowspan", rowCount);
            tr.appendChild(clonedTeamCell);
          }
        }

        // Ajouter les cellules du membre (Membre, Grade, Poste, Action)
        rowData.memberCells.forEach((cell) => {
          tr.appendChild(cell.cloneNode(true));
        });

        this.tbody.appendChild(tr);
      });
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
    // Info désactivée
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

// Initialisation automatique
document.addEventListener("DOMContentLoaded", function () {
  // Le gestionnaire sera initialisé par le composant Table.php
});
