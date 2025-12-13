<?php

class HomeController extends Controller
{
    public function index()
    {
        $actualiteModel = $this->model('Actualite');
        $eventModel = $this->model('Event');
        $partnerModel = $this->model('Partner');
        $teamModel = $this->model('Team');
        
        $data = [
            'slider' => $actualiteModel->getAllForSlider(),
            'actualites' => $actualiteModel->getRecent(6),
            'evenements' => $eventModel->getUpcoming(),
            'partenaires' => $partnerModel->getAll(),
            'teams' => $teamModel->getAll()
        ];
        
        $this->view('HomeView', $data);
    }
}
?>