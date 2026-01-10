<?php

class CardFactory
{
    public static function project($data)
    {
        $statusColors = [
            'en_cours' => 'success',
            'termine' => 'info',
            'soumis' => 'warning'
        ];
        
        $statusLabels = [
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'soumis' => 'Soumis'
        ];
        
        $badges = [];
        if (!empty($data['statut'])) {
            $badges[] = [
                'text' => $statusLabels[$data['statut']] ?? $data['statut'],
                'type' => $statusColors[$data['statut']] ?? 'info'
            ];
        }
        if (!empty($data['thematique'])) {
            $badges[] = ['text' => $data['thematique'], 'type' => 'primary'];
        }
        
        $items = array_filter([
            [
                'icon' => 'user',
                'label' => 'Responsable',
                'value' => !empty($data['responsable_prenom']) && !empty($data['responsable_nom']) 
                    ? $data['responsable_prenom'] . ' ' . $data['responsable_nom'] 
                    : null
            ],
            [
                'icon' => 'briefcase',
                'label' => 'Type de financement',
                'value' => $data['type_financement'] ?? null
            ]
        ], fn($item) => !empty($item['value']));
        
        $stats = [
            ['label' => 'Membres', 'value' => $data['nb_membres'] ?? 0],
            ['label' => 'Publications', 'value' => $data['nb_publications'] ?? 0]
        ];
        
        $meta = [];
        if (!empty($data['date_debut'])) {
            $meta[] = [
                'type' => 'icon_text',
                'icon' => 'calendar',
                'value' => DateHelper::format($data['date_debut'], 'd/m/Y') . 
                          (!empty($data['date_fin']) ? ' - ' . DateHelper::format($data['date_fin'], 'd/m/Y') : ' - En cours')
            ];
        }
        if (!empty($data['budget'])) {
            $meta[] = [
                'type' => 'icon_text',
                'icon' => 'tag',
                'value' => number_format($data['budget'], 0, ',', ' ') . ' DA'
            ];
        }
        
        $projetData = [
            'projet' => $data,
            'membres' => $data['membres'] ?? [],
            'publications' => $data['publications'] ?? [],
            'partenaires' => $data['partenaires'] ?? []
        ];
        
        return [
            'type' => 'project',
            'badges' => $badges,
            'title' => $data['titre'] ?? '',
            'description' => $data['description'] ?? '',
            'description_max_height' => 'max-h-20',
            'stats' => $stats,
            'items' => $items,
            'meta' => $meta,
            'data_attributes' => [
                'thematique' => $data['thematique'] ?? '',
                'statut' => $data['statut'] ?? '',
                'responsable-id' => $data['responsable_id'] ?? '',
                'titre' => $data['titre'] ?? '',
                'description' => $data['description'] ?? '',
                'projet' => json_encode($projetData)
            ],
            'details_section' => ['id' => 'details-' . ($data['id_projet'] ?? '')],
            'footer_buttons' => [
                [
                    'text' => 'Voir plus de détails',
                    'type' => 'primary',
                    'icon' => 'arrow-right',
                    'onclick' => 'toggleProjetDetails(' . ($data['id_projet'] ?? 0) . ')',
                    'class' => ''
                ]
            ]
        ];
    }
    
    public static function publication($data)
    {
        $badges = [];
        if (!empty($data['type_libelle'])) {
            $badges[] = ['text' => ucfirst($data['type_libelle']), 'type' => 'primary'];
        }
        if (!empty($data['annee'])) {
            $badges[] = ['text' => $data['annee'], 'type' => 'info'];
        }
        if (!empty($data['domaine'])) {
            $badges[] = ['text' => $data['domaine'], 'type' => 'success'];
        }
        
        $items = [];
        if (!empty($data['auteurs'])) {
            $items[] = [
                'label' => 'Auteurs',
                'value' => $data['auteurs'],
                'class' => 'text-sm text-gray-600'
            ];
        }
        
        $meta = [];
        if (!empty($data['doi'])) {
            $meta[] = [
                'type' => 'link',
                'text' => $data['doi'],
                'url' => 'https://doi.org/' . $data['doi'],
                'icon' => 'external-link'
            ];
        }
        if (!empty($data['date_publication'])) {
            $meta[] = [
                'type' => 'icon_text',
                'icon' => 'calendar',
                'value' => DateHelper::format($data['date_publication'])
            ];
        }
        
        $footerButtons = [];
        if (!empty($data['lien_telechargement'])) {
            $footerButtons[] = [
                'text' => 'Télécharger',
                'url' => $data['lien_telechargement'],
                'type' => 'primary',
                'icon' => 'download',
                'class' => ''
            ];
        }
        
        $description = '';
        if (!empty($data['resume'])) {
            $description = strlen($data['resume']) > 250 
                ? substr($data['resume'], 0, 250) . '...' 
                : $data['resume'];
        }
        
        return [
            'type' => 'publication',
            'badges' => $badges,
            'title' => $data['titre'] ?? '',
            'items' => $items,
            'description' => $description,
            'description_max_height' => 'max-h-20',
            'meta' => $meta,
            'meta_container_class' => 'flex flex-wrap items-center gap-4 text-sm mb-4 pb-4 border-b',
            'footer_buttons' => $footerButtons,
            'data_attributes' => [
                'title' => $data['titre'] ?? '',
                'year' => $data['annee'] ?? '',
                'type' => $data['type_libelle'] ?? '',
                'domain' => $data['domaine'] ?? '',
                'authors' => $data['auteurs'] ?? '',
                'resume' => $data['resume'] ?? '',
                'doi' => $data['doi'] ?? ''
            ]
        ];
    }
    
