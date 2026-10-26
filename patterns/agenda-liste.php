<?php
/**
 * Title: Agenda – Filtres et rendez-vous
 * Slug: savoirs-vivants/agenda-liste
 * Categories: sv-agenda
 * Description: Filtres par agenda et par mois, puis la liste des événements à venir (mise à jour automatiquement).
 * Keywords: agenda, événements, filtres
 *
 * La boucle porte "svFiltres":true dans sa requête : c'est elle que les filtres de
 * [sv_filtres_agenda] restreignent (inc/agenda-filtres.php). Une seule boucle filtrable par page.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"60rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:0;padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:shortcode -->
[sv_filtres_agenda]
<!-- /wp:shortcode -->
<!-- wp:query {"queryId":20,"query":{"perPage":20,"pages":0,"offset":0,"postType":"sv_evenement","order":"asc","orderBy":"date","inherit":false,"svFiltres":true}} -->
<div class="wp-block-query">
<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<!-- wp:group {"tagName":"article","className":"is-style-carte","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<article class="wp-block-group is-style-carte">
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
<!-- wp:post-terms {"term":"sv_pole","className":"is-style-etiquette","fontSize":"small"} /-->
<!-- wp:post-title {"level":3,"isLink":true,"className":"is-style-titre-carte"} /-->
<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"savoirs-vivants/evenement","args":{"key":"horaires_lieu"}}}},"fontSize":"small"} -->
<p class="has-small-font-size"></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:read-more {"content":"Voir le détail"} /-->
</article>
<!-- /wp:group -->
<!-- /wp:post-template -->
<!-- wp:query-pagination {"paginationArrow":"none"} -->
<!-- wp:query-pagination-previous {"label":"Événements précédents"} /-->
<!-- wp:query-pagination-numbers /-->
<!-- wp:query-pagination-next {"label":"Événements suivants"} /-->
<!-- /wp:query-pagination -->
<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Aucun rendez-vous ne correspond pour le moment. Suivez-nous sur les réseaux sociaux pour connaître les prochaines dates.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->
