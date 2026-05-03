<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Associe les réponses du configurateur aux feuilles / cellules de la grille tarifaire
 * et extrait les montants calculés (formules Excel évaluées par PhpSpreadsheet).
 */
final class QuotePricingEstimator
{
    private const SHEET_SITES = 'Création sites web';
    private const SHEET_AUDIT = 'Audit Conformité (RGPD, ISO…)';

    /** @return array{lines: list<array<string, mixed>>, total_ht: float, notes: list<string>, error: ?string} */
    public function estimate(array $quote): array
    {
        $notes = [];
        $lines = [];

        try {
            if (!PricingWorkbook::isAvailable()) {
                return [
                    'lines'    => [],
                    'total_ht' => 0.0,
                    'notes'    => ['Grille tarifaire non disponible sur ce serveur.'],
                    'error'    => 'missing_file',
                ];
            }
            PricingWorkbook::get();
        } catch (RuntimeException $e) {
            return [
                'lines'    => [],
                'total_ht' => 0.0,
                'notes'    => [$e->getMessage()],
                'error'    => 'workbook',
            ];
        }

        $poles = $quote['poles'] ?? [];
        if (!is_array($poles)) {
            $poles = [];
        }

        if (in_array('p1', $poles, true)) {
            $line = $this->tryCreationSitesLine($quote);
            if ($line !== null) {
                $lines[] = $line;
            }
        }

        if (in_array('p0', $poles, true)) {
            $line = $this->tryAuditConformiteLine($quote);
            if ($line !== null) {
                $lines[] = $line;
            }
        }

        $lines = array_merge($lines, $this->fallbackBaseLines($quote, $poles, $lines));

        $total = 0.0;
        foreach ($lines as $l) {
            $total += (float) ($l['amount_eur'] ?? 0);
        }

        if ($lines === []) {
            $notes[] = 'Aucune ligne automatique : sélectionnez des pôles et des types de projet, ou complétez les modules détaillés.';
        } else {
            $notes[] = 'Estimation indicative à partir de votre sélection : un expert validera le périmètre avec vous avant tout engagement.';
        }

        foreach ($lines as $i => $l) {
            $lines[$i] = $this->attachClientPresentation($l);
        }

        return [
            'lines'    => $lines,
            'total_ht' => round($total, 2),
            'notes'    => $notes,
            'error'    => null,
        ];
    }

    /** Colonne D=4 … AA=27 ; 3 bandes × 8 colonnes (même logique que les feuilles « grille 24 colonnes »). */
    private function gridColumn(int $band, bool $seoOptimise, bool $avecIntegration, bool $presentiel): int
    {
        $band = max(0, min(2, $band));
        $sub = ($seoOptimise ? 4 : 0) + ($avecIntegration ? 2 : 0) + ($presentiel ? 1 : 0);

        return 4 + $band * 8 + $sub;
    }

    private function columnLetter(int $columnIndex1Based): string
    {
        $n = $columnIndex1Based;
        $s = '';
        while ($n > 0) {
            $m = ($n - 1) % 26;
            $s = chr(65 + $m) . $s;
            $n = intdiv($n - 1, 26);
        }

        return $s;
    }

    /**
     * Types de projet p1 explicites + déductions depuis « Outils actuels » (souvent remplis sans cocher les types).
     *
     * @return list<string>
     */
    private function inferredP1ProjectTypes(array $quote): array
    {
        $p1 = $quote['p1'] ?? [];
        if (!is_array($p1)) {
            return [];
        }
        $types = isset($p1['project_types']) && is_array($p1['project_types']) ? $p1['project_types'] : [];
        $types = array_values(array_unique(array_map('strval', $types)));
        $tools = isset($p1['tools']) && is_array($p1['tools']) ? $p1['tools'] : [];

        if (!in_array('Site / refonte', $types, true) && in_array('Site internet', $tools, true)) {
            $types[] = 'Site / refonte';
        }
        if (!in_array('CRM / ERP', $types, true) && (in_array('CRM', $tools, true) || in_array('ERP', $tools, true))) {
            $types[] = 'CRM / ERP';
        }
        return array_values(array_unique($types));
    }

