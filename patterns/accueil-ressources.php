<?php
/**
 * Title: Accueil – Ressources
 * Slug: savoirs-vivants/accueil-ressources
 * Categories: savoirs-vivants
 * Description: Trois cartes de ressources (wireframe Accueil, section 6).
 */
$svg = get_template_directory_uri() . '/assets/svg/';
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"gris-clair","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-gris-clair-background-color has-background">
	<!-- wp:paragraph {"className":"is-style-surtitre"} -->
	<p class="is-style-surtitre">Ressources</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading -->
	<h2 class="wp-block-heading">Produire et diffuser les connaissances</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p>Rapports, publications de recherche et matériel pédagogique : des ressources pour prolonger nos actions.</p>
	<!-- /wp:paragraph -->

	<!-- wp:columns {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"is-style-carte"} -->
			<div class="wp-block-group is-style-carte">
				<!-- wp:image {"sizeSlug":"full","className":"sv-icone"} -->
				<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'document.svg' ); ?>" alt=""/></figure>
				<!-- /wp:image -->
				<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal"}},"textColor":"bleu-nuit","fontSize":"medium"} -->
				<h3 class="wp-block-heading has-bleu-nuit-color has-text-color has-medium-font-size" style="font-style:normal">Rapport d'activités 2025</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"fontSize":"small"} -->
				<p class="has-small-font-size">Histoire, équipe, finances et bilan des trois pôles.</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><a href="#">Télécharger le rapport d'activités 2025 (PDF, 6,6 Mo)</a></p>
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
				<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'document.svg' ); ?>" alt=""/></figure>
				<!-- /wp:image -->
				<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal"}},"textColor":"bleu-nuit","fontSize":"medium"} -->
				<h3 class="wp-block-heading has-bleu-nuit-color has-text-color has-medium-font-size" style="font-style:normal">USUEL : évaluer les compétences numériques</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"fontSize":"small"} -->
				<p class="has-small-font-size">Article de recherche publié dans la revue RFAS (Cairn).</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><a href="/outils-numeriques/">Découvrir le projet USUEL</a></p>
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
				<figure class="wp-block-image size-full sv-icone"><img src="<?php echo esc_url( $svg . 'boite.svg' ); ?>" alt=""/></figure>
				<!-- /wp:image -->
				<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal"}},"textColor":"bleu-nuit","fontSize":"medium"} -->
				<h3 class="wp-block-heading has-bleu-nuit-color has-text-color has-medium-font-size" style="font-style:normal">La Ressourcerie</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"fontSize":"small"} -->
				<p class="has-small-font-size">Robots et kits pédagogiques utilisés en club, à emprunter pour expérimenter à la maison.</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><a href="/ressourcerie/">Voir le matériel disponible</a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->