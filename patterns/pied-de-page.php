<?php
/**
 * Title: Pied de page (contenu)
 * Slug: savoirs-vivants/pied-de-page
 * Inserter: no
 * Description: Contenu traduisible du pied de page, appelé par parts/footer.html. Les textes sont en gettext (domaine savoirs-vivants) : modifier ce fichier plutôt que le pied de page dans l'éditeur de site, sinon les traductions sont perdues.
 */
?>
<!-- wp:group {"className":"sv-bandeau sv-pied","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|40"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group sv-bandeau sv-pied" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--40)">
	<!-- wp:columns -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"40%"} -->
		<div class="wp-block-column" style="flex-basis:40%">
			<!-- wp:paragraph {"style":{"typography":{"fontWeight":"700"}},"fontFamily":"space-mono","fontSize":"medium"} -->
			<p class="has-space-mono-font-family has-medium-font-size" style="font-weight:700">Savoirs Vivants</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Association loi 1901. Un laboratoire citoyen tourné vers la médiation et l’intermédiation scientifique en Nord Alsace.', 'savoirs-vivants' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p><a href="mailto:contact@savoirsvivants.fr">contact@savoirsvivants.fr</a><br><a href="tel:+33749798460">07 49 79 84 60</a><br>30 rue du Maire André Traband, 67500 Haguenau</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"fontSize":"small"} -->
			<h2 class="wp-block-heading has-small-font-size"><?php esc_html_e( 'Nos pôles', 'savoirs-vivants' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:list {"className":"sv-liste-liens"} -->
			<ul class="wp-block-list sv-liste-liens">
				<!-- wp:list-item --><li><a href="/creabot/"><?php esc_html_e( 'CréaBot – Robotique éducative', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="/schlouk-de-sciences/">Schlouk de Sciences</a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="/sciences-participatives/"><?php esc_html_e( 'Sciences participatives', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="/agenda/"><?php esc_html_e( 'Agenda', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"fontSize":"small"} -->
			<h2 class="wp-block-heading has-small-font-size"><?php esc_html_e( 'L’association', 'savoirs-vivants' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:list {"className":"sv-liste-liens"} -->
			<ul class="wp-block-list sv-liste-liens">
				<!-- wp:list-item --><li><a href="/a-propos/"><?php esc_html_e( 'À propos', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="/ressourcerie/"><?php esc_html_e( 'Ressourcerie', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="/outils-numeriques/"><?php esc_html_e( 'Outils numériques', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="/actualites/"><?php esc_html_e( 'Actualités', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
				<!-- wp:list-item --><li><a href="/contact/"><?php esc_html_e( 'Contact', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"fontSize":"small"} -->
			<h2 class="wp-block-heading has-small-font-size"><?php esc_html_e( 'Nous suivre', 'savoirs-vivants' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:social-links {"iconColor":"blanc","iconColorValue":"#FFFFFF","iconBackgroundColor":"bleu-nuit","iconBackgroundColorValue":"#222A60","layout":{"type":"flex","flexWrap":"wrap"}} -->
			<ul class="wp-block-social-links has-icon-color has-icon-background-color">
				<!-- wp:social-link {"url":"https://fr.linkedin.com/company/savoirs-vivants","service":"linkedin","label":"<?php echo esc_attr( sprintf( /* translators: 1 : réseau social, 2 : compte (ex. « Instagram de CréaBot »). */ __( '%1$s de %2$s', 'savoirs-vivants' ), 'LinkedIn', 'Savoirs Vivants' ) ); ?>"} /-->
				<!-- wp:social-link {"url":"https://www.youtube.com/@savoirsvivants","service":"youtube","label":"<?php echo esc_attr( sprintf( /* translators: 1 : réseau social, 2 : compte (ex. « Instagram de CréaBot »). */ __( '%1$s de %2$s', 'savoirs-vivants' ), 'YouTube', 'Savoirs Vivants' ) ); ?>"} /-->
				<!-- wp:social-link {"url":"https://www.instagram.com/creabot67/","service":"instagram","label":"<?php echo esc_attr( sprintf( /* translators: 1 : réseau social, 2 : compte (ex. « Instagram de CréaBot »). */ __( '%1$s de %2$s', 'savoirs-vivants' ), 'Instagram', 'CréaBot' ) ); ?>"} /-->
				<!-- wp:social-link {"url":"https://www.facebook.com/profile.php?id=61582631578789","service":"facebook","label":"<?php echo esc_attr( sprintf( /* translators: 1 : réseau social, 2 : compte (ex. « Instagram de CréaBot »). */ __( '%1$s de %2$s', 'savoirs-vivants' ), 'Facebook', 'CréaBot' ) ); ?>"} /-->
				<!-- wp:social-link {"url":"https://www.instagram.com/schlouk_de_sciences/","service":"instagram","label":"<?php echo esc_attr( sprintf( /* translators: 1 : réseau social, 2 : compte (ex. « Instagram de CréaBot »). */ __( '%1$s de %2$s', 'savoirs-vivants' ), 'Instagram', 'Schlouk de Sciences' ) ); ?>"} /-->
				<!-- wp:social-link {"url":"https://www.facebook.com/profile.php?id=61564440821370","service":"facebook","label":"<?php echo esc_attr( sprintf( /* translators: 1 : réseau social, 2 : compte (ex. « Instagram de CréaBot »). */ __( '%1$s de %2$s', 'savoirs-vivants' ), 'Facebook', 'Schlouk de Sciences' ) ); ?>"} /-->
				<!-- wp:social-link {"url":"https://www.linkedin.com/company/schlouk-de-sciences","service":"linkedin","label":"<?php echo esc_attr( sprintf( /* translators: 1 : réseau social, 2 : compte (ex. « Instagram de CréaBot »). */ __( '%1$s de %2$s', 'savoirs-vivants' ), 'LinkedIn', 'Schlouk de Sciences' ) ); ?>"} /-->
			</ul>
			<!-- /wp:social-links -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"padding":{"top":"var:preset|spacing|30"}},"border":{"top":{"color":"var:preset|color|gris-bordure","width":"2px"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--gris-bordure);border-top-width:2px;margin-top:var(--wp--preset--spacing--40);padding-top:var(--wp--preset--spacing--30)">
		<!-- wp:paragraph -->
		<p>© 2026 Savoirs Vivants</p>
		<!-- /wp:paragraph -->
		<!-- wp:list {"className":"sv-liste-liens sv-liste-liens--ligne"} -->
		<ul class="wp-block-list sv-liste-liens sv-liste-liens--ligne">
			<!-- wp:list-item --><li><a href="/plan-du-site/"><?php esc_html_e( 'Plan du site', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="/mentions-legales/"><?php esc_html_e( 'Mentions légales', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="/politique-de-confidentialite/"><?php esc_html_e( 'Politique de confidentialité', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="/accessibilite/"><?php esc_html_e( 'Accessibilité : non conforme', 'savoirs-vivants' ); ?></a></li><!-- /wp:list-item -->
		</ul>
		<!-- /wp:list -->
		<!-- wp:html -->
		<button type="button" class="tarteaucitronOpenPanel sv-lien-bouton"><?php esc_html_e( 'Gestion des cookies', 'savoirs-vivants' ); ?></button>
		<!-- /wp:html -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
