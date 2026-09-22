<?php
/**
 * Title: Composant – Texte et photo
 * Slug: savoirs-vivants/generique-texte-image
 * Categories: savoirs-vivants
 * Description: Titre, paragraphes et photo côte à côte.
 * Keywords: texte, image, deux colonnes
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:heading {"placeholder":"Titre de la section"} -->
<h2 class="wp-block-heading"></h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"placeholder":"Premier paragraphe…"} -->
<p></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"placeholder":"Deuxième paragraphe…"} -->
<p></p>
<!-- /wp:paragraph -->
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
