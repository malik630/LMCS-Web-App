<?php

require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../helpers/HtmlHelper.php';
require_once 'components/Section.php';
require_once 'components/Card.php';

class EquipementView extends View
{
    protected $pageTitle = 'Équipements - LMCS';
    
    public function render()
    {
        $this->renderHeader();
        echo '<div class="container mx-auto px-4 py-8">';
        
        $this->renderPageHeader();
        $this->renderFilters();
        $this->renderEquipments();
        
        echo '</div>';
        $this->renderReservationModal();
        $this->renderScript();
        $this->renderFooter();
    }
    
    private function renderPageHeader()
    {
        ?>
<div class="mb-8">
    <h1 class="text-4xl font-bold text-white mb-3">Équipements du laboratoire</h1>
    <p class="text-blue-100 text-lg">Découvrez nos ressources techniques et infrastructures</p>
</div>
<?php
    }
    
    private function renderFilters()
    {
        ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <div class="grid md:grid-cols-4 gap-4">
        <input type="text" id="search-input" placeholder="Rechercher..."
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

        <select id="filter-type" class="filter-select px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">Tous les types</option>
            <option value="1">Salles</option>
            <option value="2">Serveurs</option>
            <option value="3">PC</option>
            <option value="4">Robots</option>
            <option value="5">Imprimantes</option>
            <option value="6">Capteurs</option>
        </select>

        <select id="filter-etat" class="filter-select px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">Tous les états</option>
            <option value="libre">Libre</option>
            <option value="reserve">Réservé</option>
            <option value="maintenance">Maintenance</option>
            <option value="hors_service">Hors service</option>
        </select>

        <button id="reset-btn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            Réinitialiser
        </button>
    </div>
</div>
<?php
    }
    
    private function renderEquipments()
    {
        $equipements = $this->data;
        
        // Grouper les équipements par type
        $equipementsByType = [];
        foreach ($equipements as $eq) {
            $typeId = $eq['type_equipement_id'] ?? 'other';
            $typeLibelle = $eq['type_libelle'] ?? 'Autre';
            
            if (!isset($equipementsByType[$typeId])) {
                $equipementsByType[$typeId] = [
                    'libelle' => ucfirst($typeLibelle),
                    'items' => []
                ];
            }
            
            $equipementsByType[$typeId]['items'][] = $eq;
        }
        
        if (empty($equipements)) {
            Section::create('Liste des équipements', function() {
                echo HtmlHelper::emptyState('Aucun équipement disponible');
            });
            return;
        }
        
        ?>
<div class="mb-12">
    <h2 class="text-3xl font-serif font-bold leading-relaxed tracking-tight text-white mb-6">
        Liste des équipements
    </h2>

    <div id="items-container">
        <?php foreach ($equipementsByType as $typeId => $typeData): ?>
        <div class="thematique-section mb-8" data-type="<?php echo $typeId; ?>">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-lg p-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">
                    <?php echo htmlspecialchars($typeData['libelle']); ?>
                </h3>
                <span class="thematique-count text-blue-100 text-sm">
                    (<?php echo count($typeData['items']); ?>
                    <?php echo count($typeData['items']) > 1 ? 'éléments' : 'élément'; ?>)
                </span>
            </div>

            <div class="bg-white rounded-b-lg shadow-lg p-6">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($typeData['items'] as $eq): ?>
                    <?php $this->renderEquipmentCard($eq); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
    }
    
