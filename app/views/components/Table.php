<?php

class Table
{
    private $config = [];
    
    public function __construct(array $config)
    {
        $this->config = array_merge([
            'id' => 'data-table',
            'headers' => [],
            'data' => [],
            'searchable' => true,
            'sortable' => true,
            'filterable' => false,
            'filters' => [],
            'ajax_url' => null,
            'empty_message' => 'Aucune donnée disponible'
        ], $config);
    }
    
    public static function render(array $config)
    {
        $table = new self($config);
        $table->display();
    }
    
    private function display()
    {
        echo '<div class="table-container">';
        $this->renderControls();
        $this->renderTable();
        $this->renderScript();
        echo '</div>';
    }
    
    private function renderControls()
    {
        if (!$this->config['searchable'] && !$this->config['filterable']) {
            return;
        }
        
        ?>
<div class="table-controls mb-6 flex flex-wrap gap-4 items-end">
    <?php 
    $this->renderSearchBar();
    $this->renderFilters();
    $this->renderResetButton();
    ?>
</div>
<?php
    }
    
    private function renderSearchBar()
    {
        if (!$this->config['searchable']) {
            return;
        }
        
        ?>
<div class="flex-grow max-w-md">
    <label for="table-search" class="block text-sm font-medium text-gray-700 mb-2">
        Rechercher
    </label>
    <input type="text" id="table-search" placeholder="Rechercher dans le tableau..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
</div>
<?php
    }
    
    private function renderFilters()
    {
        if (!$this->config['filterable'] || empty($this->config['filters'])) {
            return;
        }
        
        foreach ($this->config['filters'] as $filter) {
            $this->renderSingleFilter($filter);
        }
    }
    
    private function renderSingleFilter(array $filter)
    {
        ?>
<div class="flex-shrink-0">
    <label for="filter-<?php echo htmlspecialchars($filter['id']); ?>"
        class="block text-sm font-medium text-gray-700 mb-2">
        <?php echo htmlspecialchars($filter['label']); ?>
    </label>
    <select id="filter-<?php echo htmlspecialchars($filter['id']); ?>"
        data-filter="<?php echo htmlspecialchars($filter['column']); ?>"
        class="table-filter px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <option value="">Tous</option>
        <?php foreach ($filter['options'] as $value => $label): ?>
        <option value="<?php echo htmlspecialchars($value); ?>">
            <?php echo htmlspecialchars($label); ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>
<?php
    }
    
    private function renderResetButton()
    {
        if (!$this->config['searchable'] && !$this->config['filterable']) {
            return;
        }
        
        ?>
<button id="reset-filters"
    class="flex-shrink-0 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
    Réinitialiser
</button>
<?php
    }
    
    private function renderTable()
    {
        ?>
<div class="overflow-x-auto shadow-lg rounded-lg">
    <table id="<?php echo htmlspecialchars($this->config['id']); ?>" class="w-full border-collapse bg-white">
        <?php 
        $this->renderTableHead();
        $this->renderTableBody();
        ?>
    </table>
</div>
<?php
    }
    
    private function renderTableHead()
    {
        ?>
<thead class="bg-black text-white">
    <tr>
        <?php foreach ($this->config['headers'] as $index => $header): ?>
        <?php $this->renderHeaderCell($header, $index); ?>
        <?php endforeach; ?>
    </tr>
</thead>
<?php
    }
    
    private function renderHeaderCell(array $header, int $index)
    {
        $sortableClass = $this->config['sortable'] 
            ? 'cursor-pointer hover:bg-blue-800 transition' 
            : '';
        
        $customClass = $header['class'] ?? '';
        
        ?>
<th scope="col" data-column="<?php echo $index; ?>"
    class="px-6 py-4 text-left font-semibold <?php echo $sortableClass; ?> <?php echo $customClass; ?>">
    <div class="flex items-center gap-2">
        <span><?php echo htmlspecialchars($header['label']); ?></span>
        <?php if ($this->config['sortable']): ?>
        <span class="sort-icon text-blue-200">
            <?php echo HtmlHelper::icon('sort', 'w-4 h-4'); ?>
        </span>
        <?php endif; ?>
    </div>
</th>
<?php
    }
    
    private function renderTableBody()
    {
        $bodyId = htmlspecialchars($this->config['id']) . '-body';
        
        ?>
<tbody id="<?php echo $bodyId; ?>">
    <?php if (!empty($this->config['data'])): ?>
    <?php echo $this->config['data']; ?>
    <?php else: ?>
    <?php $this->renderEmptyState(); ?>
    <?php endif; ?>
</tbody>
<?php
    }
    
    private function renderEmptyState()
    {
        $colspan = count($this->config['headers']);
        
        ?>
<tr>
    <td colspan="<?php echo $colspan; ?>" class="px-6 py-12 text-center text-gray-500">
        <?php echo HtmlHelper::emptyState($this->config['empty_message']); ?>
    </td>
</tr>
<?php
    }
    
    private function renderScript()
    {
        if (!$this->config['ajax_url']) {
            return;
        }
        
        ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const config = <?php echo json_encode([
        'tableId' => $this->config['id'],
        'ajaxUrl' => $this->config['ajax_url'],
        'searchable' => $this->config['searchable'],
        'sortable' => $this->config['sortable'],
        'filterable' => $this->config['filterable']
    ]); ?>;

    window.tableManager = window.tableManager || {};
    window.tableManager[config.tableId] = new TableManager(
        config.tableId,
        config.ajaxUrl, {
            searchable: config.searchable,
            sortable: config.sortable,
            filterable: config.filterable
        }
    );
});
</script>
<?php
    }
}
?>