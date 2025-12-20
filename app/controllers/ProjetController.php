<?php

class ProjetController extends Controller
{
    private $projetModel;
    
    public function __construct()
    {
        $this->projetModel = $this->model('Projet');
    }
    
    public function index()
    {
        $filters = [
            'thematique' => $_GET['thematique'] ?? null,
            'statut' => $_GET['statut'] ?? null,
            'responsable_id' => $_GET['responsable'] ?? null,
            'search' => $_GET['search'] ?? null
        ];
        $filters = array_filter($filters);
        
        $projets = empty($filters) 
            ? $this->projetModel->getAllWithDetails()
            : $this->projetModel->filterProjets($filters);
        
        $data = [
            'projets' => $projets,
            'thematiques' => $this->projetModel->getAllThematiques(),
            'responsables' => $this->projetModel->getAllResponsables(),
            'stats' => [
                'byThematique' => $this->projetModel->countByThematique(),
                'byStatut' => $this->projetModel->countByStatut()
            ],
            'currentFilters' => $filters
        ];
        
        $this->view('ProjetView', $data);
    }
}
?>