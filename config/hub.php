<?php

/**
 * Page Laboratoire / Hub d’innovation — projets non « officiels », POC, bêtas, idées.
 *
 * Fiches détaillées : même logique que les pages lab existantes (voir `templates/pages/lab-project.twig`,
 * route `GET /lab/:slug` via `LabProjectController`). Quand le contenu sera prêt, retirer `coming_soon`
 * et renseigner `href` / `cta_href` (ex. `/lab/stalyos`, `/lab/landing-hub`, `/lab/calendrier-potager`, `/lab/carte-a-champignons`).
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
            ['value' => '1',  'label' => 'Projets en cours'],
            ['value' => '1',  'label' => 'Produits en test'],
            ['value' => '2', 'label' => 'Idées & concepts'],
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
                'title'         => 'Stalyos',
                'description'   => 'Projet phare du laboratoire — détail à venir.',
                'status'        => 'En développement',
                'status_tone'   => 'orange',
                'accent'        => 'orange',
                'progress'      => 40,
                'tags'          => ['Hub', 'Innovation'],
                'href'          => '/lab/stalyos',
                'link_label'    => 'En savoir plus',
                'cta_beta'      => false,
                'coming_soon'   => true,
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
                'icon'           => 'layout',
                'title'          => 'Landing hub',
                'description'    => 'Espace d’accueil et de présentation des expérimentations — détail à venir.',
                'metrics'        => [
                    ['value' => '—', 'unit' => 'bêta'],
                    ['value' => '—', 'unit' => 'feedbacks'],
                ],
                'cta'            => 'Accéder à la version test',
                'cta_href'       => '/lab/landing-hub',
                'feedback_label' => 'Donner son feedback',
                'feedback_href'  => '/contact',
                'coming_soon'    => true,
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
                'icon'          => 'leaf',
                'title'         => 'Calendrier potager',
                'description'   => 'Outil de planification des semis et récoltes — détail à venir.',
                'interested'    => 0,
                'href'          => '/lab/calendrier-potager',
                'coming_soon'   => true,
            ],
            [
                'icon'          => 'mushroom',
                'title'         => 'Carte à champignons',
                'description'   => 'Guide terrain et identification — détail à venir.',
                'interested'    => 0,
                'href'          => '/lab/carte-a-champignons',
                'coming_soon'   => true,
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
