# Thème WordPress Savoirs Vivants

## Projet
Thème FSE 100 % sur-mesure (pas de thème enfant, pas de page builder) pour savoirsvivants.fr,
association loi 1901 de Haguenau (Nord Alsace). Dev local : LocalWP sous Linux (adresse dans CLAUDE.local.md, non versionné).

## Stack
- WordPress block theme : theme.json v3, templates/*.html, parts/*.html, patterns/*.php, WordPress ≥ 6.7
- Tailwind CSS v4 (CLI) : source src/css/main.css → assets/css/main.css
  - `npm run build` avant chaque commit (copie aussi tarteaucitron dans assets/vendor)
  - Ne jamais lancer `npm run dev` depuis un agent (commande sans fin)
  - Tailwind scanne patterns/ et templates/ : les classes WordPress (size-full…) peuvent générer
    des utilitaires parasites → les exclure avec `@source not inline("…")`
- JS vanilla uniquement (assets/js), PHP aux standards WordPress (inc/)
- Git : travailler sur la branche `start`, fusionner dans `master` après validation

## Règles non négociables
1. **Accessibilité RGAA** : HTML sémantique, un seul h1 par page, hiérarchie de titres, focus visible,
   liens explicites, ARIA pertinent, alternatives d'images (vide si décorative), pas d'animation
   automatique sans pause. Justifier chaque choix d'accessibilité.
2. **Charte verrouillée** (theme.json = source unique) : Bleu nuit #222A60, Vert sauge #16987C
   (titres ≥ 24px et décor uniquement), Vert foncé #117A63 (liens), Blanc, Gris clair #F7F7F7,
   Gris bordure #DCDFE6. Space Mono (titres) / Space Grotesk (texte), auto-hébergées.
   **Exception validée : univers des pôles** (inc/univers.php, section 10 de src/css/main.css),
   limitée au `<main>` des pages du pôle (en-tête, Soutien et pied de page restent à la charte globale) :
   - CréaBot : Orbitron (titres) / Exo 2 (texte), couleurs `--cb-*` ;
   - Schlouk de Sciences : Bebas Neue (titres) / Poppins (texte), couleurs `--sk-*`.
   Polices auto-hébergées (assets/fonts). Contrastes RGAA à respecter comme pour la charte globale.
3. **Mobile-first** : CSS de base pour mobile, puis `@media (min-width: …)`.
4. **RGPD** : aucun script tiers sans consentement (tarteaucitron.js, inc/assets.php).
5. **Autonomie de l'éditeur** : sections = compositions (catégorie « Savoirs Vivants – Sections »),
   pages = compositions « Pages complètes » ; styles de blocs : Surtitre, Titre de carte, Carte,
   Carte avec image, Question fréquente.

## Contenu
- Source de vérité : Rapport d'activités 2025. Aucun Lorem Ipsum, aucune info inventée.
- Mise en page : placement des wireframes (desktop + mobile), charte appliquée par-dessus.
- Ressourcerie, réseaux sociaux et adresse : repris du site actuel.

## Architecture
- inc/contenus.php : types « sv_evenement » (Agenda, /agenda/) et « sv_materiel » (/ressourcerie/xxx/),
  taxonomies sv_pole et sv_type_materiel, champs + panneau éditeur, source de liaison
  « savoirs-vivants/evenement », filtre « événements à venir ».
- inc/sync-agenda.php : import horaire (WP-Cron « sv_sync_agenda ») du flux .ics Google Agenda (SV_GCAL_ICS_URL,
  à définir dans wp-config.php) ; clé _sv_gcal_uid, nouveaux événements en brouillon, mise à jour seulement si Google
  a changé (_sv_gcal_empreinte), corbeille respectée, passés et annulés ignorés. Test : `wp cron event run sv_sync_agenda`.
- inc/formulaire-contact.php : code court [sv_formulaire_contact].
- inc/mini-sites.php : type « sv_minisite » (menu Ressourcerie → Mini-sites, administrateurs), import .zip
  vérifié et extrait dans uploads/mini-sites/<id>-<jeton>/, servi par /mini-sites/<slug>/ avec CSP « sandbox »
  (aucune ressource tierce), taxonomie hiérarchique « sv_minisite_cat » (Ressourcerie → Catégories de mini-sites,
  distincte des pôles de l'agenda sv_pole ; catégories principales creabot, schlouk-de-sciences, sciences-participatives),
  code court [sv_mini_sites format="colonnes" niveau="3"] (une carte par catégorie principale, générée depuis
  l'arborescence, mini-sites groupés par sous-catégorie sous un h4 ; utilisé par la composition ressourcerie-minisites),
  ou [sv_mini_sites categorie="…" format="liste"] pour une seule colonne (alias pole="…", sous-catégories incluses).
  Prod nginx : `location ^~ /wp-content/uploads/mini-sites/ { deny all; }` (Apache : .htaccess auto).
- inc/redirections.php : 301 depuis les anciennes adresses.
- inc/securite.php : comptes masqués (REST, archives d'auteur, sitemap, oEmbed), commentaires fermés.
- inc/smtp.php : envoi SMTP via phpmailer_init, identifiants SV_SMTP_* dans wp-config.php (jamais dans le thème).
- assets/js/carrousel.js : carrousel accessible sur toute Boucle de requête de classe sv-carrousel (chargé à la demande).
- inc/univers.php : classe `sv-univers-creabot` / `sv-univers-schlouk` sur le <body> des pages de pôle
  (et sous-pages) ; la section 10 de src/css/main.css applique la charte du pôle au seul <main>
  (CréaBot : charte officielle Orbitron / Exo 2 ; Schlouk : affiche bénévoles, Bebas Neue / Poppins ;
  Sciences participatives : charte globale, titres Space Mono italique vert sauge, motifs du rapport d'activités).
- parts/soutien.html : bloc « Suivre / Soutenir » affiché avant le pied de page sur toutes les pages.

## État d'avancement (v0.2)
- Fait : header, footer, soutien, tous les modèles, toutes les pages en compositions, agenda,
  ressourcerie, formulaire, cookies, redirections, plan du site, déclaration d'accessibilité (non conforme),
  mentions légales, politique de confidentialité, faire un don, carrousel d'actualités.
- Marqueurs à remplacer avant mise en ligne : `<mark class="sv-a-completer">` (chercher « sv-a-completer »).
- À faire : audit RGAA complet,
  outil de newsletter, plateforme de dons, mise en ligne.
