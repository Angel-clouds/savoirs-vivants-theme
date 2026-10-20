<?php
/**
 * Title: Ressourcerie – Mini-sites
 * Slug: savoirs-vivants/ressourcerie-minisites
 * Categories: sv-ressourcerie
 * Description: Mini-sites pédagogiques : une colonne par catégorie principale, regroupés par sous-catégorie, générée automatiquement depuis Ressourcerie → Catégories de mini-sites.
 * Keywords: mini-sites, ressources, en ligne, html
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:paragraph {"className":"is-style-surtitre"} -->
<p class="is-style-surtitre">En ligne</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Les mini-sites des pôles</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Des ressources à explorer directement en ligne, classées par pôle. Chaque mini-site s’ouvre dans un nouvel onglet.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[sv_mini_sites format="colonnes" niveau="3"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
