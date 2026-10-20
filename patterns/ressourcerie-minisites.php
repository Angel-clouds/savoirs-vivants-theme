<?php
/**
 * Title: Ressourcerie – Mini-sites
 * Slug: savoirs-vivants/ressourcerie-minisites
 * Categories: sv-ressourcerie
 * Description: Mini-sites pédagogiques classés par pôle (menu Ressourcerie → Mini-sites). Un pôle sans mini-site affiche un message.
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

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<h3 class="wp-block-heading" style="margin-top:var(--wp--preset--spacing--50)">CréaBot</h3>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[sv_mini_sites pole="creabot" niveau="4"]
<!-- /wp:shortcode -->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<h3 class="wp-block-heading" style="margin-top:var(--wp--preset--spacing--50)">Schlouk de Sciences</h3>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[sv_mini_sites pole="schlouk-de-sciences" niveau="4"]
<!-- /wp:shortcode -->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<h3 class="wp-block-heading" style="margin-top:var(--wp--preset--spacing--50)">Sciences participatives</h3>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[sv_mini_sites pole="sciences-participatives" niveau="4"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->