    private function tryCreationSitesLine(array $quote): ?array
    {
        $p1 = $quote['p1'] ?? [];
        if (!is_array($p1)) {
            return null;
        }
        $types = $this->inferredP1ProjectTypes($quote);
        if (!in_array('Site / refonte', $types, true)) {
            return null;
        }

        $size = (string) ($quote['company_size'] ?? '');
        $siteBlock = match ($size) {
            '1', '2-10' => 0,
            '10-50', '50-250', '250+' => 1,
            default => 1,
        };
        if (in_array('Développement sur mesure', $types, true)) {
            $siteBlock = 2;
        }

        $baseRow = match ($siteBlock) {
            0 => 30,
            1 => 42,
            2 => 54,
            default => 42,
        };

        $pagesIdx = 1;
        $designIdx = 0;

        $p2 = $quote['p2'] ?? [];
        $p2 = is_array($p2) ? $p2 : [];
        $levers = isset($p2['levers']) && is_array($p2['levers']) ? $p2['levers'] : [];
        $channels = isset($p2['channels']) && is_array($p2['channels']) ? $p2['channels'] : [];
        $seoOptimise = in_array('SEO', $levers, true) || in_array('SEO', $channels, true);

        $central = (string) ($p1['central_tool'] ?? '');
        $avecIntegration = $central === 'oui';

        $presentiel = false;

        $funcBand = 0;
        if (in_array('Automatisation / API', $types, true)) {
            $funcBand = 1;
        }

        $col = $this->gridColumn($funcBand, $seoOptimise, $avecIntegration, $presentiel);
        $row = $baseRow + $pagesIdx * 3 + $designIdx;
        $coord = $this->columnLetter($col) . $row;

        $raw = PricingWorkbook::calculatedValue(self::SHEET_SITES, $coord);
        $amount = $this->normalizeAmount($raw);

        $siteLabel = ['Site vitrine', 'Site institutionnel (professionnel)', 'Site sur mesure avancé'][$siteBlock] ?? 'Site';

        return [
            'label'         => 'Création / refonte de site web',
            'description' => sprintf(
                'Grille « %s » — %s, pages indicative « 6–15 », design standard, scénario grille %s.',
                self::SHEET_SITES,
                $siteLabel,
                $coord
            ),
            'amount_eur'    => $amount,
            'sheet'         => self::SHEET_SITES,
            'cell'          => $coord,
            'detail_params' => [
                ['param' => 'Type de site (indicatif)', 'value' => $siteLabel],
                ['param' => 'Hypothèses grille', 'value' => 'Fonctionnalités : ' . ($funcBand === 0 ? 'sans option lourde' : 'niveau intermédiaire')],
                ['param' => 'SEO', 'value' => $seoOptimise ? 'Optimisé' : 'De base'],
                ['param' => 'Intégration outils', 'value' => $avecIntegration ? 'Avec' : 'Sans'],
                ['param' => 'Mode', 'value' => $presentiel ? 'Présentiel' : 'Distanciel'],
            ],
        ];
    }