    public static function actualite($data)
    {
        return [
            'type' => 'actualite',
            'image' => $data['image'] ?? null,
            'badge' => $data['type_libelle'] ?? 'Actualité',
            'badge_type' => 'primary',
            'title' => $data['titre'] ?? '',
            'description' => $data['contenu'] ?? '',
            'footer_link' => !empty($data['detail']) ? [
                'text' => 'En savoir plus',
                'url' => BASE_URL . 'actualite/index/'
            ] : null,
            'footer_text' => !empty($data['date_publication']) 
                ? DateHelper::format($data['date_publication']) 
                : null
        ];
    }
    
    public static function actualiteDetail($data)
    {
        $badges = [];
        if (!empty($data['type_libelle'])) {
            $badges[] = ['text' => $data['type_libelle'], 'type' => 'primary'];
        }
        
        $meta = [];
        if (!empty($data['date_publication'])) {
            $meta[] = [
                'type' => 'icon_text',
                'icon' => 'clock',
                'value' => DateHelper::relative($data['date_publication'])
            ];
        }

        $shareButtons = [];
        if (!empty($data['titre'])) {
            $titre = addslashes(htmlspecialchars($data['titre']));
            $shareButtons = [
                [
                    'text' => 'Facebook',
                    'type' => 'primary',
                    'onclick' => "shareOnFacebook('$titre')",
                    'icon' => 'external-link',
                    'class' => ''
                ],
                [
                    'text' => 'Twitter',
                    'type' => 'primary',
                    'onclick' => "shareOnTwitter('$titre')",
                    'icon' => 'external-link',
                    'class' => ''
                ],
                [
                    'text' => 'Copier',
                    'type' => 'secondary',
                    'onclick' => "copyToClipboard('$titre')",
                    'icon' => 'clipboard',
                    'class' => ''
                ]
            ];
        }
        
        return [
            'type' => 'actualite_detail',
            'image' => $data['image'] ?? null,
            'badges' => $badges,
            'title' => $data['titre'] ?? '',
            'description' => $data['contenu'] ?? '',
            'meta' => $meta,
            'detail_section' => !empty($data['detail']) ? $data['detail'] : null,
            'footer_buttons' => $shareButtons,
            'data_attributes' => [
                'title' => $data['titre'] ?? '',
                'content' => $data['contenu'] ?? ''
            ],
            'id' => 'actualite-' . ($data['id_actualite'] ?? '')
        ];
    }
    
    public static function event($data)
    {
        $statutConfig = [
            'a_venir' => ['text' => 'À venir', 'type' => 'primary'],
            'en_cours' => ['text' => 'En cours', 'type' => 'success'],
            'termine' => ['text' => 'Terminé', 'type' => 'info'],
            'annule' => ['text' => 'Annulé', 'type' => 'danger']
        ];
        
        $statut = $statutConfig[$data['statut']] ?? ['text' => $data['statut'], 'type' => 'info'];
        
        $badges = [
            ['text' => $statut['text'], 'type' => $statut['type']]
        ];
        
        if (!empty($data['type_libelle'])) {
            $badges[] = ['text' => ucfirst($data['type_libelle']), 'type' => 'orange'];
        }
        
        if (isset($data['externe'])) {
            $badges[] = $data['externe'] 
                ? ['text' => 'Ouvert au public', 'type' => 'success']
                : ['text' => 'Interne', 'type' => 'warning'];
        }
        
        $items = array_filter([
            [
                'icon' => 'calendar',
                'value' => !empty($data['date_debut']) 
                    ? DateHelper::format($data['date_debut'], 'd/m/Y H:i') . 
                      (!empty($data['date_fin']) ? ' - ' . DateHelper::format($data['date_fin'], 'd/m/Y H:i') : '') 
                    : null
            ],
            [
                'icon' => 'location',
                'value' => $data['lieu'] ?? null
            ],
            [
                'icon' => 'user',
                'value' => !empty($data['capacite_max']) 
                    ? 'Capacité : ' . $data['capacite_max'] . ' places'
                    : null
            ]
        ], fn($item) => !empty($item['value']));
        
        $footerButtons = [];
        if ($data['statut'] === 'a_venir' && !empty($data['id_evenement'])) {
            $footerButtons[] = [
                'text' => 'S\'inscrire',
                'url' => BASE_URL . 'event/register/' . $data['id_evenement'],
                'type' => 'primary',
                'icon' => 'check',
                'class' => 'w-full justify-center'
            ];
        }
        
        return [
            'type' => 'event',
            'badges' => $badges,
            'title' => $data['titre'] ?? '',
            'description' => $data['description'] ?? '',
            'description_max_height' => 'max-h-20',
            'items' => $items,
            'footer_buttons' => $footerButtons
        ];
    }
    
