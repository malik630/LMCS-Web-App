<?php

class Controller
{
    protected function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }
    
    protected function view($viewClass, $data = [])
    {
        if (file_exists('../app/views/' . $viewClass . '.php')) {
            require_once '../app/views/' . $viewClass . '.php';
            $view = new $viewClass($data);
            $view->render();
        } else {
            die("View does not exist: " . $viewClass);
        }
    }
    
    protected function redirect($url)
    {
        header('Location: ' . BASE_URL . $url);
        exit;
    }
    
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>