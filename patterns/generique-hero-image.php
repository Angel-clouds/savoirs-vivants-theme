<?php
/**
 * Title: Composant – Introduction avec photo
 * Slug: savoirs-vivants/generique-hero-image
 * Categories: savoirs-vivants
 * Description: Titre principal (h1), texte, boutons et photo. À placer une seule fois, en haut de page.
 * Keywords: hero, introduction, en-tête
 */
?>
<!-- wp:group {"align":"full","className":"sv-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull sv-hero has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%">
<!-- wp:heading {"level":1,"placeholder":"Titre principal de la page"} -->
<h1 class="wp-block-heading"></h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"placeholder":"Texte de présentation…"} -->
<p></p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"placeholder":"Bouton principal"} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"></a></div>
<!-- /wp:button -->
<!-- wp:button {"textColor":"bleu-nuit","className":"is-style-outline","placeholder":"Bouton secondaire"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-bleu-nuit-color has-text-color wp-element-button"></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center","width":"42%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:42%">
<!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img alt="" style="aspect-ratio:4/3;object-fit:cover"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
