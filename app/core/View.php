<?php

abstract class View
{
    protected $data = [];
    protected $pageTitle = 'LMCS';
    
    public function __construct($data = [])
    {
        $this->data = $data;
    }
    
    abstract public function render();
    
    protected function escape($value)
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    protected function renderHeader()
    {
        require_once '../app/views/layouts/HeaderView.php';
        $headerView = new HeaderView(['pageTitle' => $this->pageTitle]);
        $headerView->render();
    }
    
    protected function renderFooter()
    {
        require_once '../app/views/layouts/FooterView.php';
        $footerView = new FooterView();
        $footerView->render();
    }

    protected function get($key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

}
?>