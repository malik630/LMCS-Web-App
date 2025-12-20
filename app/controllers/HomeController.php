<?php

class HomeController extends Controller
{
    public function index()
    {
        $actualiteModel = $this->model('Actualite');
        $eventModel = $this->model('Event');
        $partnerModel = $this->model('Partner');
        $userModel = $this->model('User');
        
        $data = [
            'slider' => $actualiteModel->getAllForSlider(),
            'actualites' => $actualiteModel->getRecent(6),
            'director' => $userModel->getDirector(),
            'chef_equipes' => $userModel->getChefEquipes(),
            'evenements' => $eventModel->getUpcoming(),
            'partenaires' => $partnerModel->getAll(),
        ];
        
        $this->view('HomeView', $data);
    }
}
?>