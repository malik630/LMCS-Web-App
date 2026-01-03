<?php

class ActualiteController extends Controller
{
    private $actualiteModel;
    
    public function __construct()
    {
        $this->actualiteModel = $this->model('Actualite');
    }
    
    public function index()
    {
        $actualites = $this->actualiteModel->getAll();
        $types = $this->actualiteModel->getTypes();
        
        $this->view('ActualiteView', [
            'actualites' => $actualites,
            'types' => $types
        ]);
    }
}
?>