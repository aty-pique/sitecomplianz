<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

class LabProjectController
{
    /** @var array<string, mixed>|null */
    private static ?array $projects = null;

    /** @return array<string, mixed> */
    private static function loadProjects(): array
    {
        if (self::$projects === null) {
            $file            = ROOT_PATH . '/config/lab_projects.php';
            self::$projects = file_exists($file) ? (array) require $file : [];
        }

        return self::$projects;
    }

    public function show(Request $request, array $params = []): void
    {
        $slug = isset($params['slug']) ? (string) $params['slug'] : '';
        $all  = self::loadProjects();
        $page = $all[$slug] ?? null;

        if ($page === null || ! is_array($page)) {
            http_response_code(404);
            require ROOT_PATH . '/templates/pages/404.php';

            return;
        }

        View::render('pages/lab-project.twig', [
            'title'            => (string) ($page['meta_title'] ?? 'Projet'),
            'meta_description' => (string) ($page['meta_description'] ?? ''),
            'project'          => $page,
            'project_slug'     => $slug,
            'feedback_ok'      => ($request->get('feedback') ?? '') === 'ok',
        ]);
    }
}