    private function tryAuditConformiteLine(array $quote): ?array
    {
        $p0 = $quote['p0'] ?? [];
        if (!is_array($p0)) {
            return null;
        }
        $which = $p0['reg_which'] ?? [];
        if (!is_array($which)) {
            $which = [];
        }

        $normTags = ['RGPD', 'ISO 9001', 'ISO 27001', 'ISO 13485', 'Secteur réglementé'];
        $count = 0;
        foreach ($normTags as $t) {
            if (in_array($t, $which, true)) {
                $count++;
            }
        }
        $oblig = (string) ($p0['reg_obligations'] ?? '');
        $goal = (string) ($p0['compliance_goal'] ?? '');
        $level = (string) ($p0['compliance_level'] ?? '');
        $aoClient = ['Appels d' . "\u{2019}" . 'offres / clients', "Appels d'offres / clients"];
        $engaged = $oblig === 'oui' || $count > 0 || $goal !== '' || $level !== ''
            || count(array_intersect($aoClient, $which)) > 0;
        if (!$engaged) {
            return null;
        }
        if ($count === 0) {
            $count = 1;
        }

        $normBand = $count >= 4 ? 2 : ($count >= 2 ? 1 : 0);

        $goal = (string) ($p0['compliance_goal'] ?? '');
        $level = (string) ($p0['compliance_level'] ?? '');
        $exigence = 1;
        if ($goal === 'certification' || $level === 'certifie') {
            $exigence = 2;
        } elseif ($goal === 'conformite' && in_array($level, ['non', 'partiel'], true)) {
            $exigence = 0;
        }

        $size = (string) ($quote['company_size'] ?? '');
        $baseRow = match ($size) {
            '1' => 30,
            '2-10' => 39,
            '10-50' => 48,
            '50-250', '250+' => 57,
            default => 39,
        };

        $row = $baseRow + $normBand * 3 + $exigence;

        $isoPicked = false;
        foreach (['ISO 9001', 'ISO 27001', 'ISO 13485'] as $iso) {
            if (in_array($iso, $which, true)) {
                $isoPicked = true;
                break;
            }
        }
        $multi = $count >= 2 && (in_array('Secteur réglementé', $which, true) || $count >= 3);
        $band = $multi ? 2 : ($isoPicked ? 1 : 0);

        $ultra = in_array('Secteur réglementé', $which, true);
        $seoOptimise = $ultra;
        $p1b = $quote['p1'] ?? [];
        $central = is_array($p1b) ? (string) ($p1b['central_tool'] ?? '') : '';
        $avecIntegration = $central === 'oui';
        $presentiel = false;

        $col = $this->gridColumn($band, $seoOptimise, $avecIntegration, $presentiel);
        $coord = $this->columnLetter($col) . $row;

        $raw = PricingWorkbook::calculatedValue(self::SHEET_AUDIT, $coord);
        $amount = $this->normalizeAmount($raw);

        return [
            'label'         => 'Audit de conformité (RGPD / ISO / réglementaire)',
            'description' => sprintf('Grille « %s », intersection automatique %s.', self::SHEET_AUDIT, $coord),
            'amount_eur'    => $amount,
            'sheet'         => self::SHEET_AUDIT,
            'cell'          => $coord,
            'detail_params' => [
                ['param' => 'Thématiques cochées', 'value' => implode(', ', $which)],
                ['param' => 'Indicateur normes (auto)', 'value' => (string) $count],
            ],
        ];
    }

    /**
     * @param array<string, true> $sheetsUsed
     */
    private function appendB4LineIfNew(
        array &$out,
        array &$sheetsUsed,
        string $sheet,
        string $label,
        string $description,
        array $detailParams = [['param' => 'Type de ligne', 'value' => 'Base indicative']],
    ): void {
        if (isset($sheetsUsed[$sheet])) {
            return;
        }
        try {
            $raw = PricingWorkbook::calculatedValue($sheet, 'B4');
            $amount = $this->normalizeAmount($raw);
        } catch (RuntimeException) {
            return;
        }
        $out[] = [
            'label'         => $label,
            'description'   => $description,
            'amount_eur'    => $amount,
            'sheet'         => $sheet,
            'cell'          => 'B4',
            'detail_params' => $detailParams,
        ];
        $sheetsUsed[$sheet] = true;
    }

