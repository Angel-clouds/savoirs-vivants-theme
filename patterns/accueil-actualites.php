<?php
/**
 * Title: Accueil – Actualités
 * Slug: savoirs-vivants/accueil-actualites
 * Categories: sv-accueil
 * Description: Carrousel accessible des dernières actualités (articles), sans défilement automatique.
 * Keywords: actualités, articles, blog
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:paragraph {"className":"is-style-surtitre"} -->
<p class="is-style-surtitre">Actualités</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">La vie de l'association</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Événements, projets, retombées dans la presse : les dernières nouvelles de Savoirs Vivants.</p>
<!-- /wp:paragraph -->
<!-- wp:query {"queryId":1,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"sv-carrousel","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-query sv-carrousel" style="margin-top:var(--wp--preset--spacing--40)">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":3,"minimumColumnWidth":"16rem"}} -->
<!-- wp:group {"className":"is-style-carte-image"} -->
<div class="wp-block-group is-style-carte-image">
<!-- wp:post-featured-image {"aspectRatio":"16/9"} /-->
<!-- wp:post-terms {"term":"category","fontSize":"small"} /-->
<!-- wp:post-date {"fontSize":"small"} /-->
<!-- wp:post-title {"level":3,"isLink":true,"className":"is-style-titre-carte"} /-->
<!-- wp:post-excerpt {"excerptLength":22} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->
<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Aucune actualité n'est publiée pour le moment.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
<!-- wp:paragraph -->
<p><a href="/actualites/">Toutes les actualités</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
