<?php
/**
 * Title: Accueil – Nos engagements
 * Slug: savoirs-vivants/accueil-engagements
 * Categories: sv-accueil
 * Description: Trois cartes d’engagements.
 */
$svg = get_template_directory_uri() . '/assets/svg/';
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:paragraph {"className":"is-style-surtitre"} -->
<p class="is-style-surtitre">Nos engagements</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Notre cap pour les années à venir</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Faire de la connaissance un levier d'émancipation pour chacun, de capacité collective d'agir face au dérèglement climatique, et de transformation démocratique de nos territoires.</p>
<!-- /wp:paragraph -->
<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"grid","minimumColumnWidth":"16rem"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)">
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'fiole.svg' ); ?>" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:heading {"level":3,"className":"is-style-titre-carte"} -->
<h3 class="wp-block-heading is-style-titre-carte">Ouvrir les sciences aux jeunes</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Continuer d'ouvrir aux jeunes les portes des sciences et des nouvelles technologies, aux côtés de l'Éducation nationale.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'feuille.svg' ); ?>" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:heading {"level":3,"className":"is-style-titre-carte"} -->
<h3 class="wp-block-heading is-style-titre-carte">Agir pour l'environnement</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Développer des programmes citoyens autour de l'environnement : îlots de chaleur urbains, qualité de l'eau, recyclage du plastique.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'reseau.svg' ); ?>" alt=""/></figure>
<!-- /wp:image -->
<!-- wp:heading {"level":3,"className":"is-style-titre-carte"} -->
<h3 class="wp-block-heading is-style-titre-carte">Renforcer nos coopérations</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Tisser des alliances avec les collectivités, les universités et les entreprises, du Nord Alsace jusqu'à Karlsruhe.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
