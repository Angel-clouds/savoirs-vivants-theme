<?php
/**
 * Title: Page – Plan du site
 * Slug: savoirs-vivants/page-plan-du-site
 * Categories: savoirs-vivants-pages
 * Description: Liste de toutes les pages, mise à jour automatiquement (RGAA 12.1).
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|30"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"48rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--30)">
<!-- wp:paragraph {"className":"is-style-surtitre"} -->
<p class="is-style-surtitre">Navigation</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Plan du site</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Toutes les pages du site Savoirs Vivants, ainsi que l’agenda de chaque pôle.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"48rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading -->
<h2 class="wp-block-heading">Pages</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Cette liste se met à jour automatiquement à chaque nouvelle page publiée.</p>
<!-- /wp:paragraph -->
<!-- wp:page-list {"className":"sv-liste-liens"} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Agenda</h2>
<!-- /wp:heading -->
<!-- wp:list {"className":"sv-liste-liens"} -->
<ul class="wp-block-list sv-liste-liens">
<!-- wp:list-item --><li><a href="/agenda/">Tous les prochains rendez-vous</a></li><!-- /wp:list-item -->
<!-- wp:list-item --><li><a href="/agenda/pole/creabot/">Agenda de CréaBot</a></li><!-- /wp:list-item -->
<!-- wp:list-item --><li><a href="/agenda/pole/schlouk-de-sciences/">Agenda de Schlouk de Sciences</a></li><!-- /wp:list-item -->
<!-- wp:list-item --><li><a href="/agenda/pole/sciences-participatives/">Agenda des sciences participatives</a></li><!-- /wp:list-item -->
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:group -->
