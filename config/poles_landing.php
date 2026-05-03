<?php

/**
 * Pages « hub » par pôle (/audit-conformite, /solutions-developpement, …).
 */
return [
    'audit-conformite' => [
        'pole'             => 0,
        'path'             => '/audit-conformite',
        'name'             => 'Conseil, Audit & Conformité',
        'meta_title'       => 'Conseil, audit et conformité — RGPD, ISO, cybersécurité, RSE | Complianz',
        'meta_description' => 'Structuration, audits, mise en conformité RGPD et ISO, cybersécurité et démarche RSE. Un seul interlocuteur pour aligner juridique, qualité et risques.',
        'hero_eyebrow'     => 'Pôle expertise',
        'hero_lead'        => 'Nous aidons les entreprises à maîtriser leurs obligations, sécuriser leur SI et piloter la qualité — avec des livrables opérationnels, pas des dossiers inutilisables.',
        'intro'            => 'Ce pôle couvre l’ensemble des sujets où la conformité devient un levier : protection des données, certifications, audits organisationnels et cyberdéfense. Notre approche relie toujours la réglementation à votre capacité à exécuter.',
        'highlights'       => [
            'Audits et diagnostics avec priorités chiffrées',
            'RGPD, ISO 9001 / 27001, SMQ : même méthode, moins de doublons',
            'Coordination avec vos équipes métier et juridiques',
        ],
        'categories'       => [
            ['href' => '/audit-conformite/audit-conseil', 'title' => 'Audit global & Conseil', 'text' => 'Audit organisationnel, structuration, gouvernance, création d’entreprise.'],
            ['href' => '/audit-conformite/conformite-rgpd', 'title' => 'Mise en conformité RGPD', 'text' => 'Audit RGPD, DPO externalisé, registre, politique de confidentialité.'],
            ['href' => '/audit-conformite/certifications-iso', 'title' => 'Certifications ISO', 'text' => 'ISO 9001, ISO 27001, SMQ, audits qualité et préparation certification.'],
            ['href' => '/audit-conformite/cybersecurite', 'title' => 'Cybersécurité', 'text' => 'Audit cyber, pentest, politique de sécurité, protection contre les attaques.'],
            ['href' => '/audit-conformite/rse', 'title' => 'Démarche RSE', 'text' => 'Stratégie RSE, bilan carbone, labels et reporting extra-financier.'],
        ],
    ],

    'solutions-developpement' => [
        'pole'             => 1,
        'path'             => '/solutions-developpement',
        'name'             => 'Solutions & Développement',
        'meta_title'       => 'Solutions & développement — sites web, ERP, CRM, logiciel sur mesure | Complianz',
        'meta_description' => 'Création de sites et e-commerce, intégration ERP/CRM, développement d’applications métiers et SaaS. Des solutions alignées sur vos processus réels.',
        'hero_eyebrow'     => 'Pôle expertise',
        'hero_lead'        => 'Nous concevons et déployons vos outils digitaux : vitrine, back-office, intégrations et automatisation — pour que la technologie serve votre organisation, pas l’inverse.',
        'intro'            => 'De la vitrine à l’ERP, nous privilégions des architectures maintenables et des choix techniques expliqués. Chaque projet inclut transfert de compétences et documentation pour vos équipes.',
        'highlights'       => [
            'Spécifications partagées avec les métiers avant développement',
            'ERP/CRM, APIs et automatisation sans empiler les silos',
            'Qualité, sécurité et hébergement cadrés dès la conception',
        ],
        'categories'       => [
            ['href' => '/solutions-developpement/creation-site-web', 'title' => 'Création de sites web', 'text' => 'Vitrine, e-commerce, refonte, landing pages, WordPress & CMS.'],
            ['href' => '/solutions-developpement/erp-crm', 'title' => 'ERP & CRM sur mesure', 'text' => 'CRM, ERP, gestion commerciale, intégration Odoo et SI existants.'],
            ['href' => '/solutions-developpement/developpement-logiciel', 'title' => 'Développement logiciel & SaaS', 'text' => 'Applications métier, SaaS, automatisation & API.'],
        ],
    ],

    'performance-digitale' => [
        'pole'             => 2,
        'path'             => '/performance-digitale',
        'name'             => 'Performance Digitale & Stratégie',
        'meta_title'       => 'Performance digitale — SEO, tunnels de vente, stratégie, pilotage | Complianz',
        'meta_description' => 'SEO, acquisition, tunnels de conversion et pilotage commercial. Des décisions basées sur les données et un langage commun marketing / ventes.',
        'hero_eyebrow'     => 'Pôle expertise',
        'hero_lead'        => 'Nous faisons le lien entre visibilité, acquisition et conversion : stratégie, contenus, optimisation continue et indicateurs orientés business.',
        'intro'            => 'Peu de tunnels « génériques » : nous partons de vos parcours réels, de votre CRM et de vos objectifs de pipeline. L’objectif est un ROI mesurable, pas des vanity metrics.',
        'highlights'       => [
            'SEO technique & contenu alignés sur la recherche et la conversion',
            'CRO et tunnels construits avec les équipes commerciales',
            'Tableaux de bord et analyses reliés à vos sources de vérité',
        ],
        'categories'       => [
            ['href' => '/performance-digitale/seo', 'title' => 'Référencement naturel (SEO)', 'text' => 'Audit SEO, local, technique, netlinking et stratégie de contenu.'],
            ['href' => '/performance-digitale/tunnel-de-vente', 'title' => 'Tunnels de conversion', 'text' => 'Création de tunnels, CRO et optimisation du taux de conversion.'],
            ['href' => '/performance-digitale/strategie-digitale', 'title' => 'Stratégie digitale', 'text' => 'Marketing digital, email, publicité en ligne et réseaux sociaux.'],
            ['href' => '/performance-digitale/pilotage-commercial', 'title' => 'Pilotage commercial', 'text' => 'Tableaux de bord, analyse de performance et data pour la direction.'],
        ],
    ],

    'support-maintenance' => [
        'pole'             => 3,
        'path'             => '/support-maintenance',
        'name'             => 'Support & Maintenance',
        'meta_title'       => 'Support, maintenance et infogérance — hébergement, sécurité, évolutions | Complianz',
        'meta_description' => 'Infogérance, hébergement managé, maintenance corrective et évolutive, support utilisateur. Gardez vos systèmes disponibles et à jour.',
        'hero_eyebrow'     => 'Pôle expertise',
        'hero_lead'        => 'Nous assurons la continuité de service : supervision, correctifs, sauvegardes et petites évolutions — avec des SLA clairs et un interlocuteur unique.',
        'intro'            => 'Que vous ayez besoin d’un filet de sécurité ou d’une infogérance complète, nous dimensionnons l’offre à votre criticité métier et à vos fenêtres de maintenance.',
        'highlights'       => [
            'Astreinte et support selon plages horaires contractualisées',
            'Mises à jour, patchs sécurité et traçabilité des changements',
            'Coordination avec hébergeurs et éditeurs pour un guichet unique',
        ],
        'categories'       => [
            ['href' => '/support-maintenance/infogerance', 'title' => 'Infogérance & Hébergement', 'text' => 'Infogérance complète, serveurs dédiés, VPS, cloud et infrastructure managée.'],
            ['href' => '/support-maintenance/maintenance', 'title' => 'Maintenance & Support technique', 'text' => 'Maintenance corrective & évolutive, debug, support externalisé.'],
        ],
    ],

    'intelligence-artificielle' => [
        'pole'             => 4,
        'path'             => '/intelligence-artificielle',
        'name'             => 'Intelligence Artificielle',
        'meta_title'       => 'Intelligence artificielle — agents IA, automatisation, formation, gouvernance | Complianz',
        'meta_description' => 'Agents IA, automatisation des processus, formation et mise en conformité (RGPD, AI Act). Déployez l’IA là où elle crée de la valeur, avec des garde-fous.',
        'hero_eyebrow'     => 'Pôle expertise',
        'hero_lead'        => 'Nous passons du POC à la production : cas d’usage priorisés, intégration SI, formation des équipes et gouvernance des données & modèles.',
        'intro'            => 'L’IA n’est pas une couche « gadget » : nous l’intégrons après avoir clarifié vos processus et vos données. Automatisation classique ou IA générative selon vos contraintes.',
        'highlights'       => [
            'Audit et feuille de route avant tout investissement massif',
            'Automatisation et agents avec reprise sur erreur et traçabilité',
            'Formation dirigeants et métiers, conformité et éthique',
        ],
        'categories'       => [
            ['href' => '/intelligence-artificielle/agents-ia', 'title' => 'Agents IA sur mesure', 'text' => 'Agents vocaux, IA agentique, RH, commercial et disponibilité 24/7.'],
            ['href' => '/intelligence-artificielle/automatisation-ia', 'title' => 'Automatisation par l’IA', 'text' => 'Processus métiers, n8n, Make, orchestration et fiabilité.'],
            ['href' => '/intelligence-artificielle/formation-ia', 'title' => 'Formation à l’IA', 'text' => 'CPF, IA générative, marketing, dirigeants et montée en compétences.'],
            ['href' => '/intelligence-artificielle/ia-entreprise', 'title' => 'Stratégie & audit IA', 'text' => 'Stratégie IA en entreprise, audits, solutions et impact.'],
        ],
    ],
];
