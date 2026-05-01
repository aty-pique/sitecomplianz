# Complianz — Maquette de site

Maquette de site web développée en PHP 8.3 avec le moteur de templates Twig.

## Prérequis

- PHP >= 8.1
- Composer

## Installation

```bash
composer install
cp .env.example .env
```

## Démarrer le serveur de développement

**Méthode 1 — Script PowerShell (recommandé) :**

```powershell
.\serve.ps1
# ou sur un port différent :
.\serve.ps1 -Port 8080
```

**Méthode 2 — Composer :**

```bash
composer serve
```

Le site est accessible sur **http://localhost:8000**

## Structure du projet

```
sitecomplianz/
├── config/
│   └── routes.php          # Définition des routes
├── public/                 # Racine web (seul dossier exposé)
│   ├── assets/
│   │   ├── css/main.css
│   │   └── js/main.js
│   ├── index.php           # Point d'entrée
│   ├── router.php          # Routeur pour le serveur intégré PHP
│   └── .htaccess           # Réécriture d'URL (Apache)
├── src/
│   ├── Controllers/        # Contrôleurs
│   ├── Core/               # Classes noyau (Router, Request, View)
│   └── Models/             # Modèles
├── templates/
│   ├── layouts/base.twig   # Layout principal
│   ├── pages/              # Templates de pages
│   └── partials/           # En-tête, pied de page, etc.
├── storage/logs/           # Logs applicatifs
├── .env                    # Variables d'environnement (non versionné)
├── .env.example            # Exemple de configuration
└── composer.json
```

## Technologies

| Outil | Version |
|---|---|
| PHP | 8.3 |
| Twig (templates) | 3.x |
| phpdotenv | 5.x |
| PHPUnit | 11.x |
