<?php

class Router
{
    private $controller = 'HomeController';
    private $method = 'index';
    private $params = [];
    private $routeMapping = [

        // Dashboard des utilisateurs
        'dashboardpublication/index' => ['DashboardPublicationController', 'index'],
        'dashboardpublication/create' => ['DashboardPublicationController', 'create'],
        'dashboardpublication/store' => ['DashboardPublicationController', 'store'],
        'dashboardpublication/edit' => ['DashboardPublicationController', 'edit'],
        'dashboardpublication/update' => ['DashboardPublicationController', 'update'],
        'dashboardpublication/delete' => ['DashboardPublicationController', 'delete'],
        'dashboardpublication/submitForApproval' => ['DashboardPublicationController', 'submitForApproval'],

        'dashboardprojet/index' => ['DashboardProjetController', 'index'],
        'dashboardprojet/detail' => ['DashboardProjetController', 'detail'],
        'dashboardprojet/create' => ['DashboardProjetController', 'create'],
        'dashboardprojet/store' => ['DashboardProjetController', 'store'],
        'dashboardprojet/edit' => ['DashboardProjetController', 'edit'],
        'dashboardprojet/update' => ['DashboardProjetController', 'update'],
        'dashboardprojet/close' => ['DashboardProjetController', 'close'],
        'dashboardprojet/addMember' => ['DashboardProjetController', 'addMember'],
        'dashboardprojet/removeMember' => ['DashboardProjetController', 'removeMember'],

        'dashboardstats/index' => ['DashboardStatsController', 'index'],
        'dashboardstats/rapportPublications' => ['DashboardStatsController', 'rapportPublications'],
        'dashboardstats/rapportProjets' => ['DashboardStatsController', 'rapportProjets'],
        'dashboardstats/rapportReservations' => ['DashboardStatsController', 'rapportReservations'],
        'dashboardstats/rapportComplet' => ['DashboardStatsController', 'rapportComplet'],

        'dashboardteam/index' => ['DashboardTeamController', 'index'],
        'dashboardteam/detail' => ['DashboardTeamController', 'detail'],
        'dashboardteam/associateProjet' => ['DashboardTeamController', 'associateProjet'],
        'dashboardteam/associatePublication' => ['DashboardTeamController', 'associatePublication'],
        'dashboardteam/rapportEquipe' => ['DashboardTeamController', 'rapportEquipe'],
 

        // Les pages admin
        'admin/users' => ['AdminUserController', 'index'],
        'admin/createUser' => ['AdminUserController', 'create'],
        'admin/storeUser' => ['AdminUserController', 'store'],
        'admin/editUser' => ['AdminUserController', 'edit'],
        'admin/updateUser' => ['AdminUserController', 'update'],
        'admin/suspendUser' => ['AdminUserController', 'suspend'],
        'admin/activateUser' => ['AdminUserController', 'activate'],
        'admin/deleteUser' => ['AdminUserController', 'delete'],
        'admin/userPermissions' => ['AdminUserController', 'permissions'],
        'admin/updateUserPermissions' => ['AdminUserController', 'updatePermissions'],
        
        'admin/projets' => ['AdminProjetController', 'index'],
        'admin/createProjet' => ['AdminProjetController', 'create'],
        'admin/storeProjet' => ['AdminProjetController', 'store'],
        'admin/editProjet' => ['AdminProjetController', 'edit'],
        'admin/updateProjet' => ['AdminProjetController', 'update'],
        'admin/deleteProjet' => ['AdminProjetController', 'delete'],
        'admin/manageProjetMembers' => ['AdminProjetController', 'manageMembers'],
        'admin/addProjetMember' => ['AdminProjetController', 'addMember'],
        'admin/removeProjetMember' => ['AdminProjetController', 'removeMember'],
        'admin/addProjetPartenaire' => ['AdminProjetController', 'addPartenaire'],
        'admin/removeProjetPartenaire' => ['AdminProjetController', 'removePartenaire'],
        'admin/rapportProjetsPDF' => ['AdminProjetController', 'rapportPDF'],
        
        'admin/equipes' => ['AdminTeamController', 'index'],
        'admin/createTeam' => ['AdminTeamController', 'create'],
        'admin/storeTeam' => ['AdminTeamController', 'store'],
        'admin/editTeam' => ['AdminTeamController', 'edit'],
        'admin/updateTeam' => ['AdminTeamController', 'update'],
        'admin/deleteTeam' => ['AdminTeamController', 'delete'],
        'admin/manageTeamMembers' => ['AdminTeamController', 'manageMembers'],
        'admin/addMember' => ['AdminTeamController', 'addMember'],
        'admin/removeMember' => ['AdminTeamController', 'removeMember'],
        'admin/updateMemberRole' => ['AdminTeamController', 'updateMemberRole'],

        'admin/publications' => ['AdminPublicationController', 'publications'],
        'admin/publication/create' => ['AdminPublicationController', 'create'],
        'admin/publication/store' => ['AdminPublicationController', 'store'],
        'admin/publication/edit' => ['AdminPublicationController', 'edit'],
        'admin/publication/update' => ['AdminPublicationController', 'update'],
        'admin/publication/publish' => ['AdminPublicationController', 'publish'],
        'admin/publication/reject' => ['AdminPublicationController', 'reject'],
        'admin/publication/delete' => ['AdminPublicationController', 'delete'],
        'admin/publication/rapports' => ['AdminPublicationController', 'rapports'],
        'admin/publication/generateRapport' => ['AdminPublicationController', 'generateRapport'],

        'admin/equipements' => ['AdminEquipementController', 'index'],
        'admin/createEquipement' => ['AdminEquipementController', 'create'],
        'admin/storeEquipement' => ['AdminEquipementController', 'store'],
        'admin/editEquipement' => ['AdminEquipementController', 'edit'],
        'admin/updateEquipement' => ['AdminEquipementController', 'update'],
        'admin/deleteEquipement' => ['AdminEquipementController', 'delete'],
        'admin/rapportsEquipements' => ['AdminEquipementController', 'rapports'],
        'admin/historiqueEquipements' => ['AdminEquipementController', 'historique'],
        'admin/exportEquipementsPDF' => ['AdminEquipementController', 'exportPDF'],

        'admin/reservations' => ['AdminReservationController', 'index'],
        'admin/confirmerReservation' => ['AdminReservationController', 'confirmer'],
        'admin/rejeterReservation' => ['AdminReservationController', 'rejeter'],
        'admin/annulerReservation' => ['AdminReservationController', 'annuler'],
        'admin/detailsReservation' => ['AdminReservationController', 'details'],

        'admin/parametres' => ['AdminSettingsController', 'index'],
        'admin/updateGeneral' => ['AdminSettingsController', 'updateGeneral'],
        'admin/updateTheme' => ['AdminSettingsController', 'updateTheme'],
        'admin/uploadLogo' => ['AdminSettingsController', 'uploadLogo'],
        'admin/backupDatabase' => ['AdminSettingsController', 'backupDatabase'],
        'admin/downloadBackup' => ['AdminSettingsController', 'downloadBackup'],
        'admin/restoreDatabase' => ['AdminSettingsController', 'restoreDatabase'],
        'admin/deleteBackup' => ['AdminSettingsController', 'deleteBackup'],
        'admin/listBackups' => ['AdminSettingsController', 'listBackups'],
        
        'admin/permissions' => ['AdminPermissionController', 'index'],
        'admin/updatePermissions' => ['AdminPermissionController', 'update']
    ];
    
