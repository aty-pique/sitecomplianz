<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

final class AdminQuoteController
{
    private static function submissionsDir(): string
    {
        return ROOT_PATH . '/storage/quote_submissions';
    }

    private static function tokenOk(Request $request): bool
    {
        $expected = trim((string) ($_ENV['QUOTE_DASHBOARD_TOKEN'] ?? ''));
        if ($expected === '') {
            return false;
        }
        $got = trim((string) ($request->get('token') ?? ''));

        return hash_equals($expected, $got);
    }

    private static function deny(): void
    {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Accès refusé. Définissez QUOTE_DASHBOARD_TOKEN dans .env et passez ?token=…';
    }

    public function index(Request $request, array $params = []): void
    {
        if (!self::tokenOk($request)) {
            self::deny();
            return;
        }
        $dir = self::submissionsDir();
        $files = is_dir($dir) ? glob($dir . '/quote_*.json') : false;
        if ($files === false) {
            $files = [];
        }
        rsort($files);
        $items = [];
        foreach ($files as $path) {
            $base = basename($path);
            $raw = @file_get_contents($path);
            $data = is_string($raw) ? json_decode($raw, true) : null;
            $q = is_array($data) && isset($data['data']['quote']) && is_array($data['data']['quote'])
                ? $data['data']['quote']
                : [];
            $items[] = [
                'file'        => $base,
                'received_at' => $data['received_at'] ?? '',
                'company'     => (string) ($q['company_name'] ?? ''),
                'email'       => (string) ($q['email'] ?? ''),
                'total_ht'    => $data['estimation']['total_ht'] ?? null,
            ];
        }

        View::render('admin/quotes_index.twig', [
            'title' => 'Demandes de pré-devis',
            'token' => (string) $request->get('token'),
            'items' => $items,
        ]);
    }

    public function show(Request $request, array $params = []): void
    {
        if (!self::tokenOk($request)) {
            self::deny();
            return;
        }
        $id = (string) ($params['id'] ?? '');
        if (!preg_match('/^quote_[a-zA-Z0-9_.-]+\.json$/', $id)) {
            http_response_code(400);
            echo 'Identifiant invalide.';
            return;
        }
        $path = self::submissionsDir() . '/' . $id;
        if (!is_readable($path)) {
            http_response_code(404);
            echo 'Demande introuvable.';
            return;
        }

        if (($request->get('format') ?? '') === 'csv') {
            self::sendCsv($path, $id);
            return;
        }

        $raw = file_get_contents($path);
        $payload = json_decode((string) $raw, true);
        if (!is_array($payload)) {
            http_response_code(500);
            echo 'Fichier JSON illisible.';
            return;
        }

        View::render('admin/quotes_detail.twig', [
            'title'        => 'Détail demande',
            'token'        => (string) $request->get('token'),
            'file'         => $id,
            'payload'      => $payload,
            'payload_json' => json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
    }

    private static function sendCsv(string $path, string $id): void
    {
        $raw = file_get_contents($path);
        $payload = json_decode((string) $raw, true);
        if (!is_array($payload)) {
            http_response_code(500);
            return;
        }
        $est = $payload['estimation'] ?? [];
        $lines = is_array($est) && isset($est['lines']) && is_array($est['lines']) ? $est['lines'] : [];

        $safeName = preg_replace('/[^a-zA-Z0-9_-]+/', '_', pathinfo($id, PATHINFO_FILENAME)) ?? 'export';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $safeName . '_lignes.csv"');
        $out = fopen('php://output', 'w');
        if ($out === false) {
            return;
        }
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Libellé', 'Description', 'Montant HT (EUR)', 'Feuille Excel', 'Cellule'], ';');
        foreach ($lines as $line) {
            if (!is_array($line)) {
                continue;
            }
            fputcsv($out, [
                (string) ($line['label'] ?? ''),
                (string) ($line['description'] ?? ''),
                isset($line['amount_eur']) ? (string) $line['amount_eur'] : '',
                (string) ($line['sheet'] ?? ''),
                (string) ($line['cell'] ?? ''),
            ], ';');
        }
        fclose($out);
    }
}
