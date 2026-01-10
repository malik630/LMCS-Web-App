<?php

require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once __DIR__ . '/components/PageHeader.php';
require_once __DIR__ . '/components/Section.php';
require_once __DIR__ . '/components/InfoGrid.php';

class DashboardProjetDetailView extends View
{
    protected $pageTitle = 'Détails du Projet - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        $projet = $this->get('projet');
        $membres = $this->get('membres');
        $publications = $this->get('publications');
        $partenaires = $this->get('partenaires');
        $isResponsable = $this->get('is_responsable');
        
        // Page header
        $badges = [
            [
                'text' => [
                    'en_cours' => 'En cours',
                    'termine' => 'Terminé',
                    'soumis' => 'Soumis'
                ][$projet['statut']] ?? $projet['statut'],
                'type' => [
                    'en_cours' => 'primary',
                    'termine' => 'success',
                    'soumis' => 'warning'
                ][$projet['statut']] ?? 'info'
            ]
        ];
        
        if ($isResponsable) {
            $badges[] = ['text' => 'Vous êtes responsable', 'type' => 'success'];
        }
        
        $actions = [];
        
        if ($isResponsable) {
            $actions[] = [
                'url' => BASE_URL . 'dashboardprojet/edit/' . $projet['id_projet'],
                'text' => 'Modifier',
                'type' => 'primary',
                'icon' => 'edit'
            ];
            
            if ($projet['statut'] == 'en_cours') {
                $actions[] = [
                    'url' => BASE_URL . 'dashboardprojet/close/' . $projet['id_projet'],
                    'text' => 'Clôturer',
                    'type' => 'warning',
                    'icon' => 'check',
                    'onclick' => 'return confirm(\'Êtes-vous sûr de vouloir clôturer ce projet ?\')'
                ];
            }
        }
        
        PageHeader::render([
            'title' => $projet['titre'],
            'back_link' => [
                'url' => BASE_URL . 'dashboardprojet/index',
                'text' => 'Retour à mes projets'
            ],
            'badges' => $badges,
            'actions' => $actions
        ]);
        
        // Informations générales
        Section::create('Informations Générales', function() use ($projet) {
            $items = array_filter([
                ['label' => 'Responsable', 'value' => $projet['responsable_prenom'] . ' ' . $projet['responsable_nom']],
                ['label' => 'Thématique', 'value' => $projet['thematique'] ?? null],
                ['label' => 'Type de financement', 'value' => $projet['type_financement'] ?? null],
                ['label' => 'Budget', 'value' => $projet['budget'] ?? null, 'format' => 'currency'],
                ['label' => 'Date de début', 'value' => $projet['date_debut'] ?? null, 'format' => 'date'],
                ['label' => 'Date de fin', 'value' => $projet['date_fin'] ?? null, 'format' => 'date']
            ], fn($item) => !empty($item['value']));
            
            InfoGrid::renderWithDescription($items, $projet['description'] ?? null);
        }, 'bg-white');
        
        // Membres
        $this->renderMembresSection($membres, $isResponsable, $projet);
        
        // Publications
        $this->renderPublicationsSection($publications);
        
        // Partenaires
        if (!empty($partenaires)) {
            $this->renderPartenairesSection($partenaires);
        }
        
