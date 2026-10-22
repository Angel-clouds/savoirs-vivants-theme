<?php
/**
 * Title: À propos – Introduction
 * Slug: savoirs-vivants/a-propos-hero
 * Categories: sv-a-propos
 * Description: Titre, présentation, boutons et photo.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
<!-- wp:paragraph {"className":"is-style-surtitre"} -->
<p class="is-style-surtitre">Histoire et équipe</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">L'association Savoirs Vivants</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Née en 2023 de l'ambition d'hommes et de femmes soucieux d'agir au quotidien, Savoirs Vivants développe des formes d'intermédiation scientifique pour associer toutes les classes sociales à la réalisation des défis de leur époque.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#adherer">Adhérer à l'association</a></div>
<!-- /wp:button -->
<!-- wp:button {"textColor":"bleu-nuit","className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-bleu-nuit-color has-text-color wp-element-button" href="<?php echo esc_url( wp_get_upload_dir()['baseurl'] . '/rapport-activites-2025.pdf' ); ?>">Consulter le rapport 2025</a></div>
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
