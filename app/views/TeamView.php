<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once __DIR__ . '/../helpers/ImageHelper.php';
require_once 'components/Section.php';
require_once 'components/Table.php';

class TeamView extends View
{
    protected $pageTitle = 'Présentation, Organigramme et Équipes - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        $this->renderPresentationSection();
        $this->renderOrganigrammeSection();
        $this->renderEquipesSection();
        echo '</div>';
        $this->renderFooter();
    }
    
    private function renderPresentationSection()
    {
        Section::create('Présentation du Laboratoire', function() {
            $this->renderPresentationContent();
        });
    }
    
    private function renderPresentationContent()
    {
        ?>
<div class="prose max-w-none">
    <div class="grid md:grid-cols-2 gap-8">
        <?php 
        $this->renderMissionBlock();
        $this->renderThematiquesBlock();
        ?>
    </div>
    <?php $this->renderVisionBlock(); ?>
</div>
<?php
    }
    
    private function renderMissionBlock()
    {
        ?>
<div>
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Notre Mission</h3>
    <p class="text-gray-700 mb-4 leading-relaxed">
        Le Laboratoire de Méthodes de Conception des Systèmes (LMCS) de l'École Supérieure d'Informatique
        (ESI) est un centre d'excellence dédié à la recherche fondamentale et appliquée dans les domaines de
        l'informatique et des technologies de l'information.
    </p>
    <p class="text-gray-700 mb-4 leading-relaxed">
        Fondé avec pour mission de contribuer à l'avancement des connaissances scientifiques et
        technologiques, le LMCS se concentre sur plusieurs axes de recherche stratégiques incluant
        l'intelligence artificielle, la sécurité informatique, les systèmes distribués, le génie logiciel et
        les réseaux de nouvelle génération.
    </p>
</div>
<?php
    }
    
    private function renderThematiquesBlock()
    {
        $thematiques = [
            [
                'titre' => 'Intelligence Artificielle & Apprentissage Automatique',
                'description' => 'Développement d\'algorithmes innovants pour le traitement de données complexes'
            ],
            [
                'titre' => 'Sécurité & Cybersécurité',
                'description' => 'Protection des systèmes d\'information et cryptographie avancée'
            ],
            [
                'titre' => 'Systèmes Distribués & Cloud Computing',
                'description' => 'Architecture et optimisation des systèmes à grande échelle'
            ],
            [
                'titre' => 'Génie Logiciel',
                'description' => 'Méthodes formelles et outils pour le développement logiciel'
            ],
            [
                'titre' => 'Réseaux & IoT',
                'description' => 'Internet des objets et communications de nouvelle génération'
            ]
        ];
        
        ?>
<div>
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Nos Thématiques de Recherche</h3>
    <ul class="space-y-3 text-gray-700">
        <?php foreach ($thematiques as $thematique): ?>
        <li class="flex items-start gap-3">
            <span class="text-blue-600 font-bold">•</span>
            <span>
                <strong><?php echo $this->escape($thematique['titre']); ?>:</strong>
                <?php echo $this->escape($thematique['description']); ?>
            </span>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
    }
    
    private function renderVisionBlock()
    {
        ?>
<div class="mt-8 p-6 bg-blue-50 rounded-lg border-l-4 border-blue-600">
    <h3 class="text-xl font-bold text-gray-900 mb-3">Vision et Objectifs</h3>
    <p class="text-gray-700 leading-relaxed">
        Le LMCS aspire à devenir un acteur majeur de la recherche en informatique au niveau national et
        international. Nos équipes travaillent en collaboration avec des partenaires académiques et industriels
        pour transformer les découvertes scientifiques en innovations technologiques concrètes, contribuant ainsi
        au développement socio-économique et à la formation de la prochaine génération de chercheurs et
        d'ingénieurs.
    </p>
</div>
<?php
    }
    
    private function renderOrganigrammeSection()
    {
        $organigramme = $this->get('organigramme', []);
        
        Section::create('Organigramme du Laboratoire', function() use ($organigramme) {
            $grouped = $this->groupOrganigrammeByLevel($organigramme);
            
            echo '<div class="space-y-8">';
            foreach ($grouped as $level => $members) {
                $this->renderOrganigrammeLevel($level, $members);
            }
            echo '</div>';
        });
    }
    
    private function groupOrganigrammeByLevel(array $organigramme)
    {
        $byLevel = [];
        foreach ($organigramme as $member) {
            if ($member['poste_hierarchique'] !== 'directeur du laboratoire') {
                $byLevel[$member['niveau']][] = $member;
            }
        }
        ksort($byLevel);
        return $byLevel;
    }
    
    private function renderOrganigrammeLevel(int $level, array $members)
    {
        if ($level == 3) {
            $this->renderOrganigrammeLevel3WithTeams($members);
        } else {
            $this->renderOrganigrammeStandardLevel($level, $members);
        }
    }
    
    private function renderOrganigrammeLevel3WithTeams(array $members)
    {
        $teamsData = $this->get('teamsForOrganigramme', []);
        $byTeam = $this->groupLevel3ByTeam($members, $teamsData);
        
        foreach ($byTeam as $teamName => $teamMembers) {
            $this->renderTeamGroup($teamName, $teamMembers, 3);
        }
    }
    
    private function groupLevel3ByTeam(array $members, array $teamsData)
    {
        $byTeam = [];
        foreach ($members as $member) {
            $memberTeams = $teamsData[$member['id_user']] ?? [];
            
            if (!empty($memberTeams)) {
                foreach ($memberTeams as $team) {
                    $byTeam[$team['nom']][] = $member;
                }
            } else {
                $byTeam['Sans équipe'][] = $member;
            }
        }
        ksort($byTeam);
        return $byTeam;
    }
    
    private function renderTeamGroup(string $teamName, array $members, int $level)
    {
        ?>
<div class="border-t-2 border-blue-200 pt-6">
    <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
        Niveau <?php echo $level; ?> - <?php echo $this->escape($teamName); ?>
    </h3>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($members as $member): ?>
        <?php $this->renderOrganigrammeMemberCard($member); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderOrganigrammeStandardLevel(int $level, array $members)
    {
        ?>
<div class="border-t-2 border-blue-200 pt-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        Niveau <?php echo $level; ?>
    </h3>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($members as $member): ?>
        <?php $this->renderOrganigrammeMemberCard($member); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderOrganigrammeMemberCard(array $member)
    {
        ?>
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition bg-white hover:border-blue-300">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <?php ImageHelper::renderUserPhoto($member, 16); ?>
        </div>
        <div class="flex-grow min-w-0">
            <div class="text-xs font-semibold text-blue-600 mb-1">
                <?php echo $this->escape($member['poste_hierarchique']); ?>
            </div>
            <h4 class="font-bold text-gray-900 text-sm mb-1 truncate">
                <?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?>
            </h4>
            <div class="text-xs text-gray-600 mb-2">
                <?php echo $this->escape($member['grade']); ?>
            </div>
            <?php if ($member['superieur_nom']): ?>
            <div class="text-xs text-gray-500 mb-2">
                Sous: <?php echo $this->escape($member['superieur_prenom'] . ' ' . $member['superieur_nom']); ?>
            </div>
            <?php endif; ?>
            <a href="<?php echo BASE_URL . 'team/member/' . $member['id_user']; ?>"
                class="inline-block text-xs text-blue-600 hover:text-blue-800 font-semibold">
                Voir profil →
            </a>
        </div>
    </div>
</div>
<?php
    }
    
    private function renderEquipesSection()
    {
        Section::create('Nos Équipes de Recherche', function() {
            $this->renderEquipesIntro();
            $this->renderEquipesTable();
        });
    }
    
    private function renderEquipesIntro()
    {
        ?>
<div class="mb-8">
    <p class="text-gray-700 leading-relaxed">
        Le laboratoire LMCS est organisé en plusieurs équipes de recherche spécialisées, chacune dirigée par un chef
        d'équipe reconnu dans son domaine. Ces équipes regroupent des enseignants-chercheurs, des doctorants et des
        collaborateurs travaillant ensemble sur des projets de recherche innovants.
    </p>
</div>
<?php
    }
    
    private function renderEquipesTable()
    {
        $teamsWithMembers = $this->get('teamsWithMembers', []);
        $tableData = $this->generateTableData($teamsWithMembers);
        $filters = $this->prepareTableFilters();
        
        Table::render([
            'id' => 'teams-table',
            'headers' => [
                ['label' => 'Équipe', 'class' => 'w-1/5'],
                ['label' => 'Membre', 'class' => 'w-2/5'],
                ['label' => 'Grade', 'class' => 'w-1/6'],
                ['label' => 'Poste', 'class' => 'w-1/6'],
                ['label' => 'Action', 'class' => 'w-1/12']
            ],
            'data' => $tableData,
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'filters' => $filters,
            'ajax_url' => BASE_URL . 'team/getTeamsData',
            'empty_message' => 'Aucune équipe disponible'
        ]);
    }
    
    private function generateTableData(array $teamsWithMembers)
    {
        if (empty($teamsWithMembers)) {
            return '';
        }
        
        $html = '';
        
        foreach ($teamsWithMembers as $data) {
            $team = $data['team'];
            $members = $data['members'];
            $html .= $this->generateTeamRows($team, $members);
        }
        
        return $html;
    }
    
    private function generateTeamRows(array $team, array $members)
    {
        $html = '';
        $memberCount = count($members);
        
        foreach ($members as $index => $member) {
            $isChef = ($team['chef_id'] == $member['id_user']);
            $poste = $member['poste'];
            
            $html .= $this->generateTableRow($team, $member, $poste, $isChef, $index, $memberCount);
        }
        
        return $html;
    }
    
    private function generateTableRow(array $team, array $member, string $poste, bool $isChef, int $index, int $memberCount)
    {
        ob_start();
        ?>
<tr class="border-b border-gray-200 hover:bg-blue-50 transition" data-team="<?php echo $this->escape($team['nom']); ?>"
    data-grade="<?php echo $this->escape($member['grade']); ?>" data-poste="<?php echo $this->escape($poste); ?>"
    data-team-id="<?php echo $team['id_team']; ?>"
    data-member-name="<?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?>">

    <?php if ($index === 0): ?>
    <?php echo $this->generateTeamCell($team, $memberCount); ?>
    <?php endif; ?>

    <?php echo $this->generateMemberCell($member, $isChef); ?>
    <?php echo $this->generateGradeCell($member); ?>
    <?php echo $this->generatePosteCell($poste); ?>
    <?php echo $this->generateActionCell($member); ?>
</tr>
<?php
        return ob_get_clean();
    }
    
    private function generateTeamCell(array $team, int $rowspan)
    {
        ob_start();
        ?>
<th scope="row" rowspan="<?php echo $rowspan; ?>"
    class="px-6 py-4 font-semibold text-gray-900 bg-gray-950 border-r-2 border-blue-200 team-cell">
    <div class="space-y-2">
        <div class="text-base font-bold text-blue-700">
            <?php echo $this->escape($team['nom']); ?>
        </div>

        <?php if (!empty($team['thematique'])): ?>
        <div class="text-xs text-gray-600 italic">
            <?php echo $this->escape($team['thematique']); ?>
        </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="<?php echo BASE_URL . 'team/detail/' . $team['id_team']; ?>"
                class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-semibold">
                Voir l'équipe <span>→</span>
            </a>
        </div>
    </div>
</th>
<?php
        return ob_get_clean();
    }
    
    private function generateMemberCell(array $member, bool $isChef)
    {
        ob_start();
        ?>
<td class="px-6 py-4">
    <div class="flex items-center gap-3">
        <div class="flex-shrink-0">
            <?php ImageHelper::renderUserPhoto($member, 12); ?>
        </div>
        <div>
            <div class="font-semibold text-gray-900">
                <?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?>
            </div>
            <?php if ($isChef): ?>
            <?php echo HtmlHelper::badge('Chef d\'équipe', 'warning'); ?>
            <?php endif; ?>
        </div>
    </div>
</td>
<?php
        return ob_get_clean();
    }
    
    private function generateGradeCell(array $member)
    {
        ob_start();
        ?>
<td class="px-6 py-4">
    <div class="font-medium text-gray-900">
        <?php echo $this->escape($member['grade']); ?>
    </div>
</td>
<?php
        return ob_get_clean();
    }
    
    private function generatePosteCell(string $poste)
    {
        ob_start();
        ?>
<td class="px-6 py-4">
    <div class="text-sm text-gray-700">
        <?php echo $this->escape($poste); ?>
    </div>
</td>
<?php
        return ob_get_clean();
    }
    
    private function generateActionCell(array $member)
    {
        ob_start();
        ?>
<td class="px-6 py-4">
    <?php 
    echo HtmlHelper::button(
        'Voir profil',
        BASE_URL . 'team/member/' . $member['id_user'],
        'secondary',
        null,
        ['class' => 'justify-center w-full text-xs']
    );
    ?>
</td>
<?php
        return ob_get_clean();
    }
    
    private function prepareTableFilters()
    {
        $teams = $this->get('teams', []);
        $teamsWithMembers = $this->get('teamsWithMembers', []);
        
        return [
            [
                'id' => 'team',
                'label' => 'Équipe',
                'column' => 'team',
                'options' => $this->extractTeamOptions($teams)
            ],
            [
                'id' => 'grade',
                'label' => 'Grade',
                'column' => 'grade',
                'options' => $this->extractGradeOptions($teamsWithMembers)
            ]
        ];
    }
    
    private function extractTeamOptions(array $teams)
    {
        $options = [];
        foreach ($teams as $team) {
            $options[$team['nom']] = $team['nom'];
        }
        return $options;
    }
    
    private function extractGradeOptions(array $teamsWithMembers)
    {
        $grades = [];
        
        foreach ($teamsWithMembers as $data) {
            foreach ($data['members'] as $member) {
                $grades[$member['grade']] = $member['grade'];
            }
        }
        
        ksort($grades);
        return $grades;
    }
}
?>