        PageHeader::close();
        $this->renderFooter();
    }
    
    private function renderMembresSection($membres, $isResponsable, $projet)
    {
        Section::create('Membres du projet (' . count($membres) . ')', function() use ($membres, $isResponsable, $projet) {
            if (empty($membres)) {
                echo HtmlHelper::emptyState('Aucun membre dans ce projet');
            } else {
                echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">';
                foreach ($membres as $membre) {
                    $this->renderMembreCard($membre, $isResponsable, $projet);
                }
                echo '</div>';
            }
            
            if ($isResponsable) {
                $this->renderAddMembreForm($projet, $membres);
            }
        }, 'bg-white');
    }
    
    private function renderMembreCard($membre, $isResponsable, $projet)
    {
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <div class="flex items-center gap-3 mb-3">
        <?php if (!empty($membre['photo'])): ?>
        <?php echo ImageHelper::renderUserPhoto($membre); ?>
        <?php endif; ?>
        <div class="flex-1">
            <h4 class="font-semibold text-gray-900">
                <?php echo $this->escape($membre['prenom'] . ' ' . $membre['nom']); ?>
            </h4>
            <p class="text-sm text-gray-600"><?php echo $this->escape($membre['grade']); ?></p>
        </div>
    </div>
    <?php if (!empty($membre['role_projet'])): ?>
    <p class="text-sm text-gray-600">
        <span class="font-semibold">Rôle:</span> <?php echo $this->escape($membre['role_projet']); ?>
    </p>
    <?php endif; ?>
    <?php if ($isResponsable && $membre['id_user'] != $projet['responsable_id']): ?>
    <div class="mt-2">
        <a href="<?php echo BASE_URL; ?>dashboardprojet/removeMember/<?php echo $projet['id_projet']; ?>/<?php echo $membre['id_user']; ?>"
            onclick="return confirm('Retirer ce membre du projet ?')" class="text-sm text-red-600 hover:text-red-800">
            Retirer
        </a>
    </div>
    <?php endif; ?>
</div>
<?php
    }
    
    private function renderAddMembreForm($projet, $membres)
    {
        $availableMembers = $this->get('available_members');
        $membreIds = array_column($membres, 'id_user');
        $options = [];
        
        foreach ($availableMembers as $user) {
            if (!in_array($user['id_user'], $membreIds)) {
                $options[$user['id_user']] = $user['prenom'] . ' ' . $user['nom'] . ' - ' . $user['grade'];
            }
        }
        
        ?>
<div class="mt-6 p-4 bg-blue-50 rounded-lg">
    <h4 class="font-semibold text-gray-900 mb-3">Ajouter un membre</h4>
    <form action="<?php echo BASE_URL; ?>dashboardprojet/addMember/<?php echo $projet['id_projet']; ?>" method="POST"
        class="flex gap-3">
        <select name="membre_id" required class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">-- Sélectionner un membre --</option>
            <?php foreach ($options as $id => $label): ?>
            <option value="<?php echo $id; ?>"><?php echo $this->escape($label); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="role_projet" placeholder="Rôle (optionnel)"
            class="px-4 py-2 border border-gray-300 rounded-lg">
        <button type="submit"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
            Ajouter
        </button>
    </form>
</div>
<?php
    }
    
    private function renderPublicationsSection($publications)
    {
        Section::create('Publications liées (' . count($publications) . ')', function() use ($publications) {
            if (empty($publications)) {
                echo HtmlHelper::emptyState('Aucune publication liée à ce projet');
            } else {
                echo '<div class="space-y-4">';
                foreach ($publications as $pub) {
                    $this->renderPublicationCard($pub);
                }
                echo '</div>';
            }
        }, 'bg-white');
    }
    
    private function renderPublicationCard($pub)
    {
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <div class="flex justify-between items-start mb-2">
        <h4 class="font-bold text-gray-900"><?php echo $this->escape($pub['titre']); ?></h4>
        <span class="text-sm font-semibold text-gray-600"><?php echo $pub['annee']; ?></span>
    </div>
    <p class="text-sm text-gray-600 mb-2">
        <span class="font-semibold">Type:</span>
        <?php echo $this->escape($pub['type_libelle'] ?? 'Non spécifié'); ?>
    </p>
    <?php if (!empty($pub['auteurs'])): ?>
    <p class="text-sm text-gray-600">
        <span class="font-semibold">Auteurs:</span> <?php echo $this->escape($pub['auteurs']); ?>
    </p>
    <?php endif; ?>
</div>
<?php
    }
    
    private function renderPartenairesSection($partenaires)
    {
        Section::create('Partenaires (' . count($partenaires) . ')', function() use ($partenaires) {
            echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">';
            foreach ($partenaires as $part) {
                $this->renderPartenaireCard($part);
            }
            echo '</div>';
        }, 'bg-white');
    }
    
    private function renderPartenaireCard($part)
    {
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <?php if (!empty($part['logo'])): ?>
    <img src="<?php echo BASE_URL; ?>assets/images/partenaires/<?php echo $this->escape($part['logo']); ?>" alt="Logo"
        class="h-16 object-contain mb-3">
    <?php endif; ?>
    <h4 class="font-semibold text-gray-900 mb-1"><?php echo $this->escape($part['nom']); ?></h4>
    <p class="text-sm text-gray-600"><?php echo $this->escape(ucfirst($part['type'])); ?></p>
    <?php if (!empty($part['pays'])): ?>
    <p class="text-sm text-gray-600"><?php echo $this->escape($part['pays']); ?></p>
    <?php endif; ?>
</div>
<?php
    }
}
?>