    /**
     * @param list<string> $poles
     * @param list<array<string,mixed>> $existing
     * @return list<array<string,mixed>>
     */
    private function fallbackBaseLines(array $quote, array $poles, array $existing): array
    {
        $sheetsUsed = [];
        foreach ($existing as $l) {
            $sheetsUsed[$l['sheet'] ?? ''] = true;
        }

        $map = [
            'CRM / ERP'                  => 'Création ERP - CRM',
            'Développement sur mesure'   => 'Dev logiciel sur mesure',
            'Automatisation / API'       => 'Automatisation & Interconnexion',
            'Infrastructure'             => 'Hébergement & Infrastructure',
            'Maintenance / debug'        => 'Maintenance corrective',
        ];

        $types = $this->inferredP1ProjectTypes($quote);

        $out = [];
        foreach ($types as $t) {
            if (!isset($map[$t])) {
                continue;
            }
            $sheet = $map[$t];
            $this->appendB4LineIfNew(
                $out,
                $sheetsUsed,
                $sheet,
                'Point de départ tarifaire — ' . $t,
                sprintf('Tarif de base lu en B4 sur la feuille « %s » (périmètre à affiner avec vous).', $sheet),
            );
        }

        if (in_array('p2', $poles, true)) {
            $p2 = $quote['p2'] ?? [];
            $p2 = is_array($p2) ? $p2 : [];
            $levers = isset($p2['levers']) && is_array($p2['levers']) ? $p2['levers'] : [];
            $channels = isset($p2['channels']) && is_array($p2['channels']) ? $p2['channels'] : [];

            $leverSheets = [
                'Stratégie digitale'      => 'Stratégie Digitale Globale',
                'SEO'                     => 'SEO & Optimisation site',
                'Tunnel de conversion'    => 'Tunnel de conversion',
                'Acquisition'             => 'Pilotage commercial digital',
                'Data / KPI'              => 'Analyse performance (KPI)',
                'Structuration marketing' => 'Structuration marketing',
            ];
            $p2Added = false;
            foreach ($levers as $lv) {
                $lv = (string) $lv;
                if (!isset($leverSheets[$lv])) {
                    continue;
                }
                $sh = $leverSheets[$lv];
                $before = count($out);
                $this->appendB4LineIfNew(
                    $out,
                    $sheetsUsed,
                    $sh,
                    'Performance digitale — ' . $lv,
                    sprintf('Point d’entrée grille « %s » (B4), selon vos leviers cochés.', $sh),
                    [['param' => 'Levier', 'value' => $lv]],
                );
                if (count($out) > $before) {
                    $p2Added = true;
                }
            }
            if (in_array('SEO', $channels, true) && !isset($sheetsUsed['SEO & Optimisation site'])) {
                $before = count($out);
                $this->appendB4LineIfNew(
                    $out,
                    $sheetsUsed,
                    'SEO & Optimisation site',
                    'Performance digitale — SEO (canal)',
                    'Point d’entrée grille « SEO & Optimisation site » (B4) — canal SEO coché.',
                    [['param' => 'Canal', 'value' => 'SEO']],
                );
                if (count($out) > $before) {
                    $p2Added = true;
                }
            }
            if (!$p2Added) {
                $this->appendB4LineIfNew(
                    $out,
                    $sheetsUsed,
                    'Stratégie Digitale Globale',
                    'Performance digitale — point d’entrée',
                    'Aucun levier précis mappé : base indicative « Stratégie Digitale Globale » (B4).',
                    [['param' => 'Pôle', 'value' => 'Performance digitale']],
                );
            }
        }

        if (in_array('p3', $poles, true)) {
            $p3 = $quote['p3'] ?? [];
            $p3 = is_array($p3) ? $p3 : [];
            $services = isset($p3['services']) && is_array($p3['services']) ? $p3['services'] : [];
            $p3Map = [
                'Maintenance corrective' => 'Maintenance corrective',
                'Maintenance évolutive'    => 'Maintenance évolutive',
                'Support technique'      => 'Support technique',
                'Debug'                    => 'Debug (sites, logiciels)',
                'Infogérance'            => 'Infogérance - Gestion serveurs',
                'Hébergement'            => 'Hébergement & Infrastructure',
            ];
            $p3Added = false;
            foreach ($services as $svc) {
                $svc = (string) $svc;
                if (!isset($p3Map[$svc])) {
                    continue;
                }
                $sh = $p3Map[$svc];
                $before = count($out);
                $this->appendB4LineIfNew(
                    $out,
                    $sheetsUsed,
                    $sh,
                    'Support & maintenance — ' . $svc,
                    sprintf('Point d’entrée grille « %s » (B4).', $sh),
                    [['param' => 'Service envisagé', 'value' => $svc]],
                );
                if (count($out) > $before) {
                    $p3Added = true;
                }
            }
            if (!$p3Added) {
                $this->appendB4LineIfNew(
                    $out,
                    $sheetsUsed,
                    'Support technique',
                    'Support & maintenance — point d’entrée',
                    'Aucun service précis coché : base indicative « Support technique » (B4).',
                    [['param' => 'Pôle', 'value' => 'Support & Maintenance']],
                );
            }
        }

        if (in_array('p4', $poles, true)) {
            $this->appendB4LineIfNew(
                $out,
                $sheetsUsed,
                'Formation IA - Agents',
                'Intelligence artificielle — point d’entrée',
                'Estimation indicative à partir de la feuille « Formation IA - Agents » (B4).',
                [['param' => 'Pôle', 'value' => 'IA']],
            );
        }

        return $out;
    }

