<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

/**
 * Retours client via clic droit — données dans dev-notes/inbox.jsonl uniquement.
 */
class DevFeedbackController
{
    private static function inboxPath(): string
    {
        return ROOT_PATH . '/dev-notes/inbox.jsonl';
    }

    public static function isEnabled(): bool
    {
        $v = strtolower(trim((string) ($_ENV['DEV_CLIENT_FEEDBACK_ENABLED'] ?? '')));

        return $v === '1' || $v === 'true' || $v === 'yes' || $v === 'on';
    }

    /** @return list<array<string, mixed>> */
    public static function readEntries(): array
    {
        $path = self::inboxPath();
        if (!file_exists($path)) {
            return [];
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
        $out   = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $decoded = json_decode($line, true);
            if (is_array($decoded)) {
                $out[] = $decoded;
            }
        }

        return $out;
    }

    public function index(Request $request, array $params = []): void
    {
        if (!self::isEnabled()) {
            http_response_code(404);
            require ROOT_PATH . '/templates/pages/404.php';
            return;
        }

        $entries = self::readEntries();
        $entries = array_reverse($entries);

        View::render('pages/dev-feedback.twig', [
            'title'                 => 'Retours client (dev)',
            'entries'               => $entries,
            'clear_token_configured' => trim((string) ($_ENV['DEV_FEEDBACK_CLEAR_TOKEN'] ?? '')) !== '',
            'flash_ok'              => $request->get('ok') === '1',
            'flash_err'             => $request->get('err'),
            'flash_cleared'         => $request->get('cleared') === '1',
        ]);
    }

    public function store(Request $request, array $params = []): void
    {
        if (!self::isEnabled()) {
            http_response_code(404);
            return;
        }

        if (!$request->isPost()) {
            header('Location: /dev-feedback', true, 303);
            exit;
        }

        $pageUrl = trim((string) $request->post('page_url', ''));
        $message = trim((string) $request->post('message', ''));
        $ajax    = (string) $request->post('ajax', '') === '1';

        if ($message === '') {
            if ($ajax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'message vide'], JSON_UNESCAPED_UNICODE);
                return;
            }
            header('Location: /dev-feedback?err=1', true, 303);
            exit;
        }

        if (mb_strlen($message) > 8000) {
            if ($ajax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'message trop long'], JSON_UNESCAPED_UNICODE);
                return;
            }
            header('Location: /dev-feedback?err=2', true, 303);
            exit;
        }

        $dir = ROOT_PATH . '/dev-notes';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $payload = [
            'id'         => bin2hex(random_bytes(8)),
            'created_at' => date('c'),
            'page_url'   => $pageUrl,
            'message'    => $message,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];

        file_put_contents(
            self::inboxPath(),
            json_encode($payload, JSON_UNESCAPED_UNICODE) . "\n",
            FILE_APPEND | LOCK_EX
        );

        if ($ajax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'id' => $payload['id']], JSON_UNESCAPED_UNICODE);
            return;
        }

        header('Location: /dev-feedback?ok=1', true, 303);
        exit;
    }

    public function clear(Request $request, array $params = []): void
    {
        if (!self::isEnabled()) {
            http_response_code(404);
            return;
        }

        if (!$request->isPost()) {
            header('Location: /dev-feedback', true, 303);
            exit;
        }

        $expected = (string) ($_ENV['DEV_FEEDBACK_CLEAR_TOKEN'] ?? '');
        if ($expected === '' || (string) $request->post('token', '') !== $expected) {
            http_response_code(403);
            echo 'Jeton invalide ou non configuré.';
            return;
        }

        $path = self::inboxPath();
        if (file_exists($path)) {
            unlink($path);
        }

        header('Location: /dev-feedback?cleared=1', true, 303);
        exit;
    }
}
