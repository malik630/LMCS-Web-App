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
        
        foreach ($projets as &$projet) {
            $projet['membres'] = $this->projetModel->getMembers($projet['id_projet']);
            $projet['publications'] = $this->projetModel->getPublications($projet['id_projet']);
            $projet['partenaires'] = $this->projetModel->getPartenaires($projet['id_projet']);
        }
        
        $data = [
            'projets' => $projets,
            'thematiques' => $this->projetModel->getAllThematiques(),
            'responsables' => $this->projetModel->getAllResponsables(),
            'currentFilters' => $filters
        ];
        
        $this->view('ProjetView', $data);
    }
    
    public function details($id)
    {
        $projet = $this->projetModel->getById($id);
        
        if (!$projet) {
            $this->redirect('projet/index');
            return;
        }
        
        $data = [
            'projet' => $projet,
            'membres' => $this->projetModel->getMembers($id),
            'publications' => $this->projetModel->getPublications($id),
            'partenaires' => $this->projetModel->getPartenaires($id)
        ];
        
        $this->view('ProjetDetailsView', $data);
    }
}
?>