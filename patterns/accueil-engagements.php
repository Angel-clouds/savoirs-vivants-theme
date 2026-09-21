<?php
/**
 * Title: Accueil – Nos engagements
 * Slug: savoirs-vivants/accueil-engagements
 * Categories: savoirs-vivants
 * Description: Trois cartes d'engagements (wireframe Accueil, section 3).
 */
$svg = get_template_directory_uri() . '/assets/svg/';
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background">
	<!-- wp:paragraph {"className":"is-style-surtitre"} -->
	<p class="is-style-surtitre">Nos engagements</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading -->
	<h2 class="wp-block-heading">Notre cap pour les années à venir</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p>Faire de la connaissance un levier d'émancipation pour chacun, de capacité collective d'agir face au dérèglement climatique, et de transformation démocratique de nos territoires.</p>
	<!-- /wp:paragraph -->

	<!-- wp:columns {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"is-style-carte"} -->
			<div class="wp-block-group is-style-carte">
				<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
				<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'fiole.svg' ); ?>" alt=""/></figure>
				<!-- /wp:image -->
				<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal"}},"textColor":"bleu-nuit","fontSize":"medium"} -->
				<h3 class="wp-block-heading has-bleu-nuit-color has-text-color has-medium-font-size" style="font-style:normal">Ouvrir les sciences aux jeunes</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Continuer d'ouvrir aux jeunes les portes des sciences et des nouvelles technologies, aux côtés de l'Éducation nationale.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"is-style-carte"} -->
			<div class="wp-block-group is-style-carte">
				<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
				<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'feuille.svg' ); ?>" alt=""/></figure>
				<!-- /wp:image -->
				<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal"}},"textColor":"bleu-nuit","fontSize":"medium"} -->
				<h3 class="wp-block-heading has-bleu-nuit-color has-text-color has-medium-font-size" style="font-style:normal">Agir pour l'environnement</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Développer des programmes citoyens autour de l'environnement : îlots de chaleur urbains, qualité de l'eau, recyclage du plastique.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"is-style-carte"} -->
			<div class="wp-block-group is-style-carte">
				<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
				<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'reseau.svg' ); ?>" alt=""/></figure>
				<!-- /wp:image -->
				<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal"}},"textColor":"bleu-nuit","fontSize":"medium"} -->
				<h3 class="wp-block-heading has-bleu-nuit-color has-text-color has-medium-font-size" style="font-style:normal">Renforcer nos coopérations</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Tisser des alliances avec les collectivités, les universités et les entreprises, du Nord Alsace jusqu'à Karlsruhe.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->