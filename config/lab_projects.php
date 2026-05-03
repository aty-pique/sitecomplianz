<?php

/**
 * Pages détail laboratoire — /lab/{slug}
 * Clés = segment d’URL (slug). Le contenu détaillé pourra être complété par pôle.
 */
$labFeedback = [
    'title' => 'Co-construction & retours',
    'lead'  => 'Nous bâtissons avec le terrain : votre avis compte autant que notre feuille de route.',
    'items' => [
        ['label' => 'Donner son avis', 'href' => '/contact'],
        ['label' => 'Proposer une fonctionnalité', 'href' => '/contact'],
    ],
];

$labIdeaValidationBase = [
    'title'  => 'Validation marché',
    'intro'  => 'Aidez-nous à tester l’intérêt avant d’industrialiser. Les réponses sont agrégées et anonymisées dans les reportings publics ; le contact sert uniquement à vous revenir si vous le souhaitez.',
    'questions' => [
        ['name' => 'market_interested', 'label' => 'Ce projet vous intéresse ?'],
        ['name' => 'market_would_use', 'label' => 'Seriez-vous prêt·e à l’utiliser ?'],
        ['name' => 'market_would_pay', 'label' => 'Seriez-vous prêt·e à payer pour ce type d’offre (dans une fourchette raisonnable) ?'],
    ],
    'vote_intro'  => 'En complément, une impression en un clic.',
    'vote_labels' => [
        'up'    => 'Intéressant',
        'mixed' => 'À améliorer',
        'down'  => 'Pas prioritaire',
    ],
    'vote_mode'   => 'simple',
    'conversion_legend' => 'Conversion — comment vous recontacter',
    'chk_beta'     => 'Je serais intéressé·e pour être bêta-testeur',
    'chk_exchange' => 'Je propose un échange / entretien de 20 minutes',
    'chk_news'     => 'Me tenir informé·e via la newsletter laboratoire',
    'submit_label' => 'Envoyer ma réponse',
];