    private function renderEquipmentCard($eq)
    {
        $etatConfig = [
            'libre' => ['text' => 'Libre', 'type' => 'success'],
            'reserve' => ['text' => 'Réservé', 'type' => 'warning'],
            'maintenance' => ['text' => 'Maintenance', 'type' => 'orange'],
            'hors_service' => ['text' => 'Hors service', 'type' => 'danger']
        ];
        
        $etat = $etatConfig[$eq['etat']] ?? ['text' => $eq['etat'], 'type' => 'info'];
        $isAvailable = $eq['etat'] === 'libre';
        
        // Tronquer la localisation si elle est trop longue
        $localisation = $eq['localisation'] ?? null;
        if ($localisation && strlen($localisation) > 40) {
            $localisation = substr($localisation, 0, 37) . '...';
        }
        
        $items = array_filter([
            ['label' => 'Type', 'value' => $eq['type_libelle'] ?? null],
            ['label' => 'Localisation', 'value' => $localisation],
            ['label' => 'Capacité', 'value' => !empty($eq['capacite']) ? $eq['capacite'] . ($eq['type_libelle'] === 'salles' ? ' personnes' : ' unités') : null],
            ['label' => 'N° série', 'value' => $eq['numero_serie'] ?? null]
        ], fn($item) => !empty($item['value']));
        
        $footerButton = null;
        if ($isAvailable) {
            if (isset($_SESSION['user_id'])) {
                $footerButton = [
                    'text' => 'Réserver',
                    'type' => 'primary',
                    'onclick' => 'openReservationModal(' . $eq['id_equipement'] . ', \'' . 
                                 addslashes($eq['nom']) . '\', ' . 
                                 ($eq['capacite'] ?? 'null') . ', \'' . 
                                 ($eq['type_libelle'] ?? '') . '\')'
                ];
            } else {
                $footerButton = [
                    'text' => 'Se connecter pour réserver',
                    'url' => BASE_URL . 'auth/login',
                    'type' => 'primary'
                ];
            }
        }
        
        // Card HTML
        echo '<div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 flex flex-col h-full" 
                   data-title="' . htmlspecialchars(strtolower($eq['nom'])) . '" 
                   data-type="' . htmlspecialchars($eq['type_equipement_id']) . '" 
                   data-etat="' . htmlspecialchars($eq['etat']) . '">';
        
        echo '<div class="p-6 flex flex-col flex-grow">';
        
        // Badge état
        echo '<div class="mb-2">' . HtmlHelper::badge($etat['text'], $etat['type']) . '</div>';
        
        // Titre
        echo '<h3 class="text-xl font-bold mt-2 mb-3">' . htmlspecialchars($eq['nom']) . '</h3>';
        
        // Description
        if (!empty($eq['description'])) {
            echo '<div class="text-gray-600 mb-4 max-h-20 overflow-y-auto pr-2 custom-scrollbar flex-grow">';
            echo '<p>' . nl2br(htmlspecialchars($eq['description'])) . '</p>';
            echo '</div>';
        }
        
        // Items
        if (!empty($items)) {
            echo '<div class="space-y-2 mb-4">';
            foreach ($items as $item) {
                echo '<div class="flex items-start gap-2 text-sm text-gray-600">';
                if (!empty($item['icon'])) {
                    echo '<span class="flex-shrink-0 mt-0.5">' . HtmlHelper::icon($item['icon']) . '</span>';
                }
                echo '<span class="break-words">';
                if (!empty($item['label'])) {
                    echo '<span class="font-semibold">' . htmlspecialchars($item['label']) . ':</span> ';
                }
                echo htmlspecialchars($item['value']);
                echo '</span>';
                echo '</div>';
            }
            echo '</div>';
        }
        
        // Footer button
        if ($footerButton) {
            echo '<div class="mt-auto pt-4 border-t border-gray-200">';
            if (isset($footerButton['onclick'])) {
                echo '<button onclick="' . $footerButton['onclick'] . '" 
                        class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition inline-flex items-center justify-center gap-2">';
                echo htmlspecialchars($footerButton['text']);
                echo '</button>';
            } else {
                echo HtmlHelper::button(
                    $footerButton['text'],
                    $footerButton['url'],
                    $footerButton['type'],
                    null,
                    ['class' => 'w-full justify-center']
                );
            }
            echo '</div>';
        }
        
        echo '</div>'; // Close p-6
        echo '</div>'; // Close card
    }
    
    private function renderReservationModal()
    {
        if (!isset($_SESSION['user_id'])) return;
        
        ?>
<!-- Modal de réservation -->
<div id="reservation-modal"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 rounded-t-lg">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Réserver un équipement</h2>
                <button onclick="closeReservationModal()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <form id="reservation-form" class="p-6">
            <input type="hidden" id="modal-equipement-id" name="equipement_id">

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2" id="modal-equipement-nom"></h3>
                <p class="text-sm text-gray-600" id="modal-equipement-info"></p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Date et heure de début *
                    </label>
                    <input type="datetime-local" id="date-debut" name="date_debut" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Date et heure de fin *
                    </label>
                    <input type="datetime-local" id="date-fin" name="date_fin" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6" id="nb-instances-container" style="display: none;">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nombre d'instances *
                </label>
                <input type="number" id="nb-instances" name="nb_instances" min="1" value="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1" id="capacite-info"></p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Motif de la réservation
                </label>
                <textarea id="motif" name="motif" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Décrivez brièvement le motif de votre réservation..."></textarea>
            </div>

            <div id="availability-message" class="mb-4"></div>

            <div class="flex gap-4">
                <button type="submit" id="submit-reservation"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    Envoyer la demande
                </button>
                <button type="button" onclick="closeReservationModal()"
                    class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-semibold transition">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>
<?php
    }
    
