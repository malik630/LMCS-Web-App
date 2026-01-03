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
    this.cacheItems();

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

    if (this.sortSelect) {
      const sortValue = this.sortSelect.value;
      items = this.config.sortFunction(items, sortValue);
    }

    this.filteredItems = items;
    this.renderItems();
    this.updateCounts();

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

    this.hideEmptyState();

    const container =
      this.itemsContainer.querySelector(".space-y-6") || this.itemsContainer;
    this.filteredItems.forEach((item) => {
      item.style.display = "";
      container.appendChild(item);
    });
  }

  updateCounts() {
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
        <div class="flex flex-col items-center gap-4">
          <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="text-lg font-semibold text-gray-700">${this.config.emptyMessage}</p>
          <button onclick="window.filterSortSearch?.reset()" class="mt-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Réinitialiser les filtres
          </button>
        </div>
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
    const authorSelect = document.getElementById("filter-author");
    if (authorSelect) {
      authorSelect.selectedIndex = -1;
    }

    this.filteredItems = [...this.allItems];
    this.renderItems();
    this.updateCounts();

    if (this.config.onUpdate) {
      this.config.onUpdate(this.filteredItems);
    }
  }

  defaultSort(items, sortValue) {
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

  refresh() {
    this.cacheItems();
    this.applyFilters();
  }
}

window.FilterSortSearch = FilterSortSearch;
