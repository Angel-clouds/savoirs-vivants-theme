<?php
/**
 * Title: Accueil – Agenda
 * Slug: savoirs-vivants/accueil-agenda
 * Categories: savoirs-vivants
 * Description: Les trois prochains événements (mis à jour automatiquement).
 * Keywords: agenda, événements
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:paragraph {"className":"is-style-surtitre"} -->
<p class="is-style-surtitre">Agenda</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">Les prochains rendez-vous</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Clubs, stages, conférences conviviales, quiz et journées de recherche participative.</p>
<!-- /wp:paragraph -->
<!-- wp:query {"queryId":2,"query":{"perPage":3,"pages":0,"offset":0,"postType":"sv_evenement","order":"asc","orderBy":"date","inherit":false},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-query" style="margin-top:var(--wp--preset--spacing--40)">
<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
			<!-- wp:group {"className":"is-style-carte","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
			<div class="wp-block-group is-style-carte">
				<!-- wp:group {"className":"sv-date","style":{"spacing":{"blockGap":"0.25rem"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
				<div class="wp-block-group sv-date">
					<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"savoirs-vivants/evenement","args":{"key":"jour"}}}},"className":"sv-date__jour"} -->
					<p class="sv-date__jour"></p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"savoirs-vivants/evenement","args":{"key":"mois"}}}},"className":"sv-date__mois"} -->
					<p class="sv-date__mois"></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
				<!-- wp:group {"className":"sv-evenement__infos","style":{"spacing":{"blockGap":"0.4rem"}},"layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group sv-evenement__infos">
					<!-- wp:post-terms {"term":"sv_pole","fontSize":"small"} /-->
					<!-- wp:post-title {"level":3,"isLink":true,"className":"is-style-titre-carte"} /-->
					<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"savoirs-vivants/evenement","args":{"key":"horaires_lieu"}}}},"fontSize":"small"} -->
					<p class="has-small-font-size"></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
				<!-- wp:read-more {"content":"Voir le détail"} /-->
			</div>
			<!-- /wp:group -->
<!-- /wp:post-template -->
<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Aucun rendez-vous n'est programmé pour le moment. Suivez-nous sur les réseaux sociaux pour connaître les prochaines dates.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
<!-- wp:paragraph -->
<p><a href="/agenda/">Voir tout l’agenda</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