    private function renderScript()
    {
        ?>
<script>
// Variables globales
let currentEquipement = null;

// Gestion du modal
function openReservationModal(id, nom, capacite, type) {
    currentEquipement = {
        id,
        nom,
        capacite,
        type
    };

    document.getElementById('modal-equipement-id').value = id;
    document.getElementById('modal-equipement-nom').textContent = nom;

    // Afficher les informations
    let info = 'Type: ' + type;
    if (capacite && type !== 'salles') {
        info += ' | Capacité: ' + capacite + ' unités';
    } else if (capacite && type === 'salles') {
        info += ' | Capacité: ' + capacite + ' personnes';
    }
    document.getElementById('modal-equipement-info').textContent = info;

    // Gérer l'affichage du champ nb_instances
    const nbInstancesContainer = document.getElementById('nb-instances-container');
    const nbInstancesInput = document.getElementById('nb-instances');
    const capaciteInfo = document.getElementById('capacite-info');

    if (type !== 'salles' && capacite) {
        nbInstancesContainer.style.display = 'block';
        nbInstancesInput.max = capacite;
        capaciteInfo.textContent = 'Capacité maximale: ' + capacite;
    } else {
        nbInstancesContainer.style.display = 'none';
        nbInstancesInput.value = 1;
    }

    // Réinitialiser le formulaire
    document.getElementById('reservation-form').reset();
    document.getElementById('modal-equipement-id').value = id;
    document.getElementById('availability-message').innerHTML = '';

    // Définir la date minimale à maintenant
    const now = new Date();
    const dateStr = now.toISOString().slice(0, 16);
    document.getElementById('date-debut').min = dateStr;
    document.getElementById('date-fin').min = dateStr;

    // Afficher le modal
    document.getElementById('reservation-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReservationModal() {
    document.getElementById('reservation-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Vérifier la disponibilité lors du changement de dates
document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date-debut');
    const dateFin = document.getElementById('date-fin');

    if (dateDebut && dateFin) {
        dateDebut.addEventListener('change', checkAvailability);
        dateFin.addEventListener('change', checkAvailability);
    }

    // Gestion de la soumission du formulaire
    const form = document.getElementById('reservation-form');
    if (form) {
        form.addEventListener('submit', handleReservationSubmit);
    }

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('reservation-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeReservationModal();
        }
    });

    // Initialiser le système de filtrage
    window.filterSortSearch = new FilterSortSearch({
        searchInput: '#search-input',
        filterSelects: '.filter-select',
        resetButton: '#reset-btn',
        itemsContainer: '#items-container',
        itemSelector: '.bg-white.rounded-lg.shadow-lg[data-title]',
        searchFields: ['data-title'],
        filterFields: {
            '#filter-type': 'data-type',
            '#filter-etat': 'data-etat'
        },
        emptyMessage: 'Aucun équipement ne correspond à vos critères.'
    });
});

function checkAvailability() {
    const equipementId = document.getElementById('modal-equipement-id').value;
    const dateDebut = document.getElementById('date-debut').value;
    const dateFin = document.getElementById('date-fin').value;
    const messageDiv = document.getElementById('availability-message');

    if (!equipementId || !dateDebut || !dateFin) return;

    const formData = new FormData();
    formData.append('equipement_id', equipementId);
    formData.append('date_debut', dateDebut);
    formData.append('date_fin', dateFin);

    fetch('<?php echo BASE_URL; ?>reservation/checkAvailability', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                messageDiv.innerHTML =
                    '<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">' +
                    '<strong>✓ Disponible:</strong> Cet équipement est disponible pour cette période.' +
                    '</div>';
            } else {
                let slotsHtml = '';
                if (data.reserved_slots && data.reserved_slots.length > 0) {
                    slotsHtml =
                        '<div class="mt-2"><strong>Créneaux réservés:</strong><ul class="list-disc list-inside mt-1">';
                    data.reserved_slots.forEach(slot => {
                        slotsHtml += '<li>' + new Date(slot.start).toLocaleString('fr-FR') + ' - ' +
                            new Date(slot.end).toLocaleString('fr-FR') + '</li>';
                    });
                    slotsHtml += '</ul></div>';
                }

                messageDiv.innerHTML =
                    '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">' +
                    '<strong>✗ Non disponible:</strong> Cet équipement est déjà réservé pour cette période.' +
                    slotsHtml +
                    '</div>';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}

function handleReservationSubmit(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submit-reservation');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Envoi en cours...';

    const formData = new FormData(e.target);

    fetch('<?php echo BASE_URL; ?>reservation/create', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher le message de succès
                const messageDiv = document.getElementById('availability-message');
                messageDiv.innerHTML =
                    '<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">' +
                    '<strong>✓ Succès:</strong> ' + data.message +
                    '</div>';

                // Fermer le modal après 2 secondes
                setTimeout(() => {
                    closeReservationModal();
                    location.reload();
                }, 2000);
            } else {
                const messageDiv = document.getElementById('availability-message');
                messageDiv.innerHTML =
                    '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">' +
                    '<strong>✗ Erreur:</strong> ' + data.message +
                    '</div>';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Envoyer la demande';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            const messageDiv = document.getElementById('availability-message');
            messageDiv.innerHTML =
                '<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">' +
                '<strong>✗ Erreur:</strong> Une erreur est survenue lors de l\'envoi de votre demande.' +
                '</div>';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Envoyer la demande';
        });
}
</script>
<?php
    }
}
?>