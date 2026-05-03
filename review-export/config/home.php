<?php

/**
 * Contenu éditorial de la page d’accueil (sous le hero).
 */
return [
    'meta_title'       => 'Complianz System — Conseil en structuration, conformité, performance et transformation des entreprises',
    'meta_description' => 'Organisation, outils, conformité, performance : nous structurons votre système global. Structuration, audits, développement, SEO, infogérance et IA — un interlocuteur unique.',

    'client_problem' => [
        'tag'       => 'Le constat',
        'title'     => 'Pourquoi votre entreprise plafonne (même avec les bons outils)',
        'subtitle'  => 'Les symptômes se ressemblent : dispersion, silos et arbitrages pris sans une vision transverse des risques et des leviers.',
        'cards'     => [
            [
                'num'    => '01',
                'theme'  => 'tools',
                'title'  => 'Trop d’outils',
                'text'   => 'Empilement de logiciels sans orchestration : coûts cachés, ressaisies et dette opérationnelle.',
                'impact' => 'Aucun système',
            ],
            [
                'num'    => '02',
                'theme'  => 'process',
                'title'  => 'Des process flous',
                'text'   => 'Des étapes interprétées différemment selon les équipes : délais, erreurs et friction au quotidien.',
                'impact' => 'Perte de temps',
            ],
            [
                'num'    => '03',
                'theme'  => 'compliance',
                'title'  => 'Une conformité subie',
                'text'   => 'Obligations traitées au dernier moment : contrôles, audits et stress récurrents pour les équipes.',
                'impact' => 'Risque constant',
            ],
            [
                'num'    => '04',
                'theme'  => 'data',
                'title'  => 'Des décisions sans données fiables',
                'text'   => 'Indicateurs partiels ou contradictoires : les priorités se décident au feeling plutôt que sur les faits.',
                'impact' => 'Manque de visibilité',
            ],
        ],
        'resolution' => [
            'headline' => [
                'line1' => 'Le problème n’est pas la complexité.',
                'line2' => 'C’est le manque de structure.',
            ],
            'body' => 'Complianz relie vos enjeux métiers, conformité et SI dans une trajectoire unique : du diagnostic à la performance mesurable — sans empiler des projets incompatibles.',
            'benefits' => [
                'Organisation claire des rôles et des flux',
                'Outils alignés sur les processus réels',
                'Conformité pilotée, pas subie',
                'Indicateurs utiles à la direction',
                'Capacité à faire évoluer le système dans la durée',
            ],
        ],
    ],

    'positioning' => [
        'tag'    => 'Différenciation',
        'hammer' => [
            ['kind' => 'reject', 'text' => 'Nous ne sommes pas une agence.'],
            ['kind' => 'reject', 'text' => 'Nous ne sommes pas un prestataire.'],
            ['kind' => 'brand', 'text' => 'Nous sommes architectes de système.'],
        ],
        'title'  => 'Nous ne vendons pas des prestations. Nous structurons votre entreprise.',
        'blocks' => [
            ['tone' => 'negative', 'label' => 'Agences', 'outcome' => 'actions isolées'],
            ['tone' => 'negative', 'label' => 'Outils', 'outcome' => 'empilement'],
            ['tone' => 'positive', 'label' => 'Complianz', 'outcome' => 'système global'],
        ],
        /** Narration scroll (chaos → système) — section immersive */
        'scroll_story' => [
            'chaos_title'   => 'Aujourd’hui : le chaos',
            'system_title'    => 'Demain : le système',
            'bridge'          => 'On ne remplace pas vos outils. On les organise.',
            'cta'             => 'Parler de votre organisation',
            /**
             * Logos outils : PNG dans public/assets/images/photos/.
             * (Chemins encodés pour les espaces dans les noms de fichier.)
             */
            'icons'           => [
                ['abbr' => 'XL', 'title' => 'Tableur & fichiers', 'img' => '/assets/images/photos/organisation%20des%20outils%20entreprise%20excel.png'],
                ['abbr' => 'Ml', 'title' => 'Messagerie', 'img' => '/assets/images/photos/organisation%20des%20outils%20entreprise%20gmail.png'],
                ['abbr' => 'Drv', 'title' => 'Stockage', 'img' => '/assets/images/photos/organisation%20des%20outils%20entreprise%20drive.png'],
                ['abbr' => 'Sl', 'title' => 'Messagerie équipe', 'img' => '/assets/images/photos/organisation%20des%20outils%20entreprise%20slack.png'],
                ['abbr' => 'Tr', 'title' => 'Suivi de tâches', 'img' => '/assets/images/photos/organisation%20des%20outils%20entreprise%20notion.png'],
                ['abbr' => 'Hb', 'title' => 'CRM', 'img' => '/assets/images/photos/organisation%20des%20outils%20entreprise%20hubspot.png'],
                ['abbr' => 'Ads', 'title' => 'Acquisition', 'img' => '/assets/images/photos/organisation%20des%20outils%20entreprise%20meta.png'],
                ['abbr' => 'BI', 'title' => 'Reporting', 'img' => '/assets/images/photos/organisation%20des%20outils%20entreprise%20bdd.png'],
            ],
            'chaos_labels' => [
                ['text' => 'Aucune visibilité globale'],
                ['text' => 'Perte de temps et d’énergie'],
                ['text' => 'Erreurs et ressaisies fréquentes'],
                ['text' => 'Décisions à l’aveugle'],
            ],
            'system_labels' => [
                ['text' => 'Vision globale en temps réel'],
                ['text' => 'Gain de temps et productivité'],
                ['text' => 'Conformité & risques maîtrisés'],
                ['text' => 'Décisions basées sur la donnée'],
            ],
            'stack' => [
                ['kicker' => 'PROCESSUS STRUCTURÉS', 'line' => 'Rôles, règles et automatisations', 'icon' => 1],
                ['kicker' => 'DONNÉES CENTRALISÉES', 'line' => 'Fiables, sécurisées, accessibles', 'icon' => 2],
                ['kicker' => 'PILOTAGE & TABLEAUX DE BORD', 'line' => 'Indicateurs en temps réel', 'icon' => 3],
            ],
            'outcome_box'    => 'Décisions éclairées',
            'outcome_subline' => 'Croissance maîtrisée',
        ],
        /** Bandeau CTA sous la section chaos → système */
        'approach_banner' => [
            'headline_line1' => 'Nous ne sommes pas une agence.',
            'headline_line2' => 'Nous concevons des systèmes.',
            'body'             => 'Complianz System est l’architecte de votre performance. Nous structurons, sécurisons et optimisons votre entreprise pour en faire un système cohérent, durable et évolutif.',
            'cta_label'        => 'Découvrir notre approche',
            'cta_href'         => '/a-propos',
        ],
    ],

    'poles_section' => [
        'tag'   => 'Transformation',
        'title' => 'Un système complet, pas des services isolés',
        'lead'  => 'Nous couvrons l’ensemble des dimensions critiques de votre entreprise pour bâtir un système cohérent, performant et durable.',
        'quote' => 'Isoler un projet règle un symptôme ; orchestrer un système fait tenir l’entreprise dans la durée.',
        /** Panneau sombre — accroche */
        'process_intro' => [
            'before' => 'De la stratégie à l’exécution,',
            'accent' => 'nous structurons votre réussite.',
        ],
        /** Hub visuel — écosystème */
        'hub' => [
            'img'    => '/assets/images/illustrations/WebP/Plan%20de%20travail%202.webp',
            'line1'  => 'Votre entreprise',
            'line2'  => 'au centre du système',
        ],
        'steps' => [
            [
                'title' => 'Structurer',
                'text'  => 'Gouvernance, gestion des risques et organisation : poser le cadre avant d’empiler les technologies.',
                'href'  => '/audit-conformite',
            ],
            [
                'title' => 'Outiller',
                'text'  => 'ERP, CRM, développement et SaaS — choisis et branchés sur vos processus réels.',
                'href'  => '/solutions-developpement',
            ],
            [
                'title' => 'Performer',
                'text'  => 'Stratégie digitale, acquisition, tunnels et pilotage : mesurer ce qui compte.',
                'href'  => '/performance-digitale',
            ],
            [
                'title' => 'Maintenir',
                'text'  => 'Infogérance, hébergement, maintenance et support : garder le SI disponible et à jour.',
                'href'  => '/support-maintenance',
            ],
            [
                'title' => 'Innover',
                'text'  => 'IA, automatisation et agents intelligents — avec gouvernance, transfert et garde-fous.',
                'href'  => '/intelligence-artificielle',
            ],
        ],
        'eco_heading' => 'Les cinq expertises — même méthode',
    ],

    'value' => [
        'tag'   => 'Pourquoi Complianz',
        'title' => 'De la conformité à la performance — sans silos',
        'lead'  => 'Nous relions juridique, SI, marketing et opérations : moins de projets parallèles incompatibles, plus de décisions pilotées par des indicateurs utiles.',
        'pillars' => [
            [
                'title' => 'Exécution',
                'text'  => 'Livrables actionnables, pas seulement des rapports : méthode commune à tous les pôles.',
            ],
            [
                'title' => 'Transparence',
                'text'  => 'Périmètres et tarifs cadrés ; vous savez quoi attendre à chaque étape.',
            ],
            [
                'title' => 'Long terme',
                'text'  => 'Documentation, transfert et maintenance pour que votre organisation tienne sans nous.',
            ],
        ],
    ],

    'process' => [
        'tag'        => 'Méthode',
        'title'      => 'Comment on transforme votre entreprise',
        /** Mot mis en avant dans le H2 (couleur marque) */
        'title_parts' => ['Comment on ', 'transforme', ' votre entreprise'],
        'lead'       => 'Rien n’est figé trop tôt : on avance par niveaux de maturité — du diagnostic au pilotage — avec des jalons et des preuves à chaque étape.',
        'macro_flow' => ['Comprendre', 'Structurer', 'Outiller', 'Optimiser', 'Piloter'],
        /** Barre de promesses sous les cartes */
        'value_bar'  => [
            ['icon' => 'shield', 'text' => 'Chaque étape a un objectif clair et un livrable concret.'],
            ['icon' => 'pie', 'text' => 'Vous gardez la maîtrise à chaque étape.'],
            ['icon' => 'users', 'text' => 'Co-construction avec vos équipes.'],
            ['icon' => 'rocket', 'text' => 'Des résultats durables, pas des effets gadgets.'],
        ],
        'steps'      => [
            [
                'num'     => '01',
                'accent'  => 'green',
                'phase'   => 'Comprendre',
                'title'   => 'Diagnostic',
                'text'    => 'Cartographie des risques, contraintes réglementaires, SI et métiers : une vision partagée avant tout budget ou livrable.',
                'livrable' => 'état des lieux et priorités claires',
            ],
            [
                'num'     => '02',
                'accent'  => 'teal',
                'phase'   => 'Structurer',
                'title'   => 'Structuration',
                'text'    => 'Gouvernance, processus, rôles et indicateurs : le cadre qui évite les projets parallèles et les réponses contradictoires.',
                'livrable' => 'cadre de gouvernance et feuille de route',
            ],
            [
                'num'     => '03',
                'accent'  => 'blue',
                'phase'   => 'Outiller',
                'title'   => 'Digitalisation',
                'text'    => 'Outils, flux et intégrations choisis pour vos usages réels — pas pour garnir un catalogue logiciel.',
                'livrable' => 'architecture cible et backlog priorisé',
            ],
            [
                'num'     => '04',
                'accent'  => 'amber',
                'phase'   => 'Optimiser',
                'title'   => 'Performance',
                'text'    => 'Visibilité, conversion, efficacité opérationnelle et qualité de donnée : on mesure et on ajuste avec vos équipes.',
                'livrable' => 'indicateurs et plan d’optimisation',
            ],
            [
                'num'     => '05',
                'accent'  => 'pine',
                'phase'   => 'Piloter',
                'title'   => 'Pilotage long terme',
                'text'    => 'Supervision, maintenance, évolutions et automatisation raisonnée — pour que le système tienne sans dépendre du chaos.',
                'livrable' => 'tableau de bord et rituels de pilotage',
            ],
        ],
    ],

    /** Slugs présents dans config/packs.php — vitrine accueil (parcours vendeurs, hors catalogue technique) */
    'featured_pack_slugs' => [
        'pack-diagnostic-vision',
        'pack-structuration-organisation',
        'pack-structuration-performance-globale',
    ],

    'offers_section' => [
        'tag'   => 'Offres',
        'title' => 'Des parcours — pas un catalogue',
        'lead'  => 'Trois façons d’avancer avec une même méthode : clarifier, structurer, ou piloter une transformation globale. Le détail du périmètre et du budget se construit avec vous — pas à partir d’une grille tarifaire sur cette page.',
        'labels' => [
            'pack-diagnostic-vision'                   => 'Diagnostic & Vision',
            'pack-structuration-organisation'          => 'Structuration & Organisation',
            'pack-structuration-performance-globale'   => 'Transformation globale',
        ],
        'pitches' => [
            'pack-diagnostic-vision' => 'Voir clair sur les blocages, les risques et les priorités avant d’engager des budgets au mauvais endroit.',
            'pack-structuration-organisation' => 'Installer une organisation lisible : qui décide, qui fait, avec quels indicateurs — pour sortir des silos.',
            'pack-structuration-performance-globale' => 'Une dynamique unique : plan maître, conformité, SI et pilotage coordonnés — équipe projet dédiée jusqu’aux résultats mesurables.',
        ],
    ],

    'client_journey' => [
        'tag'   => 'Parcours client',
        'title' => 'Votre parcours avec nous',
        'lead'  => 'Ce que vivent nos clients : une progression explicite — de la dispersion à la maîtrise — avec des étapes qu’on voit et qu’on pilote.',
        'steps' => [
            ['label' => 'Chaos', 'hint' => 'Urgences, silos, peu de cap commun'],
            ['label' => 'Clarté', 'hint' => 'Vision partagée, priorités, arbitrages'],
            ['label' => 'Structure', 'hint' => 'Organisation, outils et conformité alignés'],
            ['label' => 'Performance', 'hint' => 'Mesure, optimisation, résultats'],
            ['label' => 'Maîtrise', 'hint' => 'Pilotage long terme, autonomie renforcée'],
        ],
    ],

    'lab_teaser' => [
        'tag'   => 'Laboratoire',
        'title' => 'Hub d’innovation',
        'text'  => 'POC, bêtas et idées en incubation : testez en avant-première et influencez la roadmap.',
        'cta'   => 'Découvrir le laboratoire',
        'href'  => '/hub',
    ],

    'blog_teaser' => [
        'tag' => 'Ressources',
        'title' => 'Le blog',
        'cta_all' => 'Tous les articles',
        'href_all' => '/blog',
    ],

    'cta' => [
        'title' => 'Un projet transverse ? Parlons-en.',
        'lead'  => 'Devis, audit express ou feuille de route : nous revenons vers vous sous 24h ouvrées en général.',
    ],
];
