<?php
/**
 * Title: USUEL – Pour qui ?
 * Slug: savoirs-vivants/usuel-benefices
 * Categories: sv-outils
 * Description: Trois cartes : personnes accompagnées, médiateurs, recherche.
 */
$svg = get_template_directory_uri() . '/assets/svg/';
?>
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
<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url($svg . 'reseau.svg'); ?>" alt=""/></figure>
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
<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url($svg . 'document.svg'); ?>" alt=""/></figure>
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
<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url($svg . 'fiole.svg'); ?>" alt=""/></figure>
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