return [
    'auditia' => [
        'meta_title'       => 'AuditIA — Préparez vos audits de conformité en quelques minutes',
        'meta_description' => 'AuditIA : copilote de préparation d’audit RGPD et ISO. Questionnaires intelligents, structuration des preuves et pistes d’amélioration — projet Complianz en développement.',
        'kind'             => 'ongoing',
        'kind_label'       => 'Projet en cours',
        'hero'             => [
            'name'         => 'AuditIA',
            'status'       => 'En développement',
            'status_tone'  => 'orange',
            'eyebrow'      => 'Laboratoire',
            'value_line'   => 'AuditIA — Automatisez la préparation de vos audits de conformité en quelques minutes.',
            'ctas'         => [
                ['label' => 'Demander un accès', 'href' => '/contact', 'style' => 'primary'],
            ],
        ],
        'problem_solution' => [
            'problem'      => 'Avant un audit, les équipes perdent un temps considérable à rassembler l’info, souvent dans des fichiers éclatés et des versions qui ne coïncident plus.',
            'market_limits' => 'Beaucoup d’outils restent des grilles figées : peu d’aide à la structuration, peu de liant entre traitements, contrôles et preuves — surtout pour les PME et ETI.',
            'solution'     => 'AuditIA vous guide dans une préparation structurée : questionnaire intelligent, esquisse de cartographie, premières alertes de cohérence et export des éléments utiles à l’échange avec l’auditeur.',
            'pillars'      => [
                ['label' => 'Simplification', 'text' => 'Un parcours clair, des questions contextualisées, moins de ressaisie.'],
                ['label' => 'Structuration', 'text' => 'Données et preuves rangées pour parler le même langage que vos référentiels.'],
                ['label' => 'Efficacité', 'text' => 'Gagner des jours sur la collecte et arriver en audit avec un socle solide.'],
            ],
        ],
        'features'         => [
            'title'    => 'Fonctionnalités (niveau actuel)',
            'intro'    => 'Voici ce qui est déjà implémenté ou en test interne — pas une vision marketing à 3 ans.',
            'items'    => [
                ['title' => 'Questionnaire d’entrée guidé', 'text' => 'Scénarios selon votre secteur et le type d’audit visé (RGPD, ISO…).', 'state' => 'live'],
                ['title' => 'Brouillon de cartographie des traitements', 'text' => 'Premier schéma à affiner avec vos référentiels métiers.', 'state' => 'live'],
                ['title' => 'Export des synthèses & pièces', 'text' => 'Package prêt à compléter pour l’échange avec l’auditeur.', 'state' => 'wip'],
            ],
            'wip_note' => 'D’autres modules (connecteurs, scénarios sectoriels avancés) sont en cours de développement.',
        ],
        'demo'             => [
            'title'  => 'Démo & aperçu',
            'intro'  => 'Quelques captures de l’interface actuelle. Remplacez-les par vos propres visuels (GIF, vidéo) dès qu’ils sont prêts.',
            'items'  => [
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Tableau de bord AuditIA', 'caption' => 'Vue d’ensemble de la session de préparation'],
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Parcours questionnaire', 'caption' => 'Parcours questionnaire contextualisé'],
            ],
        ],
        'progress'         => [
            'percent'   => 65,
            'accent'    => 'orange',
            'roadmap'   => [
                ['label' => 'Prototype validé', 'state' => 'done'],
                ['label' => 'Architecture définie', 'state' => 'done'],
                ['label' => 'Développement en cours', 'state' => 'current'],
                ['label' => 'Tests internes & durcissement', 'state' => 'locked'],
            ],
        ],
        'use_cases'        => [
            'title' => 'Cas d’usage',
            'items' => [
                ['who' => 'Responsable conformité / DPO', 'context' => 'Préparer un audit ou un contrôle sans mobiliser toute l’IT pendant des semaines.'],
                ['who' => 'PME & ETI', 'context' => 'Ressources limitées : besoin d’un fil directeur et de livrables exploitables rapidement.'],
                ['who' => 'DSI / RSSI', 'context' => 'Cadrer ce qui relève de la preuve technique vs. processus, en amont des échanges avec l’auditeur.'],
            ],
        ],
        'access_cta'       => [
            'title' => 'Accès, démo & veille',
            'items' => [
                ['label' => 'Demander un accès', 'href' => '/contact', 'style' => 'primary'],
                ['label' => 'Planifier une démo', 'href' => '/contact', 'style' => 'outline'],
                ['label' => 'Être notifié', 'href' => '/hub#lab-newsletter', 'style' => 'outline'],
            ],
        ],
        'feedback'         => $labFeedback,
    ],

    'seo-dashboard' => [
        'meta_title'       => 'SEO Dashboard — Laboratoire Complianz',
        'meta_description' => 'Tableaux de suivi SEO et analytics : projet en bêta privée. Rejoignez les testeurs ou demandez un accès.',
        'kind'             => 'ongoing',
        'kind_label'       => 'Projet en cours',
        'hero'             => [
            'name'        => 'SEO Dashboard',
            'status'      => 'Bêta privée',
            'status_tone' => 'blue',
            'eyebrow'     => 'Laboratoire',
            'value_line'  => 'SEO Dashboard — Centralisez vos indicateurs SEO et analytics pour décider plus vite.',
            'ctas'        => [
                ['label' => 'Demander un accès', 'href' => '/contact', 'style' => 'primary'],
                ['label' => 'Rejoindre la bêta', 'href' => '/contact', 'style' => 'secondary'],
            ],
        ],
        'problem_solution' => [
            'problem'       => 'Les données SEO et analytics sont souvent éclatées entre plusieurs outils : difficulté à prioriser et à relier l’action à l’impact.',
            'market_limits'   => 'Les tableaux « tout-en-un » imposent souvent leur logique ; peu s’alignent sur vos jalons métiers réels.',
            'solution'      => 'Un tableau unique orienté décision : vue synthétique, tendances et pistes d’action pour vos équipes marketing et direction.',
            'pillars'       => [
                ['label' => 'Simplification', 'text' => 'Moins d’onglets, plus de signaux utiles.'],
                ['label' => 'Structuration', 'text' => 'Indicateurs regroupés par objectif, pas par outil source.'],
                ['label' => 'Efficacité', 'text' => 'Gagner du temps sur le reporting et le choix des chantiers.'],
            ],
        ],
        'features'         => [
            'title' => 'Fonctionnalités (niveau actuel)',
            'intro' => 'État réel du produit en bêta — évolutions publiées au fil des retours terrain.',
            'items' => [
                ['title' => 'Vue synthétique des KPI', 'text' => 'Vue consolidée des métriques suivies.', 'state' => 'live'],
                ['title' => 'Segments & filtres métier', 'text' => 'Découpe par ligne produit ou marché.', 'state' => 'live'],
                ['title' => 'Connecteurs avancés', 'text' => 'Élargissement des sources.', 'state' => 'wip'],
            ],
            'wip_note' => 'Priorisation selon les retours de la bêta privée.',
        ],
        'demo'             => [
            'title' => 'Démo & aperçu',
            'intro' => 'Visuels à compléter avec vos captures ou une courte vidéo de démonstration.',
            'items' => [
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Dashboard SEO', 'caption' => 'Aperçu de la vue principale'],
            ],
        ],
        'progress'         => [
            'percent' => 85,
            'accent'  => 'blue',
            'roadmap' => [
                ['label' => 'Prototype validé', 'state' => 'done'],
                ['label' => 'Architecture définie', 'state' => 'done'],
                ['label' => 'Développement en cours', 'state' => 'current'],
                ['label' => 'Tests internes', 'state' => 'upcoming'],
            ],
        ],
        'use_cases'        => [
            'title' => 'Cas d’usage',
            'items' => [
                ['who' => 'Équipe marketing / acquisition', 'context' => 'Suivre la visibilité et l’impact des campagnes sans reconstituer les rapports à la main.'],
                ['who' => 'Direction', 'context' => 'Arbitrer les budgets sur des indicateurs lisibles.'],
            ],
        ],
        'access_cta'       => [
            'title' => 'Accès & contact',
            'items' => [
                ['label' => 'Rejoindre la bêta', 'href' => '/contact', 'style' => 'primary'],
                ['label' => 'Demander une démo', 'href' => '/contact', 'style' => 'outline'],
                ['label' => 'Être notifié', 'href' => '/hub#lab-newsletter', 'style' => 'outline'],
            ],
        ],
        'feedback'         => $labFeedback,
    ],

    'agent-rh' => [
        'meta_title'       => 'Agent RH — Laboratoire Complianz',
        'meta_description' => 'Agent conversationnel pour FAQ internes et obligations RH : projet en phase d’expérimentation.',
        'kind'             => 'ongoing',
        'kind_label'       => 'Projet en cours',
        'hero'             => [
            'name'        => 'Agent RH',
            'status'      => 'Expérimentation',
            'status_tone' => 'purple',
            'eyebrow'     => 'Laboratoire',
            'value_line'  => 'Agent RH — Réponses cadrées sur vos obligations internes et vos formations.',
            'ctas'        => [
                ['label' => 'Demander un accès', 'href' => '/contact', 'style' => 'primary'],
            ],
        ],
        'problem_solution' => [
            'problem'       => 'Les mêmes questions reviennent (congés, règlement, parcours) et saturent les équipes RH et support interne.',
            'market_limits'   => 'Les chatbots génériques ne connaissent pas votre politique interne ni vos outils.',
            'solution'      => 'Un agent alimenté par vos contenus approuvés, pour libérer du temps et homogénéiser les réponses.',
            'pillars'       => [
                ['label' => 'Simplification', 'text' => 'Un point d’entrée unique pour les collaborateurs.'],
                ['label' => 'Structuration', 'text' => 'Réponses ancrées sur les documents et processus valides.'],
                ['label' => 'Efficacité', 'text' => 'Moins d’allers-retours, plus de traçabilité.'],
            ],
        ],
        'features'         => [
            'title' => 'Fonctionnalités (niveau actuel)',
            'intro' => 'Phase d’expérimentation : périmètre volontairement restreint.',
            'items' => [
                ['title' => 'Base de connaissances cadrée', 'text' => 'Indexation de contenus validés par les RH.', 'state' => 'wip'],
                ['title' => 'Scénarios de questions fréquentes', 'text' => 'Parcours sur congés, formation, règlement intérieur.', 'state' => 'wip'],
            ],
            'wip_note' => 'Déploiement élargi après retours des premiers cas pilotes.',
        ],
        'demo'             => [
            'title' => 'Démo & aperçu',
            'intro' => 'À documenter (capture d’écran, court clip).',
            'items' => [
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Aperçu Agent RH', 'caption' => 'Interface de test'],
            ],
        ],
        'progress'         => [
            'percent' => 30,
            'accent'  => 'purple',
            'roadmap' => [
                ['label' => 'Cadrage des cas pilotes', 'state' => 'done'],
                ['label' => 'Expérimentation en cours', 'state' => 'current'],
                ['label' => 'Intégration outils RH', 'state' => 'locked'],
                ['label' => 'Ouverture élargie', 'state' => 'locked'],
            ],
        ],
        'use_cases'        => [
            'title' => 'Cas d’usage',
            'items' => [
                ['who' => 'RH', 'context' => 'Réponses homogènes sur la politique interne.'],
                ['who' => 'Managers', 'context' => 'Accès rapide aux règles applicables à leur équipe.'],
            ],
        ],
        'access_cta'       => [
            'title' => 'Accès',
            'items' => [
                ['label' => 'Demander un accès', 'href' => '/contact', 'style' => 'primary'],
                ['label' => 'Être notifié', 'href' => '/hub#lab-newsletter', 'style' => 'outline'],
            ],
        ],
        'feedback'         => $labFeedback,
    ],

    'conformity-check' => [
        'meta_title'       => 'Conformity Check — Laboratoire Complianz',
        'meta_description' => 'Parcours guidé de priorisation conformité (RGPD, AI Act) : projet en développement.',
        'kind'             => 'ongoing',
        'kind_label'       => 'Projet en cours',
        'hero'             => [
            'name'        => 'Conformity Check',
            'status'      => 'En développement',
            'status_tone' => 'orange',
            'eyebrow'     => 'Laboratoire',
            'value_line'  => 'Conformity Check — Priorisez vos actions conformité selon votre secteur et votre maturité.',
            'ctas'        => [
                ['label' => 'Demander un accès', 'href' => '/contact', 'style' => 'primary'],
            ],
        ],
        'problem_solution' => [
            'problem'       => 'Les registres d’actions sont souvent des listes longues sans notion de priorisation métier.',
            'market_limits'   => 'Les grilles « one size fits all » ignorent votre secteur et votre niveau de maturité.',
            'solution'      => 'Un parcours qui classe les chantiers utiles maintenant vs. plus tard, avec un langage business.',
            'pillars'       => [
                ['label' => 'Simplification', 'text' => 'Moins de bruit, plus de clarté sur l’essentiel.'],
                ['label' => 'Structuration', 'text' => 'Cartographie des priorités alignée sur vos enjeux.'],
                ['label' => 'Efficacité', 'text' => 'Budgets et temps orientés là où le risque est maximal.'],
            ],
        ],
        'features'         => [
            'title' => 'Fonctionnalités (niveau actuel)',
            'intro' => 'Livrables en cours de consolidation avec nos utilisateurs pilotes.',
            'items' => [
                ['title' => 'Questionnaire de maturité', 'text' => 'Positionnement express par domaine.', 'state' => 'live'],
                ['title' => 'Feuille de route suggérée', 'text' => 'Liste ordonnée des chantiers à traiter.', 'state' => 'wip'],
            ],
            'wip_note' => 'Connexion aux packs métiers Complianz à préciser.',
        ],
        'demo'             => [
            'title' => 'Démo & aperçu',
            'intro' => 'Ajoutez ici captures ou vidéo du tunnel.',
            'items' => [
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Conformity Check', 'caption' => 'Parcours guidé'],
            ],
        ],
        'progress'         => [
            'percent' => 50,
            'accent'  => 'orange',
            'roadmap' => [
                ['label' => 'Prototype validé', 'state' => 'done'],
                ['label' => 'Scénarios sectoriels', 'state' => 'current'],
                ['label' => 'Durcissement UX', 'state' => 'upcoming'],
                ['label' => 'Tests utilisateurs', 'state' => 'locked'],
            ],
        ],
        'use_cases'        => [
            'title' => 'Cas d’usage',
            'items' => [
                ['who' => 'Responsable conformité', 'context' => 'Présenter un plan crédible à la direction.'],
                ['who' => 'PME en croissance', 'context' => 'Structurer la conformité sans équipe dédiée à plein temps.'],
            ],
        ],
        'access_cta'       => [
            'title' => 'Accès',
            'items' => [
                ['label' => 'Demander un accès', 'href' => '/contact', 'style' => 'primary'],
                ['label' => 'Planifier une démo', 'href' => '/contact', 'style' => 'outline'],
            ],
        ],
        'feedback'         => $labFeedback,
    ],

    'datasync-pro' => [
        'meta_title'       => 'DataSync Pro — Projet en test | Complianz',
        'meta_description' => 'Synchronisation des sources métier vers un entrepôt léger : version test — rejoignez les bêta-testeurs.',
        'kind'             => 'beta',
        'kind_label'       => 'Projet en test',
        'beta_disclaimer'  => [
            'title' => 'Version test',
            'body'  => 'Version en cours de test — certaines fonctionnalités peuvent être instables ou évoluer sans préavis. Merci de privilégier les environnements sandbox pour vos expérimentations.',
        ],
        'beta_access'      => [
            'title' => 'Accès direct',
            'intro' => 'Liens à personnaliser : URL de l’outil, environnement sandbox et parcours de création de compte testeur.',
            'items' => [
                ['label' => 'Ouvrir l’outil (version test)', 'href' => 'https://example.com/datasync', 'hint' => 'Remplacez par l’URL réelle — SSO ou identifiants fournis après inscription.', 'external' => true],
                ['label' => 'Sandbox / données de démonstration', 'href' => 'https://example.com/datasync-sandbox', 'hint' => 'Jeu de données fictives pour tester sans risque.', 'external' => true],
                ['label' => 'Créer un compte testeur', 'href' => '/contact', 'hint' => 'Accès sur demande — nous créons ou activons votre profil.', 'external' => false],
            ],
        ],
        'beta_feedback'    => [
            'id'    => 'lab-beta-feedback',
            'title' => 'Feedback structuré',
            'intro' => 'Signalez un bug, proposez une évolution ou décrivez votre usage : tout arrive dans la même file priorisée par l’équipe laboratoire.',
        ],
        'beta_changelog'   => [
            'title' => 'Changelog',
            'intro' => 'Historique des versions livrées aux testeurs — tenir ce bloc à jour renforce la transparence.',
            'items' => [
                [
                    'version'    => 'v0.4',
                    'date'       => '2026-04-28',
                    'date_label' => '28 avril 2026',
                    'changes'    => [
                        'Correction : plantage à l’import d’un fichier CSV vide.',
                        'Amélioration : libellés et ordre des connecteurs dans l’assistant.',
                    ],
                ],
                [
                    'version'    => 'v0.3',
                    'date'       => '2026-03-15',
                    'date_label' => '15 mars 2026',
                    'changes'    => [
                        'Ajout : planification des imports fichiers.',
                        'Ajout : vue synthèse des flux en erreur.',
                    ],
                ],
            ],
        ],
        'beta_community'   => [
            'title' => 'Communauté',
            'intro' => 'Canaux optionnels pour échanger entre testeurs — à activer selon votre organisation.',
            'items' => [
                ['label' => 'Canal Slack laboratoire', 'href' => '#', 'hint' => 'URL à renseigner — accès sur invitation', 'external' => false],
                ['label' => 'Liste de diffusion bêta', 'href' => '/hub#lab-newsletter', 'hint' => 'Annonces de versions et ouvertures', 'external' => false],
                ['label' => 'Discord (optionnel)', 'href' => '#', 'hint' => 'Si vous ouvrez un espace communautaire dédié', 'external' => false],
            ],
        ],
        'hero'             => [
            'name'        => 'DataSync Pro',
            'status'      => 'Bêta ouverte',
            'status_tone' => 'blue',
            'eyebrow'     => 'Laboratoire · Accès anticipé',
            'value_line'  => 'DataSync Pro — Fiabilisez vos flux données métier vers vos indicateurs, sans ressaisie permanente.',
            'ctas'        => [
                ['label' => 'Accéder à la version test', 'href' => '#lab-beta-access', 'style' => 'primary'],
                ['label' => 'Donner son feedback', 'href' => '#lab-beta-feedback', 'style' => 'secondary'],
            ],
        ],
        'problem_solution' => [
            'problem'       => 'Les fichiers et exports manuels créent décalages, erreurs et fatigue pour construire des indicateurs.',
            'market_limits'   => 'Les ETL lourds sont souvent hors budget ou hors compétences pour les équipes mid-market.',
            'solution'      => 'Une chaîne légère planifiée, pensée pour les usages décisionnels non temps réel critique.',
            'pillars'       => [
                ['label' => 'Simplification', 'text' => 'Moins de scripts artisanaux.'],
                ['label' => 'Structuration', 'text' => 'Sources et transformations documentées.'],
                ['label' => 'Efficacité', 'text' => 'Indicateurs à jour pour les réunions de pilotage.'],
            ],
        ],
        'features'         => [
            'title' => 'Fonctionnalités (niveau actuel)',
            'intro' => 'Version test — fonctionnalités stabilisées progressivement.',
            'items' => [
                ['title' => 'Connecteurs sources standards', 'text' => 'Imports planifiés depuis vos fichiers et bases courantes.', 'state' => 'live'],
                ['title' => 'Entrepôt léger', 'text' => 'Stockage structuré pour vos KPI.', 'state' => 'live'],
                ['title' => 'Monitoring des erreurs', 'text' => 'Alertes sur les flux en échec.', 'state' => 'wip'],
            ],
            'wip_note' => 'Roadmap connecteurs étendue selon retours bêta.',
        ],
        'demo'             => [
            'title' => 'Démo & aperçu',
            'intro' => 'Schéma de flux ou captures d’écran à ajouter.',
            'items' => [
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'DataSync Pro', 'caption' => 'Vue pipeline'],
            ],
        ],
        'progress'         => [
            'percent' => 72,
            'accent'  => 'blue',
            'roadmap' => [
                ['label' => 'Architecture flux validée', 'state' => 'done'],
                ['label' => 'Bêta terrain', 'state' => 'current'],
                ['label' => 'Scénarios avancés', 'state' => 'upcoming'],
                ['label' => 'Industrialisation', 'state' => 'locked'],
            ],
        ],
        'use_cases'        => [
            'title' => 'Cas d’usage',
            'items' => [
                ['who' => 'DSI / équipe data', 'context' => 'Alimenter tableaux de bord sans projet BI géant.'],
                ['who' => 'Directions métiers', 'context' => 'Indicateurs cohérents pour le comité.'],
            ],
        ],
        'access_cta'       => [
            'title' => 'Accès bêta & suivi',
            'items' => [
                ['label' => 'Rejoindre la bêta', 'href' => '/contact', 'style' => 'primary'],
                ['label' => 'Demander une démo', 'href' => '/contact', 'style' => 'outline'],
                ['label' => 'Être notifié', 'href' => '/hub#lab-newsletter', 'style' => 'outline'],
            ],
        ],
        'feedback'         => $labFeedback,
    ],

    'smartbot-builder' => [
        'meta_title'       => 'SmartBot Builder — Projet en test | Complianz',
        'meta_description' => 'Assemblage d’agents conversationnels métiers : version test Complianz.',
        'kind'             => 'beta',
        'kind_label'       => 'Projet en test',
        'beta_disclaimer'  => [
            'title' => 'Version test',
            'body'  => 'Version en cours de test — certaines fonctionnalités peuvent être instables ou évoluer sans préavis. Ne déployez pas encore sur des données critiques sans validation interne.',
        ],
        'beta_access'      => [
            'title' => 'Accès direct',
            'intro' => 'Studio de test, bac à sable et inscription — URLs à adapter à votre déploiement.',
            'items' => [
                ['label' => 'Ouvrir le studio (version test)', 'href' => 'https://example.com/smartbot', 'hint' => 'URL réelle à renseigner.', 'external' => true],
                ['label' => 'Environnement sandbox', 'href' => 'https://example.com/smartbot-demo', 'hint' => 'Corpus et agents fictifs pour prototyper.', 'external' => true],
                ['label' => 'Demander un compte testeur', 'href' => '/contact', 'hint' => 'Création de profil sous réserve de disponibilité.', 'external' => false],
            ],
        ],
        'beta_feedback'    => [
            'id'    => 'lab-beta-feedback',
            'title' => 'Feedback structuré',
            'intro' => 'Bug, suggestion ou retour d’expérience sur les agents : un formulaire unique pour nourrir la roadmap.',
        ],
        'beta_changelog'   => [
            'title' => 'Changelog',
            'intro' => 'Suivi des livraisons visibles par les testeurs.',
            'items' => [
                [
                    'version'    => 'v0.4',
                    'date'       => '2026-04-20',
                    'date_label' => '20 avril 2026',
                    'changes'    => [
                        'Correction : perte de contexte sur les conversations longues.',
                        'Amélioration : sélection du corpus par dossier.',
                    ],
                ],
                [
                    'version'    => 'v0.3',
                    'date'       => '2026-03-02',
                    'date_label' => '2 mars 2026',
                    'changes'    => [
                        'Ajout : personas métiers prédéfinis.',
                        'Ajout : export des journaux pour audit interne (beta).',
                    ],
                ],
            ],
        ],
        'beta_community'   => [
            'title' => 'Communauté',
            'intro' => 'Points de rencontre optionnels pour les utilisateurs avancés.',
            'items' => [
                ['label' => 'Slack — salon SmartBot', 'href' => '#', 'hint' => 'Lien à compléter', 'external' => false],
                ['label' => 'Newsletter laboratoire', 'href' => '/hub#lab-newsletter', 'hint' => null, 'external' => false],
            ],
        ],
        'hero'             => [
            'name'        => 'SmartBot Builder',
            'status'      => 'Bêta ouverte',
            'status_tone' => 'blue',
            'eyebrow'     => 'Laboratoire · Accès anticipé',
            'value_line'  => 'SmartBot Builder — Assemblez des agents métiers à partir de vos bases documentaires.',
            'ctas'        => [
                ['label' => 'Accéder à la version test', 'href' => '#lab-beta-access', 'style' => 'primary'],
                ['label' => 'Donner son feedback', 'href' => '#lab-beta-feedback', 'style' => 'secondary'],
            ],
        ],
        'problem_solution' => [
            'problem'       => 'Les projets d’agents IA peinent à partir sans corpus maîtrisé et sans garde-fous.',
            'market_limits'   => 'Les offres généralistes négligent la conformité et le versioning des sources.',
            'solution'      => 'Un atelier pour définir périmètre, sources et comportements attendus — puis tester vite avec les métiers.',
            'pillars'       => [
                ['label' => 'Simplification', 'text' => 'Premiers agents sans projet DSI de 18 mois.'],
                ['label' => 'Structuration', 'text' => 'Sources et règles explicites.'],
                ['label' => 'Efficacité', 'text' => 'Itérations courtes avec les utilisateurs.'],
            ],
        ],
        'features'         => [
            'title' => 'Fonctionnalités (niveau actuel)',
            'intro' => 'Ce qui est livré dans la bêta actuelle.',
            'items' => [
                ['title' => 'Import de corpus contrôlé', 'text' => 'Jeux de documents validés.', 'state' => 'live'],
                ['title' => 'Personas & prompts métiers', 'text' => 'Cadres de réponse alignés marque / conformité.', 'state' => 'live'],
                ['title' => 'Observabilité des conversations', 'text' => 'Logs pour audit interne.', 'state' => 'wip'],
            ],
            'wip_note' => 'Évolutions dictées par les pilotes.',
        ],
        'demo'             => [
            'title' => 'Démo & aperçu',
            'intro' => 'Vidéo ou GIF du configurateur recommandé.',
            'items' => [
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'SmartBot Builder', 'caption' => 'Studio de configuration'],
            ],
        ],
        'progress'         => [
            'percent' => 68,
            'accent'  => 'blue',
            'roadmap' => [
                ['label' => 'Atelier cadrage', 'state' => 'done'],
                ['label' => 'Bêta ouverte', 'state' => 'current'],
                ['label' => 'Renforcement sécurité', 'state' => 'upcoming'],
                ['label' => 'Packaging offre', 'state' => 'locked'],
            ],
        ],
        'use_cases'        => [
            'title' => 'Cas d’usage',
            'items' => [
                ['who' => 'Directions métier', 'context' => 'Automatiser FAQ et circuits documentaires.'],
                ['who' => 'Support client', 'context' => 'Réponses alignées produit / réglementation.'],
            ],
        ],
        'access_cta'       => [
            'title' => 'Accès bêta & suivi',
            'items' => [
                ['label' => 'Rejoindre la bêta', 'href' => '/contact', 'style' => 'primary'],
                ['label' => 'Être notifié', 'href' => '/hub#lab-newsletter', 'style' => 'outline'],
            ],
        ],
        'feedback'         => $labFeedback,
    ],

    'erp-conformite-tpe' => [
        'meta_title'       => 'ERP Conformité TPE — Idée & concept | Complianz',
        'meta_description' => 'Couche de pilotage conformité pour TPE : concept en incubation — manifestez votre intérêt.',
        'kind'             => 'idea',
        'kind_label'       => 'Idée & concept',
        'hero'             => [
            'name'        => 'ERP Conformité TPE',
            'status'      => 'Incubation',
            'status_tone' => 'green',
            'eyebrow'     => 'Laboratoire',
            'value_line'  => 'ERP Conformité TPE — La conformité utile au quotidien, sans module ERP surdimensionné.',
            'ctas'        => [
                ['label' => 'Répondre au questionnaire', 'href' => '#lab-idea-validation', 'style' => 'primary'],
                ['label' => 'Voir les projections', 'href' => '#lab-idea-mock-title', 'style' => 'secondary'],
            ],
        ],
        'idea_problem'     => [
            'title' => 'Le problème',
            'body'  => 'Les TPE vivent la conformité dans des fichiers Excel et des e-mails : le registre est incomplet, les preuves sont éparpillées, et au moment d’un audit tout doit être reconstitué dans l’urgence. Les modules « conformité » des grands ERP sont souvent trop chers ou trop complexes pour une équipe de 5 à 50 personnes.',
        ],
        'idea_concept'     => [
            'title'         => 'Le concept',
            'lead'          => 'Pas de bullshit : une couche focalisée sur ce dont vous avez besoin pour tenir vos obligations et rassurer votre direction.',
            'how_it_works'  => 'Une application légère qui se branche sur vos référentiels existants (exports ERP, fichiers RH…) pour tenir un registre vivant, rattacher les preuves et sortir des vues « direction » sur le niveau de couverture par domaine.',
            'what_changes'  => 'Moins de ressaisie, un déclenchement d’actions quand un traitement change, et un dossier déjà structuré quand l’auditeur ou le client vous demande des garanties.',
        ],
        'idea_mockup'      => [
            'title' => 'Mockup & projection',
            'intro' => 'Visuels de substitution à remplacer par vos maquettes Figma ou captures — l’important est de matérialiser le concept, même en « fake ».',
            'items' => [
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Wireframe tableau de bord conformité TPE', 'caption' => 'Wireframe — vue synthèse des statuts et alertes'],
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Schéma flux registre et ERP', 'caption' => 'Schéma — lien registre / sources métier (à affiner)'],
            ],
        ],
        'idea_value'       => [
            'title' => 'Proposition de valeur',
            'intro' => 'Ce que les sponsors métier entendent généralement derrière ce type d’outil.',
            'items' => [
                ['label' => 'Gain de temps', 'text' => 'Réduire les semaines de préparation avant contrôle ou audit.'],
                ['label' => 'Gain d’argent / coût évité', 'text' => 'Limiter le recours externe massif et les sanctions liées aux oublis.'],
                ['label' => 'Simplification', 'text' => 'Une lecture unique pour la direction : où on est exposé, et quoi traiter en premier.'],
            ],
        ],
        'idea_validation'  => $labIdeaValidationBase,
        'progress'         => [
            'percent' => 15,
            'accent'  => 'green',
            'roadmap' => [
                ['label' => 'Ateliers besoins TPE', 'state' => 'current'],
                ['label' => 'Prototype figé', 'state' => 'locked'],
                ['label' => 'Tests utilisateurs', 'state' => 'locked'],
                ['label' => 'Décision industrialisation', 'state' => 'locked'],
            ],
        ],
        'use_cases'        => [
            'title' => 'Pour qui ?',
            'items' => [
                ['who' => 'Dirigeant TPE', 'context' => 'Voir où est le risque sans dépendre d’un consultant en permanence.'],
                ['who' => 'Responsable qualité / conformité', 'context' => 'Tenir un dossier crédible avec peu de ressources.'],
            ],
        ],
    ],

    'assistant-ia-rgpd' => [
        'meta_title'       => 'Assistant IA RGPD — Idée & concept | Complianz',
        'meta_description' => 'Assistant contextualisé pour questions RGPD internes : concept en incubation.',
        'kind'             => 'idea',
        'kind_label'       => 'Idée & concept',
        'hero'             => [
            'name'        => 'Assistant IA RGPD',
            'status'      => 'Incubation',
            'status_tone' => 'green',
            'eyebrow'     => 'Laboratoire',
            'value_line'  => 'Assistant IA RGPD pour TPE — votre conformité sans juriste à temps plein.',
            'ctas'        => [
                ['label' => 'Répondre au questionnaire', 'href' => '#lab-idea-validation', 'style' => 'primary'],
                ['label' => 'Voir les projections', 'href' => '#lab-idea-mock-title', 'style' => 'secondary'],
            ],
        ],
        'idea_problem'     => [
            'title' => 'Le problème',
            'body'  => 'Les équipes bloquent le juridique et le DPO sur les mêmes questions : durées de conservation, sous-traitance, droits des personnes… Les générateurs de FAQ génériques ne connaissent pas vos traitements ni vos politiques internes : les réponses sont soit vagues, soit hors sujet.',
        ],
        'idea_concept'     => [
            'title'         => 'Le concept',
            'lead'          => 'Un assistant qui lit vos documents approuvés (politique, registre, notices) et répond dans un cadre défini avec vous — sans inventer de traitements inexistants.',
            'how_it_works'  => 'Questions en langage naturel → recherche dans votre corpus validé → réponse avec références aux extraits sources → escalade vers un humain si le cas sort du périmètre.',
            'what_changes'  => 'Les métiers gagnent en autonomie ; le juridique garde le contrôle sur les sources et les cas sensibles. Le registre et les documents vivants deviennent la « single source of truth ».',
        ],
        'idea_mockup'      => [
            'title' => 'Mockup & projection',
            'intro' => 'Remplacez ces placeholders par une capture de chat réelle ou un wireframe de votre futur widget intranet.',
            'items' => [
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Maquette fenêtre de dialogue assistant RGPD', 'caption' => 'Maquette — conversation avec citations de sources'],
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Schéma corpus et garde-fous', 'caption' => 'Schéma — corpus interne, filtres, escalade DPO'],
            ],
        ],
        'idea_value'       => [
            'title' => 'Proposition de valeur',
            'intro' => 'Arguments typiques pour une direction ou un sponsor projet.',
            'items' => [
                ['label' => 'Gain de temps', 'text' => 'Réduire les tickets récurrents vers le juridique et accélérer les réponses métiers.'],
                ['label' => 'Gain d’argent', 'text' => 'Éviter les conseils externes répétitifs et mieux cibler les budgets conformité.'],
                ['label' => 'Simplification', 'text' => 'Une porte d’entrée unique et des réponses traçables pour les audits.'],
            ],
        ],
        'idea_validation'  => array_merge($labIdeaValidationBase, [
            'vote_mode' => 'advanced',
            'intro'     => 'Questionnaire court + vote emoji ; les scores optionnels affinent la priorisation pour les sponsors.',
        ]),
        'progress'         => [
            'percent' => 22,
            'accent'  => 'green',
            'roadmap' => [
                ['label' => 'Design exploratoire', 'state' => 'done'],
                ['label' => 'Interviews utilisateurs', 'state' => 'current'],
                ['label' => 'Prototype conversationnel', 'state' => 'locked'],
                ['label' => 'Phase pilote', 'state' => 'locked'],
            ],
        ],
        'use_cases'        => [
            'title' => 'Pour qui ?',
            'items' => [
                ['who' => 'DPO / juridique', 'context' => 'Réduire le bruit des demandes récurrentes tout en gardant la main sur les cas sensibles.'],
                ['who' => 'Métiers', 'context' => 'Obtenir une réponse alignée sur la politique interne, avec une trace des sources.'],
            ],
        ],
    ],

    'audit-express' => [
        'meta_title'       => 'Audit Express — Idée & concept | Complianz',
        'meta_description' => 'Diagnostic court de maturité conformité : concept en incubation.',
        'kind'             => 'idea',
        'kind_label'       => 'Idée & concept',
        'hero'             => [
            'name'        => 'Audit Express',
            'status'      => 'Incubation',
            'status_tone' => 'green',
            'eyebrow'     => 'Laboratoire',
            'value_line'  => 'Audit Express — Un diagnostic de maturité en une session, avec priorités claires pour la direction.',
            'ctas'        => [
                ['label' => 'Répondre au questionnaire', 'href' => '#lab-idea-validation', 'style' => 'primary'],
                ['label' => 'Voir les projections', 'href' => '#lab-idea-mock-title', 'style' => 'secondary'],
            ],
        ],
        'idea_problem'     => [
            'title' => 'Le problème',
            'body'  => 'Beaucoup d’organisations achètent un « audit complet » alors qu’elles ne savent pas encore où elles sont vulnérables : les grilles gratuites en ligne donnent un score, mais pas une feuille de route priorisée pour votre secteur et votre taille.',
        ],
        'idea_concept'     => [
            'title'         => 'Le concept',
            'lead'          => 'Un parcours court en ligne ou accompagné : questionnaire métier, notation par domaine (RH, sous-traitance, sécurité…), puis rapport lisible pour la direction avec les trois chantiers à lancer en premier.',
            'how_it_works'  => 'Réponses cadrées → score et cartographie des écarts → recommandations ordonnées (impact × effort) → option atelier pour valider avec vos équipes.',
            'what_changes'  => 'La direction sait où investir en premier ; la conformité arrête de tout traiter avec la même urgence.',
        ],
        'idea_mockup'      => [
            'title' => 'Mockup & projection',
            'intro' => 'Exemples de livrables à produire : même un mock « fake » vaut mieux qu’un texte seul.',
            'items' => [
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Wireframe rapport Audit Express', 'caption' => 'Wireframe — synthèse scores et priorités'],
                ['type' => 'image', 'src' => '/assets/img/lab/demo-placeholder.svg', 'alt' => 'Schéma parcours questionnaire', 'caption' => 'Schéma — tunnel questionnaire → rapport'],
            ],
        ],
        'idea_value'       => [
            'title' => 'Proposition de valeur',
            'intro' => 'Ce que les décideurs cherchent avant d’engager un audit lourd.',
            'items' => [
                ['label' => 'Gain de temps', 'text' => 'Décision éclairée en jours, pas en mois de réunions non structurées.'],
                ['label' => 'Gain d’argent', 'text' => 'Éviter de financer des chantiers « dans le vent » avant d’avoir cadré les risques.'],
                ['label' => 'Simplification', 'text' => 'Un langage business pour parler risque et conformité sans jargon inutile.'],
            ],
        ],
        'idea_validation'  => $labIdeaValidationBase,
        'progress'         => [
            'percent' => 18,
            'accent'  => 'green',
            'roadmap' => [
                ['label' => 'Prototype questionnaire', 'state' => 'current'],
                ['label' => 'Tests avec 5 organisations', 'state' => 'locked'],
                ['label' => 'Calibration scoring', 'state' => 'locked'],
                ['label' => 'Décision go / no-go', 'state' => 'locked'],
            ],
        ],
        'use_cases'        => [
            'title' => 'Pour qui ?',
            'items' => [
                ['who' => 'Direction', 'context' => 'Arbitrer budget conformité sans engagement d’audit long.'],
                ['who' => 'CISO / RSSI', 'context' => 'Photographie rapide des lacunes majeures avant un chantier sécu.'],
            ],
        ],
    ],
];
