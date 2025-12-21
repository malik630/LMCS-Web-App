<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Section.php';

class TeamDetailView extends View
{
    public function render()
    {
        $team = $this->get('team');
        $this->pageTitle = $team['nom'] . ' - LMCS';
        
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderBackButton();
        $this->renderTeamHeader($team);
        $this->renderTeamDescription($team);
        $this->renderMembersSection();
        $this->renderPublicationsSection();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderBackButton()
    {
        echo '<div class="mb-6">';
        echo HtmlHelper::linkWithIcon(
            'Retour aux équipes',
            BASE_URL . 'team',
            'arrow-left',
            'text-white hover:text-gray-200 font-semibold'
        );
        echo '</div>';
    }
    
    private function renderTeamHeader(array $team)
    {
        ?>
<div class="bg-black rounded-xl shadow-2xl p-8 mb-8 text-white">
    <h1 class="text-4xl font-bold mb-4"><?php echo $this->escape($team['nom']); ?></h1>

    <?php if (!empty($team['thematique'])): ?>
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Thématique</h3>
        <p class="text-white text-xl"><?php echo $this->escape($team['thematique']); ?></p>
    </div>
    <?php endif; ?>

    <div class="flex items-center gap-2 px-4 py-2 bg-gray-800 rounded-full inline-flex">
        <?php echo HtmlHelper::icon('calendar'); ?>
        <span>Créée le: <?php echo DateHelper::format($team['date_creation'], 'd/m/Y'); ?></span>
    </div>
</div>
<?php
    }
    
    private function renderTeamDescription(array $team)
    {
        if (empty($team['description'])) return;
        
        Section::create('À propos de l\'équipe', function() use ($team) {
            ?>
<div class="prose max-w-none">
    <p class="text-gray-700 leading-relaxed whitespace-pre-line">
        <?php echo nl2br($this->escape($team['description'])); ?>
    </p>
</div>
<?php
        });
    }
    
    private function renderMembersSection()
    {
        $members = $this->get('members', []);
        $team = $this->get('team');
        
        Section::create('Membres de l\'équipe', function() use ($members, $team) {
            if (empty($members)) {
                echo HtmlHelper::emptyState('Aucun membre dans cette équipe.');
                return;
            }
            
            $grouped = $this->groupMembers($members, $team['chef_id']);
            
            if ($grouped['chef']) {
                $this->renderMemberCard($grouped['chef'], true);
            }
            
            if (!empty($grouped['others'])) {
                echo '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">';
                foreach ($grouped['others'] as $member) {
                    $this->renderMemberCard($member, false);
                }
                echo '</div>';
            }
        });
    }
    
    private function groupMembers(array $members, $chefId)
    {
        $chef = null;
        $others = [];
        
        foreach ($members as $member) {
            if ($member['id_user'] == $chefId) {
                $chef = $member;
            } else {
                $others[] = $member;
            }
        }
        
        return ['chef' => $chef, 'others' => $others];
    }
    
    private function renderMemberCard(array $member, bool $isChef)
    {
        $cardClass = $isChef 
            ? 'bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-400 rounded-xl shadow-xl p-8 mb-6' 
            : 'bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition';
        
        $photoSize = $isChef ? 32 : 24;
        
        ?>
<div class="<?php echo $cardClass; ?>">
    <div class="flex <?php echo $isChef ? 'flex-col md:flex-row' : 'flex-col'; ?> gap-6 items-center">
        <div class="flex-shrink-0">
            <?php ImageHelper::renderUserPhoto($member, $photoSize); ?>
        </div>

        <div class="flex-grow text-center <?php echo $isChef ? 'md:text-left' : ''; ?>">
            <?php if ($isChef): ?>
            <?php echo HtmlHelper::badge('Chef d\'équipe', 'warning'); ?>
            <div class="mt-2"></div>
            <?php endif; ?>

            <h3 class="text-xl font-bold text-gray-900 mb-2">
                <?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?>
            </h3>

            <?php 
            echo HtmlHelper::infoList([
                ['label' => 'Grade', 'value' => $member['grade']],
                ['label' => 'Rôle', 'value' => $member['role_dans_equipe'] ?? null],
                ['label' => 'Domaine', 'value' => $member['domaine_recherche'] ?? null]
            ], 'space-y-1 mb-4 ' . ($isChef ? '' : 'text-center'));
            ?>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderPublicationsSection()
    {
        $publications = $this->get('publications', []);
        
        Section::create('Publications de l\'équipe', function() use ($publications) {
            if (empty($publications)) {
                echo HtmlHelper::emptyState('Aucune publication pour cette équipe.');
                return;
            }
            
            echo '<div class="space-y-6">';
            foreach ($publications as $pub) {
                $this->renderPublicationCard($pub);
            }
            echo '</div>';
        });
    }
    
    private function renderPublicationCard(array $pub)
    {
        ?>
<div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition bg-white">
    <div class="flex items-start justify-between mb-3">
        <h3 class="text-lg font-bold text-gray-900 flex-grow">
            <?php echo $this->escape($pub['titre']); ?>
        </h3>
        <div class="flex-shrink-0 ml-4 flex gap-2">
            <?php echo HtmlHelper::badge($pub['type_libelle'] ?? 'Publication', 'primary'); ?>
            <?php echo HtmlHelper::badge($pub['annee'], 'info'); ?>
        </div>
    </div>

    <?php if (!empty($pub['auteurs'])): ?>
    <div class="mb-3 text-sm text-gray-600">
        <strong>Auteurs:</strong> <?php echo $this->escape($pub['auteurs']); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($pub['resume'])): ?>
    <p class="text-gray-600 mb-4 line-clamp-3">
        <?php echo $this->escape($pub['resume']); ?>
    </p>
    <?php endif; ?>

    <?php $this->renderPublicationMeta($pub); ?>
    <?php $this->renderPublicationActions($pub); ?>
</div>
<?php
    }
    
    private function renderPublicationMeta(array $pub)
    {
        $metaItems = array_filter([
            !empty($pub['domaine']) ? ['icon' => 'user', 'text' => 'Domaine: ' . $pub['domaine']] : null,
            !empty($pub['date_publication']) ? ['icon' => 'calendar', 'text' => DateHelper::format($pub['date_publication'], 'd/m/Y')] : null
        ]);
        
        if (empty($metaItems)) return;
        
        ?>
<div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
    <?php foreach ($metaItems as $item): ?>
    <div class="flex items-center gap-2">
        <?php echo HtmlHelper::icon($item['icon']); ?>
        <span><?php echo $item['text']; ?></span>
    </div>
    <?php endforeach; ?>
</div>
<?php
    }
    
    private function renderPublicationActions(array $pub)
    {
        ?>
<div class="flex gap-3">
    <?php echo HtmlHelper::linkWithIcon(
        'Voir les détails',
        BASE_URL . 'publication/index/',
        'arrow-right',
        'text-blue-600 hover:text-blue-800 font-semibold text-sm'
    ); ?>

    <?php if (!empty($pub['fichier_pdf'])): ?>
    <?php echo HtmlHelper::linkWithIcon(
            'Télécharger PDF',
            ASSETS_URL . 'documents/' . $pub['fichier_pdf'],
            'download',
            'text-green-600 hover:text-green-800 font-semibold text-sm'
        ); ?>
    <?php endif; ?>
</div>
<?php
    }
}
?>