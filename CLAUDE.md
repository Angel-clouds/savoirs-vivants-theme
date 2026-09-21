# Thème WordPress Savoirs Vivants

## Projet
Thème FSE 100 % sur-mesure (pas de thème enfant, pas de page builder) pour savoirsvivants.fr,
association loi 1901 de Haguenau (Nord Alsace). Dev local : LocalWP sous Linux.

## Stack
- WordPress block theme : theme.json v3, templates/*.html, parts/*.html, patterns/*.php
- Tailwind CSS v4 (CLI) : source src/css/main.css → assets/css/main.css
  - `npm run dev` (watch) / `npm run build` (minifié, à lancer avant chaque commit)
- JS vanilla uniquement, PHP aux standards WordPress
- Git : travailler sur la branche `start`, fusionner dans `master` après validation

## Règles non négociables
1. **Accessibilité RGAA 100 %** : HTML sémantique, un seul h1 par page, hiérarchie de titres,
   focus visible, liens explicites (jamais « En savoir plus » seul), ARIA pertinent,
   alternatives d'images (vide si décorative), pas d'animation automatique sans pause.
   Justifier chaque choix d'accessibilité.
2. **Charte graphique verrouillée** (theme.json = source unique) :
   - Bleu nuit #222A60 (texte, boutons), Vert sauge #16987C (uniquement titres ≥ 24px et décor),
     Vert sauge foncé #117A63 (liens, survols), Blanc, Gris clair #F7F7F7 (fonds), Gris bordure #DCDFE6 (décor)
   - Titres : Space Mono Bold ; H3 = Space Mono Bold Italic vert ; H4 = majuscules ; texte : Space Grotesk
   - Polices auto-hébergées dans assets/fonts (jamais Google Fonts CDN)
   - Aucune couleur/taille hors charte (palette Tailwind par défaut désactivée)
3. **Mobile-first** : CSS de base pour mobile, puis `@media (min-width: …)`.
4. **RGPD** : aucun script tiers sans consentement (tarteaucitron.js prévu).
5. **Autonomie de l'éditeur** : sections = compositions (patterns) insérables par glisser-déposer,
   catégorie « Savoirs Vivants » ; styles de blocs réutilisables (Surtitre, Carte).

## Contenu
- Source de vérité : le Rapport d'activités 2025 de Savoirs Vivants. Aucun Lorem Ipsum, aucune info inventée.
- Mise en page : suivre le placement des wireframes (desktop + mobile), avec la charte appliquée par-dessus.
- Ressourcerie et adresse (30 rue du Maire André Traband, 67500 Haguenau) : reprises du site actuel.

## État d'avancement
- Fait : squelette, theme.json, polices, header (logo + titre + slogan, nav par pôles, boutons S'engager / Faire un don),
  front-page.html, patterns accueil-hero, accueil-engagements, accueil-poles.
- À faire (accueil) : actualités (carrousel accessible, sans autoplay), agenda (CPT événement),
  ressources, chiffres clés (1800 / 1600 / 52 / 7), partenaires, newsletter + soutien.
- À faire ensuite : footer, pages CréaBot, Schlouk de Sciences, Sciences participatives, Ressourcerie (CPT matériel),
  Outils numériques, À propos, Contact, tarteaucitron.js.