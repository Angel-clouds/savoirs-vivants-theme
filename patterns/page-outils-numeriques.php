<?php
/**
 * Title: Page – Outils numériques (USUEL)
 * Slug: savoirs-vivants/page-outils-numeriques
 * Categories: savoirs-vivants-pages
 * Description: Présentation de l’outil USUEL (maquette « Outils numériques »).
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1400
 */
$svg = get_template_directory_uri() . '/assets/svg/';
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
<!-- wp:paragraph {"className":"is-style-surtitre"} -->
<p class="is-style-surtitre">Inclusion numérique</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">USUEL : évaluer les compétences numériques</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Face à la dématérialisation des services et de l'accès aux droits, USUEL est une plateforme d'auto-évaluation des compétences numériques, conçue avec des médiateurs numériques et des personnes en situation d'illectronisme.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://usuel.savoirsvivants.fr/">Accéder à USUEL</a></div>
<!-- /wp:button -->
<!-- wp:button {"textColor":"bleu-nuit","className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-bleu-nuit-color has-text-color wp-element-button" href="/contact/">Devenir terrain pilote</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
<!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img alt="" style="aspect-ratio:4/3;object-fit:cover"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"gris-clair","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-gris-clair-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:heading {"className":"sv-sr-only"} -->
<h2 class="wp-block-heading sv-sr-only">USUEL en chiffres</h2>
<!-- /wp:heading -->
<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"layout":{"type":"grid","minimumColumnWidth":"11rem"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--10)">
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:paragraph {"align":"center","className":"sv-chiffre"} -->
<p class="has-text-align-center sv-chiffre">74</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"align":"center","className":"sv-chiffre-label"} -->
<p class="has-text-align-center sv-chiffre-label">Participants à l'étude</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:paragraph {"align":"center","className":"sv-chiffre"} -->
<p class="has-text-align-center sv-chiffre">1</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"align":"center","className":"sv-chiffre-label"} -->
<p class="has-text-align-center sv-chiffre-label">Article publié dans la revue RFAS</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:paragraph {"align":"center","className":"sv-chiffre"} -->
<p class="has-text-align-center sv-chiffre">2026</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"align":"center","className":"sv-chiffre-label"} -->
<p class="has-text-align-center sv-chiffre-label">Soutenance de thèse prévue</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading -->
<h2 class="wp-block-heading">Un outil pour tout l’écosystème</h2>
<!-- /wp:heading -->
<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"grid","minimumColumnWidth":"16rem"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)">
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'reseau.svg' ); ?>" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:heading {"level":3,"className":"is-style-titre-carte"} -->
<h3 class="wp-block-heading is-style-titre-carte">Pour les personnes accompagnées</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Mesurer ses compétences numériques et suivre sa progression face aux démarches en ligne.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'document.svg' ); ?>" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:heading {"level":3,"className":"is-style-titre-carte"} -->
<h3 class="wp-block-heading is-style-titre-carte">Pour les médiateurs numériques</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Évaluer les besoins et accompagner la montée en compétences des publics.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'fiole.svg' ); ?>" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:heading {"level":3,"className":"is-style-titre-carte"} -->
<h3 class="wp-block-heading is-style-titre-carte">Pour la recherche</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Mieux comprendre l'illectronisme et ses effets sur l'accès aux droits : un article publié dans la RFAS et une thèse en cours.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"gris-clair","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-gris-clair-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:paragraph {"className":"is-style-surtitre"} -->
<p class="is-style-surtitre">En préparation</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Une application web pour les travailleurs sociaux</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Prochaines étapes : étendre le dispositif à de nouveaux partenaires de médiation numérique, consolider l'outil d'évaluation et développer une version application web à destination des travailleurs sociaux.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#">Lire l'article dans la RFAS</a></div>
<!-- /wp:button -->
<!-- wp:button {"textColor":"bleu-nuit","className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-bleu-nuit-color has-text-color wp-element-button" href="/contact/">Devenir testeur</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:image {"aspectRatio":"16/9","scale":"cover","sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img alt="" style="aspect-ratio:16/9;object-fit:cover"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
