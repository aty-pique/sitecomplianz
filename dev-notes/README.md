# Retours client → développeur

Ce dossier sert à **centraliser les commentaires** laissés depuis le site (mode balisé activé dans `.env`), **sans mélanger** avec le code métier du projet.

## Contenu

| Fichier | Rôle |
|---------|------|
| `inbox.jsonl` | Fichier généré : **une ligne JSON par retour** (horodatage, URL de la page, message). Créé automatiquement au premier envoi. |
| `README.md` | Ce mode d’emploi. |

## Pour la personne qui laisse un commentaire

1. Activer `DEV_CLIENT_FEEDBACK_ENABLED=1` dans `.env` (en local ou sur un environnement de préprod).
2. Sur le site : bouton **« Retours »** en **haut à droite** → cela **active le mode retours** (bandeau vert + bouton mis en évidence).
3. **Cliquer sur la page** à l’endroit pertinent → le panneau « Note au développeur » s’ouvre → saisir le texte → envoyer.
4. Pour **annuler** le mode sans commenter : recliquer sur **« Retours »**, ou touche **Échap**.
5. Page dédiée : `/dev-feedback` (liste de tous les retours).

> Le **menu contextuel** du navigateur (clic droit) n’est plus intercepté : il fonctionne normalement tant que le mode retours n’est pas utilisé.

## Pour le développeur

### Retirer tous les retours rapidement

- **Supprimer uniquement les messages :** effacez le fichier `dev-notes/inbox.jsonl` (ou videz-le).
- **Tout retirer sans toucher au reste du site :** supprimez **entièrement** le dossier `dev-notes/` puis recréez-le avec ce `README.md` si besoin (ou laissez-le recréé au prochain commentaire — un nouveau `inbox.jsonl` sera généré).

### Désactiver la fonctionnalité (production)

Dans `.env` :

```env
DEV_CLIENT_FEEDBACK_ENABLED=0
```

Les routes `/dev-feedback` disparaissent, le script des retours n’est plus chargé : **aucune trace côté navigateur**.

### Vider la file depuis l’interface web

Sur `/dev-feedback`, utiliser le formulaire « Vider la file » avec le jeton défini dans `.env` (`DEV_FEEDBACK_CLEAR_TOKEN`). Sans jeton, ce bouton est désactivé.

### Versionnement Git

Par défaut, `inbox.jsonl` peut être versionné pour partager les retours avec l’équipe.  
Pour **ne jamais committer** les messages, ajoutez dans `.gitignore` :

```
dev-notes/inbox.jsonl
```
