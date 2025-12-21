<?php

class PublicationController extends Controller
{
    public function index()
    {
        $publicationModel = $this->model('Publication');
        
        $data = [
            'publications' => $publicationModel->getAll(),
            'types' => $publicationModel->getTypes(),
            'authors' => $publicationModel->getAllAuthors(),
            'years' => $publicationModel->getYears(),
            'domains' => $publicationModel->getDomains()
        ];
        
        $this->view('PublicationView', $data);
    }
}
?>