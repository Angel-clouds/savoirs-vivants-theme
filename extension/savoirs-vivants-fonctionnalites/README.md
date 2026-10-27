# Savoirs Vivants – Fonctionnalités

Extension propre au site savoirsvivants.fr : tout ce qui doit survivre à un changement de thème.

| Fichier | Rôle |
| --- | --- |
| `inc/contenus.php` | Types « sv_evenement » (Agenda) et « sv_materiel » (Ressourcerie), taxonomies, champs, panneau de l'éditeur, source de liaison `savoirs-vivants/evenement`, aperçu de la carte |
| `inc/agenda-filtres.php` | Code court `[sv_filtres_agenda]` et filtres de la boucle de l'agenda |
| `inc/sync-agenda.php` | Import horaire du flux .ics Google Agenda (WP-Cron `sv_sync_agenda`) |
| `inc/mini-sites.php` | Mini-sites .zip de la Ressourcerie, code court `[sv_mini_sites]` |
| `inc/formulaire-contact.php` | Code court `[sv_formulaire_contact]` |
| `inc/redirections.php` | Redirections 301 depuis l'ancien site |
| `inc/securite.php` | Comptes masqués, commentaires fermés, `?ver=` masqué |
| `inc/smtp.php` | Envoi SMTP (identifiants `SV_SMTP_*` dans wp-config.php) |

L'affichage (modèles `single-sv_evenement`, `taxonomy-sv_pole`, compositions, CSS) reste dans le thème
« Savoirs Vivants ». Sans le thème, tout fonctionne mais sans la mise en forme de la charte.

## Réglages (wp-config.php, jamais dans le dépôt public)

- `SV_GCAL_ICS_URL` : adresse du flux .ics de Google Agenda.
- `SV_SMTP_HOST`, `SV_SMTP_PORT`, `SV_SMTP_SECURE`, `SV_SMTP_USER`, `SV_SMTP_PASSWORD`, `SV_SMTP_FROM`.
- `SV_PROXIES_CONFIANCE` (facultatif) : adresses ou plages des proxys / CDN de confiance, pour lire l'IP
  réelle des visiteurs (limite d'envois du formulaire). Exemple : `[ '127.0.0.1', '173.245.48.0/20' ]`.

## Mise en ligne

1. Le thème v0.3 (ou plus récent) doit être en ligne **avant** l'activation : il contient le garde-fou
   qui évite le double chargement du même code (`SV_FONCTIONNALITES`).
2. Archive : `wp dist-archive .` (ou un .zip du dossier sans les fichiers de `.distignore`),
   puis Extensions → Ajouter → Téléverser une extension → Activer.
   Ou : `rsync -av --delete --exclude-from=.distignore ./ utilisateur@serveur:/chemin/wp-content/plugins/savoirs-vivants-fonctionnalites/`
3. Vérifier : /agenda/, une fiche d'événement, /agenda/pole/creabot/, /ressourcerie/, un mini-site, /contact/.

Activation et désactivation ne touchent à aucun contenu : les règles d'adresse sont régénérées,
la tâche horaire de l'import Google est arrêtée à la désactivation.

## Développement local

Le dossier vit dans le dépôt du thème (`extension/`) et est relié à WordPress par un lien symbolique :

    ln -s ../themes/savoirs-vivants/extension/savoirs-vivants-fonctionnalites wp-content/plugins/savoirs-vivants-fonctionnalites

Dictionnaire : `wp i18n make-pot . languages/savoirs-vivants-fonctionnalites.pot --domain=savoirs-vivants-fonctionnalites`