    /**
     * Champs marketing / conversion (sans feuille Excel ni cellule côté client).
     *
     * @param array<string, mixed> $line
     * @return array<string, mixed>
     */
    private function attachClientPresentation(array $line): array
    {
        $sheet = (string) ($line['sheet'] ?? '');
        $meta = $this->clientMetaForSheet($sheet, (string) ($line['label'] ?? ''));

        $line['client_duration_hint'] = $meta['duration'];
        $line['client_benefits'] = $meta['benefits'];
        $line['client_detail_params'] = $this->filterClientDetailParams($line);

        return $line;
    }

    /** @return array{duration: string, benefits: list<string>} */
    private function clientMetaForSheet(string $sheet, string $label): array
    {
        $pack = static fn (string $d, array $b): array => ['duration' => $d, 'benefits' => array_values($b)];

        return match ($sheet) {
            self::SHEET_SITES => $pack(
                'En pratique : souvent 4 à 10 semaines entre cadrage et mise en ligne, selon contenus et validations.',
                [
                    'Un site aligné sur vos objectifs (image, leads, conversion).',
                    'Une base propre pour évoluer et améliorer votre référencement.',
                    'Un accompagnement jusqu’à la mise en ligne et la prise en main.',
                ],
            ),
            self::SHEET_AUDIT => $pack(
                'Phase d’audit et restitution : en général 3 à 8 semaines selon périmètre et disponibilités.',
                [
                    'Vision claire de votre niveau de conformité et des écarts.',
                    'Priorisation des actions et feuille de route réaliste.',
                    'Réduction des risques réglementaires et opérationnels.',
                ],
            ),
            'Création ERP - CRM', 'Dev logiciel sur mesure' => $pack(
                'Cadrage fonctionnel puis chiffrage détaillé : comptez souvent 3 à 6 semaines avant démarrage de réalisation.',
                [
                    'Outils adaptés à vos process (moins de ressaisie, plus de pilotage).',
                    'Une solution évolutive avec vous, pas une usine à gaz.',
                    'Une équipe technique et métier pour sécuriser le projet.',
                ],
            ),
            'Automatisation & Interconnexion' => $pack(
                'Premiers flux automatisés : souvent 2 à 5 semaines ; projets d’envergure au cas par cas.',
                [
                    'Moins d’erreurs manuelles et plus de temps pour la valeur ajoutée.',
                    'Des outils qui communiquent entre eux (données à jour partout).',
                    'Des gains mesurables sur la productivité.',
                ],
            ),
            'Hébergement & Infrastructure' => $pack(
                'Mise en place ou migration : typiquement 2 à 6 semaines selon criticité et environnements.',
                [
                    'Performance et stabilité adaptées à votre charge.',
                    'Sauvegardes, supervision et sécurité mieux cadrées.',
                    'Tranquillité d’esprit sur l’exploitation.',
                ],
            ),
            'Maintenance corrective', 'Maintenance évolutive', 'Support technique', 'Debug (sites, logiciels)' => $pack(
                'Diagnostic ou ticket : en général quelques jours à 2 semaines selon criticité.',
                [
                    'Retour à un service nominal plus rapide.',
                    'Moins d’impact sur vos équipes et vos clients.',
                    'Corrections documentées pour éviter les récidives.',
                ],
            ),
            'Infogérance - Gestion serveurs' => $pack(
                'Mise en supervision : souvent 2 à 4 semaines pour industrialiser le run.',
                [
                    'Supervision proactive et interventions cadrées.',
                    'Moins de surprises sur la disponibilité de vos services.',
                    'Un interlocuteur technique dédié.',
                ],
            ),
            'Formation IA - Agents', 'Intégration IA dans les SI', 'Création d\'agents IA', 'Chat IA sur site internet', 'Développement solutions IA', 'Audit IA - Impact système' => $pack(
                'Atelier de cadrage puis prototype : souvent 3 à 6 semaines pour une première valeur.',
                [
                    'Des cas d’usage concrets, pas de l’IA pour l’IA.',
                    'Respect de vos contraintes (données, RGPD, hébergement).',
                    'Montée en compétence de vos équipes possible.',
                ],
            ),
            'Stratégie Digitale Globale' => $pack(
                'Stratégie et plan d’action : comptez 3 à 6 semaines pour une première version opérationnelle.',
                [
                    'Alignement offre / canaux / objectifs business.',
                    'Priorisation des actions à fort impact.',
                    'Une feuille de route chiffrable et suivable.',
                ],
            ),
            'SEO & Optimisation site' => $pack(
                'Audit et premiers chantiers : souvent 2 à 6 semaines ; le suivi SEO s’étale sur plusieurs mois.',
                [
                    'Plus de visibilité sur les recherches utiles à votre activité.',
                    'Technique + contenus travaillés pour durer.',
                    'Indicateurs pour suivre la progression.',
                ],
            ),
            'Tunnel de conversion' => $pack(
                'Analyse et tests : en général 3 à 8 semaines pour une première hausse mesurable.',
                [
                    'Parcours client plus fluide = plus de conversions.',
                    'Moins de fuites entre la visite et la prise de contact.',
                    'Décisions basées sur les données de votre tunnel.',
                ],
            ),
            'Pilotage commercial digital' => $pack(
                'Mise en place des indicateurs et rituels : souvent 2 à 5 semaines.',
                [
                    'Meilleure lisibilité du pipe et des campagnes.',
                    'Décisions plus rapides et mieux partagées.',
                    'Marketing et vente mieux synchronisés.',
                ],
            ),
            'Analyse performance (KPI)' => $pack(
                'Définition des KPI et tableaux de bord : souvent 3 à 6 semaines.',
                [
                    'Une vision partagée de la performance.',
                    'Moins de tableurs « boîte noire », plus d’automatisation.',
                    'Des réunions de pilotage plus efficaces.',
                ],
            ),
            'Structuration marketing' => $pack(
                'Structuration offre / plan d’action : souvent 3 à 6 semaines.',
                [
                    'Marketing plus prévisible et moins chronophage.',
                    'Meilleure cohérence entre message, canaux et objectifs.',
                    'Base saine pour scaler l’acquisition.',
                ],
            ),
            default => $pack(
                'Délai précis défini avec vous après un court cadrage (souvent 1 à 3 semaines pour une proposition détaillée).',
                [
                    'Une vision chiffrée transparente avant engagement contractuel.',
                    'Un interlocuteur expert pour cadrer le périmètre réel.',
                    'Passage rapide de l’estimation au devis sur mesure.',
                ],
            ),
        };
    }

