<?php
/**
 * Title: Page – Ressourcerie
 * Slug: savoirs-vivants/page-ressourcerie
 * Categories: savoirs-vivants-pages
 * Description: Matériel à emprunter, modalités et FAQ (maquette « Ressourcerie »).
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
<!-- wp:paragraph {"className":"is-style-surtitre"} -->
<p class="is-style-surtitre">Matériel pédagogique</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Ressourcerie : du matériel pédagogique à utiliser chez soi</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>La Ressourcerie propose le matériel pédagogique et numérique utilisé en club et en atelier, pour permettre aux enfants de continuer à expérimenter à la maison avec des outils concrets et stimulants.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Réserver du matériel</a></div>
<!-- /wp:button -->
<!-- wp:button {"textColor":"bleu-nuit","className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-bleu-nuit-color has-text-color wp-element-button" href="#modalites">Comment ça marche ?</a></div>
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
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"gris-clair","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-gris-clair-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading -->
<h2 class="wp-block-heading">Le matériel disponible</h2>
<!-- /wp:heading -->
<!-- wp:query {"queryId":4,"query":{"perPage":12,"pages":0,"offset":0,"postType":"sv_materiel","order":"asc","orderBy":"title","inherit":false},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-query" style="margin-top:var(--wp--preset--spacing--40)">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":2}} -->
<!-- wp:group {"className":"is-style-carte-image"} -->
<div class="wp-block-group is-style-carte-image">
<!-- wp:post-featured-image {"aspectRatio":"16/9"} /-->
<!-- wp:post-terms {"term":"sv_type_materiel","fontSize":"small"} /-->
<!-- wp:post-title {"level":3,"isLink":true,"className":"is-style-titre-carte"} /-->
<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"sv_age"}}}},"style":{"typography":{"fontWeight":"700"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-weight:700"></p>
<!-- /wp:paragraph -->
<!-- wp:post-excerpt {"excerptLength":28} /-->
<!-- wp:read-more {"content":"Voir la fiche"} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->
<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Le catalogue est en cours de mise à jour. Contactez-nous pour connaître le matériel disponible.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->
<!-- wp:group {"anchor":"modalites","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"blanc","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div id="modalites" class="wp-block-group alignfull has-blanc-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading -->
<h2 class="wp-block-heading">Comment ça marche ?</h2>
<!-- /wp:heading -->
<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"grid","minimumColumnWidth":"14rem"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)">
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:paragraph {"className":"sv-etape"} -->
<p class="sv-etape">1</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"className":"is-style-titre-carte"} -->
<h3 class="wp-block-heading is-style-titre-carte">Choisir le matériel</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Consultez les fiches : l’âge conseillé, l’équipement nécessaire à la maison et le tarif sont indiqués pour chaque kit.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:paragraph {"className":"sv-etape"} -->
<p class="sv-etape">2</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"className":"is-style-titre-carte"} -->
<h3 class="wp-block-heading is-style-titre-carte">Nous contacter</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Indiquez le matériel souhaité et la période d’emprunt via le formulaire de contact.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"is-style-carte"} -->
<div class="wp-block-group is-style-carte">
<!-- wp:paragraph {"className":"sv-etape"} -->
<p class="sv-etape">3</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"className":"is-style-titre-carte"} -->
<h3 class="wp-block-heading is-style-titre-carte">Expérimenter à la maison</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Utilisez le matériel avec ses fiches, conçues pour vous guider rapidement et simplement.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"gris-clair","layout":{"type":"constrained","contentSize":"75rem"}} -->
<div class="wp-block-group alignfull has-gris-clair-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:heading -->
<h2 class="wp-block-heading">Questions fréquentes</h2>
<!-- /wp:heading -->
<!-- wp:details {"className":"is-style-faq"} -->
<details class="wp-block-details is-style-faq"><summary>À quels âges s’adressent les équipements ?</summary>
<!-- wp:paragraph -->
<p>Les matériels sont adaptés à différents âges, généralement à partir de 6-7 ans, avec des niveaux progressifs selon l’autonomie de l’enfant. L’âge recommandé est indiqué sur chaque fiche.</p>
<!-- /wp:paragraph -->
</details>
<!-- /wp:details -->
<!-- wp:details {"className":"is-style-faq"} -->
<details class="wp-block-details is-style-faq"><summary>Mon enfant doit-il déjà savoir coder ?</summary>
<!-- wp:paragraph -->
<p>Non. Les équipements comme Codey Rocky ou Makey Makey sont conçus pour être utilisés sans prérequis, grâce à des interfaces simples et intuitives. Ce sont aussi des équipements utilisés lors des clubs de robotique.</p>
<!-- /wp:paragraph -->
</details>
<!-- /wp:details -->
<!-- wp:details {"className":"is-style-faq"} -->
<details class="wp-block-details is-style-faq"><summary>De quel équipement ai-je besoin à la maison ?</summary>
<!-- wp:paragraph -->
<p>Selon le matériel, un ordinateur, une tablette ou un smartphone peut être nécessaire. Les logiciels ou applications requis sont précisés dans la fiche de chaque matériel.</p>
<!-- /wp:paragraph -->
</details>
<!-- /wp:details -->
<!-- wp:details {"className":"is-style-faq"} -->
<details class="wp-block-details is-style-faq"><summary>Comment choisir le matériel le plus adapté ?</summary>
<!-- wp:paragraph -->
<p>Vous pouvez vous baser sur l’âge de l’enfant, son niveau et ses centres d’intérêt, et les activités déjà pratiquées en club. Les fiches sont conçues pour vous guider rapidement et simplement.</p>
<!-- /wp:paragraph -->
</details>
<!-- /wp:details -->
</div>
<!-- /wp:group -->
