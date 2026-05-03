<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Services\QuotePricingEstimator;

/**
 * Page Contact + réception des formulaires (pré-devis & message).
 * Les données sont stockées côté serveur pour branchement futur sur l’API / CRM.
 */
class ContactController
{
    public function index(Request $request, array $params = []): void
    {
        $prefillEmail = isset($_GET['prefill_email']) && is_string($_GET['prefill_email'])
            ? trim($_GET['prefill_email'])
            : '';

        View::render('pages/contact.twig', [
            'title'               => 'Contact',
            'quote_sent'          => isset($_GET['quote']) && $_GET['quote'] === 'ok',
            'message_sent'        => isset($_GET['msg']) && $_GET['msg'] === 'ok',
            'message_privacy_error' => isset($_GET['msg']) && $_GET['msg'] === 'err_privacy',
            'quote_privacy_error'   => isset($_GET['quote']) && $_GET['quote'] === 'err_privacy',
            'prefill_email'         => $prefillEmail,
        ]);
    }

    public function quote(Request $request, array $params = []): void
    {
        $quote = $_POST['quote'] ?? [];
        if (($quote['privacy_ok'] ?? '') !== '1') {
            header('Location: /contact?quote=err_privacy', true, 303);
            exit;
        }

        $dir = ROOT_PATH . '/storage/quote_submissions';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $estimator = new QuotePricingEstimator();
        $estimation = $estimator->estimate(is_array($quote) ? $quote : []);

        $payload = [
            'received_at' => date('c'),
            'ip'          => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'data'        => $_POST,
            'estimation'  => $estimation,
        ];

        $file = $dir . '/quote_' . date('Y-m-d_His') . '_' . bin2hex(random_bytes(4)) . '.json';
        file_put_contents(
            $file,
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        header('Location: /contact?quote=ok', true, 303);
        exit;
    }

    public function message(Request $request, array $params = []): void
    {
        if (($request->post('privacy_ok') ?? '') !== '1') {
            if (($request->post('ajax') ?? '') === '1') {
                header('Content-Type: application/json; charset=utf-8', true, 422);
                echo json_encode(['ok' => false, 'error' => 'privacy'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            header('Location: /contact?msg=err_privacy', true, 303);
            exit;
        }

        $storageDir = ROOT_PATH . '/storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $path = $storageDir . '/contact_messages.jsonl';
        $line = json_encode(
            [
                'received_at' => date('c'),
                'ip'          => $_SERVER['REMOTE_ADDR'] ?? '',
                'data'        => $_POST,
            ],
            JSON_UNESCAPED_UNICODE
        ) . "\n";

        file_put_contents($path, $line, FILE_APPEND | LOCK_EX);

        if (($request->post('ajax') ?? '') === '1') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $redirectOk = $request->post('redirect_ok');
        if (is_string($redirectOk) && preg_match('#^/lab/[a-z0-9-]+$#', $redirectOk)) {
            header('Location: ' . $redirectOk . '?feedback=ok', true, 303);
            exit;
        }

        header('Location: /contact?msg=ok', true, 303);
        exit;
    }

    /**
     * Prévisualisation JSON de l’estimation (étape 6 du wizard).
     */
    public function quoteEstimate(Request $request, array $params = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $quote = $_POST['quote'] ?? null;
        if (!is_array($quote)) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'error' => 'invalid_payload'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $estimation = (new QuotePricingEstimator())->estimate($quote);
        echo json_encode(['ok' => true, 'estimation' => $estimation], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