    public static function partner($data)
    {
        return [
            'type' => 'partner',
            'title' => $data['nom'] ?? '',
            'description' => $data['description'] ?? null,
            'logo' => $data['logo'] ?? null,
            'meta' => array_filter([
                [
                    'type' => 'icon_text',
                    'icon' => 'location',
                    'value' => $data['pays'] ?? null
                ],
                [
                    'type' => 'icon_text',
                    'icon' => 'calendar',
                    'value' => !empty($data['date_partenariat']) 
                        ? 'Partenariat depuis ' . DateHelper::format($data['date_partenariat'], 'Y') 
                        : null
                ],
                [
                    'type' => 'link',
                    'icon' => 'external-link',
                    'text' => 'Visiter le site',
                    'url' => $data['site_web'] ?? null
                ]
            ], fn($item) => !empty($item['value']) || !empty($item['url']))
        ];
    }

    public static function equipement($data)
    {
        $etatConfig = [
            'libre' => ['text' => 'Libre', 'type' => 'success'],
            'reserve' => ['text' => 'Réservé', 'type' => 'warning'],
            'maintenance' => ['text' => 'Maintenance', 'type' => 'orange'],
            'hors_service' => ['text' => 'Hors service', 'type' => 'danger']
        ];
        
        $etat = $etatConfig[$data['etat']] ?? ['text' => $data['etat'], 'type' => 'info'];
        $isAvailable = $data['etat'] === 'libre' || $data['etat'] === 'reserve';
        
        $localisation = $data['localisation'] ?? null;
        if ($localisation && strlen($localisation) > 40) {
            $localisation = substr($localisation, 0, 37) . '...';
        }
        
        $items = array_filter([
            ['label' => 'Type', 'value' => $data['type_libelle'] ?? null],
            ['label' => 'Localisation', 'value' => $localisation],
            [
                'label' => 'Capacité', 
                'value' => !empty($data['capacite']) 
                    ? $data['capacite'] . ($data['type_libelle'] === 'salles' ? ' personnes' : ' unités') 
                    : null
            ],
            ['label' => 'N° série', 'value' => $data['numero_serie'] ?? null]
        ], fn($item) => !empty($item['value']));
        
        $footerButtons = [];
        if ($isAvailable) {
            if (isset($_SESSION['user_id'])) {
                $footerButtons[] = [
                    'text' => 'Réserver',
                    'type' => 'primary',
                    'onclick' => 'openReservationModal(' . $data['id_equipement'] . ', \'' . 
                                 addslashes($data['nom']) . '\', ' . 
                                 ($data['capacite'] ?? 'null') . ', \'' . 
                                 ($data['type_libelle'] ?? '') . '\')',
                    'class' => 'w-full justify-center'
                ];
            } else {
                $footerButtons[] = [
                    'text' => 'Se connecter pour réserver',
                    'url' => BASE_URL . 'auth/login',
                    'type' => 'primary',
                    'class' => 'w-full justify-center'
                ];
            }
        }
        
        return [
            'type' => 'equipement',
            'badges' => [
                ['text' => $etat['text'], 'type' => $etat['type']]
            ],
            'title' => $data['nom'] ?? '',
            'description' => $data['description'] ?? '',
            'description_max_height' => 'max-h-20',
            'items' => $items,
            'footer_buttons' => $footerButtons,
            'data_attributes' => [
                'title' => strtolower($data['nom'] ?? ''),
                'type' => $data['type_equipement_id'] ?? '',
                'etat' => $data['etat'] ?? ''
            ]
        ];
    }
    
    public static function dashboard($data)
    {
        return array_merge(['type' => 'dashboard'], $data);
    }
}
?>