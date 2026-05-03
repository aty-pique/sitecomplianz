<?php

declare(strict_types=1);

/**
 * Référence métier alignée sur « Questionnaire besoins.docx » (MODULE 1–4, triggers, routage).
 * Sert de carte pour le wizard, l’admin et l’évolution du configurateur — ne pas supprimer le .docx source.
 *
 * @return array<string, mixed>
 */
return [
    'source_document' => 'public/assets/docs/Questionnaire besoins.docx',
    'pole_labels'     => [
        'p0' => 'MODULE 1 — Conseil, organisation, conformité, qualité, RSE, stratégie, sécurité (tronc + blocs conditionnels)',
        'p1' => 'MODULE 2 — Solutions & développement (outils, structuration, projets, routage vers sous-modules)',
        'p2' => 'MODULE 3 — Performance digitale (canaux, objectifs, leviers → sous-modules détaillés)',
        'p3' => 'MODULE 4 — Support & maintenance (situation, problèmes, services → sous-modules)',
        'p4' => 'Intelligence artificielle (hors doc principal — complément offre)',
    ],
    /** Table « LOGIQUE DE ROUTAGE » du doc (Bloc 3 pôle 2) : type de projet → identifiant sous-module UI */
    'p1_project_routing' => [
        'CRM / ERP'                => 'crm_erp',
        'Développement sur mesure' => 'dev_sur_mesure',
        'Site / refonte'           => 'site_web',
        'Automatisation / API'     => 'automatisation',
        'Infrastructure'           => 'infrastructure',
        'Maintenance / debug'      => 'maintenance',
    ],
    /** Levier coché (étape perf. digitale) → panneau optionnel (MODULE 3, BLOC 3) */
    'p2_lever_subpanels' => [
        'Stratégie digitale'      => 'p2_strategie',
        'SEO'                     => 'p2_seo',
        'Tunnel de conversion'    => 'p2_tunnel',
        'Acquisition'             => 'p2_acquisition',
        'Data / KPI'              => 'p2_data',
        'Structuration marketing' => 'p2_marketing',
    ],
    /** Triggers textuels notables du doc (pour documentation / futur moteur de règles) */
    'trigger_examples' => [
        'p1_central_tool' => 'Doc : SI non CRM/ERP → besoin mise en place ; SI oui → optimisation (Bloc 1 pôle 2).',
        'p0_conformite'   => 'Bloc conformité : obligations, normes, objectif, périmètre (sous-modules RGPD / ISO / sectoriel dans le doc).',
        'p3_services'     => 'MODULE 4 BLOC 3 : choix du service → sous-module dédié (corrective, évolutive, support, debug, infogérance, hébergement).',
    ],
];
