<?php
/**
 * Title: Ressourcerie – Matériel disponible
 * Slug: savoirs-vivants/ressourcerie-catalogue
 * Categories: sv-ressourcerie
 * Description: Liste automatique des fiches du menu « Ressourcerie ».
 * Keywords: matériel, kits, robots
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"gris-clair","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-gris-clair-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading -->
<h2 class="wp-block-heading">Le matériel disponible</h2>
<!-- /wp:heading -->
<!-- wp:query {"queryId":4,"query":{"perPage":12,"pages":0,"offset":0,"postType":"sv_materiel","order":"asc","orderBy":"title","inherit":false},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-query" style="margin-top:var(--wp--preset--spacing--40)">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":2}} -->
<!-- wp:group {"className":"is-style-carte-image"} -->
<div class="wp-block-group is-style-carte-image">
<!-- wp:post-featured-image {"aspectRatio":"16/9"} /-->
<!-- wp:post-terms {"term":"sv_type_materiel","className":"is-style-etiquette","fontSize":"small"} /-->
<!-- wp:post-title {"level":3,"isLink":true,"className":"is-style-titre-carte"} /-->
<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"sv_age"}}}},"style":{"typography":{"fontWeight":"700"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-weight:700"></p>
<!-- /wp:paragraph -->
<!-- wp:post-excerpt {"excerptLength":28} /-->
<!-- wp:read-more {"content":"Voir la fiche"} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->
<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Le catalogue est en cours de mise à jour. Contactez-nous pour connaître le matériel disponible.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->
