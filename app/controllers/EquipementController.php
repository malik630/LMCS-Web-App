<?php

class EquipementController extends Controller {
    public function index() {
        $equipementModel = $this->model('Equipement');
        $equipements = $equipementModel->getAll();
        $data = $equipements;
        $this->view('EquipementView', $data);
    }

}
?>