<?php
/**
 * Title: Accueil – Suivre et soutenir
 * Slug: savoirs-vivants/accueil-soutien
 * Categories: savoirs-vivants
 * Description: Carte « Suivre nos actions » + carte sombre « Soutenir l'association » (wireframe Accueil, section 9).
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background">
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"}}},"backgroundColor":"gris-clair"} -->
			<div class="wp-block-group has-gris-clair-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
				<!-- wp:heading -->
				<h2 class="wp-block-heading">Suivre nos actions</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Clubs CréaBot, soirées Schlouk de Sciences, projets de recherche participative : restez informé·e des prochains rendez-vous de l'association.</p>
				<!-- /wp:paragraph -->
				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">S'inscrire à la lettre d'information</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"}}},"backgroundColor":"bleu-nuit","textColor":"blanc"} -->
			<div class="wp-block-group has-blanc-color has-bleu-nuit-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
				<!-- wp:heading -->
				<h2 class="wp-block-heading">Soutenir l'association</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Savoirs Vivants grandit grâce à vos adhésions et à vos dons. Cotisation annuelle : 10 € pour les particuliers, 50 € pour les associations et les TPE, 200 € pour les ESR et les PME.</p>
				<!-- /wp:paragraph -->
				<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
				<div class="wp-block-group">
					<!-- wp:buttons -->
					<div class="wp-block-buttons">
						<!-- wp:button -->
						<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/faire-un-don/">Faire un don</a></div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
					<!-- wp:paragraph -->
					<p><a href="/s-engager/">Adhérer à l'association</a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->