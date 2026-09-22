<?php
/**
 * Title: Composant – Bandeau d’appel à l’action
 * Slug: savoirs-vivants/generique-appel
 * Categories: savoirs-vivants
 * Description: Bandeau bleu nuit : titre, texte et deux boutons (contrastes automatiques).
 * Keywords: CTA, bouton, bandeau
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"bleu-nuit","textColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-color has-bleu-nuit-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading {"textAlign":"center","placeholder":"Titre de l’appel à l’action"} -->
<h2 class="wp-block-heading has-text-align-center"></h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","placeholder":"Texte d’accompagnement…"} -->
<p class="has-text-align-center"></p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
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
<!-- /wp:group -->
