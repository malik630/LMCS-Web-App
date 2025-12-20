class FilterSortSearch {
  constructor(config) {
    this.config = {
      searchInput: config.searchInput || "#search-input",
      filterSelects: config.filterSelects || ".filter-select",
      sortSelect: config.sortSelect || "#sort-select",
      resetButton: config.resetButton || "#reset-btn",
      itemsContainer: config.itemsContainer || "#items-container",
      itemSelector: config.itemSelector || ".item-card",
      searchFields: config.searchFields || ["data-title"],
      filterFields: config.filterFields || {},
      sortFunction: config.sortFunction || this.defaultSort.bind(this),
      emptyMessage:
        config.emptyMessage || "Aucun résultat ne correspond à vos critères.",
      onUpdate: config.onUpdate || null,
    };

    this.searchInput = document.querySelector(this.config.searchInput);
    this.filterSelects = document.querySelectorAll(this.config.filterSelects);
    this.sortSelect = document.querySelector(this.config.sortSelect);
    this.resetButton = document.querySelector(this.config.resetButton);
    this.itemsContainer = document.querySelector(this.config.itemsContainer);

    this.allItems = [];
    this.filteredItems = [];

    this.init();
  }

  init() {
    // Collecter tous les items
    this.cacheItems();

    // Événements
    if (this.searchInput) {
      this.searchInput.addEventListener(
        "input",
        this.debounce(() => this.applyFilters(), 300)
      );
    }

    if (this.filterSelects.length > 0) {
      this.filterSelects.forEach((select) => {
        select.addEventListener("change", () => this.applyFilters());
      });
    }

    if (this.sortSelect) {
      this.sortSelect.addEventListener("change", () => this.applyFilters());
    }

    if (this.resetButton) {
      this.resetButton.addEventListener("click", () => this.reset());
    }
  }

  cacheItems() {
    this.allItems = Array.from(
      this.itemsContainer.querySelectorAll(this.config.itemSelector)
    );
    this.filteredItems = [...this.allItems];
  }

  applyFilters() {
    let items = [...this.allItems];

    // Recherche textuelle
    const searchTerm = this.searchInput
      ? this.searchInput.value.toLowerCase().trim()
      : "";
    if (searchTerm) {
      items = items.filter((item) => {
        return this.config.searchFields.some((field) => {
          const value = item.getAttribute(field) || "";
          return value.toLowerCase().includes(searchTerm);
        });
      });
    }

    // Filtres
    if (this.filterSelects.length > 0) {
      this.filterSelects.forEach((select) => {
        const filterValue = select.value;
        if (filterValue) {
          const fieldName = this.config.filterFields[`#${select.id}`];
          if (fieldName) {
            items = items.filter(
              (item) => item.getAttribute(fieldName) === filterValue
            );
          }
        }
      });
    }

    // Tri
    if (this.sortSelect) {
      const sortValue = this.sortSelect.value;
      items = this.config.sortFunction(items, sortValue);
    }

    this.filteredItems = items;
    this.renderItems();
    this.updateCounts();

    // Callback personnalisé
    if (this.config.onUpdate) {
      this.config.onUpdate(this.filteredItems);
    }
  }

  renderItems() {
    // Masquer tous les items
    this.allItems.forEach((item) => (item.style.display = "none"));

    if (this.filteredItems.length === 0) {
      this.showEmptyState();
      return;
    }

    // Afficher les items filtrés
    this.filteredItems.forEach((item) => {
      item.style.display = "";
    });

    this.hideEmptyState();
  }

  updateCounts() {
    // Mettre à jour les compteurs par section (thématique, catégorie, etc.)
    const sections = this.itemsContainer.querySelectorAll(
      ".thematique-section, .category-section"
    );

    sections.forEach((section) => {
      const visibleItems = Array.from(
        section.querySelectorAll(this.config.itemSelector)
      ).filter((item) => item.style.display !== "none");

      const countElement = section.querySelector(
        ".thematique-count, .category-count"
      );
      if (countElement) {
        const count = visibleItems.length;
        countElement.textContent = `(${count} ${
          count > 1 ? "éléments" : "élément"
        })`;
      }

      // Masquer la section si vide
      section.style.display = visibleItems.length > 0 ? "" : "none";
    });
  }

  showEmptyState() {
    let emptyDiv = document.getElementById("filter-empty-state");
    if (!emptyDiv) {
      emptyDiv = document.createElement("div");
      emptyDiv.id = "filter-empty-state";
      emptyDiv.className = "bg-white rounded-lg shadow-lg p-12 text-center";
      emptyDiv.innerHTML = `
        <p class="text-lg font-semibold mb-2 text-gray-700">${this.config.emptyMessage}</p>
        <button onclick="window.filterSortSearch?.reset()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
          Réinitialiser les filtres
        </button>
      `;
      this.itemsContainer.appendChild(emptyDiv);
    }
    emptyDiv.style.display = "block";
  }

  hideEmptyState() {
    const emptyDiv = document.getElementById("filter-empty-state");
    if (emptyDiv) {
      emptyDiv.style.display = "none";
    }
  }

  reset() {
    if (this.searchInput) this.searchInput.value = "";
    if (this.filterSelects.length > 0) {
      this.filterSelects.forEach((select) => (select.value = ""));
    }
    if (this.sortSelect)
      this.sortSelect.value = this.sortSelect.options[0].value;

    this.filteredItems = [...this.allItems];
    this.renderItems();
    this.updateCounts();

    if (this.config.onUpdate) {
      this.config.onUpdate(this.filteredItems);
    }
  }

  defaultSort(items, sortValue) {
    // Tri par défaut (peut être surchargé)
    return items;
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

  // Méthode pour rafraîchir les items (si le DOM change)
  refresh() {
    this.cacheItems();
    this.applyFilters();
  }
}

// Export pour utilisation globale
window.FilterSortSearch = FilterSortSearch;
