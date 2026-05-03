<?php

/**
 * Page Laboratoire / Hub d’innovation — projets non « officiels », POC, bêtas, idées.
 */
return [
    'meta_title'       => 'Laboratoire Complianz System — Hub d’innovation, POC & bêtas',
    'meta_description' => 'Découvrez nos projets en cours, expérimentations, produits en test et idées en incubation. Participez aux bêtas et co-construisez avec nous.',

    'hero' => [
        'eyebrow'         => 'Hub d’innovation',
        'title_before'    => 'Le laboratoire',
        'title_highlight' => 'Complianz System',
        'lead'            => 'L’espace où nous prévisualisons l’avenir : prototypes, POC et produits en test avant industrialisation. Pour les équipes curieuses, les early adopters et les partenaires qui veulent avancer avec nous.',
        'cta'             => [
            'label' => 'Tester un produit',
            'href'  => '#lab-beta',
        ],
        'search_placeholder' => 'Rechercher un projet, une idée…',
        'stats'           => [
            ['value' => '4',  'label' => 'Projets en cours'],
            ['value' => '2',  'label' => 'Produits en test'],
            ['value' => '20+', 'label' => 'Idées & concepts'],
            ['value' => '50+', 'label' => 'Contributeurs'],
        ],
    ],

    'ongoing' => [
        'id'     => 'lab-ongoing',
        'title'  => 'Projets en cours',
        'lead'   => 'Les solutions sur lesquelles nous travaillons activement.',
        'legend' => [
            ['tone' => 'purple', 'label' => 'Expérimentation'],
            ['tone' => 'orange', 'label' => 'En développement'],
            ['tone' => 'blue', 'label' => 'Bêta privée'],
        ],
        'items'  => [
            [
                'title'       => 'AuditIA',
                'description' => 'Copilote d’analyse pour préparer les audits RGPD & ISO : questionnaire intelligent, cartographie des traitements esquisse.',
                'status'      => 'En développement',
                'status_tone' => 'orange',
                'accent'      => 'orange',
                'progress'    => 65,
                'tags'        => ['IA', 'RGPD', 'Compliance'],
                'href'        => '/lab/auditia',
                'link_label'  => 'En savoir plus',
                'cta_beta'    => false,
            ],
            [
                'title'          => 'SEO Dashboard',
                'description'    => 'Tableaux de suivi SEO et données analytics consolidées pour prioriser les actions et suivre la visibilité.',
                'status'         => 'Bêta privée',
                'status_tone'    => 'blue',
                'accent'         => 'blue',
                'progress'       => 85,
                'tags'           => ['SEO', 'Data'],
                'href'           => '/lab/seo-dashboard',
                'link_label'     => 'En savoir plus',
                'cta_beta'       => true,
                'beta_cta_label' => 'Rejoindre la bêta',
                'beta_href'      => '/contact',
            ],
            [
                'title'       => 'Agent RH',
                'description' => 'Agent conversationnel pour FAQ internes, relances de formation et point sur les obligations du personnel.',
                'status'      => 'Expérimentation',
                'status_tone' => 'purple',
                'accent'      => 'purple',
                'progress'    => 30,
                'tags'        => ['IA', 'RH'],
                'href'        => '/lab/agent-rh',
                'link_label'  => 'En savoir plus',
                'cta_beta'    => false,
            ],
            [
                'title'       => 'Conformity Check',
                'description' => 'Parcours guidé pour prioriser les actions conformité (RGPD, AI Act) selon votre secteur et votre maturité.',
                'status'      => 'En développement',
                'status_tone' => 'orange',
                'accent'      => 'orange',
                'progress'    => 50,
                'tags'        => ['UX', 'Conformité', 'IA'],
                'href'        => '/lab/conformity-check',
                'link_label'  => 'En savoir plus',
                'cta_beta'    => false,
            ],
        ],
    ],

    'beta' => [
        'id'          => 'lab-beta',
        'badge'       => 'Accès anticipé',
        'title'       => 'Projets en test',
        'lead'        => 'Ces solutions ne sont pas finalisées. Il peut y avoir des bugs, des fonctionnalités manquantes. Votre feedback est essentiel pour les améliorer.',
        'items'       => [
            [
                'icon'           => 'database',
                'title'          => 'DataSync Pro',
                'description'    => 'Synchronisation planifiée entre vos sources métier et un entrepôt léger pour vos indicateurs. Phase bêta pour usages non critiques.',
                'metrics'        => [
                    ['value' => '12', 'unit' => 'testeurs'],
                    ['value' => '34', 'unit' => 'feedbacks'],
                ],
                'cta'            => 'Accéder à la version test',
                'cta_href'       => '/lab/datasync-pro',
                'feedback_label' => 'Donner son feedback',
                'feedback_href'  => '/contact',
            ],
            [
                'icon'           => 'robot',
                'title'          => 'SmartBot Builder',
                'description'    => 'Assemblez des agents conversationnels métiers à partir de vos bases documentaires et règles internes. Version test pour scénarios pilotes.',
                'metrics'        => [
                    ['value' => '8', 'unit' => 'testeurs'],
                    ['value' => '21', 'unit' => 'feedbacks'],
                ],
                'cta'            => 'Accéder à la version test',
                'cta_href'       => '/lab/smartbot-builder',
                'feedback_label' => 'Donner son feedback',
                'feedback_href'  => '/contact',
            ],
        ],
    ],

    'ideas' => [
        'id'        => 'lab-ideas',
        'badge'     => 'Incubation',
        'title'     => 'Idées et concepts',
        'lead'      => 'Des idées que nous explorons. Manifestez votre intérêt pour influencer nos priorités de développement.',
        'cta_label' => 'voter pour ce projet',
        'items'     => [
            [
                'icon'        => 'lightning',
                'title'       => 'ERP Conformité TPE',
                'description' => 'Couche de pilotage conformité pensée pour les TPE : contrôles essentiels, preuves et reporting sans complexité ERP.',
                'interested'  => 47,
                'href'        => '/lab/erp-conformite-tpe',
            ],
            [
                'icon'        => 'brain',
                'title'       => 'Assistant IA RGPD',
                'description' => 'Assistant contextualisé pour répondre aux questions RGPD internes et préparer vos analyses d’impact.',
                'interested'  => 62,
                'href'        => '/lab/assistant-ia-rgpd',
            ],
            [
                'icon'        => 'leaf',
                'title'       => 'Audit Express',
                'description' => 'Parcours court pour un diagnostic rapide de maturité conformité et une feuille de route priorisée.',
                'interested'  => 31,
                'href'        => '/lab/audit-express',
            ],
        ],
    ],

    'participate' => [
        'title'   => 'Participez au laboratoire',
        'intro'   => 'Entre R&D et mise sur le marché, nous cherchons des profils pour tester, challenger et co-construire.',
        'bullets' => [
            [
                'title' => 'Tester',
                'text'  => 'Accédez aux bêtas, essayez les prototypes et remontez les frictions réelles.',
                'icon'  => 'flask',
            ],
            [
                'title' => 'Feedback',
                'text'  => 'Vos retours orientent les priorités produit et la documentation.',
                'icon'  => 'message',
            ],
            [
                'title' => 'Partenariat',
                'text'  => 'Co-pilotez un POC ou un cas d’usage dans votre secteur avec nos équipes.',
                'icon'  => 'handshake',
            ],
        ],
    ],

    'newsletter' => [
        'title' => 'Restez informé des nouveautés du laboratoire',
        'lead'  => 'Ouvertures de bêta, nouvelles idées et invitations ponctuelles — pas plus d’un mail par mois.',
    ],
];
