# Thème WordPress Savoirs Vivants

## Projet
Thème FSE 100 % sur-mesure (pas de thème enfant, pas de page builder) pour savoirsvivants.fr,
association loi 1901 de Haguenau (Nord Alsace). Dev local : LocalWP sous Linux (adresse dans CLAUDE.local.md, non versionné).

## Stack
- WordPress block theme : theme.json v3, templates/*.html, parts/*.html, patterns/*.php, WordPress ≥ 6.7
- Tailwind CSS v4 (CLI) : source src/css/main.css → assets/css/main.css
  - `npm run build` avant chaque commit (copie aussi tarteaucitron dans assets/vendor)
  - Ne jamais lancer `npm run dev` depuis un agent (commande sans fin)
  - Seules les couches theme + preflight de Tailwind sont importées (aucune classe utilitaire générée) :
    les styles passent par les styles de blocs et src/css/main.css, jamais par des classes dans les compositions
  - assets/vendor/tarteaucitron : fichiers .min uniquement (script « vendor » de package.json)
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
   **Charte 2023** (dossier `chartes graph/`, non versionné) : uniquement vert sauge et bleu nuit (le vert-jaune
   et le noir d'un fichier d'exemples sont exclus) ; H1 Space Mono gras, **H2 gras italique vert sauge**,
   **H3 gras MAJUSCULES bleu nuit** (theme.json), légendes Space Mono gras italique ; logo officiel complet
   (assets/svg/logo-savoirs-vivants.svg, patterns/logo.php) ; motif = symbole officiel (assets/svg/motif-sv-*.svg),
   jamais sous du texte. Pas de surtitre au-dessus des titres hors univers : style « Sous-titre » SOUS le titre.
   Charte SRP : aucune, la charte globale s'applique.
3. **Mobile-first** : CSS de base pour mobile, puis `@media (min-width: …)`.
4. **RGPD** : aucun script tiers sans consentement (tarteaucitron.js, inc/assets.php).
5. **Autonomie de l'éditeur** : sections = compositions (catégorie « Savoirs Vivants – Sections »),
   pages = compositions « Pages complètes » ; styles de blocs : Surtitre (univers seulement), Titre de carte, Carte,
   Carte avec image, Question fréquente, Sous-titre, Étiquette (paragraphe, termes), Filet, Mise en avant (grille).

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
- parts/footer.html appelle la composition patterns/pied-de-page.php (Inserter: no) : textes en gettext.
  Ne pas modifier le pied de page dans l'éditeur de site (la copie en base remplacerait le fichier et figerait le texte).

## Traduction (i18n, préparation FR/DE)
- Text domain `savoirs-vivants`, chargé sur `init` (functions.php), `Domain Path: /languages`.
- Tout texte affiché par inc/ passe par gettext (`__()`, `esc_html__()`, `_n()`, `_x()` + commentaire
  `/* translators: */` pour chaque `%s`/`%d`). Exceptions volontaires : noms des termes créés par défaut
  (contenus), e-mails destinés à l'équipe, fichiers de protection générés.
- Textes d'un modèle .html : impossible en gettext → les déplacer dans une composition PHP appelée par le modèle.
  Une apostrophe droite d'un modèle est affichée « ’ » (wptexturize) : écrire directement « ’ » dans les chaînes.
- Dictionnaire : `wp i18n make-pot . languages/savoirs-vivants.pot --domain=savoirs-vivants --exclude=node_modules,assets/vendor,src`.
  Traductions DANS le thème : `languages/de_DE.po` / `de_DE.mo` (nom = locale seule, sans préfixe de domaine).
- inc/univers.php compare aussi le slug de la traduction dans la langue par défaut (Polylang, `sv_univers_source()`).
- Bannière tarteaucitron : langue = `determine_locale()` si le fichier de langue existe, sinon `fr`.
- Reste à faire : JS (carrousel.js, editeur-champs.js : `wp_set_script_translations`), liens internes en dur
  (/agenda/, /contact/…) dans les compositions et le pied de page.

## État d'avancement (v0.2)
- Fait : header, footer, soutien, tous les modèles, toutes les pages en compositions, agenda,
  ressourcerie, formulaire, cookies, redirections, plan du site, déclaration d'accessibilité (non conforme),
  mentions légales, politique de confidentialité, faire un don, carrousel d'actualités.
- Marqueurs à remplacer avant mise en ligne : `<mark class="sv-a-completer">` (chercher « sv-a-completer »).
- Fait aussi : audits W3C/RGAA/SEO/PHP 8.5 (types de retour stricts), durcissement sécurité (inc/securite.php),
  taxonomie et colonnes des mini-sites, i18n étapes 1-2 (voir « Traduction »), synchronisation Google Agenda.
- À faire : audit RGAA complet,
  outil de newsletter, plateforme de dons, mise en ligne.
- En attente côté équipe :
  - fuseau du site local réglé sur « UTC+2 » fixe → mettre « Paris » (Réglages → Général), sinon
    les heures importées de Google et le filtre « à venir » sont décalés d'une heure en hiver ;
  - SV_GCAL_ICS_URL (vraie adresse du flux) et identifiants SV_SMTP_* à définir dans wp-config.php ;
  - branche `claude/focused-faraday-8kfwuv` (= start + 2 commits d'explication) à pousser : l'agent n'a pas
    d'identifiants GitHub (pas de `gh`), c'est l'utilisatrice ou l'utilisateur qui pousse.
  - i18n étape 3 : Polylang, fichiers de traduction de.po/.mo, JS et liens internes (voir « Traduction »).

## Méthode de travail (décisions de l'équipe)
- Ne jamais commiter ni pousser sans demande explicite ; l'équipe gère l'historique git et la configuration serveur.
  Travail de Claude : sur une branche `claude/…`, messages de commit détaillés (quoi, pourquoi, tests, limites).
- Dépôt public : aucun secret dans le thème (identifiants et adresses privées dans wp-config.php).
- `?ver=` des CSS/JS : version de WordPress **masquée** (empreinte, inc/securite.php), jamais supprimée
  (sinon cache navigateur périmé après mise à jour). Ne pas reposer la question.
- Code de sécurité dans inc/securite.php ; functions.php ne fait que charger les fichiers de inc/.
- Formatage PHP : standard WordPress (tabulations), défini par `.phpcs.xml.dist` (WordPress-Extra, I18n,
  PHPCompatibilityWP 8.2+, syntaxe courte `[]` autorisée). Vérifier avec `phpcs`, corriger avec `phpcbf`.
  Ne plus formater au style PEAR (ancien réglage du formateur VS Code).
- Modifier une composition déjà utilisée : les pages en gardent une copie en base (mettre à jour via
  parse_blocks/serialize_blocks en ciblant metadata.patternName).
- Skill `frontend-design` (.claude/skills, installé via `npx skills add anthropics/skills --skill frontend-design`,
  verrou skills-lock.json) : utile pour la mise en page et la hiérarchie visuelle, mais **subordonné à la charte
  et au RGAA** : jamais de nouvelles couleurs ni polices hors theme.json et univers validés.
- Vérifier après chaque changement PHP : `php -l` (8.5 et 8.2), debug.log, rendu des pages (aucune régression visuelle).
