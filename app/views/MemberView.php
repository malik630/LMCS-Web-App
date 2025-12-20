<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Section.php';

class MemberView extends View
{
    protected $pageTitle = 'Profil du Membre - LMCS';
    
    public function render()
    {
        $user = $this->get('user');
        
        if (!$user) {
            $this->renderError('Membre introuvable');
            return;
        }
        
        $this->pageTitle = $user['prenom'] . ' ' . $user['nom'] . ' - LMCS';
        
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderBiographieSection();
        $this->renderPublicationsSection();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderError($message)
    {
        echo '<div class="container mx-auto px-4 py-8">';
        echo '<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">';
        echo htmlspecialchars($message);
        echo '</div>';
        echo '</div>';
    }
    
    private function renderBiographieSection()
    {
        $user = $this->get('user');
        $teams = $this->get('teams', []);
        $projects = $this->get('projects', []);
        
        Section::create('Biographie et Informations', function() use ($user, $teams, $projects) {
            echo '<div class="space-y-6">';
            $this->renderUserHeader($user);
            $this->renderUserBio($user);
            $this->renderUserTeams($teams);
            $this->renderUserProjects($projects);
            echo '</div>';
        });
    }
    
    private function renderUserHeader(array $user)
    {
        ?>
<div class="flex flex-col md:flex-row gap-6 items-start">
    <div class="flex-shrink-0">
        <?php ImageHelper::renderUserPhoto($user, 32); ?>
    </div>
    <div class="flex-grow">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">
            <?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?>
        </h2>
        <?php 
        echo HtmlHelper::infoList([
            ['icon' => 'user', 'label' => 'Grade', 'value' => $user['grade']],
            ['icon' => 'email', 'label' => 'Email', 'value' => $user['email'] ?? null],
            ['label' => 'Domaine de recherche', 'value' => $user['domaine_recherche'] ?? null]
        ], 'space-y-2');
        ?>
    </div>
</div>
<?php
    }
    
    private function renderUserBio(array $user)
    {
        if (empty($user['biographie'])) return;
        
        ?>
<div class="border-t pt-6">
    <h3 class="text-xl font-bold text-gray-900 mb-3">À propos</h3>
    <div class="text-gray-700 leading-relaxed whitespace-pre-line">
        <?php echo nl2br($this->escape($user['biographie'])); ?>
    </div>
</div>
<?php
    }
    
    private function renderUserTeams(array $teams)
    {
        if (empty($teams)) return;
        
        ?>
<div class="border-t pt-6">
    <h3 class="text-xl font-bold text-gray-900 mb-3">Équipes de recherche</h3>
    <div class="grid md:grid-cols-2 gap-4">
        <?php foreach ($teams as $team): ?>
        <?php $this->renderTeamCard($team); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderTeamCard(array $team)
    {
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <h4 class="font-bold text-blue-700 mb-1">
        <?php echo $this->escape($team['nom']); ?>
    </h4>
    <?php if (!empty($team['role_dans_equipe'])): ?>
    <p class="text-sm text-gray-600 mb-2">
        Rôle: <?php echo $this->escape($team['role_dans_equipe']); ?>
    </p>
    <?php endif; ?>
    <?php if (!empty($team['thematique'])): ?>
    <p class="text-xs text-gray-500 italic">
        <?php echo $this->escape($team['thematique']); ?>
    </p>
    <?php endif; ?>
    <?php echo HtmlHelper::linkWithIcon(
        'Voir l\'équipe',
        BASE_URL . 'team/detail/' . $team['id_team'],
        'arrow-right',
        'text-blue-600 hover:text-blue-800 font-semibold text-xs mt-2 inline-flex'
    ); ?>
</div>
<?php
    }
    
    private function renderUserProjects(array $projects)
    {
        if (empty($projects)) return;
        
        ?>
<div class="border-t pt-6">
    <h3 class="text-xl font-bold text-gray-900 mb-3">Projets de recherche</h3>
    <div class="space-y-3">
        <?php foreach ($projects as $project): ?>
        <?php $this->renderProjectCard($project); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderProjectCard(array $project)
    {
        ?>
<div class="border-l-4 border-blue-600 pl-4 py-2 hover:bg-blue-50 transition">
    <h4 class="font-bold text-gray-900">
        <?php echo $this->escape($project['titre']); ?>
    </h4>
    <?php if (!empty($project['description'])): ?>
    <p class="text-sm text-gray-600 mt-1">
        <?php echo $this->escape(substr($project['description'], 0, 150)); ?>
        <?php echo strlen($project['description']) > 150 ? '...' : ''; ?>
    </p>
    <?php endif; ?>
    <?php if (!empty($project['role_projet'])): ?>
    <?php echo HtmlHelper::badge($project['role_projet'], 'primary'); ?>
    <?php endif; ?>
</div>
<?php
    }
    
    private function renderPublicationsSection()
    {
        $publications = $this->get('publications', []);
        
        Section::create('Publications', function() use ($publications) {
            if (empty($publications)) {
                echo HtmlHelper::emptyState('Aucune publication disponible pour ce membre.');
                return;
            }
            
            $this->renderPublicationsList($publications);
        });
    }
    
    private function renderPublicationsList(array $publications)
    {
        $byYear = $this->groupPublicationsByYear($publications);
        
        echo '<div class="space-y-8">';
        foreach ($byYear as $year => $pubs) {
            $this->renderYearGroup($year, $pubs);
        }
        echo '</div>';
    }
    
    private function groupPublicationsByYear(array $publications)
    {
        $byYear = [];
        foreach ($publications as $pub) {
            $year = $pub['annee'] ?? 'Non datée';
            $byYear[$year][] = $pub;
        }
        krsort($byYear);
        return $byYear;
    }
    
    private function renderYearGroup(string $year, array $publications)
    {
        ?>
<div>
    <h3 class="text-2xl font-bold text-blue-700 mb-4 border-b-2 border-blue-200 pb-2">
        <?php echo $this->escape($year); ?>
    </h3>
    <div class="space-y-4">
        <?php foreach ($publications as $pub): ?>
        <?php $this->renderPublicationCard($pub); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderPublicationCard(array $pub)
    {
        ?>
<div class="border-l-4 border-gray-300 pl-4 py-2 hover:border-blue-600 hover:bg-blue-50 transition">
    <div class="flex items-start justify-between gap-4">
        <div class="flex-grow">
            <h4 class="font-bold text-gray-900 mb-1">
                <?php echo $this->escape($pub['titre']); ?>
            </h4>

            <?php if (!empty($pub['auteurs'])): ?>
            <p class="text-sm text-gray-600 mb-1">
                <?php echo $this->escape($pub['auteurs']); ?>
            </p>
            <?php endif; ?>

            <?php $this->renderPublicationMeta($pub); ?>
            <?php $this->renderPublicationDOI($pub); ?>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderPublicationMeta(array $pub)
    {
        $metaItems = array_filter([
            !empty($pub['type_libelle']) ? HtmlHelper::badge($pub['type_libelle'], 'primary') : null,
            $pub['journal'] ?? null,
            !empty($pub['volume']) ? 'Vol. ' . $pub['volume'] : null,
            !empty($pub['pages']) ? 'pp. ' . $pub['pages'] : null
        ]);
        
        if (empty($metaItems)) return;
        
        ?>
<div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
    <?php foreach ($metaItems as $item): ?>
    <span><?php echo $item; ?></span>
    <?php endforeach; ?>
</div>
<?php
    }
    
    private function renderPublicationDOI(array $pub)
    {
        if (empty($pub['doi'])) return;
        
        ?>
<p class="text-xs text-gray-500 mt-1">
    DOI: <a href="https://doi.org/<?php echo $this->escape($pub['doi']); ?>" target="_blank"
        class="text-blue-600 hover:text-blue-800">
        <?php echo $this->escape($pub['doi']); ?>
    </a>
</p>
<?php
    }
}
?>