    /**
     * @param array<string, mixed> $line
     * @return list<array{param: string, value: string}>
     */
    private function filterClientDetailParams(array $line): array
    {
        $allowed = [
            'Type de site (indicatif)', 'SEO', 'Intégration outils', 'Mode',
            'Thématiques cochées', 'Levier', 'Canal', 'Service envisagé', 'Pôle', 'Type de ligne',
        ];
        $out = [];
        $det = $line['detail_params'] ?? [];
        if (!is_array($det)) {
            return $out;
        }
        foreach ($det as $row) {
            if (!is_array($row)) {
                continue;
            }
            $p = (string) ($row['param'] ?? '');
            if ($p === '' || !in_array($p, $allowed, true)) {
                continue;
            }
            $out[] = ['param' => $p, 'value' => (string) ($row['value'] ?? '')];
        }

        return $out;
    }

    private function normalizeAmount(mixed $raw): float
    {
        if (is_numeric($raw)) {
            return round((float) $raw, 2);
        }
        if (is_string($raw)) {
            $clean = str_replace(["\xc2\xa0", ' ', ','], ['', '', '.'], $raw);
            $clean = preg_replace('/[^0-9.\-]/', '', $clean) ?? '';

            return $clean !== '' ? round((float) $clean, 2) : 0.0;
        }

        return 0.0;
    }
}
