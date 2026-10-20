<?php
/**
 * Title: Ressourcerie – Mini-sites
 * Slug: savoirs-vivants/ressourcerie-minisites
 * Categories: sv-ressourcerie
 * Description: Mini-sites pédagogiques en trois colonnes, une par pôle (menu Ressourcerie → Mini-sites). Liste compacte, lisible même avec de nombreux mini-sites.
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

<!-- wp:group {"className":"sv-minisites-colonnes","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"grid","columnCount":3,"minimumColumnWidth":"16rem"}} -->
<div class="wp-block-group sv-minisites-colonnes" style="margin-top:var(--wp--preset--spacing--40)">
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:shortcode -->
[sv_mini_sites pole="creabot" format="liste" titre="CréaBot" niveau="3"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:shortcode -->
[sv_mini_sites pole="schlouk-de-sciences" format="liste" titre="Schlouk de Sciences" niveau="3"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:shortcode -->
[sv_mini_sites pole="sciences-participatives" format="liste" titre="Sciences participatives" niveau="3"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