    public function __construct()
    {
        $url = $this->parseUrl();
        $route = $this->buildRoute($url);

        if (isset($this->routeMapping[$route])) {
            list($controllerClass, $methodName) = $this->routeMapping[$route];      
            require_once '../app/controllers/' . $controllerClass . '.php';
            $this->controller = new $controllerClass;
            $this->method = $methodName;
            $routeParts = explode('/', $route);
            $urlParts = $url ?: [];
            $this->params = array_slice($urlParts, count($routeParts));
            
        } else {
            if (isset($url[0]) && file_exists('../app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
                $this->controller = ucfirst($url[0]) . 'Controller';
                unset($url[0]);
            }
       
            require_once '../app/controllers/' . $this->controller . '.php';
            $this->controller = new $this->controller;
            
            if (isset($url[1])) {
                if (method_exists($this->controller, $url[1])) {
                    $this->method = $url[1];
                    unset($url[1]);
                }
            }
            
            $this->params = $url ? array_values($url) : [];
        }
        
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', rtrim($_GET['url'], '/'));
        }
        return [];
    }
    
    private function buildRoute($url)
    {
        if (empty($url)) {
            return '';
        }
        $route = $url[0];
        if (isset($url[1])) {
            $route .= '/' . $url[1];
        }
        
        return $route;
    }
}
?>